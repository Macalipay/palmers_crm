var action = 'save';
var page = 'referral';
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
                return '<a href="#" class="align-middle fas fa-fw fa-pen edit" title="Edit" data-toggle="modal" data-target="#defaultModalPrimary" id="'+row.id+'" onclick="edit('+row.id+')"></a>' + '<a href="#" data-toggle="modal" data-target="#confirmation" onclick="deleteRecord('+row.id+', 0)"><i class="align-middle fas fa-fw fa-trash"></i></a>';
            }},
            { data: 'name', title: 'Customer Name' },
            { data: 'referral.name', title: 'Referral'},
            { data: 'contact_no', title: 'Contact Details' },
            { data: 'company_name', title: 'Company Name' },
            { data: 'location', title: 'Location' },
            { data: 'location_id', title: 'Location ID' },
            { data: 'status', title: 'Status' },
            { data: 'outcome', title: 'Outcome' },
        ]
    });
});

function saveRecord() {
    var data = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        name: $('#customer_name').val(),
        placement_id: $('#placement_id').val(),
        node_address: 1,
        contact_no: $('#contact_no').val(),
        company_name: $('#company_name').val(),
        location: $('#location').val(),
        location_id: $('#location_id').val(),
        status: $('#status').val(),
        outcome: $('#outcome').val(),
        text: $('#customer_name').val(),
        image: '/images/logo/logo.png',
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
        $.get('/'+page+'_details/destroy/' + details_id, function() {
            $('#deleteModal').modal('hide');
            $('#generated_table_details').DataTable().draw();
        });
    }
}

function clear() {
    var action = "save";

    $('#company_name').val('');
    $('#contact_person').val('');
    $('#contact_no').val('');
    $('#address').val('');
    $('#province_id').val('');
    $('#industry').val('');
    $('#company_name').val('');
    $('#tin').val('');
    $('#business_style').val('');
}
