var action = 'save';
var page = 'telemarketing';
var table = null;
var record_id = null;

var details_id = null;
var originalPofoState = null;
var isPofoViewMode = false;

var delete_module = null;
var selectedIds = []; 
var counterTimer = null;

var filter = {
    _token: '',
    assigned_to: '',
    company: '',
    start: '',
    end: '',
    status: '',
    contact: '',
    calls: '',
};

function setRecordDetailsMode(viewOnly) {
    isPofoViewMode = viewOnly;

    $('#status').prop('disabled', viewOnly);
    $('#call_duration').prop('disabled', viewOnly);
    $('#new_order_id').prop('disabled', viewOnly);
    $('#total_amount').prop('disabled', viewOnly);
    $('#remarks').prop('disabled', viewOnly);

    $('#saveRecordDetailsBtn').toggle(!viewOnly);
    $('#backOriginalPofoBtn').toggle(viewOnly);

    if (viewOnly) {
        $('#refreshButton').css({ 'pointer-events': 'none', 'opacity': '0.5' });
    } else {
        $('#refreshButton').css({ 'pointer-events': 'auto', 'opacity': '1' });
    }
}

function refreshTelemarketingCounters() {
    $.ajax({
        url: '/' + page + '/counters',
        method: 'GET',
        success: function(resp) {
            var completeRate = parseFloat(resp.complete_rate || 0);
            $('#counter_complete_rate').text(completeRate.toFixed(2) + '%');
            $('#counter_completed_target_call').text(resp.completed_call || 0);
            $('#counter_total_call_today').text(resp.total_call_today || 0);
            $('#counter_overall_completed_call').text(resp.overall_completed_call || 0);
            $('#counter_completed_call').text(resp.completed_call || 0);
            applyCompletionRateColor(completeRate);
        }
    });
}

function applyCompletionRateColor(rate) {
    var $value = $('#counter_complete_rate');
    var $label = $('#counter_complete_rate_label');
    var colorClass = 'text-danger';

    $value.removeClass('text-danger text-warning text-success');
    $label.removeClass('text-danger text-warning text-success');

    if (rate >= 75) {
        colorClass = 'text-success';
    } else if (rate >= 50) {
        colorClass = 'text-warning';
    }

    $value.addClass(colorClass);
    $label.addClass(colorClass);
}

$(function() {

    document.getElementById("sidebar").classList.add("toggled");

    var status = $('#status').val();  
            if (status === 'COMPLETED') {
            $('#completedField').show();  
        }
        
    $('input, textarea').not('[type="email"]').not('[type="password"]').not('[type="number"]').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });

    $('#refreshButton').click(function(){
        resetDate($('#reset_id').val());
    });

    $('#status').on('change', function() {
        var status = $(this).val();  
        if (status === 'COMPLETED') {
            $('#completedField').show();
        } else {
            $('#completedField').hide();
        }
    });

    // Keep mouse-wheel scrolling inside the record modal body.
    $('#recordDetails').on('wheel', '.modal-body', function(e) {
        e.stopPropagation();

        var body = this;
        var deltaY = e.originalEvent.deltaY;
        var atTop = body.scrollTop <= 0;
        var atBottom = Math.ceil(body.scrollTop + body.clientHeight) >= body.scrollHeight;

        if ((deltaY < 0 && atTop) || (deltaY > 0 && atBottom)) {
            e.preventDefault();
        }
    });

    refreshTelemarketingCounters();
    applyCompletionRateColor(parseFloat(($('#counter_complete_rate').text() || '0').replace('%', '')));
    if (counterTimer) {
        clearInterval(counterTimer);
    }
    counterTimer = setInterval(refreshTelemarketingCounters, 10000);
});

