{{-- REGION --}}
<div class="modal fade" id="regionList" style="background: rgba(0,0,0,0.5);" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Region</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body m-3">
                <table id="region_table" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Region Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($regions as $region)
                        <tr data-dismiss="modal" aria-label="Close" onclick="selectRegion('{{ $region->region_id }}', '{{ addslashes($region->name) }}')">
                            <td>{{ $region->name }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- PROVINCE --}}
<div class="modal fade" id="provinceList" style="background: rgba(0,0,0,0.5);" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Province</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body m-3">
                <table id="province_table" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Province Name</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


{{-- CITY --}}
<div class="modal fade" id="cityList" style="background: rgba(0,0,0,0.5);" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5>City</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body m-3">
                <table id="city_table" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>City Name</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



{{-- CITY --}}
<div class="modal fade" id="barangayList" style="background: rgba(0,0,0,0.5);" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5>City</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body m-3">
                <table id="barangay_table" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Barangay Name</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


{{-- CITY --}}
<div class="modal fade" id="streetList" style="background: rgba(0,0,0,0.5);" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5>City</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body m-3">
                <table id="street_table" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Street Name</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- CITY --}}
<div class="modal fade" id="voterList" style="background: rgba(0,0,0,0.5);" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Voter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body m-3">
                <table id="voter_table" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Birthday</th>
                            <th>Gender</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($voters as $voter)
                        <tr data-dismiss="modal" aria-label="Close" onclick="selectVoter({{ $voter->id }}, '{{ addslashes($voter->firstname . ' '. $voter->lastname)}}')">
                            <td>{{ $voter->firstname . ' ' . $voter->middlename . ' ' . $voter->lastname }}</td>
                            <td>{{ $voter->birthday }}</td>
                            <td>{{ $voter->gender}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
