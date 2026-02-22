var telemarketingReportTable = null;
var tmCurrency = new Intl.NumberFormat('en-PH', {
    style: 'currency',
    currency: 'PHP',
    minimumFractionDigits: 2
});

function collectTelemarketingFilters() {
    var data = {
        _token: $('meta[name="csrf-token"]').attr('content')
    };

    $('#telemarketing-report-filter')
        .serializeArray()
        .forEach(function (item) {
            data[item.name] = item.value;
        });

    return data;
}

function initializeTmSearchableDropdowns() {
    if (!$.fn.select2) {
        return;
    }

    $('.searchable-select').each(function () {
        var $select = $(this);
        if ($select.data('select2')) {
            $select.select2('destroy');
        }

        $select.select2({
            width: '100%',
            allowClear: true,
            placeholder: 'All'
        });
    });
}

$(function () {
    initializeTmSearchableDropdowns();
    loadTelemarketingSummary();

    telemarketingReportTable = $('#telemarketing-report-table').DataTable({
        responsive: false,
        serverSide: true,
        processing: true,
        paging: true,
        scrollX: true,
        ordering: false,
        dom: 'Bfrtip',
        buttons: [
            { extend: 'excelHtml5', text: 'Excel', className: 'btn btn-success btn-sm' },
            { extend: 'csvHtml5', text: 'CSV', className: 'btn btn-info btn-sm' },
            { extend: 'pdfHtml5', text: 'PDF', className: 'btn btn-danger btn-sm', orientation: 'landscape', pageSize: 'A4' },
            { extend: 'print', text: 'Print Table', className: 'btn btn-secondary btn-sm' }
        ],
        ajax: {
            url: '/reports/telemarketing/data',
            type: 'POST',
            data: function (d) {
                return $.extend({}, d, collectTelemarketingFilters());
            }
        },
        columns: [
            { data: 'DT_RowIndex', title: '#' },
            { data: 'telemarketing.company.company_name', title: 'Company', render: function (d) { return d || '--'; } },
            { data: 'csd.sale.po_no', title: 'PO/FO No', render: function (d) { return d || '--'; } },
            { data: 'date', title: 'Follow Up Date', render: function (d) { return d ? moment(d).format('MMM DD, YYYY') : '--'; } },
            { data: 'status', title: 'Status', render: function (d) { return d || '--'; } },
            { data: 'user.name', title: 'Assigned To', render: function (d) { return d || 'UNASSIGNED'; } },
            { data: 'telemarketing.company.contact_person', title: 'Contact Person', render: function (d) { return d || '--'; } },
            { data: 'telemarketing.company.contact_no', title: 'Contact No', render: function (d) { return d || '--'; } },
            { data: 'csd.sale.date_purchased', title: 'Date Purchased', render: function (d) { return d ? moment(d).format('MMM DD, YYYY') : '--'; } },
            { data: 'total_amount', title: 'Total Amount', render: function (d) { return d !== null ? Number(d).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '0.00'; } },
            { data: 'remarks', title: 'Remarks', render: function (d) { return d || '--'; } }
        ]
    });

    $('#tm-apply-filters').on('click', function () {
        loadTelemarketingSummary();
        telemarketingReportTable.ajax.reload();
    });

    $('#tm-reset-filters').on('click', function () {
        $('#telemarketing-report-filter')[0].reset();
        $('.searchable-select').val('').trigger('change');
        loadTelemarketingSummary();
        telemarketingReportTable.ajax.reload();
    });
});

function loadTelemarketingSummary() {
    $.post('/reports/telemarketing/summary', collectTelemarketingFilters()).done(function (response) {
        $('#tm-summary-total-transactions').text((response.total_transactions || 0).toLocaleString());
        $('#tm-summary-total-sales').text(tmCurrency.format(response.total_sales_amount || 0));
    });
}

