var action = 'save';
var page = 'prospect';
var table = null;
var record_id = null;

var details_id = null;

var delete_module = null;

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
                       '<a href="#" onclick="viewDetails('+row.id+')"><i class="align-middle fas fa-fw fa-list"></i></a>';
            }},

            { data: 'company.company_name', title: 'Company Name' },
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
            { data: 'lead_status', title: 'Lead Status', render:function(data, type, row, meta){
                var html;
                if (row.lead_status == 'PROSPECT') {
                    html = '<span class="badge badge-primary">PROSPECT</span>';
                } else if(row.lead_status == 'ENGAGE') {
                    html = '<span class="badge badge-secondary">ENGAGE</span>';
                } else if(row.lead_status == 'ACQUIRE') {
                    html = '<span class="badge badge-success">ACQUIRE</span>';
                } else {
                    html = '<span class="badge badge-warning">RETENTION</span>';
                }

                return html;
            }},
            { data: 'opportunity_status', title: 'Opportunity Status', render:function(data, type, row, meta){
                var html;

                if (row.opportunity_status == 'OPEN') {
                    html = '<span class="badge badge-primary">OPEN</span>';
                } else if(row.opportunity_status == 'CLOSED') {
                    html = '<span class="badge badge-warning">CLOSED</span>';
                } else if(row.opportunity_status == 'DEAL') {
                    html = '<span class="badge badge-success">DEAL</span>';
                } else {
                    html = '<span class="badge badge-danger">LOST</span>';
                }

                return html;
            }},
            { data: 'source.source', title: 'Source' },
            { data: 'product_interest', title: 'Product Interest' },
        ]
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
            $.each(data, function() {
                $.each(this, function(k, v) {
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
        $.get('/telemarketing_details/destroy/' + details_id, function() {
            $('#deleteModal').modal('hide');
            $('#generated_table_details').DataTable().draw();
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

function edit_details(id){
    action = "updated";
    details_id = id;

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/telemarketing_details/edit/' + details_id,
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
            url: '/telemarketing_details/get/' + id,
            type: 'GET'
        },
        columns: [
            { data: null, title: 'Action', render: function(data, type, row, meta) {
                return '<a href="#" class="align-middle fas fa-fw fa-pen edit" title="Edit" data-toggle="modal" data-target="#defaultModalPrimary" id="'+row.id+'" onclick="edit_details('+row.id+')"></a>' + '<a href="#" data-toggle="modal" data-target="#confirmation" onclick="deleteRecord('+row.id+', 1)"><i class="align-middle fas fa-fw fa-trash"></i></a>';
            }},
            { data: 'date', title: 'Date', render: function(data, type, row, meta) {
                return moment(row.date).format('MMM-DD-YYYY');
            }},
            { data: 'task', title: 'Task' },
            { data: 'description', title: 'Description' },
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
    var data = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        telemarketing_id: record_id,
        lead_status: 'PROSPECT',
        date: $('#date').val(),
        task: $('#task').val(),
        description: $('#description').val(),
        assigned_to: $('#assigned_to').val(),
        status: $('#status').val(),
        remarks: $('#remarks').val(),
    }

    if(action === "save") {
        $.post('/telemarketing_details/save', data).done(function(resp) {
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
        $.post('/telemarketing_details/update/'+details_id, data).done(function(resp) {
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
