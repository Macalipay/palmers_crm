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

function buildTmExportUrl(format) {
    var params = $('#telemarketing-report-filter').serializeArray()
        .filter(function (item) {
            return item.value !== null && item.value !== '';
        });

    params.push({ name: 'format', value: format });
    return '/reports/telemarketing/export?' + $.param(params);
}

function fetchTmExportData(done) {
    $.get(buildTmExportUrl('json'))
        .done(function (response) {
            done(response || { headings: [], rows: [] });
        })
        .fail(function (xhr) {
            var message = (xhr.responseJSON && xhr.responseJSON.message)
                ? xhr.responseJSON.message
                : 'Failed to load all records for export.';
            alert(message);
        });
}

function copyTmTextToClipboard(text, onSuccess) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(onSuccess);
        return;
    }

    var textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.left = '-9999px';
    document.body.appendChild(textarea);
    textarea.focus();
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
    onSuccess();
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
            {
                text: 'Excel',
                className: 'btn btn-success btn-sm',
                action: function () {
                    window.location.href = buildTmExportUrl('xlsx');
                }
            },
            {
                text: 'CSV',
                className: 'btn btn-info btn-sm',
                action: function () {
                    window.location.href = buildTmExportUrl('csv');
                }
            },
            {
                text: 'PDF',
                className: 'btn btn-danger btn-sm',
                action: function () {
                    fetchTmExportData(function (data) {
                        var body = [];
                        body.push(data.headings || []);
                        (data.rows || []).forEach(function (row) {
                            body.push(row);
                        });

                        var docDefinition = {
                            pageOrientation: 'landscape',
                            pageSize: 'A4',
                            content: [
                                { text: 'Telemarketing Report', style: 'title' },
                                {
                                    table: {
                                        headerRows: 1,
                                        body: body
                                    },
                                    layout: 'lightHorizontalLines'
                                }
                            ],
                            styles: {
                                title: { fontSize: 14, bold: true, margin: [0, 0, 0, 8] }
                            },
                            defaultStyle: {
                                fontSize: 8
                            }
                        };

                        pdfMake.createPdf(docDefinition).download('telemarketing_report_' + moment().format('YYYYMMDD_HHmmss') + '.pdf');
                    });
                }
            },
            {
                text: 'Copy',
                className: 'btn btn-primary btn-sm',
                action: function () {
                    fetchTmExportData(function (data) {
                        var lines = [];
                        lines.push((data.headings || []).join('\t'));
                        (data.rows || []).forEach(function (row) {
                            lines.push(row.join('\t'));
                        });

                        copyTmTextToClipboard(lines.join('\n'), function () {
                            if (window.toastr) {
                                toastr.success('Copied', 'All filtered records copied.');
                            }
                        });
                    });
                }
            },
            {
                text: 'Print Table',
                className: 'btn btn-secondary btn-sm',
                action: function () {
                    fetchTmExportData(function (data) {
                        var headings = data.headings || [];
                        var rows = data.rows || [];
                        var tableHtml = '<table border="1" cellspacing="0" cellpadding="4" style="border-collapse:collapse;width:100%;font-size:11px;"><thead><tr>';

                        headings.forEach(function (heading) {
                            tableHtml += '<th>' + heading + '</th>';
                        });
                        tableHtml += '</tr></thead><tbody>';

                        rows.forEach(function (row) {
                            tableHtml += '<tr>';
                            row.forEach(function (cell) {
                                tableHtml += '<td>' + (cell === null ? '' : cell) + '</td>';
                            });
                            tableHtml += '</tr>';
                        });

                        tableHtml += '</tbody></table>';

                        var printWindow = window.open('', '_blank');
                        if (!printWindow) {
                            return;
                        }
                        printWindow.document.write('<html><head><title>Telemarketing Report</title></head><body>');
                        printWindow.document.write('<h3>Telemarketing Report</h3>');
                        printWindow.document.write(tableHtml);
                        printWindow.document.write('</body></html>');
                        printWindow.document.close();
                        printWindow.focus();
                        printWindow.print();
                    });
                }
            }
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

