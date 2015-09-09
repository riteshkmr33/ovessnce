
if (window.location.href.indexOf('add') != -1 || window.location.href.indexOf('edit') != -1 || window.location.href.indexOf('reschedule') != -1) {
    applyCalendar('input#booking_time');
    updateCalender();

    var duration = ($('select.duration:first').val() != '') ? $('select.duration:first').find('option:selected').text().replace(' Mins', '') : 0;
    var address = ($('select.address:first').val() != '') ? $('select.address:first').val() : '';
    var sp = ($('select.getServices').val() != '') ? $('select.getServices').val() : 0;
    $('div.xdsoft_datetimepicker').data({duration: duration, sp: sp, address: address});
}

$(document.body).on('change', 'select.address', function() {
    updateCalender();
});

/* Functioin to update calendar request data */
function updateCalender()
{
    var duration = ($('select.duration:first').val() != '') ? $('select.duration:first').find('option:selected').text().replace(' Mins', '') : 0;
    var address = ($('select.address:first').val() != '') ? $('select.address:first').val() : '';
    var sp = ($('select.getServices').val() != '') ? $('select.getServices').val() : 0;
    $('div.xdsoft_datetimepicker').data({'sp': sp, 'duration': duration, 'address': address});
    getSlots(sp, duration, address, updateWorkdays);
}