var action = 'save';
var page = 'role';
var table = null;
var record_id = null;

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
            { data: 'name', title: 'Name' },
            { data: null, title: 'Action', render: function(data, type, row, meta) {
                return '<a href="#" class="align-middle fas fa-fw fa-pen edit" title="Edit" data-toggle="modal" data-target="#defaultModalPrimary" id="'+row.id+'" onclick="edit('+row.id+')"></a>' + '<a href="#" data-toggle="modal" data-target="#confirmation" onclick="deleteRecord('+row.id+')"><i class="align-middle fas fa-fw fa-trash"></i></a>';
            }},
        ]
    });
});

function saveRecord() {
    var data = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        name: $('#name').val()
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

function deleteRecord(id) {
    record_id = id;
    $('#deleteModal').modal('show');
}

function yesDelete() {
    $.get('/'+page+'/destroy/' + record_id, function() {
        $('#deleteModal').modal('hide');
        $('#generated_table').DataTable().draw();
    });
}

function clear() {
    var action = "save";

    $('#name').val('');
}
