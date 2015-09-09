var bookedDates = Array();
var workingDays = Array();
var timeSlots = Array();
var weekday = new Array(7);
weekday[0] = "Sunday";
weekday[1] = "Monday";
weekday[2] = "Tuesday";
weekday[3] = "Wednesday";
weekday[4] = "Thursday";
weekday[5] = "Friday";
weekday[6] = "Saturday";

function getSlots(user_id, serviceDuration, address_id, callback)
{
    // getting fully booked days  $('input#sp_id').val()
    
    $.ajax({
        url : OVEconfig.BASEURL+'/practitioner/bookeddays/',
        type : 'POST',
        dataType : 'json',
        data : {user : user_id, service_duration: serviceDuration, address_id: address_id},
        beforeSend : function(){ $('.default-load').fadeIn();},
        success : function(data){
            if (typeof data != 'undefined') {
                callback(data);
            }
        },
        error : function(xhr, errorType, errorMsg){ console.log(errorMsg); $('.default-load').fadeOut();},
    });
}

/* Datetime picker for bookings section starts here */
function logic(ref, currentDateTime, callback )
{
    // 'this' is jquery object datetimepicker
    
    var current_date = currentDateTime.getDate();
    current_date = (current_date < 10)?'0'+current_date:current_date;
    var month = currentDateTime.getMonth()+1;
    month = (month < 10)?'0'+month:month;
    var year = currentDateTime.getFullYear();
    var duration = $(ref).data('duration');
    var sp = $(ref).data('sp');
    var address_id = $(ref).data('address');  // need to be dynamic

    $.ajax({
        url : OVEconfig.BASEURL+'/practitioner/getslots',
        type : 'POST',
        //async : false,
        data : {selectedDate : year+'-'+month+'-'+current_date, duration: duration, user_id: sp, address_id: address_id},
        beforeSend : function(){
                $('.default-load').fadeIn();
        },
        success : function(data){
            data = JSON.parse(data);
            callback(data, ref);
        },
        error : function(xhr, errorType, errorMsg){
            console.log(errorMsg)
        }
    });

}

function updateSlots(data, ref)
{
    timeSlots = Array();
    $.each(data, function(key, value){
        timeSlots.push(value.start);
    });

    if (timeSlots.length > 0) {
        if (timeSlots.length < 8) {
            ref.setOptions({allowTimes:timeSlots,timepicker:true,});
        } else {
            ref.setOptions({allowTimes:timeSlots,timepicker:true,});
        }
    } else {
        ref.setOptions({timepicker:false});
    }

    $('.default-load').fadeOut();
}

function updateWorkdays(data)
{
    workingDays = Array();
    
    if (data.workdays.length > 0) {
        $.each(data.workdays, function(key, day){ workingDays.push(day)});
    } else {
        workingDays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    }
    
    if (data.bookedDates.length > 0) {
        $.each(data.bookedDates, function(key, bookedDate){ bookedDates.push(bookedDate)});
    } else {
        bookedDates = Array();
    }

    $('.default-load').fadeOut();
}

function applyCalendar(element)
{
    var minDate = new Date();
    minDate.setDate(minDate.getDate() + 3);  // avail slots after 48 hours
    
    $(element).datetimepicker({
            formatTime:'H:i',
            formatDate:'d-m-Y',
            format:'d/m/Y H:i',
            updateOnDateSelect:false,
            dynamicSlots:true,
            minDate: minDate,
            onSelectDate:function(currentDateTime){ logic(this, currentDateTime, updateSlots);},
            onShow:function(currentDateTime){ logic(this, currentDateTime, updateSlots);},
            beforeShowDay: function(date) {
                var currentDay = weekday[date.getDay()];
                var year = date.getFullYear();
                var month = date.getMonth()+1;
                var currentDate = date.getDate();

                var today = new Date();

                if (workingDays.indexOf(currentDay) != -1 && ((year >= today.getFullYear() && month >= (today.getMonth()+1)) || (year > today.getFullYear() && month <= (today.getMonth()+1)))) {
                    return [true, ""];
                } else {
                    return [false, ""];
                }
            }
            //allowTimes:timeSlots,
            /*onChangeDateTime:function(dp,$input){
                console.log($input.val())
              }*/
    });
}
/* Datetime picker for bookings section ends here */

/* Function used in fullcalendar.min.js */
function checkDate(currentDate)
{
    var currentDay = weekday[currentDate.getDay()];
    var today = new Date();
    today.setDate(today.getDate() + 2);
    
    if (calendarDays.indexOf(currentDay) == -1 || (currentDate.getTime() < today.getTime())) {
        return false;
    } else {
        return true;
    }
}