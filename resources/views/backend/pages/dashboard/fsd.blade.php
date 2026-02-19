@extends('backend.master.template')
@section('content')
<main class="content">
<div class="row mb-2">
        <div class="col-6">
            <div class="breadcrumps">
                <h1 class="header-title">
                    FSD & ASD Dashboard
                </h1>
                <p class="header-subtitle">Summary of all your records</p>
            </div>
        </div>
    </div>

    <div class="viewed-analytics">
        <div class="grid-container show" id="general_report">
            <div class="grid-card-1 g-c">
                <div class="gcard">
                    <div id="transaction">
                        <div class="title">
                            <h5 class="card-title">
                                <i class="fas fa-users"></i> Total Quotation
                            </h5>
                        </div>
                        <h1>0</h1>
                        <div class="translate">
                            <span>Overall number of quotation</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid-card-2 g-c">
                <div class="gcard">
                    <div id="daily">
                        <div class="title">
                            <h5 class="card-title">
                                <i class="fas fa-money-bill"></i> Sales Representative
                            </h5>
                        </div>
                        <h1>0</h1>
                        <div class="translate">
                            <span>Overall number of sales representative</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid-chart-1 g-c">
                <div>
                    <div id="status" style="height:67vh;">
                        <div class="title">
                            <h5 class="card-title">
                                <i class="fas fa-phone-volume"></i> Quotations
                            </h5>
                        </div>
                        <div class="chart-container mb-3">
                            <div id="status_chart"></div>
                        </div>
                        <div class="legend">
                            <div class="row">
                                <div class="col-3" id="total_calls">
                                    <div class="legend-container bg-primary">
                                        <div class="chart-title">Total</div>
                                        <div class="counts">0</div>
                                    </div>
                                </div>
                                <div class="col-3" id="complete_calls">
                                    <div class="legend-container">
                                        <div class="chart-title">Category</div>
                                        <div class="counts">0</div>
                                    </div>
                                </div>
                                <div class="col-3" id="incomplete_calls">
                                    <div class="legend-container">
                                        <div class="chart-title">Sources</div>
                                        <div class="counts">0</div>
                                    </div>
                                </div>
                                <div class="col-3" id="cancelled_calls">
                                    <div class="legend-container">
                                        <div class="chart-title">Cancelled</div>
                                        <div class="counts">0</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid-list-1 g-c">
                <div>
                    <div id="per_sales_representative">
                        <div class="title">
                            <span class="back hide"><i class="fas fa-arrow-left"></i></span>
                            <h5 class="card-title">
                                <i class="fas fa-list"></i> Total quotation per sales representative
                            </h5>
                        </div>
                        <div class="division-list"></div>
                    </div>
                </div>
            </div>
            <div class="grid-list-2 g-c">
                <div>
                    <div id="per_year">
                        <div class="title">
                            <h5 class="card-title">
                                <i class="fas fa-list"></i> Quotations per year summary
                            </h5>
                        </div>
                        <div class="associate-list"></div>
                    </div>
                </div>
            </div>
            <div class="grid-list-3 g-c">
                <div>
                    <div id="per_sources">
                        <div class="title">
                            <h5 class="card-title">
                                <i class="fas fa-list"></i> Quotations per sources
                            </h5>
                        </div>
                        <div class="agent-list"></div>
                    </div>
                </div>
            </div>
            <div class="grid-list-4 g-c">
                <div>
                    <div id="per_category">
                        <div class="title">
                            <h5 class="card-title">
                                <i class="fas fa-list"></i> Quotations per category
                            </h5>
                        </div>
                        <div class="industry-list"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-details">
            <div id="record_details">
                <div class="record-header mb-2">
                    <div class="row">
                        <div class="col-9">
                            <div class="record-title"></div>
                        </div>
                        <div class="col-3">
                            <div class="record-close text-right">
                                <button onclick="hideDetails()"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="row">
                            <div class="col-3">
                                <label for="">Company:</label>
                                <input type="text" class="form-control form-control-sm" id="f_item_name" name="f_item_name" oninput="filterBox('f_item_name', 'sales-company-name')">
                            </div>
                            <div class="col-3">
                                <label for="">Date:</label>
                                <input type="date" class="form-control form-control-sm" id="f_date" name="f_date" onchange="filterBox('f_date', 'sales-date')">
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <hr/><br>
                        <div class="sales_list"></div>
                    </div>
                    {{-- <div class="col-6">
                        <table id="generated_table" class="table table-striped" style="width:100%"></table>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>

</main>
@endsection

@section('scripts')
<script src="/js/datatables.min.js"></script>
<script src="/js/apex.js"></script>
<script src="/js/phil.js"></script>
@endsection


@section('style')
<style>
    .card.in-progress.badge-warning {
        background-color: #ffa200 !important;
        color: white !important;
    }
    .card.to-do.badge-primary {
        background: #0b2972 !important;
    }
    .card.cancelled.badge-danger {
        background: #a70e13 !important;
    }
    .card {
        border: none !important;
        border-radius: 0 !important;
    }
    . {
        color: black;
    }
    main.content {
        margin-top: 3.5em !important;
    }
    .header-title {
        font-size: 1.64063rem;
        color: #000000 !important;
        font-weight: bold;
    }
    .header-subtitle {
        font-size: .9375rem;
        color: #960301;
    }
    .rounded-circle {
        border-radius: 50% !important;
        background: #960301;
    }
    .card.pending.badge-info {
        background: #007dfe;
        color: white;
    }
    .card.completed.badge-success {
        background: #04ab04 !important;
    }
</style>
<link href="{{ asset('css/custom/graph.css') }}" rel="stylesheet">

@endsection
