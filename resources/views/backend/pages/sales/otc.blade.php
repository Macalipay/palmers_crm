@extends('backend.master.template')
@section('content')
    <main class="content">
        <div class="container-fluid">
            <div class="header">
                <h1 class="header-title">
                    Sale Screen (OTC)
                </h1>
            </div>
            <div class="row">
                <div class="col-md-6 col-lg-4 col-xl">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col mt-0">
                                    <h5 class="card-title">Overall Transaction</h5>
                                </div>

                                <div class="col-auto">
                                    <div class="avatar">
                                        <div class="avatar-title rounded-circle ">
                                            <i class="align-middle" data-feather="phone-outgoing"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h1 class="display-5 mt-1 mb-3">{{ $overall_transaction }}</h1>
                            <div class="mb-0">
                               Overall # of Sales Transaction 
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 col-xl">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col mt-0">
                                    <h5 class="card-title">Transaction this Year</h5>
                                </div>

                                <div class="col-auto">
                                    <div class="avatar">
                                        <div class="avatar-title rounded-circle ">
                                            <i class="align-middle" data-feather="phone-missed"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h1 class="display-5 mt-1 mb-3">{{ $this_year_transaction }}</h1>
                            <div class="mb-0">
                                Total # of Sales Transaction this Year
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 col-xl">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col mt-0">
                                    <h5 class="card-title">Transaction Today</h5>
                                </div>

                                <div class="col-auto">
                                    <div class="avatar">
                                        <div class="avatar-title rounded-circle ">
                                            <i class="align-middle" data-feather="phone-call"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h1 class="display-5 mt-1 mb-3">{{ $today_transaction }}</h1>
                            <div class="mb-0">
                                Total # of Sales Transaction Today
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <button type="button" class="btn btn-sm btn-primary add"  onclick="addOrganization()" style="float:right">
                                    ADD RECORD
                                </button> 
                                <button type="button" class="btn btn-sm btn-warning mr-2"  onclick="$('#filterModal').modal('show')" style="float:right;padding: 3px 10px;">
                                    FILTER RECORD
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
                            <div class="form-group col-md-12">
                                <label for="company">Company</label>
                                <div class="input-group">
                                    <input type="hidden" id="company_id" name="company_id" class="form-control col-10"/>
                                    <input type="text" class="form-control company_name" placeholder="Select Record" disabled>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#companyList">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="company">Store</label>
                                <div class="input-group">
                                    <input type="hidden" id="store_id" name="store_id" class="form-control col-10"/>
                                    <input type="text" class="form-control store_name" placeholder="Select Record" disabled>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#storeList">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                           

                            <div class="form-group col-6">
                                <label>Source</label>
                                <select name="source_id" id="source_id" class="form-control">
                                    @foreach ($sources as $source)
                                        <option value="{{ $source->id }}">{{ $source->source }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-6">
                                <label>PO/OF/WARRANTY CERTIFICATE No</label>
                                <input type="text" class="form-control" id="po_no" name="po_no" placeholder="Enter PO/OF/WARRANTY CERTIFICATE Number">
                            </div>

                            <div class="form-group col-12">
                                <label>Date Purchased</label>
                                <input type="date" class="form-control" id="date_purchased" name="date_purchased">
                            </div>

                            <div class="form-group col-6">
                                <label>Merchandiser</label>
                                <select name="merchandiser_id" id="merchandiser_id" class="form-control">
                                    <option value=""></option>
                                    @foreach ($merchandisers as $merchandiser)
                                        <option value="{{ $merchandiser->id }}">{{ $merchandiser->merchandiser }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-6">
                                <label>Sales Associate</label>
                                <select name="sales_associate_id" id="sales_associate_id" class="form-control">
                                    <option value=""></option>
                                    @foreach ($sales_associates as $sales_associate)
                                        <option value="{{ $sales_associate->id }}">{{ $sales_associate->sales_associate }}</option>
                                    @endforeach
                                </select>
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

                            <div class="col-md-12">
                                DATE PUCHASED RANGE:
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
                                <label>Sales Associate</label>
                                <select name="f_sales_associate_id" id="f_sales_associate_id" class="form-control">
                                    <option value=""></option>
                                    @foreach ($sales_associates as $sales_associate)
                                        <option value="{{ $sales_associate->id }}">{{ $sales_associate->sales_associate }}</option>
                                    @endforeach
                                </select>
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

                                <div class="text-right" style="padding: 25px 15px;color: #181818;font-weight: bold;text-transform: uppercase;">
                                    Grand Total: <span id="g_total"></span>
                                </div>
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
                                <label>Item</label>
                                <select name="item_id" id="item_id" class="form-control">
                                    <option value="">Select Item/Product</option>
                                    @foreach ($items as $item)
                                        <option value="{{ $item->id }}">{{ $item->item_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-6">
                                <label>Brand</label>
                                <select name="brand_id" id="brand_id" class="form-control">
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->brand }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-12">
                                <label>Description</label>
                                <select name="description" id="description" class="form-control">
                                    <option value="BRANDNEW">BRANDNEW</option>
                                    <option value="REFILL">REFILL</option>
                                    <option value="SWAP/REFILL">SWAP/REFILL</option>
                                    <option value="FOR WARRANTY">FOR WARRANTY</option>
                                </select>
                            </div>
                            <div class="form-group col-4">
                                <label>Quantity</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" placeholder="Enter Quantity" value="1">
                            </div>
                            <div class="form-group col-4">
                                <label>Amount</label>
                                <input type="number" class="form-control" id="amount" name="amount" placeholder="Enter Amount">
                            </div>
                            <div class="form-group col-4">
                                <label>Discount</label>
                                <input type="number" class="form-control" id="discount" name="discount" placeholder="Enter Discount" value="0">
                            </div>
                            <div class="form-group col-12">
                                <label>Total</label>
                                <input type="number" class="form-control" id="total" name="total" placeholder="Enter Total" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer text-right">
                        <button class="btn btn-primary" onclick="saveRecordDetails()">SAVE</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="serialModal" tabindex="-1" role="dialog" aria-hidden="true">
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
                            <div class="col-12">
                                <table id="generated_table_serial" class="table table-striped" style="width:100%"></table>
                            </div>
                        </div>
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
    </main>
@endsection

@section('scripts')
<script src="/js/datatables.min.js"></script>
<script src="/js/page/otc.js"></script>
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
</style>