$(function() {
    var table = $('#generated_table').DataTable({
        responsive: false,
        serverSide: true,
        paging: true,
        scrollX: true,
        ordering: false,
        ajax: {
            url: '/' + page + '/get',
            type: 'GET'
        },
        columns: [
            { data: null, title: '<input type="checkbox" id="select-all">', searchable: false, orderable: false, render: function(data, type, row, meta) {
                return '<input type="checkbox" class="select-row" data-id="' + row.id + '">';
            }},
            { data: null, title: 'Action', searchable: false, orderable: false, render: function(data, type, row, meta) {
                return '<a href="#" onclick="edit(' + row.id + ')"><i class="align-middle fas fa-fw fa-tasks"></i></a>';
            }},
            { data: 'date', title: 'Date', 
                render: function(data) {
                    return data ? moment(data).format('MMM D, YYYY') : ''; 
                }
            },
            {
                data: 'user.name',
                title: 'Assigned to',
                render: function(data, type, row) {
                    return row.user.name === 'Super Admin' ? 'UNASSIGNED' : row.user.name;
                }
            },
            { data: 'status', title: 'Status', render: function(data, type, row, meta) {
                var html;
                if (row.status === 'TO DO') {
                    html = '<span class="badge badge-primary">TO DO</span>';
                } else if (row.status === 'IN PROGRESS') {
                    html = '<span class="badge badge-warning">IN PROGRESS</span>';
                } else if (row.status === 'CANCELLED') {
                    html = '<span class="badge badge-danger">CANCELLED</span>';
                } else if (row.status === 'PENDING') {
                    html = '<span class="badge badge-info">PENDING</span>';
                } else if (row.status === 'ON-HOLD') {
                    html = '<span class="badge badge-secondary">ON-HOLD</span>';
                } else {
                    html = '<span class="badge badge-success">COMPLETED</span>';
                }
                return html;
            }},
            { data: 'telemarketing.company.company_name', title: 'Company Name' },
            { data: 'telemarketing.company.contact_person', title: 'Contact Person' },
            { data: 'telemarketing.company.contact_no', title: 'Contact No.' },
            { data: 'description', title: 'Description',
                render: function(data, type, row) {
                    if (!data) return "";
                    let cleanedData = data.replace("CUSTOMER ORDERED", "").trim();
                    let dateMatch = cleanedData.match(/\b(\d{4})-(\d{2})-(\d{2})\b/);
            
                    if (dateMatch) {
                        let formattedDate = moment(dateMatch[0]).format("MMM D, YYYY");
                        cleanedData = cleanedData.replace(dateMatch[0], formattedDate);
                    }
            
                    return cleanedData;
                }
            },
            { data: 'telemarketing.company.address', title: 'Address', render: function(data, type, row) {
                if (row.telemarketing.company.address != null) {
                    return '<span title="' + data + '">' + (type === 'display' && data.length > 30 ? data.substr(0, 30) + '...' : data) + '</span>';
                } else {
                    return '--';
                }
            }},
            { data: 'remarks', title: 'Remarks' }
        ],
        rowCallback: function(row, data) {
            $(row).css('cursor', 'pointer').on('dblclick', function(event) {
                if (!$(event.target).is("input[type='checkbox'], a, i")) { 
                    edit(data.id); // Trigger edit function on double-click
                }
            });
        }
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

    $('#assign_table').DataTable({
        responsive: true,
        serverSide: true,
        paging: true,
        ordering: false,
        ajax: {
            url: '/telemarketing/list',
            type: 'GET'
        },
        columns: [
            { data: 'name', title: 'Name' },
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

    $('#assign_table tbody').on('dblclick', 'tr', function () {
        var data = $('#assign_table').DataTable().row( this ).data();

        $('.f_assigned_to').val(data.name);
        $('#f_assigned_to').val(data.id);

        $('#assignedList').modal('hide');
    });

    $('#select-all').on('click', function() {
        var rows = table.rows({ 'search': 'applied' }).nodes();
        $('input[type="checkbox"].select-row').click();
    });

    $('#generated_table tbody').on('change', 'input[type="checkbox"].select-row', function() {
        var id = $(this).data('id');
        if (this.checked) {
            selectedIds.push(id);
        } else {
            selectedIds = selectedIds.filter(function(value) {
                return value != id;
            });
            var el = $('#select-all').get(0);
            if (el && el.checked && ('indeterminate' in el)) {
                el.indeterminate = true;
            }
        }
        console.log(selectedIds); // Debugging: Check selected IDs
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

function resetDate(id) {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/'+page+'/resetDate/' + id,
        method: 'get',
        data: {},
        success: function(data) {
            toastr.success("FOLLOW UP DATE", "Date Successfully updated")
            refreshTelemarketingCounters();
        }
    });
}
function saveRecord() {
    var data = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        company_id: $('#company_id').val(),
        lead_status: $('#lead_status').val(),
        opportunity_status: $('#opportunity_status').val(),
        source_id: $('#source_id').val(),
        product_interest: $('#product_interest').val(),
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

function pick_telemarketing() {
    $('#telemarketingModal').modal('show');
}

function assignedTask() {
    var data = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        records: selectedIds,
        telemarketing_id: $('#telemarketing_id').val(),
    }

    $.post('/'+page+'/assign', data).done(function(resp) {
        $('#generated_table').DataTable().draw();
        clear();
        $('#telemarketingModal').modal('hide');
        selectedIds = [];
        refreshTelemarketingCounters();

        toastr.success("TELEMARKETING TASK", "Task Successfully Assigned")
    }).fail(function(resp) {
        var r = resp.responseJSON.errors;

        $('.form-control').removeClass('required');
        $.each(r, function(i,v) {
            $('#' + i).addClass('required');
        });
    });
}

function edit(id){
    action = "updated";
    record_id = id;
    details_id = id;

    $('#reset_id').val(id)
    $('#telemarketing_detail_id').val(details_id);
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/'+page+'_details/edit/' + details_id,
        method: 'get',
        data: {},
        success: function(data) {
            console.log(data);
            po_detail(data.data.csd.sale.id, data.data.csd.id);
            let rawDate = new Date(data.data.date);
            let options = { year: 'numeric', month: 'short', day: 'numeric' };
            let saleAmount = parseFloat(data.data.csd.sale.amount) || 0;
            let itemAmount = parseFloat(data.data.csd.amount) || 0;


            let itemName = data.data.csd.item.item_name;
            let brandNew = data.data.csd.item.duration.brandnew + ' month';
            let refill = data.data.csd.item.duration.refill + ' month';
            let forWarranty = data.data.csd.item.duration.for_warranty + ' month';
            
            let newTitle = `Item: ${itemName}. Duration - Brand New: ${brandNew}, Refill: ${refill}, For Warranty: ${forWarranty}.`;
            
            $('#infoButton').attr('title', newTitle).tooltip('dispose').tooltip({
                trigger: 'hover'
            });

            $('#detail_date').text(rawDate.toLocaleDateString('en-US', options));
            $('#detail_task').text(data.data.task);
            $('#detail_description').text(
                formatDescription(data.data.description.replace("CUSTOMER ORDERED", "").trim())
            );
            $('#detail_company_name').text(data.data.telemarketing.company.company_name);
            $('#company_pofo_title').text(data.data.telemarketing.company.company_name);
            $('#detail_contact_person').text(data.data.telemarketing.company.contact_person);
            $('#detail_contact_no').text(data.data.telemarketing.company.contact_no);
            $('#po_no').text(data.data.csd.sale.po_no);
            $('#pofo_company_id').val(data.data.telemarketing.company.id);
            $('#total_price').text('₱' + saleAmount.toLocaleString('en-PH', { minimumFractionDigits: 2 }));
            $('#item_price').text('₱' + itemAmount.toLocaleString('en-PH', { minimumFractionDigits: 2 }));
            $('#detail_remarks').text(data.data.remarks);
            $('#assigned_date').text(data.data.assigned_date);
            $('#call_duration').text(data.data.call_duration);
            if (data.data.csd.sale.sales_associate) {
                $('#sales_associate').text(data.data.csd.sale.sales_associate.sales_associate);
            } else {
                $('#sales_associate').text("No Sales Associate Assigned");
            }

            if (data.data.status === 'COMPLETED') {
                $('#completedField').show();  // Show if already completed
            } else {
                $('#completedField').hide();  // Show if already completed
            }

            originalPofoState = {
                saleId: data.data.csd.sale.id,
                itemId: data.data.csd.id,
                date: rawDate.toLocaleDateString('en-US', options),
                task: data.data.task,
                description: formatDescription(data.data.description.replace("CUSTOMER ORDERED", "").trim()),
                poNo: data.data.csd.sale.po_no,
                totalPrice: 'â‚±' + saleAmount.toLocaleString('en-PH', { minimumFractionDigits: 2 }),
                itemPrice: 'â‚±' + itemAmount.toLocaleString('en-PH', { minimumFractionDigits: 2 }),
                detailRemarks: data.data.remarks,
                assignedDate: data.data.assigned_date,
                status: data.data.status,
                remarksValue: data.data.remarks || '',
                callDuration: data.data.call_duration || ''
            };

            setRecordDetailsMode(false);

            $('#recordDetails').modal('show');
            $.each(data, function() {
                $.each(this, function(k, v) {
                    $('#'+k).val(v);
                });
            });

            $('.activity-feed').empty();

            $('.activity-feed').empty(); // Clear previous logs before appending new ones

            $('.activity-feed').empty(); // Clear previous logs before appending new ones

            if (data.call_logs.length === 0) {
                $('.activity-feed').append(`
                    <li class="feed-item">
                        <span class="text text-muted">No activity history available.</span>
                    </li>
                `);
            } else {
                $.each(data.call_logs, function(index, log) {
                    let extraText = "";
                    let amount = parseFloat(log.total_amount) || 0;
            
                    if (log.new_order_id && log.total_amount) {
                        extraText = `
                            <div class="mt-1 text-success">
                                <i class="fas fa-exchange-alt"></i> 
                                This transaction was re-ordered with Order ID 
                                <b>${log.new_order_id}</b>, amounting to 
                                <b>₱${amount.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</b>.
                            </div>`;
                    }
            
                    let remarksText = log.remarks ? `
                        <div class="mt-1 text-muted">
                            <i class="fas fa-comment-alt"></i> ${log.remarks}
                        </div>` : "";
            
                    let logItem = `
                        <li class="feed-item">
                            <time class="date">${moment(log.created_at).format('MMM DD, YYYY - hh:mmA')}</time>
                            <span class="text">
                                The status was set to <b>"${log.status}"</b> by ${log.user.name}.
                            </span>
                            ${extraText}
                            ${remarksText}
                        </li>
                    `;
            
                    $('.activity-feed').append(logItem);
                });
            }
            

        }
    });

}

function viewReport(id) {
    $('#reportModal').modal('show');
    record_id = id;
}

function formatDescription(description) {
    let dateRegex = /(\d{4}-\d{2}-\d{2})/;
    let match = description.match(dateRegex);

    if (match) {
        let date = new Date(match[0]); // Convert to Date object

        let formattedDate = date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });

        return description.replace(match[0], formattedDate);
    }

    return description; 
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
            refreshTelemarketingCounters();
        });
    }
    else {
        $.get('/'+page+'_details/destroy/' + details_id, function() {
            $('#deleteModal').modal('hide');
            $('#generated_table_details').DataTable().draw();
            refreshTelemarketingCounters();
        });
    }
}

