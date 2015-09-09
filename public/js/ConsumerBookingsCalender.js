/* calender code starts */
// Documentation http://arshaw.com/fullcalendar/docs/event_data/events_json_feed/
var date = new Date();
var d = date.getDate();
var m = date.getMonth();
var y = date.getFullYear();
var calendarDays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

var h = {};

if ($('#bookings_calendar').width() <= 400) {
    $('#bookings_calendar').addClass("mobile");
    h = {
        left: 'title, prev, next',
        center: '',
        right: 'agendaDay,agendaWeek'  // Changed as per client's requirement
        //right: 'today,month,agendaWeek,agendaDay'
                //right: 'today,month,basicWeek,agendaDay'
    };
} else {
    $('#bookings_calendar').removeClass("mobile");
    if (App.isRTL()) {
        h = {
            right: 'title',
            center: '',
            left: 'prev,next,agendaDay,agendaWeek'   // Changed as per client's requirement
            //left: 'prev,next,today,month,agendaWeek,agendaDay'
                    //left: 'prev,next,today,month,basicWeek,agendaDay'
        };
    } else {
        h = {
            left: 'title',
            center: '',
            right: 'prev,next,agendaDay,agendaWeek'  // Changed as per client's requirement
            //right: 'prev,next,today,month,agendaWeek,agendaDay'
                    //right: 'prev,next,today,month,basicWeek,agendaDay'
        };
    }
}

$('#bookings_calendar').fullCalendar('destroy'); // destroy the calendar
$('#bookings_calendar').fullCalendar({//re-initialize the calendar
    disableDragging: false,
    header: h,
    editable: false,
    events:
            {
                url: (OVEconfig.BASEURL + '/consumer/bookingsCalender?sp_id=' + $('#sp_id').val()), // use the `url` property
                //color: 'yellow',    // an option!
                //textColor: 'black'  // an option!
            }
});
/* calender code ends */
