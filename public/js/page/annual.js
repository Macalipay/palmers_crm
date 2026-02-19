var status_chart = null;
var source_chart = null;

var seriesData = [];

var color = [];
var formatter = new Intl.NumberFormat('en-PH', {
    style: 'currency',
    currency: 'PHP',
});

var dynamicColors = function() {
    var r = Math.floor(Math.random() * 255);
    var g = Math.floor(Math.random() * 255);
    var b = Math.floor(Math.random() * 255);
    return "rgb(" + r + "," + g + "," + b + ")";
 };

$(function() {
    generateReport('all', $('#growth_year').val());
    
    $(".sidebar-toggle").on("click touch", function() {
        
        setTimeout(() => {
            // status_chart.resize();
            // source_chart.resize();
        }, 500);
    });
});

function handleClick(event, chartContext, config) {
    const dataPointIndex = config.dataPointIndex;
    const clickedData = seriesData[dataPointIndex];
    // alert(`You clicked on ${clickedData.x} with value ${clickedData.y}`);
    
    var label = clickedData.x;
    
    $.post('/annual/get_calls', { _token: $('meta[name="csrf-token"]').attr('content'), date: $('#filter_date').val(), status: label }).done((response) => {
        var el = '';
        var status = '';
        var lead = '';
        

        if(response.status.length !== 0) {
            $.each(response.status, (i,v)=>{

                if (v.status == 'TO DO') {
                    status = '<span class="badge badge-primary">TO DO</span>';
                } else if(v.status == 'IN PROGRESS') {
                    status = '<span class="badge badge-warning">IN PROGRESS</span>';
                } else if(v.status == 'CANCELLED') {
                    status = '<span class="badge badge-danger">CANCELLED</span>';
                } else if(v.status == 'PENDING') {
                    status = '<span class="badge badge-info">PENDING</span>';
                } else if(v.status == 'ON-HOLD') {
                    status = '<span class="badge badge-secondary">ON-HOLD</span>';
                } else {
                    status = '<span class="badge badge-success">COMPLETED</span>';
                }

                
                if (v.lead_status == 'LEAD') {
                    lead = '<span class="badge badge-primary">LEAD</span>';
                } else if(v.lead_status == 'PROSPECT') {
                    lead = '<span class="badge badge-secondary">PROSPECT</span>';
                } else if(v.lead_status == 'WON') {
                    lead = '<span class="badge badge-success">WON</span>';
                } else {
                    lead = '<span class="badge badge-warning">LOST</span>';
                }

                el += `<div class="sales-item">
                    <div class="row">
                        <div class="col-8 sales-company-name">${v.telemarketing.company.company_name}</div>
                        <div class="col-4 text-right"><b>Status:</b> ${status} | <b>Lead Status:</b> ${lead}</div>
                        <div class="col-12"><span class="sales-interest">${v.telemarketing.product_interest}</span> | <span class="sales-date">${moment(v.date).format('MMM DD, YYYY')}</span></div>
                    </div>
                </div>`;
            });
        }
        else {
            el += `<div class="text-center no-item">NO RECORDS FOUND</div>`;
        }

        $('.sales_list').html(el);
    });

    $('.record-title').text('Calls Report (Status: '+label+')');
    
    showDetails();
}