function clear() {
    var action = "save";

    $('#name').val('');
    $('#description').val('');
    $('#status').val('1');
    $('#color').val('');
}

function addOrganization() {
    clear();
    $('#recordModal').modal('show');
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
            { data: null, title: 'Action', searchable: false, orderable: false, render: function(data, type, row, meta) {
                return '<a href="#" class="align-middle fas fa-fw fa-pen edit" title="Edit" data-toggle="modal" data-target="#defaultModalPrimary" id="'+row.id+'" onclick="edit_details('+row.id+')"></a>' + '<a href="#" data-toggle="modal" data-target="#confirmation" onclick="deleteRecord('+row.id+', 1)"><i class="align-middle fas fa-fw fa-trash"></i></a>';
            }},
            { data: 'date', title: 'Date', render: function(data, type, row, meta) {
                return moment(row.date).format('MMM-DD-YYYY');
            }},
            { data: 'task', title: 'Task' },
            { 
                data: 'description', 
                title: 'Description',
                render: function(data, type, row) {
                    return data ? data.replace("CUSTOMER ORDERED", "").trim() : "";
                }
            },
            { data: 'user.name', title: 'Assigned_to' },
            { data: 'remarks',title: 'Remarks', render: function (data, type, row) {
                if(row.remarks != null) {
                    return '<span title="' + data + '">' + (type === 'display' && data.length > 30 ? data.substr(0, 30) + '...' : data) + '</span>';
                } else {
                    return '--';
                }
            }},
            { data: 'status', title: 'Status', render:function(data, type, row, meta){
                var html;

                if (row.status == 'TO DO') {
                    html = '<span class="badge badge-primary">TO DO</span>';
                } else if(row.status == 'IN PROGRESS') {
                    html = '<span class="badge badge-warning">IN PROGRESS</span>';
                } else if(row.status == 'CANCELLED') {
                    html = '<span class="badge badge-danger">CANCELLED</span>';
                } else if(row.status == 'PENDING') {
                    html = '<span class="badge badge-info">PENDING</span>';
                } else if(row.status == 'ON-HOLD') {
                    html = '<span class="badge badge-secondary">ON-HOLD</span>';
                } else {
                    html = '<span class="badge badge-success">COMPLETED</span>';
                }

                return html;
            }},
        ]
    });
}

