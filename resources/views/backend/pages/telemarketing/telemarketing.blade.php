@extends('backend.master.template')
@section('page-title')
<span class="page-title">TeleMarketing Screen</span>
@endsection
@section('content')
    <main class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-10">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                @role('SUPER ADMIN|Super Admin')
                                    <button type="button" class="btn btn-primary btn-sm mr-2"  onclick="pick_telemarketing()" style="float:right">
                                        ASSIGN
                                    </button>
                                @endrole

                                <button type="button" class="btn btn-warning btn-sm mr-2"  onclick="filterRecord()" style="float:right">
                                    FILTER RECORD
                                </button>
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
                                    <table id="generated_table" class="table table-striped" style="width:100%"></table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card legend-card">
                        <div class="card-body side-card">
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
                            <h1 class="display-5 mt-1">{{ $total_active_call }}</h1>
                            <div class="mb-0">
                                <span class="text-danger"> <i class="mdi mdi-arrow-bottom-right"></i> 0% </span> of calls remaining for today
                            </div>
                        </div>
                    </div>
                    <div class="card legend-card">
                        <div class="card-body side-card">
                            <div class="row">
                                <div class="col mt-0">
                                    <h5 class="card-title">Total Calls For Today</h5>
                                </div>

                                <div class="col-auto">
                                    <div class="avatar">
                                        <div class="avatar-title rounded-circle ">
                                            <i class="align-middle" data-feather="phone-call"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h1 class="display-5 mt-1">{{ $overall_completed_call }} / {{ $total_call_today }}</h1>
                            <div class="mb-0">
                                <span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i> Total calls</span> scheduled for today
                            </div>
                        </div>
                    </div>
                    <div class="card legend-card">
                        <div class="card-body side-card">
                            <div class="row">
                                <div class="col mt-0">
                                    <h5 class="card-title">Total Calls For Today</h5>
                                </div>

                                <div class="col-auto">
                                    <div class="avatar">
                                        <div class="avatar-title rounded-circle ">
                                            <i class="align-middle" data-feather="phone-call"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h1 class="display-5 mt-1">{{ $overall_completed_call }} / {{ $total_call_today }}</h1>
                            <div class="mb-0">
                                <span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i> Total calls</span> scheduled for today
                            </div>
                        </div>
                    </div>
                    <div class="card legend-card">
                        <div class="card-body side-card">
                            <div class="row">
                                <div class="col mt-0">
                                    <h5 class="card-title">Done Calls</h5>
                                </div>

                                <div class="col-auto">
                                    <div class="avatar">
                                        <div class="avatar-title rounded-circle ">
                                            <i class="align-middle" data-feather="check-circle"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h1 class="display-5 mt-1">{{ $completed_call }}/40</h1>
                            <div class="mb-0">
                                <span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i>DONE</span> calls scheduled for today
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="recordDetails" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-fullscreen-custom" role="document">
                <div class="modal-content modal-content-fullscreen">
                    <div class="modal-header">
                        <h5 class="modal-title"><span id="detail_company_name"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="details-section p-3 mb-4 border rounded bg-light">
                                    <div class="row">
                                        <div class="col-6 d-flex align-items-center">
                                            <span class="fw-bold"><strong>FOLLOW UP DATE: </strong> </span> 
                                            <span id="detail_date" class="ms-2"></span>
                                            
                                            <a id="refreshButton" class="mx-2 text-dark fs-5" style="cursor: pointer;">
                                                <i class="fas fa-sync-alt"></i> <!-- Refresh Icon -->
                                            </a>
                                            <a id="infoButton" class="text-dark fs-5" style="cursor: pointer;" 
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="This is the information text.">
                                                <i class="fas fa-info-circle"></i> <!-- Info Icon -->
                                            </a>
                                            <input type="hidden" id="reset_id">
                                        </div>
                                        
                                        <div class="col-6">
                                            <p class="mb-2"><strong>TASK:</strong> <span id="detail_task"></span></p>
                                        </div>
                                        <div class="col-12">
                                            <p class="mb-2"><strong>DESCRIPTION:</strong> <span id="detail_description"></span></p>
                                        </div>
                                        <div class="col-6">
                                            <p class="mb-2"><strong>CONTACT PERSON:</strong> <span id="detail_contact_person"></span></p>
                                        </div>
                                        <div class="col-6">
                                            <p class="mb-2"><strong>CONTACT #:</strong> <span id="detail_contact_no"></span></p>
                                        </div>
                                            <div class="col-6">
                                            <p class="mb-2"><strong>SALES ASSOCIATE:</strong> <span id="sales_associate"></span></p>
                                        </div>
                                        <div class="col-6">
                                            <p class="mb-2"><strong>PO/OF NO:</strong> <span id="po_no"></span></p>
                                        </div>
                                        <div class="col-6">
                                            <p class="mb-2"><strong>TOTAL PRICE:</strong> <span id="total_price"></span></p>
                                        </div>
                                        <div class="col-6">
                                            <p class="mb-2"><strong>ITEM PRICE:</strong> <span id="item_price"></span></p>
                                        </div>
                                        <div class="col-6">
                                            <p class="mb-2"><strong>REMARKS:</strong> <span id="detail_remarks"></span></p>
                                        </div>
                                        <div class="col-6">
                                            <p class="mb-2"><strong>DATE ASSIGNED:</strong> <span id="assigned_date"></span></p>
                                        </div>
                                    </div>
                                    <hr style="border: none; border-top: 2px dashed black;">
                                    <table id="generated_table_details" class="table table-striped" style="width:100%"></table>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
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
                                    <div class="col-12 col-md-12 mb-12">
                                                <label for="total_amount">Call Duration <small>(optional)</small></label>
                                                <input type="text" class="form-control" id="call_duration" name="call_duration" placeholder="Enter Call Duration">
                                            </div>
                                    <div class="form-group col-12" id="completedField" style="display: none;">
                                        <div class="row">
                                            <div class="col-12 col-md-6 mb-3">
                                                <label for="new_order_id">Order ID</label>
                                                <input type="text" class="form-control" id="new_order_id" name="new_order_id" placeholder="Enter ORDER ID">
                                            </div>
                                            <div class="col-12 col-md-6 mb-3">
                                                <label for="total_amount">Total Amount</label>
                                                <input type="number" class="form-control" id="total_amount" name="total_amount" placeholder="Enter Total Amount">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group col-12">
                                        <label>Remarks</label>
                                        <input type="hidden" class="form-control" id="telemarketing_detail_id" name="telemarketing_detail_id">
                                        <textarea class="form-control" id="remarks" name="remarks" placeholder="Enter Remarks" cols="30" rows="5"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5>Activity History</h5>
                                        <ol class="activity-feed"></ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="modal-footer text-right">
                        <button class="btn btn-primary" onclick="saveRecordDetails()">SAVE</button>
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

        <div class="modal fade" id="telemarketingModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">ASSIGNING TASKS</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group col-12">
                                    <label>Telemarketing</label>
                                    <select name="telemarketing_id" id="telemarketing_id" class="form-control">
                                        @foreach ($telemarketings as $telemarketing)
                                            @if ($telemarketing->id === 1)
                                                <option value="{{ $telemarketing->id }}"> UNASSIGNED </option>
                                            @else
                                                <option value="{{ $telemarketing->id }}"> {{ $telemarketing->name }} </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer text-right">
                        <button class="btn btn-primary" onclick="assignedTask()">SAVE</button>
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
        
        <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">FILTER RECORD</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            @role('SUPER ADMIN|Super Admin')
                                <div class="form-group col-md-12">
                                    <label for="assigned_to">Assigned To</label>
                                    <div class="input-group">
                                        <input type="hidden" id="f_assigned_to" name="f_assigned_to" class="form-control col-10"/>
                                        <input type="text" class="form-control f_assigned_to" placeholder="Select Record" disabled>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#assignedList">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endrole

                            <div class="form-group col-12">
                                <label>Status</label>
                                <select name="f_status" id="f_status" class="form-control">
                                    <option value="TO DO">TO DO</option>
                                    <option value="IN PROGRESS">IN PROGRESS</option>
                                    <option value="CANCELLED">CANCELLED</option>
                                    <option value="PENDING">PENDING</option>
                                    <option value="ON-HOLD">ON-HOLD</option>
                                    <option value="COMPLETED">COMPLETED</option>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="company">Company</label>
                                <div class="input-group">
                                    <input type="hidden" id="f_company_id" name="f_company_id" class="form-control col-10"/>
                                    <input type="text" class="form-control f_company_name" placeholder="Select Record" disabled>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#companyList">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="company">Store</label>
                                <div class="input-group">
                                    <input type="hidden" id="f_store_id" name="f_store_id" class="form-control col-10"/>
                                    <input type="text" class="form-control f_store_name" placeholder="Select Record" disabled>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#storeList">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-12 mb-1">
                                DATE PURCHASED RANGE:
                            </div>

                            <div class="form-group col-md-6">
                                <label for="company">Start</label>
                                <input type="date" class="form-control" id="p_start" name="p_start"/>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="company">End</label>
                                <input type="date" class="form-control" id="p_end" name="p_end"/>
                            </div>

                            <div class="col-md-12 mb-1">
                                DATE FOLLOW UP RANGE:
                            </div>

                            <div class="form-group col-md-6">
                                <label for="company">Start</label>
                                <input type="date" class="form-control" id="f_start" name="f_start"/>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="company">End</label>
                                <input type="date" class="form-control" id="f_end" name="f_end"/>
                            </div>

                                <div class="form-group col-12">
                                    @role('SUPER ADMIN|Super Admin')
                                        <input type="checkbox" id="f_unassigned" name="f_unassigned">
                                        <label for="f_contact">Unassigned</label>
                                        <input type="checkbox" id="f_contact" name="f_contact">
                                        <label for="f_contact">No Contact Number</label>
                                        <input type="checkbox" id="f_calls" name="f_calls">
                                        <label for="f_calls">Future Calls</label>
                                    @endrole
                                        <input type="checkbox" id="f_accessories" name="f_accessories">
                                        <label for="f_accessories">Accessories</label>
                                </div>
                        </div>
                    </div>
                    <div class="modal-footer text-right">
                        <button class="btn btn-secondary" onclick="clearFilter()">CLEAR</button>
                        <button class="btn btn-primary" onclick="generateRecord()">GENERATE RECORD</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="companyList" style="background: rgba(0,0,0,0.5);" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Company</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body m-3">
                        <table id="company_table" class="table table-striped" style="width:100%"></table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="storeList" style="background: rgba(0,0,0,0.5);" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Store</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body m-3">
                        <table id="store_table" class="table table-striped" style="width:100%"></table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="assignedList" style="background: rgba(0,0,0,0.5);" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Store</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body m-3">
                        <table id="assign_table" class="table table-striped" style="width:100%"></table>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
