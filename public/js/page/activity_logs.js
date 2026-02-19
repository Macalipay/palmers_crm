var action = 'save';
var page = 'activity_logs';
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
            // { 
            //     data: 'activity_type', 
            //     title: 'Activity',
            //     render: function(data, type, row) {
            //         return '<div>' + data + '</div>';
            //     }
            // },
            // { 
            //     data: 'details', 
            //     title: 'Details',
            //     render: function(data, type, row) {
            //         return '<div>' + data + '</div>';
            //     }
            // },
            // { 
            //     data: 'user.name', 
            //     title: 'Name',
            //     render: function(data, type, row) {
            //         return '<div>' + data + '</div>';
            //     }
            // },
            // { 
            //     data: 'created_at', 
            //     title: 'Date & Time', 
            //     render: function(data, type, row) {
            //         var date = new Date(data);
            //         var options = { month: 'long', day: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric' };
            //         var formattedDate = date.toLocaleDateString('en-US', options);
            //         return '<div>' + formattedDate + '</div>';
            //     }
            // }
            { data: "action", title: "LOGS", render:function(data, type, row, meta) {
                var html = '';
                    html += "<div class='activity-item'>";
                    html += '<div class="user"><div class="profiel-pic" style="background:url(/images/profile/'+row.user.picture+')no-repeat !important;background-size:cover !important;background-position:center center !important;"></div>';
                    html += '<i class="fab fa-'+row.device_info.toLowerCase()+'"></i>';
                    html += '</div>';
                        html += '<div class="log-details">';
                                html += '<div class="log-content"><b>'+ row.user.name +'</b>: '+ row.details +'</div>';
                                html += '<span class="log-date">'+ moment(row.created_at).format('MMM DD, YYYY - h:mm A') +'</span>';
                                html += '<div class="log-sub">IP Address: '+ row.ip_address +'</div>';
                        html += '</div>';
                    html += "</div>";
                return html;
            }}
        ]
    });
});