function generateReport(date, growth) {
    $.get('/annual/get_record/' + date + '/' + growth, function(response) {
        var division_html = '';
        var associate_html = '';
        var agent_html = '';
        var industry_html = '';

        $('#daily h1').text(formatter.format(response.daily));
        $('#transaction h1').text(response.trans);
        $('#annual h1').text(formatter.format(response.annual));
        $('#telemarketing h1').text(response.telemarketing);
        $('#growth h1').text(formatter.format(response.growth.amount));
        $('#growth .perc').text(response.growth.percentage.toFixed(2) + "%");
        
        if(date === "all") {
            
            seriesData = [
                { x: 'COMPLETED', y: response.calls.completed },
                { x: 'INCOMPLETE', y: response.calls.incomplete },
                { x: 'CANCELLED', y: response.calls.cancelled }
            ];
            
            var options = {
                series: [
                    {
                        name: "CALLS",
                        data: seriesData,
                    },
                ],
                chart: {
                    type: 'bar',
                    height: 350,
                    events: {
                        click: handleClick
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 0,
                        horizontal: true,
                        barHeight: '80%',
                        isFunnel: true,
                    },
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val, opt) {
                        return opt.w.globals.labels[opt.dataPointIndex] + ':  ' + val
                    },
                    dropShadow: {
                        enabled: false,
                    },
                },
                title: {
                },
                xaxis: {
                    // categories: [
                    //     'COMPLETED',
                    //     'INCOMPLETE',
                    //     'CANCELLED'
                    // ],
                },
                legend: {
                    show: false,
                },
            };
            options.series[0].data.sort((a, b) => b.y - a.y);
    
            status_chart = new ApexCharts(document.querySelector("#status_chart"), options);
            status_chart.render();

            // const status_data = {
            //     labels: ['COMPLETED', 'INCOMPLETE', 'CANCELLED'],
            //     datasets: [
            //         {
            //             data: [
            //                 response.calls.completed,
            //                 response.calls.incomplete,
            //                 response.calls.cancelled
            //             ],
            //             backgroundColor: ['#3490dc', '#c9c9c9', '#ff9f45'],
            //             hoverOffset: 4
            //         }
            //     ]
            // };
            
            // status_chart = new Chart(document.getElementById("status_chart"), {
            //     type: 'pie',
            //     data: status_data,
            //     options: {
            //         plugins: {
            //             legend: {
            //                 display: true
            //             }
            //         },
            //         legend: {
            //             display: true,
            //             position: 'bottom'
            //         },
            //         maintainAspectRatio: false,
            //         responsive: true
            //     }
            // });
        
            const source_data = {
                id: [],
                labels: [],
                datasets: [
                    {
                        data: [],
                        backgroundColor: [],
                        hoverOffset: 4
                    }
                ]
            };

            $.each(response.source, function(i,v) {
                source_data.id.push(v.id);
                source_data.labels.push(v.source);
                source_data.datasets.forEach((dataset) => {
                    dataset.data.push(v.sumAmount);
                    dataset.backgroundColor.push('#3490dc');
                });
            });
            
            // source_chart = new Chart(document.getElementById("source_chart"), {
            //     type: 'bar',
            //     data: source_data,
            //     options: {
            //         plugins: {
            //             legend: {
            //                 display: true
            //             }
            //         },
            //         legend: {
            //             display: false
            //         },
            //         maintainAspectRatio: false,
            //         responsive: true
            //     }
            // });

            console.log('1');
        }
        else {
            // status_chart.data.datasets[0].data = [response.calls.completed,response.calls.incomplete,response.calls.cancelled];
            // status_chart.update();
            
            seriesData = [
                { x: 'COMPLETED', y: response.calls.completed },
                { x: 'INCOMPLETE', y: response.calls.incomplete },
                { x: 'CANCELLED', y: response.calls.cancelled }
            ];

            seriesData.sort((a, b) => b.y - a.y);

            // status_chart.updateSeries([{
            //     data: seriesData
            // }]);

            // source_chart.data.labels = [];
            // source_chart.data.datasets[0].data = [];

            // $.each(response.source, function(i,v) {
            //     source_chart.data.id.push(v.id);
            //     source_chart.data.labels.push(v.source);
            //     source_chart.data.datasets.forEach((dataset) => {
            //         dataset.data.push(v.sumAmount);
            //         dataset.backgroundColor.push('#3490dc');
            //     });
            // });
            // source_chart.update();
            console.log('2');
        }

        $('#total_calls .counts').text(response.total_calls);
        $('#complete_calls .counts').text(response.calls.completed);
        $('#incomplete_calls .counts').text(response.calls.incomplete);
        $('#cancelled_calls .counts').text(response.calls.cancelled);
        
        $.each(response.division, function(i,v) {
            division_html += "<div class='division-item list-t' id='division_"+v.id+"' onclick='getDivision("+v.id+")'>";
                division_html += "<span class='division-item'>"+v.division+"</span>";
                division_html += "<span class='division-amount'>"+formatter.format(v.sumAmount)+"</span>";
            division_html += "</div>";
        });

        $('.division-list').html(division_html);
        
        $.each(response.associate, function(i,v) {
            associate_html += "<div class='associate-item list-t' id='associates_"+v.id+"' onclick='getAssociates("+v.id+")'>";
                associate_html += "<span class='associate-item'>"+v.sales_associate+"</span>";
                associate_html += "<span class='associate-amount'>"+formatter.format(v.sumAmount)+"</span>";
            associate_html += "</div>";
        });

        $('.associate-list').html(associate_html);
        
        $.each(response.agent, function(i,v) {
            agent_html += "<div class='agent-item list-t' id='agent_"+v.id+"' onclick='getAgent("+v.id+")'>";
                agent_html += "<span class='agent-item'>"+v.name+"</span>";
                agent_html += "<span class='agent-amount'>"+formatter.format(v.sumAmount)+"</span>";
            agent_html += "</div>";
        });

        $('.agent-list').html(agent_html);
        
        $.each(response.industry, function(i,v) {
            industry_html += "<div class='industry-item list-t' id='ind_"+v.industry+"' onclick='getIndustry(\""+v.industry+"\")'>";
                industry_html += "<span class='industry-item'>"+v.industry+"</span>";
                industry_html += "<span class='industry-amount'>"+formatter.format(v.sumAmount)+"</span>";
            industry_html += "</div>";
        });

        $('.industry-list').html(industry_html);

        // Chart function
        
        // document.getElementById("source_chart").onclick = function(evt) {
        //     var a_points = source_chart.getElementsAtEvent(evt);
        //     if (a_points[0]) {
        //         var chartData = a_points[0]['_chart'].config.data;
        //         var idx = a_points[0]['_index'];
        
        //         var label = chartData.labels[idx];
        //         var id = chartData.id[idx];

        //         console.log(id);

        //         if ($.fn.DataTable.isDataTable('#generated_table')) {
        //             $('#generated_table').DataTable().destroy();
        //             $('#generated_table').empty();
        //         }
            
        //         $('#generated_table').DataTable({
        //             responsive: true,
        //             serverSide: true,
        //             paging: true,
        //             ordering: false,
        //             scrollX: true,
        //             pageLength: 20,
        //             ajax: {
        //                 url: '/annual/get_daily',
        //                 type: 'POST',
        //                 data: { _token: $('meta[name="csrf-token"]').attr('content'), date: $('#filter_date').val() }
        //             },
        //             columns: [
        //                 { data: 'company.company_name', title: 'Company' },
        //                 { data: 'source.source', title: 'Source' },
        //                 { data: 'date_purchased', title: 'Date Puchased', render: function(data, type, row, meta) {
        //                     return moment(row.date_purchased).format('MMM DD, YYYY');
        //                 }},
        //                 { data: 'amount', title: 'Amount', render: function(data, type, row, meta) {
        //                     return formatter.format(row.amount);
        //                 }}

        //             ]
        //         });

        //         $('.record-title').text('Sales Report (Source: '+label+')');
                
        //         showDetails();
        //     }
        // };
    });
}