function addDetails() {
    $('#recordDetails').modal('show');
    action = 'save';
}

function saveRecordDetails() {
    if (isPofoViewMode) {
        toastr.warning("VIEW ONLY", "You are viewing connected PO/FO history. Click BACK TO ORIGINAL PO/FO to edit.");
        return;
    }

    var data = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        telemarketing_detail_id: $('#telemarketing_detail_id').val(),
        new_order_id: $('#new_order_id').val(),
        total_amount: $('#total_amount').val(),
        status: $('#status').val(),
        call_duration: $('#call_duration').val(),
        remarks: $('#remarks').val(),
    }

    $.post('/'+page+'_details/update/'+$('#telemarketing_detail_id').val(), data).done(function(resp) {
        $('#generated_table').DataTable().draw();
        $('#recordDetails').modal('hide');
        clearDetails();
        refreshTelemarketingCounters();
    }).fail(function(resp) {
        var r = resp.responseJSON.errors;

        $('.form-control').removeClass('required');
        $.each(r, function(i,v) {
            $('#' + i).addClass('required');
        });
    });
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

function filterRecord() {
    $('#filterModal').modal('show');
}

function generateRecord() {

    filter._token = $('meta[name="csrf-token"]').attr('content');
    filter.assigned_to = $('#f_assigned_to').val();
    filter.company = $('#f_company_id').val();
    filter.start_date = $('#f_start').val();
    filter.end_date = $('#f_end').val();
    filter.p_start_date = $('#p_start').val();
    filter.p_end_date = $('#p_end').val();
    filter.status = $('#f_status').val();
    filter.unassigned = $('#f_unassigned').length ? ($('#f_unassigned')[0].checked ? 1 : 0) : 0;
    filter.contact = $('#f_contact').length ? ($('#f_contact')[0].checked ? 1 : 0) : 0;
    filter.calls = $('#f_calls').length ? ($('#f_calls')[0].checked ? 1 : 0) : 0;
    filter.accessories = $('#f_accessories').length ? ($('#f_accessories')[0].checked ? 1 : 0) : 0;
    
    
    if ($.fn.DataTable.isDataTable('#generated_table')) {
        $('#generated_table').DataTable().destroy();
    }

    $('#generated_table').DataTable({
        responsive: false,
        serverSide: false,
        paging: true,
        scrollX: true,
        ordering: false,
         dom: 'Bfrtip', // <-- THIS ENABLES BUTTONS

    buttons: [
        {
            extend: 'excelHtml5',
            text: 'Download Excel',
            className: 'btn btn-success btn-sm'
        },
        {
            extend: 'csvHtml5',
            text: 'Download CSV',
            className: 'btn btn-info btn-sm'
        },
        {
            extend: 'pdfHtml5',
            text: 'Download PDF',
            className: 'btn btn-danger btn-sm',
            orientation: 'landscape',
            pageSize: 'A4'
        },
        {
            extend: 'print',
            text: 'Print',
            className: 'btn btn-secondary btn-sm'
        }
    ],
        ajax: {
            url: '/telemarketing/filter',
            type: 'POST',
            data: filter
        },
        columns: [
            { data: null, title: '<input type="checkbox" id="select-all">', searchable: false, orderable: false, render: function(data, type, row, meta) {
                return '<input type="checkbox" class="select-row" data-id="' + row.id + '">';
            }},
            { data: null, title: 'Action', searchable: false, orderable: false, render: function(data, type, row, meta) {
                return '<a href="#" onclick="edit(' + row.id + ')"><i class="align-middle fas fa-fw fa-tasks"></i></a>';
            }},
            { data: 'date', title: 'Date', 
                render: function(data) {
                    return data ? moment(data).format('MMM D, YYYY') : ''; 
                }
            },
            {
                data: 'user.name',
                title: 'Assigned to',
                render: function(data, type, row) {
                    return row.user.name === 'Super Admin' ? 'UNASSIGNED' : row.user.name;
                }
            },
            { data: 'status', title: 'Status', render: function(data, type, row, meta) {
                var html;
                if (row.status === 'TO DO') {
                    html = '<span class="badge badge-primary">TO DO</span>';
                } else if (row.status === 'IN PROGRESS') {
                    html = '<span class="badge badge-warning">IN PROGRESS</span>';
                } else if (row.status === 'CANCELLED') {
                    html = '<span class="badge badge-danger">CANCELLED</span>';
                } else if (row.status === 'PENDING') {
                    html = '<span class="badge badge-info">PENDING</span>';
                } else if (row.status === 'ON-HOLD') {
                    html = '<span class="badge badge-secondary">ON-HOLD</span>';
                } else {
                    html = '<span class="badge badge-success">COMPLETED</span>';
                }
                return html;
            }},
            { data: 'telemarketing.company.company_name', title: 'Company Name' },
            { data: 'telemarketing.company.contact_person', title: 'Contact Person' },
            { data: 'telemarketing.company.contact_no', title: 'Contact No.' },
            { data: 'description', title: 'Description',
                render: function(data, type, row) {
                    if (!data) return "";
                    let cleanedData = data.replace("CUSTOMER ORDERED", "").trim();
                    let dateMatch = cleanedData.match(/\b(\d{4})-(\d{2})-(\d{2})\b/);
            
                    if (dateMatch) {
                        let formattedDate = moment(dateMatch[0]).format("MMM D, YYYY");
                        cleanedData = cleanedData.replace(dateMatch[0], formattedDate);
                    }
            
                    return cleanedData;
                }
            },
            { data: 'telemarketing.company.address', title: 'Address', render: function(data, type, row) {
                if (row.telemarketing.company.address != null) {
                    return '<span title="' + data + '">' + (type === 'display' && data.length > 30 ? data.substr(0, 30) + '...' : data) + '</span>';
                } else {
                    return '--';
                }
            }},
            { data: 'remarks', title: 'Remarks' }
        ],
        rowCallback: function(row, data) {
            $(row).css('cursor', 'pointer').on('dblclick', function(event) {
                if (!$(event.target).is("input[type='checkbox'], a, i")) { 
                    edit(data.id); // Trigger edit function on double-click
                }
            });
        }
    });

    $('#filterModal').modal('hide');

    $('#select-all')[0].checked = false;
}

