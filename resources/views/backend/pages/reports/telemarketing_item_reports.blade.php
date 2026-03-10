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
@endsection

@section('scripts')
<script src="/js/datatables.min.js"></script>
<script>
$(function () {
    $('#telemarketing-item-report-table').DataTable({
        responsive: true,
        serverSide: true,
        processing: true,
        ordering: false,
        ajax: {
            url: '/telemarketing_item_reports/get',
            type: 'GET'
        },
        columns: [
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
                title: 'Report Status'
            },
            {
                data: 'created_at',
                title: 'Date Reported',
                render: function(data) {
                    return data ? moment(data).format('MMM D, YYYY hh:mm A') : '--';
                }
            }
        ]
    });
});
</script>
@endsection
