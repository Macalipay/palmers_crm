var action = 'save';
var page = 'sale';
var table = null;
var record_id = null;

var details_id = null;

var delete_module = null;

var filter = {
    _token :'',
    'company' : '',
    'store' : '',
    'start_date' : '',
    'end_date' : '',
    'associate' : ''
};

$(function() {
    // Attach event listener to input fields
    $('input, textarea').not('[type="email"]').not('[type="password"]').not('[type="number"]').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });
});

$(function() {
    $('#generated_table').DataTable({
        responsive: false,
        serverSide: true,
        paging: true,
        scrollX: true,
        ordering: false,
        ajax: {
            url: '/'+page+'/get',
            type: 'GET'
        },
        columns: [
            { data: null, title: 'Action', render: function(data, type, row, meta) {
                return '<a href="#" class="align-middle fas fa-fw fa-pen edit" title="Edit" data-toggle="modal" data-target="#defaultModalPrimary" id="'+row.id+'" onclick="edit('+row.id+')"></a>' +
                       '<a href="#" data-toggle="modal" data-target="#confirmation" onclick="deleteRecord('+row.id+', 0)"><i class="align-middle fas fa-fw fa-trash"></i></a>' +
                       '<a href="#" onclick="viewDetails('+row.id+')"><i class="align-middle fas fa-fw fa-cart-plus"></i></a>';
            }},
            { data: 'status', title: '', render:function(data, type, row, meta){
                return '<span class="active-'+row.status+'"></span>'
            }},
            { data: 'company.company_name', title: 'Company Name' },
            { 
                data: 'store.store_name', 
                title: 'Store Name',
                render: function(data, type, row) {
                    return data ? data : '--';
                }
            },
            { data: 'company.contact_person', title: 'Contact Person' },
            { data: 'company.contact_no', title: 'Contact No.' },
            {data: 'company.address',title: 'Address', render: function (data, type, row) {
                if(row.company.address != null) {
                    return '<span title="' + data + '">' + (type === 'display' && data.length > 30 ? data.substr(0, 30) + '...' : data) + '</span>';
                } else {
                    return '--';
                }
            }},
            { data: 'company.province.province', title: 'Province' },
            { data: 'company.industry', title: 'Industry' },
            { data: 'payment_term', title: 'Terms' },
            { data: 'customer_type', title: 'Customer Type' },
            { data: 'source.source', title: 'Source' },
            { data: 'po_no', title: 'PO/OF Number' },
            { data: 'date_purchased', title: 'Date Purchased', render: function(data, type, row, meta) {
                return moment(row.date_purchased).format('MMM-DD-YYYY');
            }},
            { data: 'amount', title: 'Amount' },
            { data: 'sales_associate.sales_associate', title: 'Sales Associate', render: function(data, type, row, meta) {
                return (row.sales_associate !== null?row.sales_associate.sales_associate:'-');
            }},
            { data: 'user.name', title: 'Sales Agent', render: function(data, type, row, meta) {
                return (row.user !== null?row.user.name:'-');
            }},
            { data: 'division.division', title: 'Division', render: function(data, type, row, meta) {
                return row.division !== null?row.division.division:'-';
            } },
            { data: 'branch.branch_name', title: 'Branch', render: function(data, type, row, meta) {
                return row.branch !== null?row.branch.branch_name:'-';
            }},
            { data: 'agreed_delivery_date', title: 'Agreed Delivery Date', render: function(data, type, row, meta) {
                if(row.agreed_delivery_date != null) {
                    return moment(row.agreed_delivery_date).format('MMM-DD-YYYY');
                } else {
                    return '--';
                }
            }},
            { data: 'actual_delivery_date', title: 'Actual Delivery Date', render: function(data, type, row, meta) {
                if(row.actual_delivery_date != null) {
                    return moment(row.actual_delivery_date).format('MMM-DD-YYYY');
                } else {
                    return '--';
                }
            }},
        ]
    });
    
    $('#company_table').DataTable({
        responsive: true,
        serverSide: true,
        paging: true,
        ordering: false,
        ajax: {
            url: '/company/get',
            type: 'GET'
        },
        columns: [
            { data: 'company_name', title: 'Company Name' },
        ]
    });

    $('#store_table').DataTable({
        responsive: true,
        serverSide: true,
        paging: true,
        ordering: false,
        ajax: {
            url: '/store/list/get',
            type: 'GET'
        },
        columns: [
            { data: 'code', title: 'Code' },
            { data: 'store_name', title: 'Store Name' },
        ]
    });

    $('#company_table tbody').on('dblclick', 'tr', function () {
        var data = $('#company_table').DataTable().row( this ).data();

        $('.company_name').val(data.company_name);
        $('.f_company_name').val(data.company_name);
        $('#company_id').val(data.id);
        $('#f_company_id').val(data.id);

        $('#companyList').modal('hide');

        if ($.fn.DataTable.isDataTable('#store_table')) {
            // Clear and destroy the existing DataTable
            $('#store_table').DataTable().clear().destroy();
        }

        $('#store_table').DataTable({
            responsive: true,
            serverSide: true,
            paging: true,
            ordering: false,
            ajax: {
                url: '/store/list/' + data.id,
                type: 'GET'
            },
            columns: [
                { data: 'code', title: 'Code' },
                { data: 'store_name', title: 'Store Name' },
            ]
        });
    
    });

    $('#store_table tbody').on('dblclick', 'tr', function () {
        var data = $('#store_table').DataTable().row( this ).data();

        $('.store_name').val(data.store_name);
        $('.f_store_name').val(data.store_name);
        $('#store_id').val(data.id);
        $('#f_store_id').val(data.id);

        $('#storeList').modal('hide');
    });

    $('#item_id').change(function(){
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/'+page+'_details/item/' + this.value,
            method: 'get',
            data: {},
            success: function(data) {
                $('#amount').val(data.data.amount);
                var total = ($('#amount').val() * $('#quantity').val()) - $('#discount').val();
                $('#total').val(total);
            }
        });
    });

    $('#quantity, #amount, #discount').change(function(){
        var total = ($('#amount').val() * $('#quantity').val()) - $('#discount').val();
        $('#total').val(total);
    });
});

