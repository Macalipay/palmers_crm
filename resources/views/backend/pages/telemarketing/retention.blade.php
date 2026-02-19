@extends('backend.master.template')
@section('content')
    <main class="content">
        <div class="container-fluid">
            <div class="header">
                <h1 class="header-title">
                    Retention Screen
                </h1>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <button type="button" class="btn btn-primary add"  onclick="addOrganization()" style="float:right">
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
                                <label>Company</label>
                                <select name="company_id" id="company_id" class="form-control">
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-6">
                                <label>Lead Status</label>
                                <select name="lead_status" id="lead_status" class="form-control">
                                    <option value="PROSPECT">PROSPECT</option>
                                    <option value="ENGAGE">ENGAGE</option>
                                    <option value="ACQUIRE">ACQUIRE</option>
                                    <option value="RETENTION">RETENTION</option>
                                </select>
                            </div>

                            <div class="form-group col-6">
                                <label>Opportunity Status</label>
                                <select name="opportunity_status" id="opportunity_status" class="form-control">
                                    <option value="OPEN">OPEN</option>
                                    <option value="CLOSED">CLOSED</option>
                                    <option value="DEAL">DEAL</option>
                                    <option value="LOST">LOST</option>
                                </select>
                            </div>

                            <div class="form-group col-12">
                                <label>Lead Source</label>
                                <select name="source_id" id="source_id" class="form-control">
                                    @foreach ($sources as $source)
                                        <option value="{{ $source->id }}">{{ $source->source }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-12">
                                <label>Product Interest</label>
                                <textarea  type="text" cols="30" rows="10" class="form-control" id="product_interest" name="product_interest" placeholder="Enter Product Interest"></textarea>
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

        <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">RECORD</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 text-right mb-2">
                                <button class="btn btn-sm btn-primary" onclick="addDetails()">ADD DETAILS</button>
                            </div>
                            <div class="col-12">
                                <table id="generated_table_details" class="table table-striped" style="width:100%"></table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="recordDetails" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">DETAILS RECORD</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-6">
                                <label>Date</label>
                                <input type="date" class="form-control" id="date" name="date">
                            </div>
                            <div class="form-group col-6">
                                <label>Task</label>
                                <input type="text" class="form-control" id="task" name="task" placeholder="Enter Task">
                            </div>
                            <div class="form-group col-12">
                                <label>Description</label>
                                <input type="text" class="form-control" id="description" name="description" placeholder="Enter Description">
                            </div>
                            <div class="form-group col-6">
                                <label>Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="TO DO">TO DO</option>
                                    <option value="IN PROGRESS">IN PROGRESS</option>
                                    <option value="CANCELLED">CANCELLED</option>
                                    <option value="PENDING">PENDING</option>
                                    <option value="ON-HOLD">ON-HOLD</option>
                                    <option value="COMPLETED">COMPLETED</option>
                                </select>
                            </div>

                            <div class="form-group col-6">
                                <label>Assigned To</label>
                                <select name="assigned_to" id="assigned_to" class="form-control">
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-12">
                                <label>Remarks</label>
                                <textarea type="text" class="form-control" id="remarks" name="remarks" placeholder="Enter Remarks"cols="30" rows="10"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer text-right">
                        <button class="btn btn-primary" onclick="saveRecordDetails()">SAVE</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
<script src="/js/datatables.min.js"></script>
<script src="/js/page/retention.js"></script>
@endsection

<style>

    table.dataTable thead th {
        white-space: nowrap
    }

    table.dataTable td {
        padding: 3px 10px;
        width: 1px;
        white-space: nowrap;
    }
    .dataTables_scrollBody table {
        margin-left:0px;
    }
    .avatar-title.rounded-circle {
        background: #960301;
    }
    h5.card-title {
        font-weight: bold;
    }
    img.company-logo {
        width: 250px;
        float: right;
    }
    .report-container {
        margin: auto;
        width: 1480px;
        padding: 2em;
    }
    p.company-name {
        font-size: 22px;
        font-weight: bold;
        text-transform: uppercase;
        margin-top: 25px;
    }
    .heading {
        border-bottom: 5px solid gray;
        padding-bottom: 1em;
    }
    p.company-dtl {
        margin-bottom: 0px;
        font-size: 18px;
    }
    .row.other-dtl {
        margin-top: 10px;
    }
    .spacer {
        padding: 1em;
    }
    .spacer-max {
        padding: 3em;
    }
    p.contact-person {
        font-size: 20px;
    }
    p.company-name {
        font-size: 2em;
        font-weight: bold;
        text-transform: uppercase;
        margin-bottom: 0px !important;
    }
    p.report-heading {
        text-align: center;
        font-size: 2em;
        font-weight: bold;
    }
    p.th-title {
        margin-bottom: -5px !important;
        font-size: 20px;
    }
    p.th-status {
        margin-bottom: 0px !important;
        text-align: right;
        font-size: 20px;
    }
    p.th-date {
        text-align: right;
        font-size: 13px;
    }
    p.th-sub {
        font-size: 13px;
    }
    span.tagging {
        font-size: 12px;
        background: #3490dc;
        color: white;
        padding: 2px 5px;
        border-radius: 10px;
    }
    span.title {
        font-size: 15px;
        font-weight: bold;
    }
    p.th-sub, p.th-date  {
        color: gray;
        margin-bottom: 0px;
    }
    p.th-status>span {
        font-size: 12px;
        background: #38c172;
        padding: 2px 10px;
        border-radius: 10px;
        color: white;
    }
    .row.th-list {
        border-bottom: 1px solid #f6f6f6;
        margin: 5px 0px;
    }
</style>
