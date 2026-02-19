@extends('backend.master.template')
@section('content')
    <main class="content">
        <div class="container-fluid">
            <div class="header">
                <h1 class="header-title">
                    Company Screen
                </h1>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <button type="button" class="btn btn-primary add"  onclick="addRecord()" style="float:right">
                                    ADD RECORD
                                </button>
                            </h5>
                        </div>
                        @include('backend.partials.flash-message')
                        <div class="card-body col-12">
                            <div class="row">
                                <div class="col-12">
                                    <table id="generated_table" class="table table-striped" style="width:100%"></table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL --}}
        <div class="modal fade" id="recordModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">RECORD</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-12">
                                <label>Company Name</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Enter Company Name">
                            </div>
                            <div class="form-group col-12">
                                <label>Contact Person</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person" placeholder="Enter Contact Person">
                            </div>
                            <div class="form-group col-12">
                                <label>Contact No</label>
                                <input type="number" class="form-control" id="contact_no" name="contact_no" placeholder="Enter Contact No">
                            </div>
                            <div class="form-group col-12">
                                <label>Address</label>
                                <input type="text" class="form-control" id="address" name="address" placeholder="Enter Address">
                            </div>
                            <div class="form-group col-12">
                                <label>Province</label>
                                <select name="province_id" id="province_id" class="form-control">
                                    @foreach ($provinces as $province)
                                        <option value="{{ $province->id }}">{{ $province->province }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-12">
                                <label>Industry</label>
                                <select name="industry" id="industry" class="form-control">
                                    <option value="CORPORATE">CORPORATE</option>
                                    <option value="RESIDENTIAL">RESIDENTIAL</option>
                                    <option value="CONSTRUCTION">CONSTRUCTION</option>
                                    <option value="GOVERNMENT">GOVERNMENT</option>
                                </select>
                            </div>
                            <div class="form-group col-12">
                                <label>TIN No.</label>
                                <input type="text" class="form-control" id="tin" name="tin" placeholder="Enter TIN No">
                            </div>
                            <div class="form-group col-12">
                                <label>Business Style</label>
                                <input type="text" class="form-control" id="business_style" name="business_style" placeholder="Enter Business Style">
                            </div>
                            <div class="form-group col-12">
                                <label>Status</label>
                                <select name="active" id="active" class="form-control">
                                    <option value="1">ACTIVE</option>
                                    <option value="0">INACTIVE</option>
                                </select>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer text-right">
                        <button class="btn btn-primary" onclick="saveRecord()">SAVE</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">RECORD</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                Are you sure you want to delete this record ?
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer text-right">
                        <button class="btn btn-success" onclick="yesDelete()">YES</button>
                        <button class="btn btn-danger" data-dismiss="modal" aria-label="Close">NO</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
<script src="/js/datatables.min.js"></script>
<script src="/js/page/company.js"></script>
@endsection
