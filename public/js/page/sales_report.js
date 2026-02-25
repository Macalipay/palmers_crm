var salesReportTable = null;
var phpCurrency = new Intl.NumberFormat('en-PH', {
    style: 'currency',
    currency: 'PHP',
    minimumFractionDigits: 2
});

function collectFilters() {
    var data = {
        _token: $('meta[name="csrf-token"]').attr('content')
    };

    $('#sales-report-filter')
        .serializeArray()
        .forEach(function (item) {
            data[item.name] = item.value;
        });

    return data;
}

function initializeSearchableDropdowns() {
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

function buildExportSummary() {
    var totalTransactions = $('#summary-total-transactions').text() || '0';
    var totalSales = $('#summary-total-sales').text() || phpCurrency.format(0);
    return 'Total Transactions: ' + totalTransactions + '\nTotal Sales Amount: ' + totalSales;
}

function getSummaryValues() {
    return {
        totalTransactions: $('#summary-total-transactions').text() || '0',
        totalSales: $('#summary-total-sales').text() || phpCurrency.format(0)
    };
}

function appendTotalsToExcel(xlsx) {
    var summary = getSummaryValues();
    var sheet = xlsx.xl.worksheets['sheet1.xml'];
    var $sheetData = $('sheetData', sheet);
    var $rows = $('row', sheet);
    var lastRow = $rows.length ? parseInt($rows.last().attr('r'), 10) : 1;
    var row1 = lastRow + 2;
    var row2 = row1 + 1;
    var totalsRows =
        '<row r="' + row1 + '">' +
            '<c t="inlineStr" r="A' + row1 + '"><is><t>Total Transactions</t></is></c>' +
            '<c t="inlineStr" r="B' + row1 + '"><is><t>' + summary.totalTransactions + '</t></is></c>' +
        '</row>' +
        '<row r="' + row2 + '">' +
            '<c t="inlineStr" r="A' + row2 + '"><is><t>Total Sales Amount</t></is></c>' +
            '<c t="inlineStr" r="B' + row2 + '"><is><t>' + summary.totalSales + '</t></is></c>' +
        '</row>';

    $sheetData.append(totalsRows);

    var $dimension = $('dimension', sheet);
    var ref = $dimension.attr('ref');
    if (ref && ref.indexOf(':') > -1) {
        var parts = ref.split(':');
        var endCell = parts[1];
        var colMatch = endCell.match(/[A-Z]+/);
        var endCol = colMatch ? colMatch[0] : 'A';
        $dimension.attr('ref', parts[0] + ':' + endCol + row2);
    }
}

function exportAllRows(e, dt, button, config) {
    var self = this;
    var oldStart = dt.settings()[0]._iDisplayStart;

    dt.one('preXhr', function (event, s, data) {
        data.start = 0;
        data.length = 2147483647;
        data.export_all = 1;

        dt.one('preDraw', function (event, settings) {
            var exportType = (config.extend || '').toLowerCase();
            var buttonEl = button && button[0] ? button[0] : null;
            var buttonClassName = buttonEl ? buttonEl.className : '';

            if (exportType.indexOf('excel') >= 0 || buttonClassName.indexOf('buttons-excel') >= 0) {
                $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
            } else if (exportType.indexOf('csv') >= 0 || buttonClassName.indexOf('buttons-csv') >= 0) {
                $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button, config);
            } else if (exportType.indexOf('pdf') >= 0 || buttonClassName.indexOf('buttons-pdf') >= 0) {
                $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config);
            } else if (exportType.indexOf('print') >= 0 || buttonClassName.indexOf('buttons-print') >= 0) {
                $.fn.dataTable.ext.buttons.print.action.call(self, e, dt, button, config);
            }

            dt.one('preXhr', function (event, s, data) {
                settings._iDisplayStart = oldStart;
                data.start = oldStart;
                data.length = settings._iDisplayLength;
                data.export_all = 0;
            });

            setTimeout(function () {
                dt.ajax.reload(null, false);
            }, 0);
            return false;
        });
    });

    dt.ajax.reload();
}

