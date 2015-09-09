$(window).load(function() {

    /* Booking reschedule code starts here */
    $(document.body).on('click', 'span.reschedule', function() {

        cancelSuggestion();
        getSlots($(this).data('sp'), 0, $(this).data('address'), updateWorkdays);

        // Open the selected entry
        $('div.xdsoft_datetimepicker').data({duration: $(this).data('durtn'), sp: $(this).data('sp')});
        $(this).parents('tr').find('input.datetimepicker').fadeIn('slow');
        $(this).parents('tr').find('div.bookingTime').slideUp('slow');
        $(this).parents('tr').find('div.update').toggle('slow');

    });

    $(document.body).on('click', 'span.cancel', function() {
        cancelSuggestion();
    });

    $(document.body).on('click', 'span.bookingCancel', function() {
        
        if (window.location.pathname.indexOf('consumer') != -1) {
            if (confirm("YOU ARE ABOUT TO CANCEL YOUR APPOINTMENT, PLEASE NOTE THAT YOU CAN SELECT A NEW DATE AND TIME . IT'S FREE OF CHARGE. IF YOU STILL WANT TO CANCEL, PLEASE NOTE THAT THE DEPOSIT IS NOT REFUNDABLE")) {
                changeBookingStatus([$(this).attr('id')], 6);
            }
        } else {
            changeBookingStatus([$(this).attr('id')], 6);
        }
    });

    $(document.body).on('click', 'span.bookingConfirm', function() {
        changeBookingStatus([$(this).attr('id')], 4);
    });

    $(document.body).on('click', 'span.bookingReschedule', function() {
        $('div.error-msg, div.success-msg').slideUp();
        var booking_id = $(this).attr('id');

        if (booking_id != '' && !isNaN(booking_id)) {
            var newDate = $(this).parents('tr').find('input.datetimepicker').val();
            if (newDate != "") {
                $.ajax({
                    url: OVEconfig.BASEURL + '/booking/suggest',
                    type: 'POST',
                    data: {booking: booking_id, date: newDate},
                    beforeSend: function() {
                        $('.default-load').fadeIn();
                    },
                    success: function(data) {
                        //console.log(data);
                        if (data != '') {
                            data = JSON.parse(data);
                            if (data.status == '1') {
                                paginateBookings($('input#total_bookings').val()); // regenrate pagination

                                /* Notifications updating code starts here */
                                if ($('a#total_notification').next('span').length == 1) {
                                    if (data.notifications.total > 0) {
                                        $('a#total_notification').next('span').html(data.notifications.total);
                                        $('a.icon-flag').children('span').html(data.notifications.total);
                                    } else {
                                        $('a#total_notification').next('span').remove();
                                        $('a.icon-flag').children('span').html(data.notifications.total);
                                    }
                                } else {
                                    if (data.notifications.total > 0) {
                                        $('a#total_notification').after('<span>' + data.notifications.total + '</span>');
                                        $('a.icon-flag').children('span').html(data.notifications.total);
                                    }
                                }

                                if (data.notifications.booking > 0) {
                                    $('li#booking_notification').html('<a href="javascript:;" onclick="$(\'li#booking\').trigger(\'click\')">' + data.notifications.booking + ' New Bookings</a>');
                                } else {
                                    $('li#booking_notification').html('<a href="javascript:;">No New Bookings</a>');
                                }
                                /* Notifications updating code ends here */

                                $('div.success-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                                scrollTo('div.success-msg', 100, 'top');
                            } else {
                                $('div.error-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                                scrollTo('div.error-msg', 100, 'top');
                            }

                        } else {
                            $('div.error-msg').html('<label>Unable to suggest new date and time for appointment..!!</label>').fadeIn('slow');
                            scrollTo('div.error-msg', 100, 'top');
                        }
                    },
                    error: function(xhr, errorType, errorMsg) {
                        console.log(errorMsg)
                    },
                    complete: function() {
                        $('.default-load').fadeOut();
                    },
                })
            } else {
                $('div.error-msg').html('<label>Please enter new date and time to reschedule..!!</label>').fadeIn('slow');
                scrollTo('div.error-msg', 100, 'top');
            }
        } else {
            $('div.error-msg').html('<label>Please select a booking to reschedule..!!</label>').fadeIn('slow');
            scrollTo('div.error-msg', 100, 'top');
        }
    });
    /* Booking reschedule code ends here */
});

/* Function to hide suggestion fields */
function cancelSuggestion()
{
    // Close previous entries
    $('input.datetimepicker').fadeOut('slow');
    $('div.bookingTime').slideDown('slow');
    $('div.send').hide('slow');
    $('div.reschedule').show('slow');
}

/* Function to change booking status */
function changeBookingStatus(ids, status_id)
{
    if (ids.length > 0) {
        if (status_id != '') {

            $.ajax({
                url: OVEconfig.BASEURL + '/booking/update',
                type: 'POST',
                data: {action: 'booking_status', bookings: ids, status: status_id},
                beforeSend: function() {
                    $('.default-load').fadeIn();
                },
                success: function(data) {
                    if (data != "") {
                        data = JSON.parse(data);
                        if (data.status == '1') {

                            paginateBookings($('input#total_bookings').val());  // regenerate pagination

                            /* Notifications updating code starts here */
                            if ($('a#total_notification').next('span').length == 1) {
                                if (data.notifications.total > 0) {
                                    $('a#total_notification').next('span').html(data.notifications.total);
                                    $('a.icon-flag').children('span').html(data.notifications.total);
                                } else {
                                    $('a#total_notification').next('span').remove();
                                    $('a.icon-flag').children('span').html(data.notifications.total);
                                }
                            } else {
                                if (data.notifications.total > 0) {
                                    $('a#total_notification').after('<span>' + data.notifications.total + '</span>');
                                    $('a.icon-flag').children('span').html(data.notifications.total);
                                }
                            }

                            if (data.notifications.booking > 0) {
                                $('li#booking_notification').html('<a href="javascript:;" onclick="$(\'li#booking\').trigger(\'click\')">' + data.notifications.booking + ' New Bookings</a>');
                            } else {
                                $('li#booking_notification').html('<a href="javascript:;">No New Bookings</a>');
                            }
                            /* Notifications updating code ends here */

                            $('div.success-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                            scrollTo('div.success-msg', 100, 'top');
                            setTimeout("$('div.success-msg').slideUp('slow')", 5000);
                        } else {
                            $('div.error-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                            scrollTo('div.error-msg', 100, 'top');
                        }
                    } else {
                        $('div.error-msg').html('<label>Failed to change status of bookings..!!</label>').fadeIn('slow');
                        scrollTo('div.error-msg', 100, 'top');
                    }

                    $('.default-load').fadeOut();
                },
                error: function(xhr, errorType, errorMsg) {
                    console.log(errorMsg);
                }
            });
        } else {
            $('div.error-msg').html('<label>Please select status to be updated..!!</label>').fadeIn('slow');
            scrollTo('div.error-msg', 100, 'top');
        }
    } else {
        $('div.error-msg').html('<label>Please select at least 1 record to change status..!!</label>').fadeIn('slow');
        scrollTo('div.error-msg', 100, 'top');
    }
}

/* Booking details starts here */
function bookingDetails(element)  // function used in fullcalendar.min.js
{
    var booking_id = $(element).attr('id');
    var endDate = $(element).data('end');
    var duration = $(element).data('duration');

    $.ajax({
        url: OVEconfig.BASEURL + '/booking/details/',
        type: 'POST',
        data: {booking: booking_id},
        beforeSend: function() {
            $('.default-load').fadeIn();
        },
        dataType: 'json',
        success: function(data) {
            var results = data.data[0];

            if (data.status == 1) {

                (window.location.pathname.indexOf('practitioner') != -1) ? $('input#username').val(results.consumer_first_name + " " + results.consumer_last_name).prop('disabled', true) : $('input#username').val(results.sp_first_name + " " + results.sp_last_name).prop('disabled', true);
                $('input#booking_id').val(results.id);
                $('select#service_id').val(results.service_id).prop('disabled', true);
                $('select#duration_list').val('').prop('disabled', true).parents('div.row').hide();
                $('input#booking_time').val(formatDate(results.booking_status.booking_time, 'Day d/m/Y h:i A')).prop('disabled', true);
                //$('input#end_time').val(formatDate(endDate, 'Day d/m/Y h:i A')).prop('disabled', true);
                $('input#duration').val(duration+' Mins').prop('disabled', true).parents('div.row').show();
                $('select#service_address_id').val(results.service_address_id).prop('disabled', true);
                (window.location.pathname.indexOf('practitioner') != -1) ? $('select#booking_status').val(results.booking_status.status_id).prop('disabled', false):$('select#booking_status').val(results.booking_status.status_id).prop('disabled', true);
                (window.location.pathname.indexOf('practitioner') != -1) ? $('input#submit').show():$('input#submit').hide();
                $("div#booking-overlay").fadeIn("slow");
                scrollTo('div#booking-overlay', 100, 'top');
            } else {
                $("div#booking-overlay").fadeOut("slow");
                $('div.error-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                scrollTo('div.error-msg', 100, 'top');
            }

            $('.default-load').fadeOut();
        },
        error: function(xhr, errorType, errorMsg) {
            console.log(errorMsg);
            $('.default-load').fadeOut();
        }
    });
}
/* Booking details starts here */