function filterByDate() {
    generateReport($('#filter_date').val(), $('#growth_year').val());
    hideDetails();
}

function getDaily() {
    $.post('/annual/get_daily', { _token: $('meta[name="csrf-token"]').attr('content'), date: $('#filter_date').val() }).done((response) => {
        var el = '';
        if(response.sale.length !== 0) {
            $.each(response.sale, (i,v)=>{
                el += `<div class="sales-item">
                    <div class="row">
                        <div class="col-8 sales-company-name">${v.company.company_name}</div>
                        <div class="col-4 text-right"><span class="sales-source">${v.source.source}</span> | <b>Total:</b> <span class="sales-amount">${formatter.format(v.amount)}</span></div>
                        <div class="col-12"><span class="sales-date">${moment(v.date_purchased).format('MMM DD, YYYY')}</span></div>
                        <div class="col-12 table-title">Sales Item List</div>
                        <div class="col-12"><table style="width: 100%;"><thead><th>ITEM NAME</th><th>DESCRIPTION</th><th>BRAND</th><th>QUANTITY</th><th>AMOUNT</th><thead><tbody>`;

                    if(v.details.length !== 0) {
                        $.each(v.details, (i,v) => {
                            el += `<tr>
                                <td>${v.item.item_name}</td>
                                <td>${v.description}</td>
                                <td>${v.brand.brand}</td>
                                <td>${v.quantity}</td>
                                <td>${formatter.format(v.amount)}</td>
                            </tr>`;
                        });
                    }
                    else {
                        el += `<tr><td class="text-center no-item" colspan="5">NO RECORDS FOUND</td></tr>`;
                    }

                el +=`</tbody></table></div>
                    </div>
                </div>`;
            });
        }
        else {
            el += `<div class="text-center no-item">NO RECORDS FOUND</div>`;
        }

        $('.sales_list').html(el);
    });
    
    $('.record-title').text('Sales Report');

    showDetails();
}

