@extends('backend.master.template')
@section('content')
<main class="content">
    <div class="container-fluid">

        <div class="header">
            <h1 class="header-title">
                Quick Count Dashboard
            </h1>
            <p class="header-subtitle">Summary of Quick Counts.</p>
        </div>

        <div class="row">
            <div class="col-md-6 col-lg-3 col-xl">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col mt-0">
                                <h5 class="card-title">Total City Voters</h5>
                            </div>

                            <div class="col-auto">
                                <div class="avatar">
                                    <div class="avatar-title rounded-circle btn-secondary">
                                        <i class="fas fa-city"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h1 class="display-5 mt-1 mb-3">53,306</h1>
                        <div class="mb-0">
                            <span class="text-default"> Total Registered Voters of {{Auth::user()->city->name}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 col-xl">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col mt-0">
                                <h5 class="card-title">Total Voters per Barangay</h5>
                            </div>

                            <div class="col-auto">
                                <div class="avatar">
                                    <div class="avatar-title rounded-circle bg-success">
                                        <i class="fas fa-building"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h1 class="display-5 mt-1 mb-3">12,256</h1>
                        {{-- <h1 class="display-5 mt-1 mb-3">{{$voters_barangay}}</h1> --}}
                        <div class="mb-0">
                            <span class="text-default"> Total Registered Voters of {{Auth::user()->barangay->name}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 col-xl">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col mt-0">
                                <h5 class="card-title">Total Poll/Survey</h5>
                            </div>

                            <div class="col-auto">
                                <div class="avatar">
                                    <div class="avatar-title rounded-circle bg-primary">
                                        <i class="fas fa-poll"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h1 class="display-5 mt-1 mb-3">{{$polls}}</h1>
                        <div class="mb-0">
                            <span class="text-default"> Total Poll/Survey Created.
                        </div>
                    </div>
                </div>
            </div>
        </div>

            <div class="row">
                <div class="col-12">
                    <h4>Poll Survey</h4>
                </div>
                @foreach ($poll_creates as $key => $poll_create)
                
                <div class="col-12">
                    <h3>#{{++$key}}</h3>
                </div>
                
                <div class="row col-12">
                    <div class="col-3">
                        <div class="poll-img border-success" style="background:url({{ asset('/images/poll/' . rawurlencode($poll_create->picture))}})no-repeat; background-size: cover; background-position: center center; width:150px; height: 150px; border-radius: 100%; border: 5px solid; margin: auto;"></div>

                        <div class="poll-title">{{ $poll_create->title}} </div>
                        <div class="poll-description">{{ $poll_create->description }}.</div>
                        
                        <div class="poll-action">
                            <a href="{{url('poll/quick-count/' . $poll_create->id)}}" class="btn btn-primary mb-2">Quick Count Analyst</a> <br>
                            <a href="{{url('poll/manage-quick-count/' . $poll_create->id)}}" class="btn btn-light">Manage Quick Count</a>
                        </div>
                    </div>

                    <div class="col-9">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col mt-0">
                                        <h5 class="card-title">{{ $poll_create->title . ' (' .  $poll_create->voter . ' ' . 'Total Voters)' }} - Votes Summary </h5>
                                    </div>
                                </div>
                                <br>
                                    <table id="datatables" class="table table-striped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th style="width: 10%">Picture</th>
                                                <th style="width: 20%">Politician</th>
                                                <th style="width: 10%">Position</th>
                                                <th style="width: 10%">Count</th>
                                                <th style="width: 50%">% Total Count</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                                @forelse ($poll_datas as $poll_data)
                                                    @if ($poll_create->id == $poll_data->poll_id)
                                                    @php ($i =  $poll_data->position->position)
                                                    @php ($vote =  App\PollVoteTag::where('poll_id', $poll_data->poll_id)->where('politician_id', $poll_data->politician_id)->count())
                                                    @php ($total_vote =   App\PollVoteTag::where('poll_id', $poll_data->poll_id)->where('position_id', $poll_data->position_id)->groupBy('position_id', 'poll_id')->count())
                                                    {{-- @php ($percentage = $vote / $total_vote) --}}
                                                        <tr>
                                                            <td><img src="{{asset('/images/politician/' . $poll_data->politician->picture)}}" alt="" class="img-fluid rounded-circle mb-2" style="height: 30px; width:30px"></td>
                                                            <td>{{ $poll_data->politician->name }}</td>
                                                            <td class="text-right">{{ $poll_data->position->position }}</td>
                                                            <td class="text-right">{{ $count = $poll_data->count }}</td>
                                                            <td class="d-none d-xl-table-cell">
                                                                <div class="progress">
                                                                    @if ($count !== 0 && $poll_create->voter !== 0)
                                                                    <div class="progress-bar bg-primary-dark" role="progressbar" style="width: {{($count / $poll_create->voter) * 100}}%">{{
                                                                        number_format($count/$poll_create->voter, 2)
                                                                    }}%</div>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endif

                                                @empty
                                                    <h1>NO DATA AVAILABLE!</h1>
                                                @endforelse
                                                
                                        </tbody>
                                    </table>
                                <div class="mb-0">
                                    
                                    <span class="text-default"> Summary of Poll/Survey Result of {{ $poll_create->title}}.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @endforeach
            </div>
    </div>
</main>
@endsection

@section('scripts')
<script src="/js/datatables.min.js"></script>
    <script>
        function edit(id){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/position/edit/' + id,
                method: 'get',
                data: {

                },
                success: function(data) {
                    $('.modal-title').text('Update Position');
                    $('.submit-button').text('Update');
                        $.each(data, function() {
                            $.each(this, function(k, v) {
                                $('#'+k).val(v);
                            });
                        });
                    $('#modal-form').attr('action', 'position/update/' + data.positions.id);
                }
            });

        }

        $(function() {
            $('#datatables').DataTable({
                responsive: true
            });
        });
    </script>
@endsection

@section('style')
<style>
.poll-title {
    text-align: center;
    font-size: 20px;
    font-weight: bold;
    color: #2469ce;
    margin-bottom: 10px;
}

.poll-action {
    text-align: center;
    padding: 10px;
}

.poll-description {
    text-align: center;
}
</style>
@endsection