function saveRecord() {
    var data = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        company_id: $('#company_id').val(),
        store_id: $('#store_id').val(),
        assist_by: $('#assist_by').val(),
        customer_type: $('#customer_type').val(),
        source_id: $('#source_id').val(),
        po_no: $('#po_no').val(),
        date_purchased: $('#date_purchased').val(),
        user_id: $('#user_id').val(),
        sales_associate_id: $('#sales_associate_id').val(),
        division_id: $('#division_id').val(),
        merchandiser_id: $('#merchandiser_id').val(),
        branch_id: $('#branch_id').val(),
        agreed_delivery_date: $('#agreed_delivery_date').val(),
        payment_term: $('#payment_term').val(),
        active: $('#active').val(),
    }

    if(action === "save") {
        $.post('/'+page+'/save', data).done(function(resp) {
            $('#generated_table').DataTable().draw();
            $('#recordModal').modal('hide');
            clear();
        }).fail(function(resp) {
            var r = resp.responseJSON.errors;

            $('.form-control').removeClass('required');
            $.each(r, function(i,v) {
                $('#' + i).addClass('required');
            });
        });
    }
    else {
        $.post('/'+page+'/update/'+record_id, data).done(function(resp) {
            $('#generated_table').DataTable().draw();
            $('#recordModal').modal('hide');
            clear();
        }).fail(function(resp) {
            var r = resp.responseJSON.errors;

            $('.form-control').removeClass('required');
            $.each(r, function(i,v) {
                $('#' + i).addClass('required');
            });
        });
    }
}

function edit(id){
    action = "updated";
    record_id = id;

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/'+page+'/edit/' + id,
        method: 'get',
        data: {},
        success: function(data) {
            $('.company_name').val(data.data.company.company_name);

            $('.store_name').val(data.data.store !== null? data.data.store.store_name:'');

            $('#recordModal').modal('show');
            $.each(data, function() {
                $.each(this, function(k, v) {
                    $('#'+k).val(v);
                    if(k === 'division_id') {
                        divisionSelect();
                    }
                    else if(k === 'branch_id') {
                        setTimeout(() => {
                            $('#branch_id').val(v);
                        }, 1000);
                    }
                });
            });
            
        }
    });

}

function deleteRecord(id, type) {
    delete_module = type;

    if(type === 0) {
        record_id = id;
    }
    else {
        details_id = id;
    }

    $('#deleteModal').modal('show');
}

function yesDelete() {
    if(delete_module === 0) {
        $.get('/'+page+'/destroy/' + record_id, function() {
            $('#deleteModal').modal('hide');
            $('#generated_table').DataTable().draw();
        });
    }
    else {
        $.get('/'+page+'_details/destroy/' + details_id, function() {
            $('#deleteModal').modal('hide');
            $('#generated_table_details').DataTable().draw();
        });
    }
}

function clear() {
    var action = "save";

    $('.company_name').val('');
    $('.f_company_name').val('');
    $('#company_id').val('');
    $('#f_company_id').val('');

    $('#name').val('');
    $('#description').val('');
    $('#status').val('1');
    $('#color').val('');
}

function addOrganization() {
    clear();
    $('#recordModal').modal('show');
}

function edit_details(id){
    action = "updated";
    details_id = id;

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/'+page+'_details/edit/' + details_id,
        method: 'get',
        data: {},
        success: function(data) {
            $('#recordDetails').modal('show');
            $.each(data, function() {
                $.each(this, function(k, v) {
                    $('#'+k).val(v);
                });
            });
        }
    });
}

