var table = null;

$(function(){
    var table = $('#generated_dashboard_table').DataTable({
        responsive: false,
        serverSide: true,
        paging: true,
        scrollX: true,
        ordering: false,
        ajax: {
            url: '/telemarketing/getDashboard',
            type: 'GET'
        },
        columns: [
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
            { data: 'telemarketing.company.address', title: 'Address', render: function(data, type, row) {
                if (row.telemarketing.company.address != null) {
                    return '<span title="' + data + '">' + (type === 'display' && data.length > 30 ? data.substr(0, 30) + '...' : data) + '</span>';
                } else {
                    return '--';
                }
            }},
            { data: 'date', title: 'Date' },
            { data: 'task', title: 'Task' },
            { data: 'description', title: 'Description' },
            { data: 'remarks', title: 'Remarks' }
        ]
    });
})

function filterResults() {

    if ($.fn.DataTable.isDataTable('#generated_dashboard_table')) {
        $('#generated_dashboard_table').DataTable().destroy();
    }

    $('#generated_dashboard_table').DataTable({
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
            url: '/telemarketing/getDashboard_range/'+ $('#user_id').val() + '/' + $('#start_date').val() + '/' + $('#end_date').val(),
            type: 'GET'
        },
        columns: [
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
            { data: 'telemarketing.company.address', title: 'Address', render: function(data, type, row) {
                if (row.telemarketing.company.address != null) {
                    return '<span title="' + data + '">' + (type === 'display' && data.length > 30 ? data.substr(0, 30) + '...' : data) + '</span>';
                } else {
                    return '--';
                }
            }},
            { data: 'date', title: 'Date' },
            { data: 'task', title: 'Task' },
            { data: 'description', title: 'Description' },
            { data: 'remarks', title: 'Remarks' }
        ]
    });

    $.get('/telemarketing/status_range/'+ $('#user_id').val() + '/' + $('#start_date').val() + '/' + $('#end_date').val() , function(response) {
        $('#total_amount').text(response.formatted_total_amount);
        $('#total_active_call').text(response.total_active_call);
        $('#total_backlogs').text(response.total_backlogs);
        $('#user_todo_call').text(response.user_todo_call);
        $('#user_cancelled_call').text(response.user_cancelled_call);
        $('#user_inprogress_call').text(response.user_inprogress_call);
        $('#user_pending_call').text(response.user_pending_call);
        $('#user_onhold_call').text(response.user_onhold_call);
        $('#user_completed_call').text(response.user_completed_call);
    });
}