var action = 'save';
var page = 'user';
var table = null;
var record_id = null;

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
                        '<a href="#" data-toggle="modal" data-target="#confirmation" onclick="deleteRecord('+row.id+')"><i class="align-middle fas fa-fw fa-trash"></i></a>'+
                        '<a href="#" data-toggle="modal" data-target="#confirmation" onclick="resetPassword('+row.id+')"><i class="align-middle fas fa-fw fa-key"></i></a>';
            }},
            { data: 'name', title: 'Name' },
            { data: 'division.division', title: 'Division', render: function(data, type, row, meta) {
                return row.division !== null?row.division.division:'-';
            }},
            { data: 'branch.branch_name', title: 'Branch', render: function(data, type, row, meta) {
                return row.branch !== null?row.branch.branch_name:'-';
            }},
            { data: 'designation', title: 'Designation' },
            { data: 'email', title: 'Email' },
            { data: 'contact_number', title: 'Contact Number' },
            { data: 'active', title: 'Status', render: function (data, type, row) {
                    const statuses = { 1: 'ACTIVE', 2: 'INACTIVE', 3: 'RESIGNED' };
                    const badgeClasses = { 
                        1: 'badge-success',  // Green for ACTIVE
                        2: 'badge-secondary', // Gray for INACTIVE
                        3: 'badge-danger'    // Red for RESIGNED
                    };
                    const statusText = statuses[row.active] || 'UNKNOWN';
                    const badgeClass = badgeClasses[row.active] || 'badge-dark'; // Default dark badge for unknown
                    return `<span class="badge ${badgeClass}">${statusText}</span>`;
                }
            }
        ]
    });
});

function saveRecord() {
    var data = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        name: $('#name').val(),
        designation: $('#designation').val(),
        email: $('#email').val(),
        contact_number: $('#contact_number').val(),
        division_id: $('#division_id').val(),
        active: $('#active').val(),
        branch_id: $('#branch_id').val(),
        role_id: $('#role_id').val(),
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

            if(resp.message == 'RESIGN') {
                toastr.success("TELEMARKETING TASKS REASSIGNED", "All records have been set to Unassigned.")
            }
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

function resetPassword(userId) {
    if (confirm("Are you sure you want to reset the password for this user?")) {
        $.ajax({
            url: '/'+page+'/reset-password/' + userId,
            type: 'POST',            
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')             },
            success: function(response) {
                alert("Success: " + response);
            },
            error: function(xhr, status, error) {
                alert("Error: " + xhr.responseText);
            }
        });
    } else {
        // If the user cancels, do nothing
        alert("Password reset canceled.");
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

            $('#role_id').val(data.data.role.role_id);
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
    record_id = null;

    $('#name').val('');
    $('#designation').val('');
    $('#email').val('');
    $('#contact_number').val('');
    $('#rfid_code').val('');
    $('#candidate_id').val('');
    $('#role_id').val('');
}


function addRecord() {
    clear();
    $('#recordModal').modal('show');
}