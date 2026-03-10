<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TelemarketingOldToNewSyncService
{
    /**
     * @var string
     */
    protected $sourceConnection;

    /**
     * @var string
     */
    protected $targetConnection;

    /**
     * @var int
     */
    protected $chunkSize;

    /**
     * @var array
     */
    protected $forceSourceWins;

    /**
     * @var array
     */
    protected $tables = [
        'branches',
        'divisions',
        'province_names',
        'sources',
        'brands',
        'companies',
        'users',
        'sales_associates',
        'stores',
        'items',
        'item_durations',
        'sales',
        'sale_details',
        'telemarketings',
        'telemarketing_details',
        'telemarketing_call_logs',
    ];

    public function __construct($sourceConnection = 'old_mysql', $targetConnection = null, $chunkSize = 500, $forceSourceWins = false)
    {
        $this->sourceConnection = $sourceConnection;
        $this->targetConnection = $targetConnection ?: config('database.default');
        $this->chunkSize = (int) $chunkSize;
        $this->forceSourceWins = (bool) $forceSourceWins;
    }

    public function sync($since = null)
    {
        $summary = [
            'source_connection' => $this->sourceConnection,
            'target_connection' => $this->targetConnection,
            'chunk_size' => $this->chunkSize,
            'since' => $since,
            'tables' => [],
            'totals' => [
                'processed' => 0,
                'inserted' => 0,
                'updated' => 0,
                'skipped' => 0,
                'failed' => 0,
            ],
        ];

        foreach ($this->tables as $table) {
            try {
                $tableSummary = $this->syncTable($table, $since);
            } catch (\Throwable $exception) {
                $tableSummary = [
                    'processed' => 0,
                    'inserted' => 0,
                    'updated' => 0,
                    'skipped' => 0,
                    'failed' => 1,
                    'errors' => [$exception->getMessage()],
                ];
            }

            $summary['tables'][$table] = $tableSummary;
            $summary['totals']['processed'] += $tableSummary['processed'];
            $summary['totals']['inserted'] += $tableSummary['inserted'];
            $summary['totals']['updated'] += $tableSummary['updated'];
            $summary['totals']['skipped'] += $tableSummary['skipped'];
            $summary['totals']['failed'] += $tableSummary['failed'];
        }

        return $summary;
    }

    public function getTelemarketingScreenCountByEmail($connection, $email)
    {
        $user = DB::connection($connection)
            ->table('users')
            ->select('id', 'email', 'name')
            ->where('email', $email)
            ->first();

        if (!$user) {
            return null;
        }

        $count = DB::connection($connection)
            ->table('telemarketing_details')
            ->join('sale_details', 'telemarketing_details.order_id', '=', 'sale_details.id')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('items', 'sale_details.item_id', '=', 'items.id')
            ->where('items.branch_id', 2)
            ->where('telemarketing_details.status', 'TO DO')
            ->where('telemarketing_details.assigned_to', $user->id)
            ->where('sales.branch_id', '!=', 3)
            ->distinct()
            ->count('sales.company_id');

        return [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'count' => $count,
        ];
    }

    protected function syncTable($table, $since = null)
    {
        $columns = $this->getSharedColumns($table);

        if (empty($columns)) {
            return [
                'processed' => 0,
                'inserted' => 0,
                'updated' => 0,
                'skipped' => 0,
                'failed' => 1,
                'errors' => ['No shared columns found for table ['.$table.'].'],
            ];
        }

        $summary = [
            'processed' => 0,
            'inserted' => 0,
            'updated' => 0,
            'skipped' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        $sourceQuery = DB::connection($this->sourceConnection)
            ->table($table)
            ->select($columns)
            ->orderBy('id');

        if ($since) {
            $sourceQuery->where('updated_at', '>=', $this->normalizeSince($since));
        }

        $sourceQuery->chunkById($this->chunkSize, function ($rows) use ($columns, $table, &$summary) {
            foreach ($rows as $row) {
                $summary['processed']++;

                try {
                    $sourceRow = (array) $row;
                    $sourceId = $sourceRow['id'];
                    $targetRow = DB::connection($this->targetConnection)
                        ->table($table)
                        ->where('id', $sourceId)
                        ->first();

                    if (!$targetRow) {
                        DB::connection($this->targetConnection)
                            ->table($table)
                            ->insert($sourceRow);
                        $summary['inserted']++;
                        continue;
                    }

                    if ($this->shouldUpdate($sourceRow, (array) $targetRow)) {
                        $updatePayload = $sourceRow;
                        unset($updatePayload['id']);

                        DB::connection($this->targetConnection)
                            ->table($table)
                            ->where('id', $sourceId)
                            ->update($updatePayload);
                        $summary['updated']++;
                        continue;
                    }

                    $summary['skipped']++;
                } catch (\Throwable $exception) {
                    $summary['failed']++;

                    if (count($summary['errors']) < 10) {
                        $summary['errors'][] = sprintf(
                            'Table [%s], ID [%s]: %s',
                            $table,
                            isset($sourceRow['id']) ? $sourceRow['id'] : 'unknown',
                            $exception->getMessage()
                        );
                    }
                }
            }
        }, 'id');

        return $summary;
    }

    protected function getSharedColumns($table)
    {
        $sourceColumns = Schema::connection($this->sourceConnection)->getColumnListing($table);
        $targetColumns = Schema::connection($this->targetConnection)->getColumnListing($table);

        $sharedColumns = array_values(array_intersect($sourceColumns, $targetColumns));

        return $sharedColumns;
    }

    protected function shouldUpdate(array $sourceRow, array $targetRow)
    {
        if ($this->forceSourceWins) {
            return $this->rowsDiffer($sourceRow, $targetRow);
        }

        $sourceUpdatedAt = $this->parseTimestamp(isset($sourceRow['updated_at']) ? $sourceRow['updated_at'] : null);
        $targetUpdatedAt = $this->parseTimestamp(isset($targetRow['updated_at']) ? $targetRow['updated_at'] : null);

        if (!$sourceUpdatedAt && !$targetUpdatedAt) {
            return false;
        }

        if ($sourceUpdatedAt && !$targetUpdatedAt) {
            return true;
        }

        if (!$sourceUpdatedAt) {
            return false;
        }

        return $sourceUpdatedAt->gt($targetUpdatedAt);
    }

    protected function rowsDiffer(array $sourceRow, array $targetRow)
    {
        foreach ($sourceRow as $column => $value) {
            $targetValue = isset($targetRow[$column]) ? $targetRow[$column] : null;

            if ($this->normalizeValue($value) !== $this->normalizeValue($targetValue)) {
                return true;
            }
        }

        return false;
    }

    protected function normalizeValue($value)
    {
        if ($value === null) {
            return null;
        }

        if (is_bool($value)) {
            return (int) $value;
        }

        return (string) $value;
    }

    protected function parseTimestamp($value)
    {
        if (empty($value)) {
            return null;
        }

        return Carbon::parse($value);
    }

    protected function normalizeSince($since)
    {
        return Carbon::parse($since)->toDateTimeString();
    }
}