function viewDetails(id) {
    $('#detailsModal').modal('show');
    record_id = id;

    if ($.fn.DataTable.isDataTable('#generated_table_details')) {
        $('#generated_table_details').DataTable().destroy();
    }

    $('#generated_table_details').DataTable({
        responsive: true,
        serverSide: true,
        paging: true,
        ordering: false,
        ajax: {
            url: '/'+page+'_details/get/' + id,
            type: 'GET',
            dataSrc: function(json) {
                $('#g_total').text(json.total);

                return json.data;
            }
        },
        columns: [
            { data: null, title: 'Action', render: function(data, type, row, meta) {
                return '<a href="#" class="align-middle fas fa-fw fa-pen edit" title="Edit" data-toggle="modal" data-target="#defaultModalPrimary" id="'+row.id+'" onclick="edit_details('+row.id+')"></a>' + '<a href="#" data-toggle="modal" data-target="#confirmation" onclick="deleteRecord('+row.id+', 1)"><i class="align-middle fas fa-fw fa-trash"></i></a>'+ '<a href="#" data-toggle="modal" data-target="#confirmation" onclick="viewSaleSerial('+row.id+')"><i class="align-middle fas fa-fw fa-key"></i></a>'
            }},
            { data: 'item.item_name', title: 'Item Name' },
            { data: 'brand.brand', title: 'Brand' },
            { data: 'description', title: 'Description' },
            { data: 'quantity', title: 'Quantity' },
            { data: 'amount', title: 'Amount' },
            { data: 'discount', title: 'Discount' },
            { data: 'total', title: 'Total' },
        ]
    });
}

function addDetails() {
    $('#recordDetails').modal('show');
    action = 'save';
}

function saveRecordDetails() {
    var data = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        sale_id: record_id,
        item_id: $('#item_id').val(),
        brand_id: $('#brand_id').val(),
        description: $('#description').val(),
        amount: $('#amount').val(),
        quantity: $('#quantity').val(),
        discount: $('#discount').val(),
        total: $('#total').val(),
    }

    if(action === "save") {
        $.post('/'+page+'_details/save', data).done(function(resp) {
            $('#generated_table_details').DataTable().draw();
            $('#recordDetails').modal('hide');
            clearDetails();
        }).fail(function(resp) {
            var r = resp.responseJSON.errors;

            $('.form-control').removeClass('required');
            $.each(r, function(i,v) {
                $('#' + i).addClass('required');
            });
        });
    }
    else {
        $.post('/'+page+'_details/update/'+details_id, data).done(function(resp) {
            $('#generated_table_details').DataTable().draw();
            $('#recordDetails').modal('hide');
            clearDetails();
        }).fail(function(resp) {
            var r = resp.responseJSON.errors;

            $('.form-control').removeClass('required');
            $.each(r, function(i,v) {
                $('#' + i).addClass('required');
            });
        });
    }
}

function clearDetails() {
    var action = "save";

    $('#details_name').val('');
    $('#details_address').val('');
    $('#details_start_date').val('1');
    $('#president').val('');
    $('#details_color').val('#000000');
    $('#status').val('1');
}

function divisionSelect() {
    var division = $('#division_id').val();

    $.get('/branch/get_list/'+division, function(response) {
        var html = "";
        html += "<option value=''></option>";
        $.each(response.data, function(i,v) {
            html += "<option value='"+v.id+"'>"+v.branch_name+"</option>";
        });
        $('#branch_id').html(html);
    });
}

function viewSaleSerial(id) {
    if ($.fn.DataTable.isDataTable('#generated_table_serial')) {
        $('#generated_table_serial').DataTable().destroy();
    }
    
    $('#generated_table_serial').DataTable({
        responsive: true,
        serverSide: true,
        paging: true,
        ordering: false,
        ajax: {
            url: '/serial/get/' + id,
            type: 'GET'
        },
        columns: [
            { data:'DT_RowIndex'},
            { data: 'serial_no', title: 'SERIAL NO.', render:function(data, type, row, meta) {
                return "<input class='form-control' id='serial_"+row.id+"' value='"+row.serial_no+"' placeholder='Serial Number' oninput='autoSaveSerial("+row.id+", "+id+")'/>";
            }},
            { data: 'warranty_no', title: 'WARRANTY NO.', render:function(data, type, row, meta) {
                return "<input class='form-control'  id='warranty_"+row.id+"' value='"+row.warranty_no+"' placeholder='Warranty Number' oninput='autoSaveWarranty("+row.id+", "+id+")'/>";
            }}
        ]
    });

    $('#serialModal').modal('show');
}

