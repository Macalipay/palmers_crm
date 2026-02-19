var action = 'save';
var page = 'division';
var table = null;
var record_id = null;

var details_id = null;

var delete_module = null;

$(function() {
    // Attach event listener to input fields
    $('input, textarea').not('[type="email"]').on('input', function() {
        // Convert the input value to uppercase
        $(this).val($(this).val().toUpperCase());
    });
});

$(function() {
    $('#generated_table').DataTable({
        responsive: true,
        serverSide: true,
        paging: true,
        ordering: false,
        ajax: {
            url: '/'+page+'/get',
            type: 'GET'
        },
        columns: [
            { data: null, title: 'Action', render: function(data, type, row, meta) {
                return '<a href="#" class="align-middle fas fa-fw fa-pen edit" title="Edit" data-toggle="modal" data-target="#defaultModalPrimary" id="'+row.id+'" onclick="edit('+row.id+')"></a>' + '<a href="#" data-toggle="modal" data-target="#confirmation" onclick="deleteRecord('+row.id+', 0)"><i class="align-middle fas fa-fw fa-trash"></i></a>' + '<a href="#" onclick="viewDetails('+row.id+')"><i class="align-middle fas fa-fw fa-th"></i></a>';
            }},
            { data: 'division', title: 'Division' },
            { data: 'active', title: 'Status', render: function(data, type, row, meta) {
                if (row.active == 1) {
                    html = '<span class="badge badge-success">ACTIVE</span>';
                } else {
                    html = '<span class="badge badge-secondary">INACTIVE</span>';
                }

                return html;
            }},
        ]
    });
});

function saveRecord() {
    var data = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        division: $('#division').val(),
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

function addRecord() {
    action = "save";
    record_id = null;
    $('#recordModal').modal('show');
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
        $.get('/branch/destroy/' + details_id, function() {
            $('#deleteModal').modal('hide');
            $('#generated_table_details').DataTable().draw();
        });
    }
}

function clear() {
    var action = "save";

    $('#division').val('');
}


function viewDetails(id) {
    details_id = id;

    $.get('/division/edit/'+id, function(response){
        $('#division_title').text(response.data.division);
        
        if ($.fn.DataTable.isDataTable('#generated_table_details')) {
            $('#generated_table_details').DataTable().destroy();
        }

        $('#generated_table_details').DataTable({
            responsive: true,
            serverSide: true,
            paging: true,
            ordering: false,
            ajax: {
                url: '/branch/get/'+id,
                type: 'GET'
            },
            columns: [
                { data: null, title: 'Action', render: function(data, type, row, meta) {
                    return '<a href="#" class="align-middle fas fa-fw fa-pen edit" title="Edit" data-toggle="modal" data-target="#defaultModalPrimary" id="'+row.id+'" onclick="editDetails('+row.id+')"></a>' + '<a href="#" data-toggle="modal" data-target="#confirmation" onclick="deleteRecord('+row.id+', 1)"><i class="align-middle fas fa-fw fa-trash"></i></a>';
                }},
                { data: 'branch_name', title: 'Branch Name' },
                { data: 'active', title: 'Status', render: function(data, type, row, meta) {
                    if (row.active == 1) {
                        html = '<span class="badge badge-success">ACTIVE</span>';
                    } else {
                        html = '<span class="badge badge-secondary">INACTIVE</span>';
                    }

                    return html;
                }},
            ]
        });
    });
    $('#detailsModal').modal('show');
}

function addRecordDetails() {
    action = "save";
    record_id = null;
    
    $('#branch_name').val('');
    $('#recordDetailsModal').modal('show');
}


function saveDetailsRecord() {
    var data = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        branch_name: $('#branch_name').val(),
        active: $('#active').val(),
        division_id: details_id,
    }

    if(action === "save") {
        $.post('/branch/save', data).done(function(resp) {
            $('#generated_table_details').DataTable().draw();
            $('#recordDetailsModal').modal('hide');
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
        $.post('/branch/update/'+record_id, data).done(function(resp) {
            $('#generated_table_details').DataTable().draw();
            $('#recordDetailsModal').modal('hide');
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

function editDetails(id){
    action = "updated";
    record_id = id;

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/branch/edit/' + id,
        method: 'get',
        data: {},
        success: function(data) {
            $('#recordDetailsModal').modal('show');
            $.each(data, function() {
                $.each(this, function(k, v) {
                    $('#'+k).val(v);
                });
            });
        }
    });

}