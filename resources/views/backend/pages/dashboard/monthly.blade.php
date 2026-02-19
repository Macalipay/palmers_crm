@php
    $toggled = 'toggled';
@endphp
@extends('backend.master.template')
@section('content')
<main class="content">
    <div class="row mb-2">
        <div class="col-9">
            <div class="breadcrumps">
                <b>Monthly Viewing</b>
                <span onclick="hideDetails()">General View</span>
                <span class="detailed-view hide">Detailed View</span>
            </div>
        </div>
        <div class="col-3">
            <div class="filter text-right">
                <select name="filter_date" id="filter_date" class="form-control form-control-sm" onchange="filterByDate()">
                    <option value="">Select a Month</option>
                    <option value="01">January</option>
                    <option value="02">February</option>
                    <option value="03">March</option>
                    <option value="04">April</option>
                    <option value="05">May</option>
                    <option value="06">June</option>
                    <option value="07">July</option>
                    <option value="08">August</option>
                    <option value="09">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>
            </div>
        </div>
    </div>

    <div class="viewed-analytics">
        <div class="grid-container show" id="general_report">
            <div class="grid-card-1 g-c" onclick="getDaily()">
                <div class="gcard">
                    <div id="transaction">
                        <div class="title">
                            <h5 class="card-title">
                                <i class="fas fa-users"></i> Transaction in Sales
                            </h5>
                        </div>
                        <h1>0</h1>
                        <div class="translate">
                            <span>Monthly total Transaction</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid-card-2 g-c green" onclick="getDaily()">
                <div class="gcard">
                    <div id="daily">
                        <div class="title">
                            <h5 class="card-title">
                                <i class="fas fa-money-bill"></i> Sales
                            </h5>
                        </div>
                        <h1>0</h1>
                        <div class="translate">
                            <span>Monthly total sales</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid-chart-1 g-c">
                <div>
                    <div id="status" style="height:67vh;">
                        <div class="title">
                            <h5 class="card-title">
                                <i class="fas fa-phone-volume"></i> Calls
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
                                        <div class="chart-title">Completed</div>
                                        <div class="counts">0</div>
                                    </div>
                                </div>
                                <div class="col-3" id="incomplete_calls">
                                    <div class="legend-container">
                                        <div class="chart-title">Incomplete</div>
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
                    <div id="division_record">
                        <div class="title">
                            <span class="back hide" onclick="backDivision()"><i class="fas fa-arrow-left"></i></span>
                            <h5 class="card-title">
                                <i class="fas fa-list"></i> Total sales per division
                            </h5>
                        </div>
                        <div class="division-list"></div>
                    </div>
                </div>
            </div>
            <div class="grid-list-2 g-c">
                <div>
                    <div id="associate_record">
                        <div class="title">
                            <h5 class="card-title">
                                <i class="fas fa-list"></i> Sales by Sales Associate
                            </h5>
                        </div>
                        <div class="associate-list"></div>
                    </div>
                </div>
            </div>
            <div class="grid-list-3 g-c">
                <div>
                    <div id="agent_record">
                        <div class="title">
                            <h5 class="card-title">
                                <i class="fas fa-list"></i> Sales by Sales Agent
                            </h5>
                        </div>
                        <div class="agent-list"></div>
                    </div>
                </div>
            </div>
            <div class="grid-list-4 g-c">
                <div>
                    <div id="industry_record">
                        <div class="title">
                            <h5 class="card-title">
                                <i class="fas fa-list"></i> Sales by Industry
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
<script src="/js/page/monthly.js"></script>
@endsection


@section('style')
<link href="{{ asset('css/custom/graph.css') }}" rel="stylesheet">
@endsection
