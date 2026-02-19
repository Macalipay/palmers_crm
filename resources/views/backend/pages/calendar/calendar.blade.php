@php
    $toggled = 'toggled';
@endphp
@extends('backend.master.template')
@section('content')
<main class="content">
    <div class="row">
        <div class="col-12">
            <div class="breadcrumps">
                <span onclick="general()">Calendar Schedule</span>
            </div>
        </div>
    </div>

    <div class="grid-container" id="general_report">
        <div class="grid-filter">
            <div>
                <div id="filter">
                    <div class="row">
                        <div class="col mt-0">
                            <h5 class="card-title text-light" style="margin: 0px;">FILTER BY</h5>
                        </div>
                    </div>

                    <ul class="sidebar-nav filter">
                        <li class="sidebar-item">
                            <a href="#category_filter" data-toggle="collapse" class="sidebar-link">
                                <span class="align-middle" style="font-weight: bold;">Status</span>
                            </a>
                            <ul id="category_filter" class="sidebar-dropdown list-unstyled collapse in show" data-parent="#sidebar">
                                <li class="sidebar-item"><a class="sidebar-link" href="#"><input type="checkbox" class="input-check" checked/> TO DO</a></li>
                                <li class="sidebar-item"><a class="sidebar-link" href="#"><input type="checkbox" class="input-check" checked/> IN-PROGRESS</a></li>
                                <li class="sidebar-item"><a class="sidebar-link" href="#"><input type="checkbox" class="input-check" checked/> CANCELLED</a></li>
                                <li class="sidebar-item"><a class="sidebar-link" href="#"><input type="checkbox" class="input-check" checked/> PENDING</a></li>
                                <li class="sidebar-item"><a class="sidebar-link" href="#"><input type="checkbox" class="input-check" checked/> ON-HOLD</a></li>
                                <li class="sidebar-item"><a class="sidebar-link" href="#"><input type="checkbox" class="input-check" checked/> COMPLETED</a></li>
                            </ul>
                        </li>
                        <li class="sidebar-item">
                            <a href="#group_filter" data-toggle="collapse" class="sidebar-link">
                                <span class="align-middle" style="font-weight: bold;">Opportunity Status</span>
                            </a>
                            <ul id="group_filter" class="sidebar-dropdown list-unstyled collapse in show" data-parent="#sidebar">
                                <li class="sidebar-item"><a class="sidebar-link" href="#"><input type="checkbox" class="input-check" checked/> OPEN</a></li>
                                <li class="sidebar-item"><a class="sidebar-link" href="#"><input type="checkbox" class="input-check" checked/> CLOSED</a></li>
                                <li class="sidebar-item"><a class="sidebar-link" href="#"><input type="checkbox" class="input-check" checked/> DEAL</a></li>
                                <li class="sidebar-item"><a class="sidebar-link" href="#"><input type="checkbox" class="input-check" checked/> LOST</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="grid-card-1 g-c">
            <div>
                <div id="citizen">
                    <div class="title">
                        <h5 class="card-title">
                            <i class="fas fa-tasks"></i> Total No. of Planned Task
                        </h5>
                    </div>
                    <h1>{{ $total_event }}</h1>
                    <div class="translate">
                        <span>Kabuuang Bilang na Planong Gawain</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid-card-2 g-c">
            <div>
                <div id="groups">
                    <div class="title">
                        <h5 class="card-title">
                            <i class="fas fa-check"></i> Total No. of Completed Task
                        </h5>
                    </div>
                    <h1>{{ $completed }}</h1>
                    <div class="translate">
                        <span>Kabuuang Bilang ng Natapos na Gawain</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid-card-3 g-c">
            <div>
                <div id="organization">
                    <div class="title">
                        <h5 class="card-title">
                            <i class="fas fa-spinner"></i> Total No. of In-progress Task
                        </h5>
                    </div>
                    <h1>{{ $inprogress }}</h1>
                    <div class="translate">
                        <span>Kabuuang Bilang ng Nakabinbin na Gawain</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid-card-4 g-c">
            <div>
                <div id="gender">
                    <div class="title">
                        <h5 class="card-title">
                            <i class="fas fa-hand-holding-medical"></i> Total No. Cancelled/On-Hold
                        </h5>
                    </div>
                    <div class="dash-card male gender" data-toggle="tooltip" data-placement="bottom" title="Completed" id="g_male">
                        <div class="icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="details">
                            <div class="count">0</div>
                        </div>
                    </div>
                    <hr>
                    <div class="dash-card female gender" data-toggle="tooltip" data-placement="bottom" title="Pending" id="g_female">
                        <div class="icon">
                            <i class="fas fa-spinner"></i>
                        </div>
                        <div class="details">
                            <div class="count">0</div>
                        </div>
                    </div>
                    <div class="translate">
                        <span>Bilang ng mga Panghihingi at Hiling ng mga Mamamayan</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid-card-5 g-c">
            <div>
                <div id="degree">
                    <div class="title">
                        <h5 class="card-title">
                            <i class="fas fa-pause-circle"></i> Total No. of In-progress and Pending
                        </h5>
                    </div>
                    <div class="dash-card college degree" data-toggle="tooltip" data-placement="bottom" title="Block" id="d_yes">
                        <div class="icon">
                            <i class="fas fa-shield-virus"></i>
                        </div>
                        <div class="details">
                            <div class="count">0</div>
                        </div>
                    </div>
                    <hr>
                    <div class="dash-card non-college degree" data-toggle="tooltip" data-placement="bottom" title="Cancelled" id="d_no">
                        <div class="icon">
                            <i class="fas fa-pause"></i>
                        </div>
                        <div class="details">
                            <div class="count">0</div>
                        </div>
                    </div>
                    <div class="translate">
                        <span>Antas ng edukasyon</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid-card-6 g-c">
            <div>
                <div id="organization">
                    <div class="title">
                        <h5 class="card-title">
                            <i class="fas fa-window-close"></i> Total No. of Cancelled Task
                        </h5>
                    </div>
                    <h1>3</h1>
                    <div class="translate">
                        <span>Kabuuang Bilang ng Nakansela na Gawain</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid-main g-c">
            <div id="fullcalendar"></div>
        </div>
    </div>

</main>
@endsection

@section('scripts')
<script src="/js/datatables.min.js"></script>
<script src="/js/apex.js"></script>
<script src="/js/phil.js"></script>
<script src="/js/page/calendar.js"></script>
@endsection


@section('style')
<link href="{{ asset('css/custom/calendar.css') }}" rel="stylesheet">
@endsection
