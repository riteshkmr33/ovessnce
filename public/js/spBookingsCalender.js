$('#sp_id').data('spid', $('#sp_id').val());
/* calender code starts */
// Documentation http://arshaw.com/fullcalendar/docs/event_data/events_json_feed/
var weekday = new Array(7);
weekday[0] = "Sunday";
weekday[1] = "Monday";
weekday[2] = "Tuesday";
weekday[3] = "Wednesday";
weekday[4] = "Thursday";
weekday[5] = "Friday";
weekday[6] = "Saturday";
var date = new Date();
var d = date.getDate();
var m = date.getMonth();
var y = date.getFullYear();
var dayOfWeek = weekday[date.getUTCDay()];

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

function updateCalenderWorkdays(data)
{
    calendarDays = Array();

    if (data.workdays.length > 0) {
        $.each(data.workdays, function(key, day) {
            calendarDays.push(day)
        });
    } else {
        calendarDays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    }

    $('.default-load').fadeOut();
}

getSlots($('#sp_id').data('spid'), '15', '', updateCalenderWorkdays);

$('#bookings_calendar').fullCalendar('destroy'); // destroy the calendar
var viewname = $('#bookings_calendar').fullCalendar('getView').name;
$('#bookings_calendar').fullCalendar({//re-initialize the calendar
    //var bookedDate = getBookedDate($('#sp_id').val());
    disableDragging: false,
    header: h,
    editable: false,
    //isResizingEvent = true,
    eventResizeStart: function() {
        isResizingEvent = true;
    },
    events:
            {
                url: (OVEconfig.BASEURL + '/practitioner/bookingsCalender?sp_id=' + $('#sp_id').val()), // use the `url` property
                //color: 'yellow',    // an option!
                //textColor: 'black'  // an option!

            },
    dayRender: function(date, cell)
    {
        getSlots($('#sp_id').val(), '15', '', updateWorkdays);
        var day = weekday[date.getDay()];//date.toString().split(" ",1);
        var workingDays = $("#working_days").val().split(",");

        if ($.inArray(day, workingDays) > -1) {
            $(cell).addClass('fc-state-highlight');
        } else {
            cell.css("background-color", "#f8694d");
        }

        /*if (($.inArray(newDate, bookedDates) > -1) && ($.inArray(day, workingDays) > -1)) {
         cell.css("background-color", "#f8694d");
         }*/

    },
    dayClick: function(date, jsEvent, view) {

        if (checkDate(date) == true && window.location.pathname.indexOf('practitioner') != -1) {
            $('input#booking_id').val('');
            $('input#duration').val('').prop('disabled', true).parents('div.row').hide();
            $('input#booking_time').val(formatDate(date, 'Day d/m/Y h:i A')).prop('disabled', true);
            $('input#username').val('').prop('disabled', false);
            $('select#service_id').val('').prop('disabled', false);
            $('select#duration_list').val('').prop('disabled', true).parents('div.row').show();
            $('input#end_time').val('').prop('disabled', true);
            $('select#service_address_id').val('').prop('disabled', false);
            $('select#booking_status').val('').prop('disabled', false);
            $('input#submit').show();
            $("div#booking-overlay").fadeIn("slow");
            scrollTo('div#booking-overlay', 100, 'top');
        }
        /*var viewGet = $('#bookings_calendar').fullCalendar('getView');
         if (viewGet.name == 'month') {
         $('#bookings_calendar').fullCalendar('changeView', 'agendaDay').fullCalendar('gotoDate', date);
         }*/
    },
    eventRender: function(event, element, jsEvent, view) {
        //console.log(event);

        var viewGet = $('#bookings_calendar').fullCalendar('getView');
        if (viewGet.name == 'month')
            $('.fc-event').remove();

    },
    eventAfterAllRender: function(event, element, view) {
        var viewGet = $('#bookings_calendar').fullCalendar('getView');

        if (viewGet.name == 'agendaWeek') {
            $('div[class="fc-event fc-event-vert fc-event-start fc-event-end"]').each(function() {
                $(this).attr('style', $(this).attr('style').replace('width: 82px;', '') + ' width: 92px !important;')
            });
        } else if (viewGet.name == 'agendaDay') {
            $('div[class="fc-event fc-event-vert fc-event-start fc-event-end"]').each(function() {
                $(this).attr('style', $(this).attr('style').replace('width: 82px;', '') + ' width: 656px !important;')
            });
        }
    },
    eventClick: function(event) {

        //window.open(OVEconfig.BASEURL + '/practitioner/compose?user_id=' + event.user_id);
        //window.location= (OVEconfig.BASEURL+'/practitioner/compose');
        return false;
    },
});
/* calender code ends */