function getDivision(id) {
    
    $.post('/annual/get_division', { _token: $('meta[name="csrf-token"]').attr('content'), date: $('#filter_date').val(), division_id: id }).done(function(response) {
        // console.log(response);
        var division_html = '';
        $('#division_record .card-title').html('<i class="fas fa-list"></i> Total sales per branch');
        
        $.each(response.branch, function(i,v) {
            division_html += "<div class='branch-item list-t' id='branch_"+v.id+"' onclick='getBranch("+v.id+")'>";
                division_html += "<span class='branch-item'>"+v.branch_name+"</span>";
                division_html += "<span class='branch-amount'>"+formatter.format(v.sumAmount)+"</span>";
            division_html += "</div>";
        });

        $('.division-list').html(division_html);
        $('#division_record span.back').removeClass('hide');
    });

    $('.list-t').removeClass('selected');
    $('#division_'+id).addClass('selected');

    $('.record-title').text('Total sales per branch');
}

function getBranch(id) {
    $.post('/annual/get_branch', { _token: $('meta[name="csrf-token"]').attr('content'), date: $('#filter_date').val(), branch_id: id }).done((response) => {
        var el = '';
        if(response.sale.length !== 0) {
            $.each(response.sale, (i,v)=>{
                el += `<div class="sales-item">
                    <div class="row">
                        <div class="col-8 sales-company-name">${v.company.company_name}</div>
                        <div class="col-4 text-right"><span class="sales-source">${v.source.source}</span> | <b>Total:</b> <span class="sales-amount">${formatter.format(v.amount)}</span></div>
                        <div class="col-12"><span class="sales-date">${moment(v.date_purchased).format('MMM DD, YYYY')}</span></div>
                        <div class="col-12 table-title">Sales Item List</div>
                        <div class="col-12"><table style="width: 100%;"><thead><th>ITEM NAME</th><th>DESCRIPTION</th><th>BRAND</th><th>QUANTITY</th><th>AMOUNT</th><thead><tbody>`;

                        if(v.details.length !== 0) {
                            $.each(v.details, (i,v) => {
                                el += `<tr>
                                    <td>${v.item.item_name}</td>
                                    <td>${v.description}</td>
                                    <td>${v.brand.brand}</td>
                                    <td>${v.quantity}</td>
                                    <td>${formatter.format(v.amount)}</td>
                                </tr>`;
                            });
                        }
                        else {
                            el += `<tr><td class="text-center no-item" colspan="5">NO RECORDS FOUND</td></tr>`;
                        }

                el +=`</tbody></table></div>
                    </div>
                </div>`;
            });
        }
        else {
            el += `<div class="text-center no-item">NO RECORDS FOUND</div>`;
        }

        $('.sales_list').html(el);
    });


    $('.list-t').removeClass('selected');
    $('#branch_'+id).addClass('selected');

    $('.record-title').text('Sales Report');

    showDetails();
}