<script src="/js/datatables.min.js"></script>
<script src="/js/page/telemarketing.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<!-- Export dependencies -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
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
    font-size: 12px;
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
.mb-0 {
    font-size: 10px;
}

#select-all {
    width: 100%;
    text-align: center;
    display: inline-block;
}
input.select-row {
    display: inline-block !important;
}
.card-body.side-card {
    padding: 15px !important;
}
.avatar {
    width: 30px !important;
    height: 30px !important;
}
h1.display-5.mt-1 {
    font-size: 1.5em;
    font-weight: bold;
}
.card.legend-card {
    margin-bottom: 10px;
}
/* .wrapper:before {
    height: 70px !important;
} */
/* .header-title {
    color: #000000 !important;
} */

.modal-fullscreen-custom {
    width: 100%;
    max-width: 100%;
    height: 100%;
    margin: 0;
}

.modal-dialog.modal-lg.modal-fullscreen-custom {
    width: 100%;
    max-width: 90%;
}
.activity-feed {
  padding: 15px;
  list-style: none;

  .feed-item {
    position: relative;
    padding-bottom: 20px;
    padding-left: 15px;
    border-left: 2px solid #e4e8eb;

    &:last-child {
      border-color: transparent;
    }

    &::after {
      content: "";
      display: block;
      position: absolute;
      top: 0;
      left: -6px;
      width: 10px;
      height: 10px;
      border-radius: 6px;
      background: #fff;
      border: 1px solid @brand-secondary;
    }

    .date {
      display: block;
      position: relative;
      top: -5px;
      color: #8c96a3;
      text-transform: uppercase;
      font-size: 13px;
    }
    .text {
      position: relative;
      top: -3px;
    }
  }
}
</style>
