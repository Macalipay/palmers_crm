@extends('backend.master.template')
@section('content')
<main class="content">
    <div class="container-fluid">
        <div class="header row">
            <div class="col-md-5 col-sm-12">
                <h1 class="header-title">
                    Telemarketing Dashboard
                </h1>
                <p class="header-subtitle">Summary of all your calls</p>
            </div>

            <div class="col-md-2 col-sm-12">
                                <div class="form-group col-12">
                                    <select name="user_id" id="user_id" class="form-control">
                                        @foreach ($telemarketings as $telemarketing)
                                            <option value="{{ $telemarketing->id }}"> {{ $telemarketing->name }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
            <div class="col-md-2 col-sm-12">
                <input type="date" class="form-control" id="start_date" name="start_date" value="2024-10-20">
            </div>
            <div class="col-md-2 col-sm-12">
                <input type="date" class="form-control" id="end_date" name="end_date" value="2024-10-20">
            </div>
            <div class="col-md-1 col-sm-12">
                <button class="btn btn-primary" id="generate_button" onclick="filterResults()">
                    <i class="fas fa-search"></i> Generate
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-lg-12 col-xl">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col mt-0">
                                <h5 class="card-title">Total Sales</h5>
                            </div>

                            <div class="col-auto">
                                <div class="avatar">
                                    <div class="avatar-title rounded-circle ">
                                        <i class="align-middle fas fa-money-bill" ></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h1 class="display-5 mt-1 mb-3" id="total_amount">{{ $formatted_total_amount }}</h1>
                        <div class="mb-0">
                            Total Sales based on Telemarketing only
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col mt-0">
                                <h5 class="card-title">Overall Calls</h5>
                            </div>

                            <div class="col-auto">
                                <div class="avatar">
                                    <div class="avatar-title rounded-circle ">
                                        <i class="align-middle" data-feather="phone-outgoing"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h1 class="display-5 mt-1 mb-3" id="total_active_call">{{ $total_active_call }}</h1>
                        <div class="mb-0">
                             Overall total calls assigned
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col mt-0">
                                <h5 class="card-title">Backlog Calls</h5>
                            </div>

                            <div class="col-auto">
                                <div class="avatar">
                                    <div class="avatar-title rounded-circle ">
                                        <i class="align-middle" data-feather="phone-missed"></i>
                                    </div>
                                </div>
                            </div>
                        </div>  
                        <h1 class="display-5 mt-1 mb-3" id="total_backlogs">0</h1>
                        <div class="mb-0">
                          Total backlog calls 
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-lg-4 col-xl-4">
                <div class="card to-do badge-primary">
                    <div class="card-body">
                        <div class="row">
                            <div class="col mt-0">
                                <h3>To-Do</h3>
                            </div>
                        </div>
                        <h1 class="display-5 mt-1 mb-3 bank-text" id="user_todo_call">{{ $user_todo_call }}</h1>
                        <div class="mb-0">
                            <span class="">To-Do Calls
                        </span></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-xl-4">
                <div class="card cancelled badge-danger">
                    <div class="card-body">
                        <div class="row">
                            <div class="col mt-0">
                                <h3>Cancelled</h3>
                            </div>
                        </div>
                        <h1 class="display-5 mt-1 mb-3 bank-text" id="user_cancelled_call">{{ $user_cancelled_call }}</h1>
                        <div class="mb-0">
                            <span class="">Cancelled Calls
                        </span></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-xl-4">
                <div class="card in-progress badge-warning">
                    <div class="card-body">
                        <div class="row">
                            <div class="col mt-0">
                                <h3>In Progress</h3>
                            </div>
                        </div>
                        <h1 class="display-5 mt-1 mb-3 bank-text" id="user_inprogress_call">{{ $user_inprogress_call }}</h1>
                        <div class="mb-0">
                            <span class="">In-Progress Calls
                        </span></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-xl-4">
                <div class="card pending badge-info">
                    <div class="card-body">
                        <div class="row">
                            <div class="col mt-0">
                                <h3>Pending</h3>
                            </div>
                        </div>
                        <h1 class="display-5 mt-1 mb-3 bank-text" id="user_pending_call">{{ $user_pending_call }}</h1>
                        <div class="mb-0">
                            <span class="">Pending Calls
                        </span></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-xl-4">
                <div class="card on-hold badge-secondary">
                    <div class="card-body">
                        <div class="row">
                            <div class="col mt-0">
                                <h3>On-Hold</h3>
                            </div>
                        </div>
                        <h1 class="display-5 mt-1 mb-3 bank-text" id="user_onhold_call">{{ $user_onhold_call }}</h1>
                        <div class="mb-0">
                            <span class="">On-Hold Calls
                        </span></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-xl-4">
                <div class="card completed badge-success">
                    <div class="card-body">
                        <div class="row">
                            <div class="col mt-0">
                                <h3>Completed</h3>
                            </div>
                        </div>
                        <h1 class="display-5 mt-1 mb-3 bank-text" id="user_completed_call">{{ $user_completed_call }}</h1>
                        <div class="mb-0">
                            <span class="">Completed Calls
                        </span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                Completed Calls
                            </h5>
                        </div>
                        <div class="card-body col-12">
                            <div class="row">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-6">

                                        </div>
                                        <div class="col-6">
                                            
                                        </div>
                                    </div>
                                    <table id="generated_dashboard_table" class="table table-striped" style="width:100%"></table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
<script src="/js/page/dashboard/telemarketing_dashboard.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<!-- Export dependencies -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
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