function getAgent(id) {
    $.post('/annual/get_agent', { _token: $('meta[name="csrf-token"]').attr('content'), date: $('#filter_date').val(), agent: id }).done((response) => {
        var el = '';
        if(response.sale.length !== 0) {
            $.each(response.sale, (i,v)=>{
                el += `<div class="sales-item">
                    <div class="row">
                        <div class="col-8 sales-company-name">${v.company.company_name}</div>
                        <div class="col-4 text-right"><span class="sales-source">${v.source.source}</span> | <b>Total:</b> <span class="sales-amount">${formatter.format(v.amount)}</span></div>
                        <div class="col-12"><span class="sales-date">${moment(v.date_purchased).format('MMM DD, YYYY')}</span></div>
                        <div class="col-12 table-title">Sales Item List</div>
                        <div class="col-12"><table style="width: 100%;"><thead><th>ITEM NAME</th><th>DESCRIPTION</th><th>BRAND</th><th>QUANTITY</th><th>AMOUNT</th><thead><tbody>`;

                    $.each(v.details, (i,v) => {
                        el += `<tr>
                            <td>${v.item.item_name}</td>
                            <td>${v.description}</td>
                            <td>${v.brand.brand}</td>
                            <td>${v.quantity}</td>
                            <td>${formatter.format(v.amount)}</td>
                        </tr>`;
                    });

                el +=`</tbody></table></div>
                    </div>
                </div>`;
            });
        }
        else {
            el += `<div class="text-center no-item">NO RECORDS FOUND</div>`;
        }

        $('.sales_list').html(el);
    });
    
    $('.list-t').removeClass('selected');
    $('#agent_'+id).addClass('selected');

    $('.record-title').text('Sales Report');

    showDetails();
}

function getAssociates(id) {
    $.post('/annual/get_associate', { _token: $('meta[name="csrf-token"]').attr('content'), date: $('#filter_date').val(), associate: id }).done((response) => {
        var el = '';
        if(response.sale.length !== 0) {
            $.each(response.sale, (i,v)=>{
                el += `<div class="sales-item">
                    <div class="row">
                        <div class="col-8 sales-company-name">${v.company.company_name}</div>
                        <div class="col-4 text-right"><span class="sales-source">${v.source.source}</span> | <b>Total:</b> <span class="sales-amount">${formatter.format(v.amount)}</span></div>
                        <div class="col-12"><span class="sales-date">${moment(v.date_purchased).format('MMM DD, YYYY')}</span></div>
                        <div class="col-12 table-title">Sales Item List</div>
                        <div class="col-12"><table style="width: 100%;"><thead><th>ITEM NAME</th><th>DESCRIPTION</th><th>BRAND</th><th>QUANTITY</th><th>AMOUNT</th><thead><tbody>`;

                        if(v.details.length !== 0) {
                            $.each(v.details, (i,v) => {
                                el += `<tr>
                                    <td>${v.item.item_name}</td>
                                    <td>${v.description}</td>
                                    <td>${v.brand.brand}</td>
                                    <td>${v.quantity}</td>
                                    <td>${formatter.format(v.amount)}</td>
                                </tr>`;
                            });
                        }
                        else {
                            el += `<tr><td class="text-center no-item" colspan="5">NO RECORDS FOUND</td></tr>`;
                        }

                el +=`</tbody></table></div>
                    </div>
                </div>`;
            });
        }
        else {
            el += `<div class="text-center no-item">NO RECORDS FOUND</div>`;
        }

        $('.sales_list').html(el);
    });

    $('.list-t').removeClass('selected');
    $('#associates_'+id).addClass('selected');
    
    $('.record-title').text('Sales Report');

    showDetails();
}

