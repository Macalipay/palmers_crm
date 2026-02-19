@php
    $toggled = 'toggled';
@endphp
@extends('backend.master.template')
@section('content')
<main class="content">
    
    <div class="row">
        <div class="col-12">
            <div class="breadcrumps">
                <span onclick="general()">Candidates Dashboard</span>
            </div>
        </div>
    </div>

    <div class="grid-container" id="home_report">
        <div class="grid-candidate g-c">
            <div>
                <div id="candidate-info">
                    <div class="candidate-pic">
                        <img src="/images/profile/{{Auth::user()->picture}}" class="img-fluid rounded-circle mb-2" alt="Voter's Picture" width="100"/>
                    </div>
                    <div class="candidate-details">
                        <div class="c-name">{{Auth::user()->name}}</div>
                        <div class="c-position">{{Auth::user()->candidate->position}}</div>
                        <div class="c-voter"><b>Total voter: </b><span>0</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid-gender g-c">
            <div>
                <div id="gender">
                    <div class="title">
                        <h5 class="card-title">
                            <i class="fas fa-venus-mars"></i> Gender Statistics
                        </h5>
                    </div>
                    <div class="dash-card male gender" data-toggle="tooltip" data-placement="bottom" title="Male" id="g_male">
                        <div class="icon">
                            <i class="fas fa-mars"></i>
                        </div>
                        <div class="details">
                            <div class="count">0</div>
                        </div>
                    </div>
                    <hr>
                    <div class="dash-card female gender" data-toggle="tooltip" data-placement="bottom" title="Female" id="g_female">
                        <div class="icon">
                            <i class="fas fa-venus"></i>
                        </div>
                        <div class="details">
                            <div class="count">0</div>
                        </div>
                    </div>
                    <div class="translate">
                        <span>Estadistika ng Kasarian</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid-degree g-c">
            <div>
                <div id="degree">
                    <div class="title">
                        <h5 class="card-title">
                            <i class="fas fa-book-open"></i> Educational Status
                        </h5>
                    </div>
                    <div class="dash-card college degree" data-toggle="tooltip" data-placement="bottom" title="College" id="d_yes" onclick="generic_filtering('d_yes', 'degree', 'YES')">
                        <div class="icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="details">
                            <div class="count">0</div>
                        </div>
                    </div>
                    <hr>
                    <div class="dash-card non-college degree" data-toggle="tooltip" data-placement="bottom" title="Non College" id="d_no" onclick="generic_filtering('d_no', 'degree', 'NO')">
                        <div class="icon">
                            <i class="fas fa-user"></i>
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
        <div class="grid-contribution g-c">
            <div>
                <div id="contribution">
                    <div class="title">
                        <h5 class="card-title">
                            <i class="fas fa-money-bill"></i> Total Contribution Received
                        </h5>
                    </div>
                    <div class="amount text-success">
                        0.00
                    </div>
                </div>
            </div>
        </div>
        <div class="grid-expenditures g-c">
            <div>
                <div id="expenditure">
                    <div class="title">
                        <h5 class="card-title">
                            <i class="fas fa-hand-holding-usd"></i> Total Expenditures
                        </h5>
                    </div>
                    <div class="amount text-danger">
                        0.00
                    </div>
                </div>
            </div>
        </div>
        <div class="grid-voter g-c">
            <div>
                <div>
                    <div id="voter_list">
                        <div class="title">
                            <h5 class="card-title">
                                <i class="fas fa-users"></i> Voter List
                            </h5>
                        </div>
                        <table id="generated_table" class="table table-striped" style="width:100%"></table>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid-events g-c">
            <div>
                <div id="events">
                    <div class="title">
                        <h5 class="card-title">
                            <i class="fas fa-users"></i> Events
                        </h5>
                    </div>
                    <div class="event-container"></div>
                </div>
            </div>
        </div>
        <div class="grid-ranking g-c">
            <div>
                <div id="ranking">
                    <div class="title">
                        <h5 class="card-title">
                            <i class="fas fa-poll"></i> Ranking
                        </h5>
                    </div>
                    <div class="rank-container"></div>
                </div>
            </div>
        </div>
        <div class="grid-age g-c">
            <div>
                <div id="age">
                    <div id="age_map"></div>
                </div>
            </div>
        </div>
        <div class="grid-barangay g-c">
            <div>
                <div id="barangay">
                    <div class="title">
                        <h5 class="card-title">
                            <i class="fas fa-th-list"></i> Barangay
                        </h5>
                    </div>
                    <br>
                    <div class="table">
                        <table id="g_barangay">
                            <thead>
                                <th>Barangay</th>
                                <th>Count</th>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
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
<script src="/js/page/home.js"></script>
@endsection


@section('style')
<link href="{{ asset('css/custom/home.css') }}" rel="stylesheet">
@endsection