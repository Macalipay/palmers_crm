@extends('backend.master.template')
@section('content')
<main class="content">
    <div class="row mb-2">
        <div class="col-md-5 col-sm-12">
            <div class="breadcrumps">
                <h1 class="header-title">
                    OTC Dashboard
                </h1>
                <p class="header-subtitle">Summary of all your records</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-12">
            <input type="date" class="form-control form-control-sm" id="start_date" name="start_date">
        </div>
        <div class="col-md-3 col-sm-12">
            <input type="date" class="form-control form-control-sm" id="end_date" name="end_date">
        </div>
        <div class="col-md-1 col-sm-12">
            <button class="btn btn-primary btn-sm" id="generate_button" onclick="viewDetailed()">
                <i class="fas fa-search"></i> Generate
            </button>
        </div>
    </div>

    <div class="viewed-analytics">
        <div class="grid-container show" id="general_report">
            <div class="grid-card-1 g-c">
                <div class="gcard">
                    <div id="transaction">
                        <div class="title">
                            <h5 class="card-title">
                                <i class="fas fa-users"></i> Total Sales
                            </h5>
                        </div>
                        <h1>₱ <span id="total_sales">{{number_format($sale, 2)}}</span></h1>
                        <div class="translate">
                            <span>Total Sales based on OTC only</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid-card-2 g-c">
                <div class="gcard" onclick="clickCard('merchandiser')" id="merchandiser">
                    <div id="daily">
                        <div class="title">
                            <h5 class="card-title">
                                <i class="fas fa-money-bill"></i> Merchandiser
                            </h5>
                        </div>
                        <h1><span id="merchandiser_value">{{number_format($merchandiser)}}</span></h1>
                        <div class="translate">
                            <span>Total Merchandiser Involved</span>
                        </div>
                        <div class="filter-display"></div>
                    </div>
                </div>
            </div>
            <div class="grid-card-3 g-c">
                <div class="gcard" onclick="clickCard('sales_associate')" id="sales_associate">
                    <div id="daily">
                        <div class="title">
                            <h5 class="card-title">
                                <i class="fas fa-money-bill"></i> Sales Associate
                            </h5>
                        </div>
                        <h1><span id="associate_value">{{$sales_associates}}</span></h1>
                        <div class="translate">
                            <span>Total Sales Associate Involved</span>
                        </div>
                        <div class="filter-display"></div>
                    </div>
                </div>
            </div>
            <div class="grid-card-4 g-c">
                <div class="gcard" onclick="clickCard('source')" id="source">
                    <div id="daily">
                        <div class="title">
                            <h5 class="card-title">
                                <i class="fas fa-money-bill"></i> Source
                            </h5>
                        </div>
                        <h1><span id="source_value">{{$source}}</span></h1>
                        <div class="translate">
                            <span>Total source involved</span>
                        </div>
                        <div class="filter-display"></div>
                    </div>
                </div>
            </div>
            <div class="grid-list-1 g-c">
                <div>
                    <div id="per_sales_representative">
                        <div class="title">
                            <span class="back hide"><i class="fas fa-arrow-left"></i></span>
                            <h5 class="card-title">
                                <i class="fas fa-list"></i> Top 10 Sales per Company
                            </h5>
                        </div>
                        <div class="company-list">
                            @foreach ($company as $key => $item)
                                <div class="division-item">
                                    <div class="count">{{$key + 1}}</div>
                                    <div class="item-description">
                                        <div class="row">
                                            <div class="col-8">
                                                <div class="company-name">{{$item['company']['company_name']}}</div>
                                                <div class="company-industry">{{$item['company']['industry']}}</div>
                                            </div>
                                            <div class="col-4 text-right price-amount">₱ {{number_format($item['sales_amount'], 2)}}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid-list-2 g-c">
                <div>
                    <div id="per_year">
                        <div class="title">
                            <h5 class="card-title">
                                <i class="fas fa-list"></i> Top 10 Sales per Store
                            </h5>
                        </div>
                        <div class="store-list">
                            @foreach ($store as $key => $item)
                                <div class="division-item">
                                    <div class="count">{{$key + 1}}</div>
                                    <div class="item-description">
                                        <div class="row">
                                            <div class="col-8">
                                                <div class="company-name">{{$item['store']['store_name']}}</div>
                                                <div class="company-industry">{{$item['store']['address'] !== null?$item['store']['address']:'-'}}</div>
                                            </div>
                                            <div class="col-4 text-right price-amount">₱ {{number_format($item['sales_amount'], 2)}}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
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

    <div class="detailed-analytics">
        <div class="list-viewer">
            <div class="title row">
                <div class="col-8">
                    <h5 class="card-title">
                        <i class="fas fa-money-bill"></i> <span class="card-selected-title"></span>
                    </h5>
                </div>
                <div class="col-4 text-right">
                    <button class="btn btn-sm btn-light" onclick="backButton()"><i class="fas fa-arrow-left"></i> BACK</button>
                </div>
            </div>
            <div class="table-list">
                <table id="generated_dashboard_table" class="table table-striped" style="width:100%"></table>
            </div>
        </div>
    </div>

</main>
@endsection

@section('scripts')
<script src="/js/datatables.min.js"></script>
<script src="/js/apex.js"></script>
<script src="/js/phil.js"></script>
<script src="/js/page/dashboard/otc.js"></script>
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
<link href="{{ asset('css/custom/otc.css') }}" rel="stylesheet">

@endsection