function getIndustry(detail) {
    $.post('/annual/get_industry', { _token: $('meta[name="csrf-token"]').attr('content'), date: $('#filter_date').val(), industry: detail }).done((response) => {
        var el = '';
        $.each(response.sale, (i,v)=>{
            el += `<div class="sales-item-company">
                <div class="row">
                    <div class="col-12 sales-companies">${v.company_name}</div>
                    <div class="col-12">`;
                    if(v.industry.length !== 0) {
                        $.each(v.industry, (i,v)=>{
                            el += `<div class="sales-item">
                                <div class="row">
                                    <div class="col-8"><span class="sales-date">${moment(v.date_purchased).format('MMM DD, YYYY')}</span></div>
                                    <div class="col-4 text-right"><span class="sales-source">${v.source.source}</span> | <b>Total:</b> <span class="sales-amount">${formatter.format(v.amount)}</span></div>
                                    <div class="col-12 table-title">Sales Item List</div>
                                    <div class="col-12"><table style="width: 100%;"><thead><th>ITEM NAME</th><th>DESCRIPTION</th><th>BRAND</th><th>QUANTITY</th><th>AMOUNT</th><thead><tbody>`;
    
                                    if(v.details.length !== 0) {
                                        $.each(v.details, (i,v) => {
                                            el += `<tr>
                                                <td>${v.item.item_name}</td>
                                                <td>${v.description}</td>
                                                <td>${v.brand.brand}</td>
                                                <td>${v.quantity}</td>
                                                <td>${formatter.format(v.amount)}</td>
                                            </tr>`;
                                        });
                                    }
                                    else {
                                        el += `<tr><td class="text-center no-item" colspan="5">NO RECORDS FOUND</td></tr>`;
                                    }
    
                            el +=`</tbody></table></div>
                                </div>
                            </div>`;
                        });
                    }
                    else {
                        el += `<div class="text-center no-item">NO RECORDS FOUND</div>`;
                    }

            el += ` </div>
                </div>
            </div>`;
        });

        $('.sales_list').html(el);
    });

    $('.list-t').removeClass('selected');
    $('#ind_'+detail).addClass('selected');
    
    $('.record-title').text('Sales Report');

    showDetails();
}

function showDetails() {
    $('.grid-container').removeClass('show');
    $('.grid-details').addClass('show');
    $('.detailed-view').removeClass('hide');
    setTimeout(() => {
        // status_chart.resize();
        // source_chart.resize();
    }, 500);
}

function hideDetails() {
    $('.grid-container').addClass('show');
    $('.grid-details').removeClass('show');
    $('.list-t').removeClass('selected');
    $('.detailed-view').addClass('hide');
    $('#f_date').val('');
    $('#f_item_name').val('');
}


function backDivision() {
    if($('#filter_date').val() === "") {
        generateReport('all');
        hideDetails();
    }
    else {
        filterByDate();
    }
    
    $('#division_record span.back').addClass('hide');
}

function filterBox(val, item) {
    var value = val === "f_date"?moment($('#'+val).val()).format("MMM DD, YYYY").toLowerCase():$('#'+val).val().toLowerCase();

    $(".sales-item ." + item).filter(function() {
        // console.log($($(this).parent()[0]));
        if(val === "f_date") {
            $($($($(this).parent()[0]).parent()[0]).parent()[0]).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        }
        else {
            $($($(this).parent()[0]).parent()[0]).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        }
    });
}