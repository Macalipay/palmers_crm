<?php

namespace App\Console\Commands;

use App\Services\TelemarketingOldToNewSyncService;
use Illuminate\Console\Command;

class SyncOldTelemarketingToNew extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telemarketing:sync-old-to-new
                            {--since= : Only sync rows updated at or after this datetime}
                            {--chunk=500 : Number of source rows to process per chunk}
                            {--force-source-wins : Overwrite local rows with old DB values whenever they differ}
                            {--validate-email= : Compare the exact TeleMarketing screen count for this user on old vs local}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Telemarketing data from the old database into the local database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $service = new TelemarketingOldToNewSyncService(
            'old_mysql',
            config('database.default'),
            (int) $this->option('chunk'),
            (bool) $this->option('force-source-wins')
        );

        if ($this->option('validate-email')) {
            return $this->validateScreenCount($service, $this->option('validate-email'));
        }

        $this->info('Starting Telemarketing sync from old database to local database.');

        $summary = $service->sync($this->option('since'));

        $this->line('');
        $this->table(
            ['Table', 'Processed', 'Inserted', 'Updated', 'Skipped', 'Failed'],
            $this->buildTableRows($summary['tables'])
        );

        $this->line('');
        $this->info(sprintf(
            'Totals: processed=%d, inserted=%d, updated=%d, skipped=%d, failed=%d',
            $summary['totals']['processed'],
            $summary['totals']['inserted'],
            $summary['totals']['updated'],
            $summary['totals']['skipped'],
            $summary['totals']['failed']
        ));

        foreach ($summary['tables'] as $table => $tableSummary) {
            foreach ($tableSummary['errors'] as $error) {
                $this->warn($error);
            }
        }

        return $summary['totals']['failed'] > 0 ? 1 : 0;
    }

    protected function validateScreenCount(TelemarketingOldToNewSyncService $service, $email)
    {
        $this->info('Comparing TeleMarketing screen counts on old vs local.');

        $source = $service->getTelemarketingScreenCountByEmail('old_mysql', $email);
        $target = $service->getTelemarketingScreenCountByEmail(config('database.default'), $email);

        if (!$source) {
            $this->error(sprintf('User [%s] was not found on the old database.', $email));
            return 1;
        }

        if (!$target) {
            $this->error(sprintf('User [%s] was not found on the local database.', $email));
            return 1;
        }

        $this->table(
            ['Connection', 'User ID', 'Name', 'Email', 'TeleMarketing Screen Count'],
            [
                ['old_mysql', $source['user_id'], $source['name'], $source['email'], $source['count']],
                [config('database.default'), $target['user_id'], $target['name'], $target['email'], $target['count']],
            ]
        );

        $difference = $target['count'] - $source['count'];

        if ($difference === 0) {
            $this->info('Validation matched. Old and local screen counts are equal.');
            return 0;
        }

        $this->warn(sprintf(
            'Validation mismatch. Local is %d %s than old.',
            abs($difference),
            $difference > 0 ? 'higher' : 'lower'
        ));

        return 0;
    }

    protected function buildTableRows(array $tables)
    {
        $rows = [];

        foreach ($tables as $table => $summary) {
            $rows[] = [
                $table,
                $summary['processed'],
                $summary['inserted'],
                $summary['updated'],
                $summary['skipped'],
                $summary['failed'],
            ];
        }

        return $rows;
    }
}
