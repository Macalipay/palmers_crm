@extends('backend.master.template')

@section('page-title')
<span class="page-title">Telemarketing Item Reports</span>
@endsection

@section('content')
    <main class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Submitted Telemarketing Item Reports</h5>
                </div>
                <div class="card-body">
                    <table id="telemarketing-item-report-table" class="table table-striped" style="width:100%"></table>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="resolveReportModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Resolve Report</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="resolve_report_id">
                    <div class="form-group">
                        <label for="resolution_remarks">Resolution Notes</label>
                        <textarea id="resolution_remarks" class="form-control" rows="4" placeholder="Optional notes about how this issue was resolved."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmResolveBtn" class="btn btn-success">Mark Resolved</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="/js/datatables.min.js"></script>
<script>
$(function () {
    var reportTable = $('#telemarketing-item-report-table').DataTable({
        responsive: true,
        serverSide: true,
        processing: true,
        ordering: false,
        ajax: {
            url: '/telemarketing_item_reports/get',
            type: 'GET'
        },
        columns: [
            {
                data: null,
                title: 'Action',
                searchable: false,
                orderable: false,
                render: function(data, type, row) {
                    if (row.status === 'RESOLVED') {
                        return '<span class="text-success font-weight-bold">Resolved</span>';
                    }

                    return '<button type="button" class="btn btn-sm btn-success" onclick="openResolveReportModal(' + row.id + ')">Resolve</button>';
                }
            },
            { data: 'id', title: 'Report ID' },
            { data: 'telemarketing_detail_id', title: 'Detail ID' },
            {
                data: 'telemarketing_detail.telemarketing.company.company_name',
                title: 'Company',
                defaultContent: '--'
            },
            {
                data: 'telemarketing_detail.csd.sale.po_no',
                title: 'PO/FO No.',
                defaultContent: '--',
                render: function(data) {
                    return data ? data : '--';
                }
            },
            {
                data: 'telemarketing_detail.status',
                title: 'Telemarketing Status',
                defaultContent: '--'
            },
            {
                data: 'reporter.name',
                title: 'Reported By',
                defaultContent: '--'
            },
            {
                data: 'remarks',
                title: 'Report Remarks',
                render: function(data, type) {
                    if (!data) {
                        return '--';
                    }

                    if (type === 'display' && data.length > 80) {
                        return '<span title="' + $('<div>').text(data).html() + '">' + $('<div>').text(data.substr(0, 80) + '...').html() + '</span>';
                    }

                    return $('<div>').text(data).html();
                }
            },
            {
                data: 'status',
                title: 'Report Status',
                render: function(data) {
                    if (data === 'RESOLVED') {
                        return '<span class="badge badge-success">RESOLVED</span>';
                    }

                    return '<span class="badge badge-danger">OPEN</span>';
                }
            },
            {
                data: 'created_at',
                title: 'Date Reported',
                render: function(data) {
                    return data ? moment(data).format('MMM D, YYYY hh:mm A') : '--';
                }
            },
            {
                data: 'resolver.name',
                title: 'Resolved By',
                defaultContent: '--'
            },
            {
                data: 'resolved_at',
                title: 'Date Resolved',
                render: function(data) {
                    return data ? moment(data).format('MMM D, YYYY hh:mm A') : '--';
                }
            },
            {
                data: 'resolution_remarks',
                title: 'Resolution Notes',
                render: function(data, type) {
                    if (!data) {
                        return '--';
                    }

                    if (type === 'display' && data.length > 80) {
                        return '<span title="' + $('<div>').text(data).html() + '">' + $('<div>').text(data.substr(0, 80) + '...').html() + '</span>';
                    }

                    return $('<div>').text(data).html();
                }
            }
        ]
    });

    window.openResolveReportModal = function(id) {
        $('#resolve_report_id').val(id);
        $('#resolution_remarks').val('');
        $('#resolveReportModal').modal('show');
    };

    $('#confirmResolveBtn').on('click', function() {
        var reportId = $('#resolve_report_id').val();
        var remarks = $('#resolution_remarks').val().trim();
        var $button = $(this);

        if (!reportId) {
            return;
        }

        $button.prop('disabled', true);

        $.post('/telemarketing_item_reports/resolve/' + reportId, {
            _token: $('meta[name="csrf-token"]').attr('content'),
            resolution_remarks: remarks
        }).done(function(resp) {
            $('#resolveReportModal').modal('hide');
            reportTable.draw(false);
            toastr.success('REPORT RESOLVED', (resp && resp.message) ? resp.message : 'Report marked as resolved.');
        }).fail(function(resp) {
            var message = (resp.responseJSON && (resp.responseJSON.message || resp.responseJSON.error)) ? (resp.responseJSON.message || resp.responseJSON.error) : 'Unable to resolve report.';
            toastr.error('RESOLVE ERROR', message);
        }).always(function() {
            $button.prop('disabled', false);
        });
    });
});
</script>
@endsection