/* Function to get booking end time starts here */
function getEndTime(element, startTime, target)
{
    if ($(element).children("option:selected").val() != '') {
        var duration = parseInt($(element).children("option:selected").text().replace(' Mins', ''));  // duration in seconds
        var endTime = new Date(formatDate(startTime.replace(/\//g, '-'), 'Y/m/d h:i:s'));
        endTime.setMinutes((endTime.getMinutes() + duration));
        $(target).val(formatDate(endTime, 'Day d/m/Y h:i A')).prop('disabled', true);
    }
}
/* Function to get booking end time ends here */

/* Add mannual booking code starts here */
$(document.body).on('submit', 'form#mannualBooking', function() {

    var error = false;

    $('div.booking-detail').find('input,select').each(function() {
        if ($(this).val() == '' && $(this).attr('id') != 'duration' && $('input#booking_id').val() == '') {
            error = true;
        }
    });

    if (error == true) {
        $('div.error-msg').html('<label>Please fill all the details to register booking..!!</label>').fadeIn('slow');
        $("div#booking-overlay").fadeOut("slow");
        scrollTo('div.error-msg', 100, 'top');
        return false;
    }
    $('input#booking_time').prop('disabled', false);

    $.ajax({
        url: OVEconfig.BASEURL + '/booking/mannualbooking/',
        type: 'POST',
        data: {bookingData: $('form#mannualBooking').serialize()},
        beforeSend: function() {
            $('.default-load').fadeIn();
        },
        success: function(data) {
            console.log(data);
            if (data != "") {
                data = JSON.parse(data);
                if (data.status == '1') {
                    $('#bookings_calendar').fullCalendar('refetchEvents');  // get updated events
                    $('div.success-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                    scrollTo('div.success-msg', 100, 'top');
                    setTimeout("$('div.success-msg').slideUp('slow')", 5000);
                } else {
                    $('div.error-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                    scrollTo('div.error-msg', 100, 'top');
                }
            } else {
                $('div.error-msg').html('<label>Failed to add booking..!!</label>').fadeIn('slow');
                scrollTo('div.error-msg', 100, 'top');
            }
            $("div#booking-overlay").fadeOut("slow");
            $('.default-load').fadeOut();
        },
        error: function(xhr, errorType, errorMsg) {
            console.log(errorMsg);
            $('.default-load').fadeOut();
        }
    });

    return false;
});
/* Add mannual booking code ends here */

/* Send Invitation code starts here */
$(document.body).on('click', 'input#sendInvitation', function() {
    $('div.error-msg, div.success-msg').slideUp();
    var email = $('input#inviteEmail').val();
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    if (pattern.test(email)) {
        $.ajax({
            url: OVEconfig.BASEURL + '/practitioner/update/',
            type: 'POST',
            data: {action: 'invite', user: $('input#sp_id').val(), email: email},
            beforeSend: function() {
                $('.default-load').fadeIn();
            },
            success: function(data) {
                if (data != "") {
                    data = JSON.parse(data);
                    if (data.status == '1') {
                        $('input#inviteEmail').val('');
                        $('div.success-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                        scrollTo('div.success-msg', 100, 'top');
                        setTimeout("$('div.success-msg').slideUp('slow')", 5000);
                    } else {
                        $('div.error-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                        scrollTo('div.error-msg', 100, 'top');
                    }
                } else {
                    $('div.error-msg').html('<label>Failed to send invitation..!!</label>').fadeIn('slow');
                    scrollTo('div.error-msg', 100, 'top');
                }
                $('.default-load').fadeOut();
            },
            error: function(xhr, errorType, errorMsg) {
                console.log(errorMsg)
            }
        });

    } else {
        $('div.error-msg').html('<label>Please enter a valid email address to send invitation..!!</label>').fadeIn('slow');
        scrollTo('div.error-msg', 100, 'top');
    }
    return false;
});
/* Send Invitation code ends here */
