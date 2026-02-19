var action = 'save';
var page = 'program';
var table = null;
var record_id = null;
var fullcalendar;
var eventSourcesByStatus = {}; // Object to store event sources by status
var initialEventSource; // Store initial event source

$(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#voter_table').DataTable();

    // Initialize FullCalendar
    fullcalendar = $('#fullcalendar').fullCalendar({
        editable: false,
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        displayEventTime: true,
        eventRender: function (event, element, view) {
            var titleWithStatus = event.title + ' - ' + event.location;
            element.find('.fc-title').text(titleWithStatus);
        },
        selectable: true,
        selectHelper: true,
        eventSources: [ // Specify initial event sources as an array
            {
                url: 'calendar/get/', // Initial event source URL
                type: 'GET', // Optional: specify request type (GET by default)
                dataType: 'json', // Optional: specify expected data type (json by default)
                success: function(response) {
                    // Optional: handle success callback
                    initialEventSource = this; // Save initial event source
                },
                error: function() {
                    // Optional: handle error callback
                }
            }
        ]
    });

    // Event listener for checkbox changes
    $('.input-check').on('change', function() {
        var status = $(this).parent().text().trim(); // Get the text of the checkbox label
        var isChecked = $(this).is(':checked');
        var eventSourceUrl = '/calendar/status_filter/' + status;

        if (isChecked) {
            if (!(status in eventSourcesByStatus)) {
                // If event source for this status doesn't exist, add it
                $.get(eventSourceUrl, function(response) {
                    var eventSource = { url: eventSourceUrl };
                    $('#fullcalendar').fullCalendar('addEventSource', eventSource);
                    eventSourcesByStatus[status] = eventSource; // Store event source by status
                });
            }
        } else {
            if (status in eventSourcesByStatus) {
                $('#fullcalendar').fullCalendar('removeEventSource', eventSourcesByStatus[status]);
                delete eventSourcesByStatus[status]; // Remove from eventSourcesByStatus
            }

            // Remove initial event source if no checkboxes are checked
            if (!$('.input-check:checked').length && initialEventSource) {
                $('#fullcalendar').fullCalendar('removeEventSource', initialEventSource);
                initialEventSource = null; // Reset initial event source
            }
        }
    });
});
