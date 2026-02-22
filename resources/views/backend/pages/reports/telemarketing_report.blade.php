@extends('backend.master.template')

@section('content')
<main class="content">
    <div class="container-fluid">
        <div class="header">
            <h1 class="header-title">Telemarketing Report</h1>
        </div>

        <div class="card report-filter-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Filter Parameters</h5>
                <small class="text-muted">Refine telemarketing records</small>
            </div>
            <div class="card-body">
                <form id="telemarketing-report-filter">
                    <div class="form-row compact-row">
                        <div class="form-group col-lg-4 col-md-6">
                            <label class="filter-label">Company</label>
                            <select name="company_id" class="form-control form-control-sm searchable-select">
                                <option value="">All</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-lg-4 col-md-6">
                            <label class="filter-label">Assigned To</label>
                            <select name="assigned_to" class="form-control form-control-sm searchable-select">
                                <option value="">All</option>
                                @foreach ($telemarketers as $telemarketer)
                                    <option value="{{ $telemarketer->id }}">
                                        {{ $telemarketer->id == 1 ? 'UNASSIGNED' : $telemarketer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-lg-4 col-md-6">
                            <label class="filter-label">Status</label>
                            <select name="status" class="form-control form-control-sm searchable-select">
                                <option value="">All</option>
                                <option value="TO DO">TO DO</option>
                                <option value="IN PROGRESS">IN PROGRESS</option>
                                <option value="CANCELLED">CANCELLED</option>
                                <option value="PENDING">PENDING</option>
                                <option value="ON-HOLD">ON-HOLD</option>
                                <option value="COMPLETED">COMPLETED</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row compact-row">
                        <div class="form-group col-lg-3 col-md-6">
                            <label class="filter-label">Follow Up Start</label>
                            <input type="date" name="follow_up_start" class="form-control form-control-sm">
                        </div>
                        <div class="form-group col-lg-3 col-md-6">
                            <label class="filter-label">Follow Up End</label>
                            <input type="date" name="follow_up_end" class="form-control form-control-sm">
                        </div>
                        <div class="form-group col-lg-3 col-md-6">
                            <label class="filter-label">Date Purchased Start</label>
                            <input type="date" name="date_purchased_start" class="form-control form-control-sm">
                        </div>
                        <div class="form-group col-lg-3 col-md-6">
                            <label class="filter-label">Date Purchased End</label>
                            <input type="date" name="date_purchased_end" class="form-control form-control-sm">
                        </div>
                    </div>

                    <div class="text-right filter-actions">
                        <button type="button" class="btn btn-secondary btn-sm" id="tm-reset-filters">Reset</button>
                        <button type="button" class="btn btn-primary btn-sm" id="tm-apply-filters">Apply Filters</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body py-2">
                <div class="row">
                    <div class="col-md-6 mb-2 mb-md-0">
                        <div class="summary-box">
                            <div class="summary-label">Total Transactions</div>
                            <div class="summary-value" id="tm-summary-total-transactions">0</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="summary-box">
                            <div class="summary-label">Total Sales Amount</div>
                            <div class="summary-value" id="tm-summary-total-sales">0.00</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <table id="telemarketing-report-table" class="table table-striped" style="width:100%"></table>
            </div>
        </div>
    </div>
</main>
@endsection

@section('scripts')
<script src="/js/datatables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="/js/page/telemarketing_report.js"></script>
@endsection

@section('style')
<style>
    .report-filter-card {
        border: 1px solid #d9dde3;
    }
    .compact-row .form-group {
        margin-bottom: 0.6rem;
    }
    .filter-label {
        font-size: 12px;
        margin-bottom: 3px;
        font-weight: 600;
        color: #2f3a47;
    }
    .filter-actions {
        margin-top: 6px;
        border-top: 1px solid #eceff3;
        padding-top: 10px;
    }
    .summary-box {
        border: 1px solid #eceff3;
        border-radius: 6px;
        padding: 10px 12px;
        background: #fafbfc;
    }
    .summary-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #6c757d;
        font-weight: 700;
    }
    .summary-value {
        font-size: 22px;
        font-weight: 700;
        color: #2f3a47;
        line-height: 1.2;
        margin-top: 2px;
    }
    .select2-container {
        width: 100% !important;
    }
    .select2-container .select2-selection--single {
        height: 31px;
        border: 1px solid #ced4da;
        border-radius: .2rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 29px;
        font-size: 12px;
        color: #495057;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 29px;
    }
</style>
@endsection

