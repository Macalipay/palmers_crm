@extends('backend.master.template')
@section('content')
    <main class="content">
        <div class="container-fluid">
            <div class="header">
                <h1 class="header-title">
                    User Screen
                </h1>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <button type="button" class="btn btn-primary add" style="float:right" onclick="addRecord()">
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
                                <label>Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name">
                            </div>
                            
                            <div class="form-group col-6">
                                <label>Division</label>
                                <select name="division_id" id="division_id" class="form-control" onchange="divisionSelect()">
                                    <option value=""></option>
                                    @foreach ($divisions as $division)
                                        <option value="{{ $division->id }}">{{ $division->division }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-6">
                                <label>Branch</label>
                                <select name="branch_id" id="branch_id" class="form-control">
                                    <option value=""></option>
                                </select>
                            </div>

                            <div class="form-group col-12">
                                <label>Designation</label>
                                <select name="designation" id="designation" class="form-control">
                                    <option value="MARKETING HEAD">MARKETING HEAD</option>
                                    <option value="SALES AGENT">SALES AGENT</option>
                                    <option value="ENCODER">ENCODER</option>
                                    <option value="TELEMARKETING HEAD">TELEMARKETING HEAD</option>
                                    <option value="CSD HEAD">CSD HEAD</option>
                                    <option value="OTC HEAD">OTC HEAD</option>
                                    <option value="DIRECTOR">DIRECTOR</option>
                                    <option value="SUPER ADMIN">SUPER ADMIN</option>
                                </select>
                            </div>
                            <div class="form-group col-6">
                                <label>Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email">
                            </div>
                            <div class="form-group col-6">
                                <label>Contact Number</label>
                                <input type="number" class="form-control" id="contact_number" name="contact_number" placeholder="Enter Contact Number">
                            </div>
                            <div class="form-group col-12">
                                <label>Role</label>
                                <select name="role_id" id="role_id" class="form-control">
                                    @foreach ($role as $item)
                                        <option value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-12">
                                <label>Status</label>
                                <select name="active" id="active" class="form-control">
                                    <option value="1">ACTIVE</option>
                                    <option value="2">INACTIVE</option>
                                    <option value="3">RESIGNED</option>
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
<script src="/js/page/user.js"></script>
@endsection
