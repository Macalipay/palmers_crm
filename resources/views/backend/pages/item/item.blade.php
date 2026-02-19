@extends('backend.master.template')
@section('content')
    <main class="content">
        <div class="container-fluid">
            <div class="header">
                <h1 class="header-title">
                    Item Screen
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
                                <label>Item Name</label>
                                <input type="text" class="form-control" id="item_name" name="item_name" placeholder="Enter Item Name">
                            </div>
                            <div class="form-group col-12">
                                <label>Division</label>
                                <select name="division_id" id="division_id" class="form-control">
                                    <option value=""></option>
                                    @foreach ($divisions as $division)
                                        <option value="{{ $division->id }}">{{ $division->division }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-12">
                                <label>Description</label>
                                <input type="text" class="form-control" id="description" name="description" placeholder="Enter Description">
                            </div>
                            <div class="form-group col-12">
                                <label>Amount</label>
                                <input type="number" class="form-control" id="amount" name="amount" placeholder="Enter Amount">
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

        <div class="modal fade" id="durationModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">DURATION</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-12">
                                <label>Brandnew <small>(Month/s)</small></label>
                                <input type="number" class="form-control" id="brandnew" name="brandnew" placeholder="Enter Duration for Brandnew Item">
                            </div>
                            <div class="form-group col-12">
                                <label>Refill <small>(Month/s)</small></label>
                                <input type="number" class="form-control" id="refill" name="refill" placeholder="Enter Duration for Refill Item">
                            </div>
                            <div class="form-group col-12">
                                <label>For Warranty <small>(Month/s)</small></label>
                                <input type="number" class="form-control" id="for_warranty" name="for_warranty" placeholder="Enter Duration for Warranty">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer text-right">
                        <button class="btn btn-primary" onclick="saveDuration()">SAVE</button>
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
<script src="/js/page/item.js"></script>
@endsection