$(function () {
    initializeSearchableDropdowns();
    loadSummary();

    salesReportTable = $('#sales-report-table').DataTable({
        responsive: false,
        serverSide: true,
        processing: true,
        paging: true,
        scrollX: true,
        ordering: false,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Excel',
                className: 'btn btn-success btn-sm',
                action: exportAllRows,
                customize: function (xlsx) {
                    appendTotalsToExcel(xlsx);
                }
            },
            {
                extend: 'csvHtml5',
                text: 'CSV',
                className: 'btn btn-info btn-sm',
                action: exportAllRows,
                customize: function (csv) {
                    var summary = getSummaryValues();
                    return csv + '\n\nTotal Transactions,' + summary.totalTransactions +
                        '\nTotal Sales Amount,' + summary.totalSales;
                }
            },
            {
                extend: 'pdfHtml5',
                text: 'PDF',
                className: 'btn btn-danger btn-sm',
                orientation: 'landscape',
                pageSize: 'A4',
                action: exportAllRows,
                messageBottom: function () { return buildExportSummary(); }
            },
            {
                extend: 'print',
                text: 'Print Table',
                className: 'btn btn-secondary btn-sm',
                action: exportAllRows,
                messageBottom: function () { return buildExportSummary(); }
            }
        ],
        ajax: {
            url: '/reports/sales/data',
            type: 'POST',
            data: function (d) {
                return $.extend({}, d, collectFilters());
            }
        },
        columns: [
            { data: 'DT_RowIndex', title: '#' },
            { data: 'company.company_name', title: 'Company' },
            { data: 'store.store_name', title: 'Store', render: function (d) { return d || '--'; } },
            { data: 'company.industry', title: 'Industry', render: function (d) { return d || '--'; } },
            { data: 'source.source', title: 'Source', render: function (d) { return d || '--'; } },
            { data: 'payment_term', title: 'Payment Term', render: function (d) { return d || '--'; } },
            { data: 'po_no', title: 'PO/OF No', render: function (d) { return d || '--'; } },
            { data: 'date_purchased', title: 'Date Purchased', render: function (d) { return d ? moment(d).format('MMM DD, YYYY') : '--'; } },
            { data: 'amount', title: 'Amount', render: function (d) { return d !== null ? Number(d).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '0.00'; } },
            { data: 'user.name', title: 'Sales Agent', render: function (d) { return d || '--'; } },
            { data: 'sales_associate.sales_associate', title: 'Sales Associate', render: function (d) { return d || '--'; } },
            { data: 'merchandiser.merchandiser', title: 'Merchandiser', render: function (d) { return d || '--'; } },
            { data: 'division.division', title: 'Division', render: function (d) { return d || '--'; } },
            { data: 'branch.branch_name', title: 'Branch', render: function (d) { return d || '--'; } },
            { data: 'agreed_delivery_date', title: 'Agreed Delivery', render: function (d) { return d ? moment(d).format('MMM DD, YYYY') : '--'; } },
            { data: 'actual_delivery_date', title: 'Actual Delivery', render: function (d) { return d ? moment(d).format('MMM DD, YYYY') : '--'; } },
            { data: 'date_posted', title: 'Date Posted', render: function (d) { return d ? moment(d).format('MMM DD, YYYY') : '--'; } },
            { data: 'date_encode', title: 'Date Encode', render: function (d) { return d ? moment(d).format('MMM DD, YYYY') : '--'; } },
            { data: 'date_received', title: 'Date Received', render: function (d) { return d ? moment(d).format('MMM DD, YYYY') : '--'; } },
            { data: 'date_filed', title: 'Date Filed', render: function (d) { return d ? moment(d).format('MMM DD, YYYY') : '--'; } },
            { data: 'deadline', title: 'Deadline', render: function (d) { return d ? moment(d).format('MMM DD, YYYY') : '--'; } },
            { data: 'project_title', title: 'Project Title', render: function (d) { return d || '--'; } },
            { data: 'contact_person', title: 'Contact Person', render: function (d) { return d || '--'; } },
            { data: 'telephone_no', title: 'Telephone', render: function (d) { return d || '--'; } },
            { data: 'email', title: 'Email', render: function (d) { return d || '--'; } },
            { data: 'active', title: 'Status', render: function (d) { return String(d) === '1' ? 'ACTIVE' : 'INACTIVE'; } }
        ]
    });

    $('#apply-filters').on('click', function () {
        loadSummary();
        salesReportTable.ajax.reload();
    });

    $('#reset-filters').on('click', function () {
        $('#sales-report-filter')[0].reset();
        $('.searchable-select').val('').trigger('change');
        loadSummary();
        salesReportTable.ajax.reload();
    });
});

function loadSummary() {
    $.post('/reports/sales/summary', collectFilters()).done(function (response) {
        $('#summary-total-transactions').text((response.total_transactions || 0).toLocaleString());
        $('#summary-total-sales').text(phpCurrency.format(response.total_sales_amount || 0));
    });
}