function autoSaveSerial(id, sales_id) {
    var data = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        id: id,
        sales_id: sales_id,
        value: $('#serial_'+id).val(),
        type: 'serial_no'
    };

    $.post('/serial/save', data).done(function(response) {});
}

function autoSaveWarranty(id, sales_id) {
    var data = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        id: id,
        sales_id: sales_id,
        value: $('#warranty_'+id).val(),
        type: 'warranty_no'
    };

    $.post('/serial/save', data).done(function(response) {});
}

function generateRecord() {
    filter._token = $('meta[name="csrf-token"]').attr('content');
    filter.company = $('#f_company_id').val();
    filter.store = $('#f_store_id').val();
    filter.start_date = $('#f_start').val();
    filter.end_date = $('#f_end').val();
    filter.associate = $('#f_sales_associate_id').val();
    
    if ($.fn.DataTable.isDataTable('#generated_table')) {
        $('#generated_table').DataTable().destroy();
    }
    
    $('#generated_table').DataTable({
        responsive: false,
        serverSide: true,
        paging: true,
        scrollX: true,
        ordering: false,
        ajax: {
            url: '/sale/filter',
            type: 'POST',
            data: filter
        },
        columns: [
            { data: null, title: 'Action', render: function(data, type, row, meta) {
                return '<a href="#" class="align-middle fas fa-fw fa-pen edit" title="Edit" data-toggle="modal" data-target="#defaultModalPrimary" id="'+row.id+'" onclick="edit('+row.id+')"></a>' +
                       '<a href="#" data-toggle="modal" data-target="#confirmation" onclick="deleteRecord('+row.id+', 0)"><i class="align-middle fas fa-fw fa-trash"></i></a>' +
                       '<a href="#" onclick="viewDetails('+row.id+')"><i class="align-middle fas fa-fw fa-cart-plus"></i></a>';
            }},
            { data: 'status', title: '', render:function(data, type, row, meta){
                return '<span class="active-'+row.status+'"></span>'
            }},
            { data: 'company.company_name', title: 'Company Name' },
            { 
                data: 'store.store_name', 
                title: 'Store Name',
                render: function(data, type, row) {
                    return data ? data : '--';
                }
            },
            { data: 'company.contact_person', title: 'Contact Person' },
            { data: 'company.contact_no', title: 'Contact No.' },
            {data: 'company.address',title: 'Address', render: function (data, type, row) {
                if(row.company.address != null) {
                    return '<span title="' + data + '">' + (type === 'display' && data.length > 30 ? data.substr(0, 30) + '...' : data) + '</span>';
                } else {
                    return '--';
                }
            }},
            { data: 'company.province.province', title: 'Province' },
            { data: 'company.industry', title: 'Industry' },
            { data: 'payment_term', title: 'Terms' },
            { data: 'customer_type', title: 'Customer Type' },
            { data: 'source.source', title: 'Source' },
            { data: 'po_no', title: 'PO/OF Number' },
            { data: 'date_purchased', title: 'Date Purchased', render: function(data, type, row, meta) {
                return moment(row.date_purchased).format('MMM-DD-YYYY');
            }},
            { data: 'amount', title: 'Amount' },
            { data: 'sales_associate.sales_associate', title: 'Sales Associate', render: function(data, type, row, meta) {
                return (row.sales_associate !== null?row.sales_associate.sales_associate:'-');
            }},
            { data: 'user.name', title: 'Sales Agent', render: function(data, type, row, meta) {
                return (row.user !== null?row.user.name:'-');
            }},
            { data: 'division.division', title: 'Division', render: function(data, type, row, meta) {
                return row.division !== null?row.division.division:'-';
            } },
            { data: 'branch.branch_name', title: 'Branch', render: function(data, type, row, meta) {
                return row.branch !== null?row.branch.branch_name:'-';
            }},
            { data: 'agreed_delivery_date', title: 'Agreed Delivery Date', render: function(data, type, row, meta) {
                if(row.agreed_delivery_date != null) {
                    return moment(row.agreed_delivery_date).format('MMM-DD-YYYY');
                } else {
                    return '--';
                }
            }},
            { data: 'actual_delivery_date', title: 'Actual Delivery Date', render: function(data, type, row, meta) {
                if(row.actual_delivery_date != null) {
                    return moment(row.actual_delivery_date).format('MMM-DD-YYYY');
                } else {
                    return '--';
                }
            }},
        ]
    });

    $('#filterModal').modal('hide');
}

function clearFilter() {
    $('#f_company_id').val('');
    $('#f_store_id').val('');
    $('.f_store_name').val('');
    $('.f_company_name').val('');
    $('#f_start').val('');
    $('#f_end').val('');
    $('#f_sales_associate_id').val('');

    filter = {
        _token :'',
        'company' : '',
        'store' : '',
        'start_date' : '',
        'end_date' : '',
        'associate' : ''
    };

    generateRecord();
}