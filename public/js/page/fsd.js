var action = 'save';
var page = 'fsd';
var table = null;
var record_id = null;

var details_id = null;

var delete_module = null;

var product_data = [];

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
                       '<a href="#" class="align-middle fas fa-fw fa-paperclip edit" onclick="attach('+row.id+')"></a>';
            }},
            { data: 'active', title: '', render:function(data, type, row, meta){
                return '<span class="active-'+row.active+'"></span>'
            }},
            { data: 'source.source', title: 'Source' },
            { data: 'rfq_no', title: 'RFQ No.' },
            { data: 'category', title: 'Category' },
            { data: 'customer_type', title: 'Customer Type' },
            { data: 'project_title', title: 'Project Title' },
            { data: 'company_name', title: 'Company Name' },
            { data: 'company_address', title: 'Company Address' },
            { data: 'contact_person', title: 'Contact Person' },
            { data: 'designation', title: 'Designation' },
            { data: 'telephone', title: 'Telephone' },
            { data: 'email', title: 'Email' },
            { data: 'date_received', title: 'Date Received', render: function(data, type, row, meta) {
                if(row.date_received != null) {
                    return moment(row.date_received).format('MMM-DD-YYYY');
                } else {
                    return '--';
                }
            }},
            { data: 'date_filed', title: 'Date Filed', render: function(data, type, row, meta) {
                if(row.date_filed != null) {
                    return moment(row.date_filed).format('MMM-DD-YYYY');
                } else {
                    return '--';
                }
            }},
            { data: 'project_location', title: 'Project Location' },
            { data: 'tcp', title: 'TPC/ Mark up' },
            { data: 'deadline', title: 'Deadline' },
            { data: 'comments', title: 'Comment' },
            { data: 'sales.name', title: 'Sales In Charge' },
            { data: 'design.name', title: 'Design & Estimate' },
            { data: 'supervisor.name', title: 'Design & Supervisor' },
            { data: 'date_submitted', title: 'Date Filed', render: function(data, type, row, meta) {
                if(row.date_submitted != null) {
                    return moment(row.date_submitted).format('MMM-DD-YYYY');
                } else {
                    return '--';
                }
            }},
            { data: 'quoted_amount', title: 'Quoted Amount' },
            { data: 'reference_no', title: 'Reference Number' },
            { data: 'date_puchased', title: 'Date Filed', render: function(data, type, row, meta) {
                if(row.date_puchased != null) {
                    return moment(row.date_puchased).format('MMM-DD-YYYY');
                } else {
                    return '--';
                }
            }},
            { data: 'po_no', title: 'PO No.' },
            { data: 'po_amount', title: 'PO Amount' },
            
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
        rfq_no: $('#rfq_no').val(),
        source_id : $('#source_id ').val(),
        category: $('#category').val(),
        customer_type: $('#customer_type').val(),
        project_title: $('#project_title').val(),
        company_name: $('#company_name').val(),
        company_address: $('#company_address').val(),
        contact_person: $('#contact_person').val(),
        designation: $('#designation').val(),
        telephone: $('#telephone').val(),
        email: $('#email').val(),
        date_received: $('#date_received').val(),
        date_filed: $('#date_filed').val(),
        project_location: $('#project_location').val(),
        tcp: $('#tcp').val(),
        deadline: $('#deadline').val(),
        comments: $('#comments').val(),
        sales_associate_id: $('#sales_associate_id').val(),
        design_id: $('#design_id').val(),
        supervisor: $('#supervisor').val(),
        date_submitted: $('#date_submitted').val(),
        quoted_amount: $('#quoted_amount').val(),
        reference_no: $('#reference_no').val(),
        date_purchased: $('#date_purchased').val(),
        po_no: $('#po_no').val(),
        po_amount: $('#po_amount').val(),
        remarks: $('#remarks').val(),
        type: 2,
        product_record: product_data
    };

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
            $('#recordModal').modal('show');
            $('.selection').removeClass('selected');
            product_data = [];

            $.each(data, function() {
                $.each(this, function(k, v) {

                    if(k === "setup") {
                        v.forEach(setup => {
                            $($(`#${setup.product} .setup-value[data-type="${setup.type}"] .selection`)[setup.indx - 1]).addClass('selected');

                            product_data.push({
                                "product": setup.product,
                                "type": setup.type,
                                "indx": setup.indx,
                                "other_value": setup.other_value !== null? setup.other_value:null,
                            });
                            
                            if(setup.other_value !== null) {
                                $(`#${setup.product} .setup-value[data-type="${setup.type}"] textarea`).val(setup.other_value);
                                $(`#${setup.product} .setup-value[data-type="${setup.type}"] textarea`).removeClass('hide');
                            }
                        });
                    }

                    $('#'+k).val(v);
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
            type: 'GET'
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
                return "<input class='form-control' id='serial_"+row.id+"' value='"+row.serial_no+"' placeholder='Serial Number' onkeyup='autoSaveSerial("+row.id+", "+id+")'/>";
            }},
            { data: 'warranty_no', title: 'WARRANTY NO.', render:function(data, type, row, meta) {
                return "<input class='form-control'  id='warranty_"+row.id+"' value='"+row.warranty_no+"' placeholder='Warranty Number' onkeyup='autoSaveWarranty("+row.id+", "+id+")'/>";
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

function attach(id) {
    $('#view_attachment').modal('show');
    $('#bill_detail_id').val(id);
    $.post('/fsd/view_attachment', { _token:$('meta[name="csrf-token"]').attr('content'), id: id }, (response)=>{
        var html = '';

        console.log((response.attachment).length)

        if((response.attachment).length === 0 ) {
            $('#file-viewer').attr("src", "/images/no_files.jpg");
            $('#file-view').html('No Files');
        }
        else {
            $.each(response.attachment, function(i, val) {
                html += '<div class="row file-line"><div class="col-md-10">';
                html += '<div class="file-list" onclick="viewFile('+"'"+val.filename+"'"+')">'+val.filename+'</div></div>';
                html += '<div class="col-md-2 action-btn"><div class="btn-group">';
                html += '<a class="align-middle fas fa-fw fa-trash" onclick="deleteFile('+"'"+val.id+"'"+')"></a></div></div></div>';
            });
            $('#file-view').html(html);
            viewFile(response.attachment[0].filename);
        }
    });
}

function viewFile(filename) {
    var file = "/storage/images/attachment/sale/"+filename;
    $('#file-viewer').attr('src', file);
}

function uploadFile() {
    var formData = new FormData();
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    formData.append('sale_id', $('#bill_detail_id').val());

    var attachmentInput = $('#manual_attachment')[0];

    // Check if files were selected
    if (attachmentInput.files.length > 0) {
    for (var i = 0; i < attachmentInput.files.length; i++) {
            formData.append('manual_attachment[]', attachmentInput.files[i]);
        }
    }

    $.ajax({
        url: '/fsd/upload', // Replace with your server endpoint
        type: 'POST',
        data: formData,
        enctype: 'multipart/form-data', // This line is not needed
        processData: false, // Don't process the data
        contentType: false, // Set content type to false
        success: function (response) {
            attach($('#bill_detail_id').val());
            $('#manual_attachment').val(null);
        },
        error: function (error) {
            for (var field in response.responseJSON.errors) {
                $('#bills_payment_details_form #'+field+"_error_message").remove();
                $('#bills_payment_details_form .'+field).append('<span id="'+field+'_error_message" class="error-message">'+response.responseJSON.errors[field][0]+'</span>');
            }
        }
      });
}


function deleteFile(id) {
    $.get('/fsd/delete_file/'+id).done(function(response){
        attach($('#bill_detail_id').val());
        $('#file-viewer').attr('src',  "/images/no_files.jpg");
    })
}

function selectedProduct(product, type, indx, other) {
    if(other === undefined) {
        if(!$($(`#${product} .setup-value[data-type="${type}"] .selection`)[indx - 1]).hasClass('selected')) {
            $($(`#${product} .setup-value[data-type="${type}"] .selection`)[indx - 1]).addClass('selected');

            product_data.push({
                "product": product,
                "type": type,
                "indx": indx,
                "other_value": null,
            })
        }
        else {
            $($(`#${product} .setup-value[data-type="${type}"] .selection`)[indx - 1]).removeClass('selected');

            product_data = product_data.filter
                            (item => 
                                item.product !== product ||
                                item.type !== type ||
                                item.indx !== indx
                            );
        }
    }
    else {
        if(!$($(`#${product} .setup-value[data-type="${type}"] .selection`)[indx - 1]).hasClass('selected')) {
            $($(`#${product} .setup-value[data-type="${type}"] .selection`)[indx - 1]).addClass('selected');
            $(`#${product} .setup-value[data-type="${type}"] textarea`).removeClass('hide');
            
            product_data.push({
                "product": product,
                "type": type,
                "indx": indx,
                "other_value": $(`#${product} .setup-value[data-type="${type}"] textarea`).val(),
            });
        }
        else {
            $($(`#${product} .setup-value[data-type="${type}"] .selection`)[indx - 1]).removeClass('selected');
            $(`#${product} .setup-value[data-type="${type}"] textarea`).addClass('hide');
            $(`#${product} .setup-value[data-type="${type}"] textarea`).val('');

            product_data = product_data.filter
                            (item => 
                                item.product !== product ||
                                item.type !== type ||
                                item.indx !== indx
                            );
        }
    }
}

function otherValue(product, type, indx, other) {
    
    product_data = product_data.filter
                    (item => 
                        item.product !== product ||
                        item.type !== type ||
                        item.indx !== indx
                    );

    product_data.push({
                "product": product,
                "type": type,
                "indx": indx,
                "other_value": $(`#${product} .setup-value[data-type="${type}"] textarea`).val(),
            });
                    
}