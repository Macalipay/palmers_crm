var filter = {
    _token: $('meta[name="csrf-token"]').attr('content'),
    company: null,
    store: null,
    start_date: null,
    end_date: null,
    merchandiser: null,
    sales_associate: null,
    source: null
};

function viewDetailed() {
    filter.start_date = $('#start_date').val();
    filter.end_date = $('#end_date').val();

    $('#loadModal').modal('show');

    $.post('/otc/dashboard/filtered', filter).done((response) => {
        var company_html = '';
        var store_html = '';

        $('#total_sales').text(response.sale.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#merchandiser_value').text(response.merchandiser.toLocaleString());
        $('#associate_value').text(response.sales_associates.toLocaleString());
        $('#source_value').text(response.source.toLocaleString());

        response.company.forEach((company, index) => {
            company_html += `<div class="division-item">
                                    <div class="count">${parseInt(index + 1)}</div>
                                    <div class="item-description">
                                        <div class="row">
                                            <div class="col-8">
                                                <div class="company-name">${company.company.company_name}</div>
                                                <div class="company-industry">${company.company.industry}</div>
                                            </div>
                                            <div class="col-4 text-right price-amount">₱ ${company.sales_amount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</div>
                                        </div>
                                    </div>
                                </div>`;
        });
        
        response.store.forEach((store, index) => {
            store_html += `<div class="division-item">
                                    <div class="count">${parseInt(index + 1)}</div>
                                    <div class="item-description">
                                        <div class="row">
                                            <div class="col-8">
                                                <div class="company-name">${store.store !== null?store.store.store_name:'-'}</div>
                                                <div class="company-industry">${store.store !== null?store.store.address:'-'}</div>
                                            </div>
                                            <div class="col-4 text-right price-amount">₱ ${store.sales_amount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</div>
                                        </div>
                                    </div>
                                </div>`;
        });

        $('.company-list').html(company_html);
        $('.store-list').html(store_html);

        $('#loadModal').modal('hide');
    });
}

function clickCard(selected) {
    $('.viewed-analytics').addClass('clicked');
    $('.detailed-analytics').addClass('clicked');

    switch(selected) {
        case "merchandiser":
            $('.card-selected-title').text('Merchandiser');

            if ($.fn.DataTable.isDataTable('#generated_dashboard_table')) {
                $('#generated_dashboard_table').DataTable().destroy();
            }
        
            $('#generated_dashboard_table').DataTable({
                responsive: false,
                serverSide: true,
                processing:true,
                paging: true,
                scrollX: true,
                ordering: false,
                ajax: {
                    url: `/otc/dashboard/filterBy/${selected}`,
                    type: 'POST',
                    data: filter
                },
                columns: [
                    { data: null, title: 'NAME', render: function(data, type, row) {
                        return `<div class="tbl-selection" onclick="clickCell(${row.merchandiser_id}, '${row.merchandiser !== null?row.merchandiser.merchandiser:"-"}', '${selected}')">${row.merchandiser !== null?row.merchandiser.merchandiser:"-"}</div>`;
                    }},
                ]
            });

            break;
            
        case "sales_associate":
            $('.card-selected-title').text('Sales Associate');

            if ($.fn.DataTable.isDataTable('#generated_dashboard_table')) {
                $('#generated_dashboard_table').DataTable().destroy();
            }
        
            $('#generated_dashboard_table').DataTable({
                responsive: false,
                serverSide: true,
                processing:true,
                paging: true,
                scrollX: true,
                ordering: false,
                ajax: {
                    url: `/otc/dashboard/filterBy/${selected}`,
                    type: 'POST',
                    data: filter
                },
                columns: [
                    { data: null, title: 'NAME', render: function(data, type, row) {
                        return `<div class="tbl-selection" onclick="clickCell(${row.sales_associate_id}, '${row.sales_associate !== null?row.sales_associate.sales_associate:"-"}', '${selected}')">${row.sales_associate !== null?row.sales_associate.sales_associate:"-"}</div>`;
                    }},
                ]
            });

            break;
            
        case "source":
            $('.card-selected-title').text('Sales Associate');

            if ($.fn.DataTable.isDataTable('#generated_dashboard_table')) {
                $('#generated_dashboard_table').DataTable().destroy();
            }
        
            $('#generated_dashboard_table').DataTable({
                responsive: false,
                serverSide: true,
                processing:true,
                paging: true,
                scrollX: true,
                ordering: false,
                ajax: {
                    url: `/otc/dashboard/filterBy/${selected}`,
                    type: 'POST',
                    data: filter
                },
                columns: [
                    { data: null, title: 'NAME', render: function(data, type, row) {
                        return `<div class="tbl-selection" onclick="clickCell(${row.source_id}, '${row.source !== null?row.source.source:"-"}', '${selected}')">${row.source !== null?row.source.source:"-"}</div>`;
                    }},
                ]
            });

            break;
    }
}


function clickCell(id, title, type) {
    filter[type] = id;

    $('.viewed-analytics').removeClass('clicked');
    $('.detailed-analytics').removeClass('clicked');

    $(`#${type} .filter-display`).html(`<span>${title} <i class="fas fa-times" onclick="removeFilter('${type}')"></i></span>`);

    viewDetailed();
}

function removeFilter(type) {
    event.stopPropagation();
    filter[type] = null;

    $(`#${type} .filter-display`).html('');
    viewDetailed();
}

function backButton() {
    $('.viewed-analytics').removeClass('clicked');
    $('.detailed-analytics').removeClass('clicked');
}