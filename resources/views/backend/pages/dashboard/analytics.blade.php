@extends('backend.master.template')
@section('content')
<main class="content">
    <div class="container-fluid">
        <div class="header">
            <h1 class="header-title">
                Dashboard
            </h1>
        </div>

        <div class="row">
            <div class="col-lg-3">
                <div class="card bg-success text-light">
                    <div class="card-body">
                        <div class="label">Total Number of Voters</div>
                        <div class="counter" id="voter">{{$total_voter}}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="label">Total Number of Organization</div>
                        <div class="counter" id="organization">{{$org}}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="label">Total Number of Groups</div>
                        <div class="counter" id="group">{{$group}}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="label">Total Number of Schools</div>
                        <div class="counter" id="school">{{$school}}</div>
                    </div>
                </div>
            </div>
            
            <div class="col-12">
                <div class="row">
                    <div class="col-lg-3">
                        <canvas id="org_graph"></canvas>
                    </div>
                    <div class="col-lg-9">
                        <h5>ORGANIZATION</h5>
                        <div class="organization-card">
                            <div id="org_list" class="row"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-12">
                <div class="row">
                    <div class="col-lg-8">
                        <h5>GROUP</h5>
                        <div class="group-card">
                            <canvas id="group_graph"></canvas>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div id="group_list" class="row"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@section('scripts')
<script src="/js/chart.js"></script>
<script src="/js/page/analytics.js"></script>
@endsection

@section('style')
<style>
.counter {
    font-size: 50px;
    font-weight: bold;
}
canvas {
    width: 100% !important;
    height: auto !important;
}
.org-item>div {
    margin-bottom: 10px;
    padding: 10px;
    border-radius: 10px;
}
.org-name {
    background: #00000050;
    font-weight: bold;
    padding: 3px;
    text-align: center;
    text-transform: uppercase;
    font-size: 12px;
    color: #fff;
}
.c-label {
    text-align: center;
    color: #fff;
    font-size: 10px;
    font-weight: bold;
    margin-top: 5px;
}
.c-value {
    font-weight: bold;
    text-align: center;
    font-size: 19px;
    color: #fff;
}
table.group-table {
    width: 100% !important;
}
table.group-table thead th {
    background: #0f6cc2;
    color: #fff;
    padding: 5px;
}
table.group-table tbody tr:nth-child(2n) {
    background: #ccc;
}
table.group-table tbody td {
    padding: 5px;
}
</style>
@endsection