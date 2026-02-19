@extends('backend.master.template')
@section('content')
    <main class="content">
        <div class="container-fluid">
            <div class="header">
                <h1 class="header-title">
                    FSD : Sale Screen
                </h1>
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
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label>RFQ No.</label>
                                        <input type="text" class="form-control form-control-sm" id="rfq_no" name="rfq_no"/>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Source</label>
                                        <select name="source_id" id="source_id" class="form-control form-control-sm">
                                            <option value=""></option>
                                            @foreach ($sources as $item)
                                            <option value="{{$item->id}}">{{$item->source}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Category</label>
                                        <select name="category" id="category" class="form-control form-control-sm">
                                            <option value=""></option>
                                            <option value="CORPORATE">CORPORATE</option>
                                            <option value="RESIDENTIAL">RESIDENTIAL</option>
                                            <option value="CONSTRUCTION">CONSTRUCTION</option>
                                            <option value="GOVERNMENT">GOVERNMENT</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Customer Type</label>
                                        <select name="customer_type" id="customer_type" class="form-control form-control-sm">
                                            <option value="NEW">NEW</option>
                                            <option value="RETENTION">RETENTION</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Project Title</label>
                                        <input type="text" class="form-control form-control-sm" id="project_title" name="project_title"/>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Date Received</label>
                                        <input type="date" class="form-control form-control-sm" id="date_received" name="date_received"/>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Date Filed</label>
                                        <input type="date" class="form-control form-control-sm" id="date_filed" name="date_filed"/>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label>Company Name</label>
                                        <input type="text" class="form-control form-control-sm" id="company_name" name="company_name"/>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label>Company Address</label>
                                        <textarea class="form-control form-control-sm" id="company_address" name="company_address"></textarea>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Contact Person</label>
                                        <input type="text" class="form-control form-control-sm" id="contact_person" name="contact_person"/>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Designation</label>
                                        <input type="text" class="form-control form-control-sm" id="designation" name="designation"/>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Telephone</label>
                                        <input type="text" class="form-control form-control-sm" id="telephone" name="telephone"/>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Email</label>
                                        <input type="email" class="form-control form-control-sm" id="email" name="email"/>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Project Location</label>
                                        <input type="text" class="form-control form-control-sm" id="project_location" name="project_location"/>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>TPC/ Mark up</label>
                                        <input type="email" class="form-control form-control-sm" id="tcp" name="tcp"/>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Deadline</label>
                                        <input type="date" class="form-control form-control-sm" id="deadline" name="deadline"/>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label>Comments</label>
                                        <textarea class="form-control form-control-sm" id="comments" name="comments"></textarea>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Sales In Charge</label>
                                        <select name="sales_associate_id" id="sales_associate_id" class="form-control form-control-sm">
                                            <option value=""></option>
                                            @foreach ($sales as $item)
                                            <option value="{{$item->id}}">{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Design & Estimate</label>
                                        <select name="design_id" id="design_id" class="form-control form-control-sm">
                                            <option value=""></option>
                                            @foreach ($design as $item)
                                            <option value="{{$item->id}}">{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Design & Supervisor</label>
                                        <select name="supervisor" id="supervisor" class="form-control form-control-sm">
                                            <option value=""></option>
                                            @foreach ($supervisor as $item)
                                            <option value="{{$item->id}}">{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Date submitted to sales in charge</label>
                                        <input type="date" class="form-control form-control-sm" id="date_submitted" name="date_submitted"/>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Quoted Amount</label>
                                        <input type="number" class="form-control form-control-sm" id="quoted_amount" name="quoted_amount"/>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>FSD No./ Client Reference No.</label>
                                        <input type="text" class="form-control form-control-sm" id="reference_no" name="reference_no"/>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Date of Purchase Order</label>
                                        <input type="date" class="form-control form-control-sm" id="date_purchased" name="date_purchased"/>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>PO No.</label>
                                        <input type="text" class="form-control form-control-sm" id="po_no" name="po_no"/>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>PO Amount</label>
                                        <input type="number" class="form-control form-control-sm" id="po_amount" name="po_amount"/>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label>Remarks</label>
                                        <textarea class="form-control form-control-sm" id="remarks" name="remarks"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-12 mb-2" id="afss">
                                        <div class="product-catalog">
                                            <div class="setup-header">AFSS (Automatic Fire Sprinkler System)</div>
                                            <div class="setup-value" data-type="brand">
                                                <span class="selection" onclick="selectedProduct('afss', 'brand', 1)">Wet System</span>
                                                <span class="selection" onclick="selectedProduct('afss', 'brand', 2)">Deluge System/ Foam System</span>
                                                <span class="selection" onclick="selectedProduct('afss', 'brand', 3)">Pre-Action</span>
                                                <span class="selection" onclick="selectedProduct('afss', 'brand', 4)">Dry Type</span>
                                                <span class="selection" onclick="selectedProduct('afss', 'brand', 5, 'other')">Other/Pls. Specify</span>
                                                <textarea name="other" class="form-control form-control-sm hide" onkeyup="otherValue('afss', 'brand', 5, $(this).val())"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-2" id="cafss">
                                        <div class="product-catalog">
                                            <div class="setup-header">Clean Agent-fire Suppression System</div>
                                            <div class="setup-value" data-type="brand">
                                                <span class="selection" onclick="selectedProduct('cafss', 'brand', 1)">FM200 (Brand 1: Masteco, Brand:2 Janus)</span>
                                                <span class="selection" onclick="selectedProduct('cafss', 'brand', 2)">Novec1230 (Brand 1: Janus)</span>
                                                <span class="selection" onclick="selectedProduct('cafss', 'brand', 3)">CO2 (Brand: Janus)</span>
                                                <span class="selection" onclick="selectedProduct('cafss', 'brand', 4)">Fire Detect (Brand: Rotarex)</span>
                                                <span class="selection" onclick="selectedProduct('cafss', 'brand', 5, 'other')">Other/Pls. Specify</span>
                                                <textarea name="other" class="form-control form-control-sm hide" onkeyup="otherValue('cafss', 'brand', 5, $(this).val())"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-2" id="akfss">
                                        <div class="product-catalog">
                                            <div class="setup-header">AKFSS (Automatic Kitchen Fire Supression System)</div>
                                            <div class="setup-value" data-type="brand">
                                                <span class="selection" onclick="selectedProduct('akfss', 'brand', 1)">AKFSS (Brand: Lehavot)</span>
                                                <span class="selection" onclick="selectedProduct('akfss', 'brand', 2, 'other')">Other/Pls. Specify</span>
                                                <textarea name="other" class="form-control form-control-sm hide" onkeyup="otherValue('akfss', 'brand', 3, $(this).val())"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-2" id="so">
                                        <div class="product-catalog">
                                            <div class="setup-header">Supply Only</div>
                                            <div class="setup-value" data-type="brand">
                                                <span class="selection" onclick="selectedProduct('so', 'brand', 1)">AFSS</span>
                                                <span class="selection" onclick="selectedProduct('so', 'brand', 2)">Clean Agent</span>
                                                <span class="selection" onclick="selectedProduct('so', 'brand', 3)">AKFSS</span>
                                                <span class="selection" onclick="selectedProduct('so', 'brand', 4, 'other')">Other/Pls. Specify</span>
                                                <textarea name="other" class="form-control form-control-sm hide" onkeyup="otherValue('so', 'brand', 4, $(this).val())"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-2" id="pm">
                                        <div class="product-catalog">
                                            <div class="setup-header">Preventive Maintenance</div>
                                            <div class="setup-value" data-type="brand">
                                                <span class="selection" onclick="selectedProduct('pm', 'brand', 1)">AFSS</span>
                                                <span class="selection" onclick="selectedProduct('pm', 'brand', 2)">Clean Agent</span>
                                                <span class="selection" onclick="selectedProduct('pm', 'brand', 3)">AKFSS</span>
                                                <span class="selection" onclick="selectedProduct('pm', 'brand', 4, 'other')">Other/Pls. Specify</span>
                                                <textarea name="other" class="form-control form-control-sm hide" onkeyup="otherValue('pm', 'brand', 4, $(this).val())"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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

        <div class="modal fade" id="view_attachment" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document" style="max-width: 1300px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Attachment</h5>
                    </div>
                    <div class="modal-body m-3">
                        <div class="row">
                            <div class="col-4">
                                <div class="row">
                                    <div class="col-8"><input type="file" class="form-control" name="manual_attachment[]" id="manual_attachment" multiple></div>
                                    <div class="col-4"><button type="button" class="form-control btn btn-primary" id="uploadFile" onclick="uploadFile()">Upload</button></div>
                                </div>
                                <input type="hidden" name="bill_detail_id" id="bill_detail_id" class="form-control">
                                <br>
                                <div class="col-1"><h5>Files:</h5></div>
        
                                <div class="files">
                                    <div id="file-view"></div>
                                </div>
                            </div>
                            <div class="col-8" style="height: 700px; overflow-y: scroll;">
                                <img src="" width="100%" id="file-viewer"/>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        
        
    </main>
@endsection

@section('scripts')
<script src="/js/datatables.min.js"></script>
<script src="/js/page/fsd.js"></script>
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
.row.file-line {
    padding: 10px;
    background: #fff;
    margin-bottom: 10px;
    box-shadow: 0 0 10px #ccc;
    margin: 10px;
    border-radius: 10px;
}
.setup-header {
    background: #ff8484;
    padding: 10px;
    color: #fff;
    font-weight: bold;
}
.setup-type {
    background: #ffbcbc;
    padding: 5px 10px;
    color: #fff;
}
span.selection {
    padding: 5px 10px;
    background: #eee;
    border-radius: 50px;
    display: inline-block;
    font-size: 12px;
    font-weight: bold;
    cursor: pointer;
    margin-bottom: 5px;
}
.setup-value {
    padding: 5px 10px;
}
.product-catalog {
    border: 1px solid #ff8484;
    height: 100%;
}
textarea.hide {
    display: none !important;
}
.selection.selected {
    background: black !important;
    color: #fff !important;
}
</style>
