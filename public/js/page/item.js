var action = 'save';
var page = 'item';
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
                return '<a href="#" class="align-middle fas fa-fw fa-pen edit" title="Edit" data-toggle="modal" data-target="#defaultModalPrimary" id="'+row.id+'" onclick="edit('+row.id+')"></a>' +
                '<a href="#" data-toggle="modal" data-target="#confirmation" onclick="deleteRecord('+row.id+', 0)"><i class="align-middle fas fa-fw fa-trash"></i></a>'+
                '<a href="#" onclick="edit_duration('+row.id+')"><i class="align-middle fas fa-fw fa-clock"></i></a>';
            }},
            { data: 'item_name', title: 'Item Name' },
            { data: 'division.division', title: 'Division', render: function(data, type, row, meta) {
                if (row.division != null) {
                    html = row.division.division;
                } else {
                    html = '-';
                }

                return html;
            }},
            { data: 'description', title: 'Description'},
            { data: 'amount', title: 'Amount' },
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
        item_name: $('#item_name').val(),
        division_id: $('#division_id').val(),
        description: $('#description').val(),
        amount: $('#amount').val(),
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

function edit_duration(id){
    details_id = id;
    action = 'update';

    duraction_clear();

    $('#durationModal').modal('show');

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/'+page+'_duration/edit/' + details_id,
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


function saveDuration(){
    var data = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        item_id: details_id,
        brandnew: $('#brandnew').val(),
        refill: $('#refill').val(),
        for_warranty: $('#for_warranty').val(),
    }

    if(action === "save") {
        $.post('/'+page+'_duration/save', data).done(function(resp) {
            $('#durationModal').modal('hide');
            duraction_clear();
        }).fail(function(resp) {
            var r = resp.responseJSON.errors;

            $('.form-control').removeClass('required');
            $.each(r, function(i,v) {
                $('#' + i).addClass('required');
            });
        });
    }
    else {
        $.post('/'+page+'_duration/update/'+details_id, data).done(function(resp) {
            $('#durationModal').modal('hide');
            duraction_clear();
        }).fail(function(resp) {
            var r = resp.responseJSON.errors;

            $('.form-control').removeClass('required');
            $.each(r, function(i,v) {
                $('#' + i).addClass('required');
            });
        });
    }
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

    $('#item_name').val('');
    $('#description').val('');
    $('#amount').val('');
}

function duraction_clear() {

    $('#brandnew').val('');
    $('#refill').val('');
    $('#for_warranty').val('');
}
