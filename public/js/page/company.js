var action = 'save';
var page = 'company';
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
        ordering: false, // Disable ordering if not required
        ajax: {
            url: '/' + page + '/get',
            type: 'GET',
        },
        columns: [
            { 
                data: null, 
                title: 'Action', 
                orderable: false, // Prevent sorting on this column
                searchable: false, // Prevent searching on this column
                render: function(data, type, row, meta) {
                    return '<a href="#" class="align-middle fas fa-fw fa-pen edit" title="Edit" data-toggle="modal" data-target="#defaultModalPrimary" onclick="edit(' + row.id + ')"></a>' +
                           '<a href="#" class="align-middle fas fa-fw fa-trash" title="Delete" data-toggle="modal" data-target="#confirmation" onclick="deleteRecord(' + row.id + ', 0)"></a>';
                }
            },
            { data: 'company_name', title: 'Company Name' },
            { data: 'contact_person', title: 'Contact Person' },
            { data: 'contact_no', title: 'Contact No' },
            { data: 'address', title: 'Address' },
            { data: 'province.province', title: 'Province' }, // Adjust this depending on your relationship naming
            { data: 'industry', title: 'Industry' },
            { data: 'tin', title: 'TIN No.' },
            { data: 'business_style', title: 'Bus. Style' },
            { 
                data: 'active', 
                title: 'Status', 
                render: function(data, type, row, meta) {
                    return row.active == 1 
                        ? '<span class="badge badge-success">ACTIVE</span>' 
                        : '<span class="badge badge-secondary">INACTIVE</span>';
                }
            },
        ]
    });
});

function saveRecord() {
    var data = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        company_name: $('#company_name').val(),
        contact_person: $('#contact_person').val(),
        contact_no: $('#contact_no').val(),
        address: $('#address').val(),
        province_id: $('#province_id').val(),
        industry: $('#industry').val(),
        company_name: $('#company_name').val(),
        tin: $('#tin').val(),
        business_style: $('#business_style').val(),
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

function filterRecord() {
    $('#filterModal').modal('show');
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