function po_detail(id, item_id) {
    record_id = id;

    if ($.fn.DataTable.isDataTable('#generated_table_details')) {
        $('#generated_table_details').DataTable().destroy();
    }

    $('#generated_table_details').DataTable({
        responsive: false,
        serverSide: true,
        paging: false,
        ordering: false,
        searching: false, // Removes the search box
        lengthChange: false,
        scrollX: true,
        info: true,
        ajax: {
            url: '/sale_details/get/' + id,
            type: 'GET',
            dataSrc: function(json) {
                $('#g_total').text(json.total);

                return json.data;
            }
        },
        columns: [
            { data: 'item.item_name', title: 'Item Name' },
            { data: 'brand.brand', title: 'Brand' },
            { data: 'description', title: 'Description' },
            { data: 'quantity', title: 'Quantity' },
            { 
                data: 'amount', 
                title: 'Amount', 
                render: function(data) {
                    return formatCurrency(data);
                }
            },
            { 
                data: 'discount', 
                title: 'Discount', 
                render: function(data) {
                    return formatCurrency(data);
                }
            },
            { 
                data: 'total', 
                title: 'Total', 
                render: function(data) {
                    return formatCurrency(data);
                }
            }
        ], rowCallback: function(row, data) {
            if (data.id === item_id) { // Replace with the item_id you want to match
                $(row).css('background-color', 'lightyellow');
            }
        }
    });
}

