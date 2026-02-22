@extends('backend.master.template')

@section('content')
<main class="content">
    <div class="container-fluid">
        <div class="header">
            <h1 class="header-title">Sales Report</h1>
        </div>

        <div class="card report-filter-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Filter Parameters</h5>
                <small class="text-muted">Refine before export or print</small>
            </div>
            <div class="card-body">
                <form id="sales-report-filter" class="report-filter-form">
                    <div class="report-filter-section">Organization</div>
                    <div class="form-row compact-row organization-row">
                        <div class="form-group col-lg-3 col-md-4 col-sm-6">
                            <label class="filter-label">Company</label>
                            <select name="company_id" class="form-control form-control-sm searchable-select">
                                <option value="">All</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-lg-3 col-md-4 col-sm-6">
                            <label class="filter-label">Store</label>
                            <select name="store_id" class="form-control form-control-sm searchable-select">
                                <option value="">All</option>
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->store_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-lg-3 col-md-4 col-sm-6">
                            <label class="filter-label">Source</label>
                            <select name="source_id" class="form-control form-control-sm searchable-select">
                                <option value="">All</option>
                                @foreach ($sources as $source)
                                    <option value="{{ $source->id }}">{{ $source->source }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-lg-3 col-md-4 col-sm-6">
                            <label class="filter-label">Division</label>
                            <select name="division_id" class="form-control form-control-sm searchable-select">
                                <option value="">All</option>
                                @foreach ($divisions as $division)
                                    <option value="{{ $division->id }}">{{ $division->division }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-lg-3 col-md-4 col-sm-6">
                            <label class="filter-label">Branch</label>
                            <select name="branch_id" class="form-control form-control-sm searchable-select">
                                <option value="">All</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-8">
                            <div class="report-filter-section">Purchase Date Range</div>
                            <div class="form-row compact-row">
                                <div class="form-group col-sm-6">
                                    <label class="filter-label">Date Purchased Start</label>
                                    <input type="date" name="date_purchased_start" class="form-control form-control-sm">
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="filter-label">Date Purchased End</label>
                                    <input type="date" name="date_purchased_end" class="form-control form-control-sm">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="report-filter-section">Sales Personnel</div>
                            <div class="form-row compact-row">
                                <div class="form-group col-md-4 col-sm-6">
                                    <label class="filter-label">Sales Agent</label>
                                    <select name="user_id" class="form-control form-control-sm searchable-select">
                                        <option value="">All</option>
                                        @foreach ($salesAgents as $agent)
                                            <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4 col-sm-6">
                                    <label class="filter-label">Sales Associate</label>
                                    <select name="sales_associate_id" class="form-control form-control-sm searchable-select">
                                        <option value="">All</option>
                                        @foreach ($salesAssociates as $associate)
                                            <option value="{{ $associate->id }}">{{ $associate->sales_associate }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4 col-sm-6">
                                    <label class="filter-label">Merchandiser</label>
                                    <select name="merchandiser_id" class="form-control form-control-sm searchable-select">
                                        <option value="">All</option>
                                        @foreach ($merchandisers as $merchandiser)
                                            <option value="{{ $merchandiser->id }}">{{ $merchandiser->merchandiser }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-right filter-actions">
                        <button type="button" class="btn btn-secondary btn-sm" id="reset-filters">Reset</button>
                        <button type="button" class="btn btn-primary btn-sm" id="apply-filters">Apply Filters</button>
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
                            <div class="summary-value" id="summary-total-transactions">0</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="summary-box">
                            <div class="summary-label">Total Sales Amount</div>
                            <div class="summary-value" id="summary-total-sales">0.00</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <table id="sales-report-table" class="table table-striped" style="width:100%"></table>
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
<script src="/js/page/sales_report.js"></script>
@endsection

@section('style')
<style>
    .report-filter-card {
        border: 1px solid #d9dde3;
    }
    .report-filter-form .compact-row .form-group {
        margin-bottom: 0.6rem;
    }
    .report-filter-section {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #6c757d;
        font-weight: 700;
        margin: 8px 0 8px;
        border-bottom: 1px solid #eceff3;
        padding-bottom: 4px;
    }
    .report-filter-section:first-child {
        margin-top: 0;
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
    @media (min-width: 1200px) {
        .organization-row .form-group {
            -ms-flex: 0 0 20%;
            flex: 0 0 20%;
            max-width: 20%;
        }
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