function viewCompanyPofoHistory() {
    var companyId = $('#pofo_company_id').val();

    if (!companyId) {
        toastr.warning("PO/FO HISTORY", "Company record is not available.");
        return;
    }

    if ($.fn.DataTable.isDataTable('#company_pofo_table')) {
        $('#company_pofo_table').DataTable().destroy();
    }

    var pofoTable = $('#company_pofo_table').DataTable({
        responsive: true,
        serverSide: true,
        paging: true,
        ordering: false,
        ajax: {
            url: '/telemarketing_details/company-pofo/' + companyId,
            type: 'GET'
        },
        columns: [
            { data: 'DT_RowIndex', title: '#' },
            {
                data: 'po_no',
                title: 'PO/FO Number',
                render: function(data) {
                    return data ? data : '--';
                }
            },
            {
                data: 'date_purchased',
                title: 'Date Purchased',
                render: function(data) {
                    return data ? moment(data).format('MMM D, YYYY') : '--';
                }
            },
            {
                data: 'amount',
                title: 'Amount',
                render: function(data) {
                    return formatCurrency(data);
                }
            },
            {
                data: 'sales_agent_name',
                title: 'Sales Agent',
                render: function(data) {
                    return data ? data : '--';
                }
            },
            {
                data: 'sales_associate_name',
                title: 'Sales Associate',
                render: function(data) {
                    return data ? data : '--';
                }
            },
            {
                data: 'telemarketer_name',
                title: 'Assigned Telemarketer',
                render: function(data) {
                    if (!data || data === 'Super Admin') {
                        return 'UNASSIGNED';
                    }

                    return data;
                }
            }
        ]
    });

    $('#company_pofo_table tbody').off('click').on('click', 'tr', function () {
        var data = pofoTable.row(this).data();

        if (!data) {
            return;
        }

        po_detail(data.id, -1);
        $('#detail_date').text(data.date_purchased ? moment(data.date_purchased).format('MMM D, YYYY') : '--');
        $('#detail_task').text('PO/FO HISTORY');
        $('#detail_description').text('Viewing connected transaction details from PO/FO history.');
        $('#po_no').text(data.po_no ? data.po_no : '--');
        $('#total_price').text(formatCurrency(data.amount));
        $('#item_price').text('--');
        $('#detail_remarks').text('--');
        $('#assigned_date').text('--');
        $('#completedField').hide();
        setRecordDetailsMode(true);
        $('#companyPofoModal').modal('hide');
    });

    $('#companyPofoModal').modal('show');
}

function backToOriginalPofo() {
    if (!originalPofoState) {
        toastr.warning("PO/FO HISTORY", "Original record is not available.");
        return;
    }

    po_detail(originalPofoState.saleId, originalPofoState.itemId);
    $('#detail_date').text(originalPofoState.date);
    $('#detail_task').text(originalPofoState.task);
    $('#detail_description').text(originalPofoState.description);
    $('#po_no').text(originalPofoState.poNo ? originalPofoState.poNo : '--');
    $('#total_price').text(originalPofoState.totalPrice);
    $('#item_price').text(originalPofoState.itemPrice);
    $('#detail_remarks').text(originalPofoState.detailRemarks ? originalPofoState.detailRemarks : '--');
    $('#assigned_date').text(originalPofoState.assignedDate ? originalPofoState.assignedDate : '--');

    $('#status').val(originalPofoState.status);
    $('#remarks').val(originalPofoState.remarksValue);
    $('#call_duration').val(originalPofoState.callDuration);

    if (originalPofoState.status === 'COMPLETED') {
        $('#completedField').show();
    } else {
        $('#completedField').hide();
    }

    setRecordDetailsMode(false);
}

function clearFilter() {
    $('#f_assigned_to').val('');
    $('#f_company_id').val('');
    $('.f_start').val('');
    $('.f_end').val('');
    $('#p_start').val('');
    $('#p_end').val('');
    $('#f_status').val('');
    $('#f_unassigned').val('');
    $('#f_contact').val('');
    $('#f_calls').val('');

    filter = {
        _token: '',
        assigned_to: '',
        company: '',
        start: '',
        end: '',
        start: '',
        end: '',
        status: '',
        contact: '',
        calls: '',
    };

    generateRecord();
}

function formatCurrency(value) {
    if (value === null || value === undefined || isNaN(value)) return "₱0.00"; // Default to ₱0.00 if invalid

    return "₱" + parseFloat(value).toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}
