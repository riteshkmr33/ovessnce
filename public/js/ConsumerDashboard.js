$(function() {

    //getAllWishlists();

    /* initializing calender in calender tab */
    $("#calendar").on("click", function() {
        window.setTimeout(function() {
            $('#bookings_calendar').fullCalendar('changeView', 'agendaWeek');
        }, 500);
    });


    $('#deleteWishlist').on('click', function() {
        if ($('.checkWishlist').is(':checked')) {
            var id_list = new Array()
            $('.checkWishlist:checked').each(function() {
                id_list.push(this.value);
            });
            delWishlist(id_list);
            paginateWishlists($('input#total_wishlists').val()); // wishlist pagination
            //getAllWishlists();
        } else {
            $('.error-msg').html("<label>Please select atleat one</label>").fadeIn('slow');
            scrollTo('div.error-msg', 100, 'top');
        }
        return false;
    });

    if ($('input#total_bookings').val() > 0) {
        paginateBookings($('input#total_bookings').val()); // booking pagination
    } else {
        $('div.services-data > table#bookingTable >tbody').html('<tr class="recent"><td colspan="6"> No Records Found</td></tr>');
    }

    if ($('input#total_wishlists').val() > 0) {
        paginateWishlists($('input#total_wishlists').val()); // wishlist pagination
    } else {
        $('div.services-data > table#wishlistTable >tbody').html('<tr class="recent"><td colspan="5"> No Records Found</td></tr>');
    }
    if ($('input#total_contact_list').val() > 0) {
        paginateContactlist($('input#total_contact_list').val()); // wishlist pagination
    } else {
        $('div.services-data > table#contactTable >tbody').html('<tr class="recent"><td colspan="5"> No Records Found</td></tr>');
    }
});

$(document.body).on("click", "li#invitation", function() {
    setTimeout(applyAutoCompleteName('#getPractitioners', getPractitioner), 1000);
});

function getPractitioner(data) {
    $('input[name="referSp_id"]').val(data.key);
}

/* Check allWishlist*/
$('#checkAllWishlist').on('click', function(event) {
    if (this.checked) {
        $('.checkWishlist').each(function() {
            this.checked = true;
        });
    } else {
        $('.checkWishlist').each(function() {
            this.checked = false;
        });
    }
});

/* Pagination functions start here */
function paginateBookings(totalBookings)
{
    $("div#booking-pagination ul").pagination(totalBookings, {
        callback: bookingCallback,
        items_per_page: 5,
        num_display_entries: 10,
        num_edge_entries: 2,
        current_page: $('input#page').val(),
        prev_text: '&lt;',
        next_text: '&gt;'
    });
}

function paginateWishlists(totalWishlists)
{
    $("div#wishlist-pagination ul").pagination(totalWishlists, {
        callback: wishlistCallback,
        items_per_page: 5,
        num_display_entries: 10,
        num_edge_entries: 2,
        current_page: $('input#page').val(),
        prev_text: '&lt;',
        next_text: '&gt;'
    });
}


/* Pagination functions start here */
function paginateContactlist(totalContactlist)
{
    $("div#contact-pagination ul").pagination(totalContactlist, {
        callback: contactListCallback,
        items_per_page: 5,
        num_display_entries: 10,
        num_edge_entries: 2,
        current_page: $('input#page').val(),
        prev_text: '&lt;',
        next_text: '&gt;'
    });
}

function contactListCallback(page_index, jq)
{

    $("div.pagination-list ul li:empty").remove(); // Removing blank elements
    $('input#page').val(page_index);  // storing current page number

    var contactList = Array();
    var items_per_page = 5;
    var page = parseInt(page_index) + 1;

    $.ajax({
        url: OVEconfig.BASEURL + '/consumer/getcontactlist/',
        type: 'POST',
        async: false,
        data: {page: page, items: items_per_page, user_id: $('input#consumer_id').val()},
        dataType: 'json',
        success: function(data) {

            contactList = data;
        },
        error: function(xhr, errorType, errorMsg) {
            console.log(errorMsg)
        }
    });

    var max_elem = Math.min((page_index + 1) * items_per_page, contactList.length);
    var newcontent = Array();
    if (contactList.length > 0) {
        // Iterate through a selection of the content and build an HTML string
        // Iterate through a selection of the content and build an HTML string
        var users = [];
        for (var i = 0; i < max_elem; i++)
        {

            var user = (contactList[i].to_user_details != null && typeof contactList[i].to_user_details != 'undefined') ? JSON.parse(contactList[i].to_user_details) : null;
            var services = contactList[i].to_user_services;

            var serviceArray = [];
            if (services != null && typeof services != 'undefined') {
                $.each(services, function(key, servicesss) {
                    service = JSON.parse(servicesss);
                    serviceArray.push(service.category_name);

                });
            }

            (user != null && typeof user != 'undefined' && !$.inArray(user.user_id, users)) ? users.push(user.user_id) : '';
            newcontent.push('<tr class="recent">');
            (user != null && typeof user != 'undefined' && $.inArray(user.user_id, users)) ? newcontent.push('<td> <a href="' + OVEconfig.BASEURL + '/practitioner/view/' + user.user_id + '">' + user.first_name + ' ' + user.last_name + '</a> </td>') : newcontent.push('<td>NA</td>');
            (serviceArray.length > 0)?newcontent.push('<td>' + serviceArray.join(',') + '  </td>'):newcontent.push('<td> No Specialities </td>');

            newcontent.push('</tr>');

        }
    } else {
        newcontent.push('<tr class="recent"><td colspan="5"> No Records Found</td></tr>');
    }

    // Replace old content with new content
    $('div.services-data > table#contactTable >tbody').html(newcontent.join(''));

    // Prevent click eventpropagation
    return false;
}

function bookingCallback(page_index, jq)
{

    $("div.pagination-list ul li:empty").remove(); // Removing blank elements
    $('input#page').val(page_index);  // storing current page number

    var bookings = Array();
    var items_per_page = 5;
    var page = parseInt(page_index) + 1;

    $.ajax({
        url: OVEconfig.BASEURL + '/booking/getbooking/',
        type: 'POST',
        async: false,
        data: {page: page, items: items_per_page, user_id: $('input#consumer_id').val()},
        dataType: 'json',
        success: function(data) {
            bookings = data;
        },
        error: function(xhr, errorType, errorMsg) {
            console.log(errorMsg)
        }
    });

    var max_elem = Math.min((page_index + 1) * items_per_page, bookings.length);
    var newcontent = Array();

    var weekday = new Array(7);
    weekday[0] = "Sunday";
    weekday[1] = "Monday";
    weekday[2] = "Tuesday";
    weekday[3] = "Wednesday";
    weekday[4] = "Thursday";
    weekday[5] = "Friday";
    weekday[6] = "Saturday";

    if (bookings.length > 0) {
        // Iterate through a selection of the content and build an HTML string
        for (var i = 0; i < max_elem; i++)
        {
            var d = new Date();
            var current_timestamp = d.getTime() / 1000;
            //alert(current_timestamp);

            var booked_date = new Date(bookings[i].booking_status.booking_time.replace('+00:00', '').replace(/-/g, '/'));
            var booked_timestamp = booked_date.getTime() / 1000;
            //alert(booked_timestamp);

            var Y = booked_date.getFullYear();
            var m = booked_date.getMonth() + 1;
            var d = booked_date.getDate();
            var day = weekday[booked_date.getDay()];

            var n = booked_date;
            n.setDate(n.getDate() - 2);
            var locked_date = new Date(n);
            var locked_timestamp = locked_date.getTime() / 1000;
            //alert(locked_timestamp);

            var h = booked_date.getHours() % 12 || 12;
            h = (h < 10) ? '0' + h : h;
            var ms = booked_date.getMinutes();
            ms = (ms < 10) ? '0' + ms : ms;
            var s = booked_date.getSeconds();
            var A = (booked_date.getHours() < 12) ? 'AM' : 'PM';

            switch (bookings[i].booking_status.status_id) {
                case '4' :
                    var status = 'Confirmed';
                    break;

                case '5' :
                    var status = 'Pending Approval';
                    break;

                case '6' :
                    var status = 'Cancelled';
                    break;
                case '7' :
                    var status = 'Paid';
                    break
                default :
                    var status = 'Pending Approval';
                    break;
            }

            var avatar_url = (typeof bookings[i].sp_avtar_url == "undefined" || bookings[i].sp_avtar_url == "None" || bookings[i].sp_avtar_url == "" || bookings[i].sp_avtar_url == null) ? '/img/profile-pic-1.jpg' : bookings[i].sp_avtar_url.replace('Media', 'Media_thumb');

            newcontent.push('<tr class="recent">');
            newcontent.push('<td>' + bookings[i].id + '</td>');
            newcontent.push('<td><span class="profile"><img src="' + avatar_url + '" alt="" /></span>' + bookings[i].sp_first_name.replace(/\\/g, '') + ' ' + bookings[i].sp_last_name.replace(/\\/g, '') + '</td>');
            newcontent.push('<td><div class="bookingTime">' + formatDate(bookings[i].booking_status.booking_time, 'Day d/m/Y h:i A') + '</div>');
            newcontent.push('<input class="datetimepicker" readonly="" style="display: none;" />');
            newcontent.push('</td>');
            newcontent.push('<td>' + bookings[i].category_name + '</td>');
            newcontent.push('<td>' + status + '</td>');
            newcontent.push('<td>');

            if ((bookings[i].booking_status.user_id != bookings[i].user_id || bookings[i].booking_status.status_id == '4') && bookings[i].booking_status.confirmations < 3 && bookings[i].booking_status.status_id != '6' && (current_timestamp <= locked_timestamp || bookings[i].booking_status.status_id == '5') && current_timestamp < booked_timestamp) {
                //if ((bookings[i].booking_status.user_id != bookings[i].user_id || bookings[i].booking_status.status_id == '4') && bookings[i].booking_status.confirmations < 3 && bookings[i].booking_status.status_id != '6' && current_timestamp <= locked_timestamp) {
                newcontent.push('<div class="update reschedule">');
                newcontent.push('<span class="btn-rating btn-reschedule reschedule" id="' + bookings[i].id + '" data-durtn="' + bookings[i].duration + '" data-sp="' + bookings[i].service_provider_id + '" data-address="' + bookings[i].service_address_id + '">New Date & Time</span>');  // data-address="'+bookings[i].service_address_id+'"  added by Ritesh
                (bookings[i].booking_status.status_id == '5') ? newcontent.push('<span class="btn-rating btn-ok bookingConfirm" id="' + bookings[i].id + '">Confirm</span>') : '';
                (bookings[i].booking_status.status_id == '5') ? newcontent.push('<span class="btn-rating btn-cancel bookingCancel" id="' + bookings[i].id + '">Cancel</span>') : '';
                newcontent.push('</div>');
                newcontent.push('<div class="update send" style="display:none;">');
                newcontent.push('<span class="btn-rating btn-ok bookingReschedule" id="' + bookings[i].id + '" >Confirm</span>');
                newcontent.push('<span class="btn-rating btn-cancel cancel" id="' + bookings[i].id + '">Cancel</span>');
                newcontent.push('</div>');
            } else if (current_timestamp >= booked_timestamp && bookings[i].booking_status.status_id == '4') {

                // if past booking show rating and review button 
                newcontent.push('<a href="' + OVEconfig.BASEURL + '/consumer/ratings/' + bookings[i].service_provider_id + '/?review=1&s_id=' + bookings[i].service_provider_service_id + '" class="btn-rating" >Rating</a>');
                newcontent.push('<a href=' + OVEconfig.BASEURL + '/practitioner/view/' + bookings[i].service_provider_id + '/?tab=review&review=1&s_id=' + bookings[i].service_provider_service_id + ' class="btn-rating" >review</a>');
            } else if (current_timestamp >= locked_timestamp && bookings[i].booking_status.status_id == '4') {
                newcontent.push('<span class="response-pending">Booking Logged</span>');
            } else if (current_timestamp > booked_timestamp && bookings[i].booking_status.status_id == '5') {
                newcontent.push('<span class="response-pending">Booking Expired</span>');
            } else if (current_timestamp < booked_timestamp && bookings[i].booking_status.status_id == '4') {
                newcontent.push('<span class="response-pending">Booking Logged</span>');
            } else if (bookings[i].booking_status.status_id == '6') {
                newcontent.push('<span class="response-pending">Booking Cancelled</span>');
            } else {
                newcontent.push('<span class="response-pending">Response Pending</span>');
            }

            /*if (current_timestamp <= locked_timestamp && bookings[i].booking_status.status_id == 5) {
             
             // if booking still not locked i.e 48 hours before booked date 
             
             if (bookings[i].booking_status.user_id != bookings[i].user_id) {
             newcontent.push('<div class="update reschedule">');
             newcontent.push('<span class="btn-reschedule reschedule update" id="' + bookings[i].id + '" data-durtn="' + bookings[i].duration + '" data-sp="' + bookings[i].service_provider_id + '">D</span>');
             (bookings[i].booking_status.status_id != 4) ? newcontent.push('<span class="btn-ok bookingConfirm" id="' + bookings[i].id + '">D</span>') : '';
             (bookings[i].booking_status.status_id != 6) ? newcontent.push('<span class="btn-cancel bookingCancel" id="' + bookings[i].id + '">D</span>') : '';
             newcontent.push('</div>');
             } else {
             newcontent.push('<span class="response-pending">Response Pending</span>');
             }
             
             newcontent.push('<div class="update send" style="display:none;">');
             newcontent.push('<span class="btn-ok bookingReschedule" id="' + bookings[i].id + '" >D</span>');
             newcontent.push('<span class="btn-cancel cancel" id="' + bookings[i].id + '">D</span>');
             newcontent.push('</div>');
             newcontent.push('<span class="btn-ok bookingReschedule update" id="' + bookings[i].id + '" style="display:none;">D</span>');
             } else if (current_timestamp <= locked_timestamp && bookings[i].booking_status.status_id == 4) {
             
             newcontent.push('<div class="update reschedule">');
             newcontent.push('<span class="btn-reschedule reschedule update" id="' + bookings[i].id + '" data-durtn="' + bookings[i].duration + '" data-sp="' + bookings[i].service_provider_id + '">D</span>');
             (bookings[i].booking_status.status_id != 4) ? newcontent.push('<span class="btn-ok bookingConfirm" id="' + bookings[i].id + '">D</span>') : '';
             (bookings[i].booking_status.status_id != 6) ? newcontent.push('<span class="btn-cancel bookingCancel" id="' + bookings[i].id + '">D</span>') : '';
             newcontent.push('</div>');
             } else if (bookings[i].booking_status.status_id == 6) {
             newcontent.push('<span class="response-cancelled">Rejected</span>');
             } else if (current_timestamp >= booked_timestamp && bookings[i].booking_status.status_id == 4) {
             
             // if past booking show rating and review button 
             newcontent.push('<a href="' + OVEconfig.BASEURL + '/consumer/ratings/' + bookings[i].service_provider_id + '/?review=1&s_id=' + bookings[i].service_provider_service_id + '" class="btn-rating" target="_blank" >Rating</a>');
             newcontent.push('<a href=' + OVEconfig.BASEURL + '/practitioner/view/' + bookings[i].service_provider_id + '/?tab=review&review=1&s_id=' + bookings[i].service_provider_service_id + ' class="btn-rating" target="_blank" >review</a>');
             
             } else if (current_timestamp < booked_timestamp && bookings[i].booking_status.status_id == 4) {
             newcontent.push('<span class="response-pending">Booking Logged</span>');
             } else {
             newcontent.push('<span class="response-pending">Response Pending</span>');
             }*/

            newcontent.push('</td>');
            newcontent.push('</tr>');
        }
    } else {
        newcontent.push('<tr class="recent"><td colspan="6"> No Records Found</td></tr>');
    }

    // Replace old content with new content
    $('div.services-data > table#bookingTable >tbody').html(newcontent.join(''));
    applyCalendar('.datetimepicker');
    // Prevent click eventpropagation
    return false;
}

function wishlistCallback(page_index, jq)
{

    $("div.pagination-list ul li:empty").remove(); // Removing blank elements
    $('input#page').val(page_index);  // storing current page number

    var wishlists = Array();
    var items_per_page = 5;
    var page = parseInt(page_index) + 1;

    $.ajax({
        url: OVEconfig.BASEURL + '/consumer/getwishlist/',
        type: 'POST',
        async: false,
        data: {page: page, items: items_per_page, user_id: $('input#consumer_id').val()},
        dataType: 'json',
        success: function(data) {
            wishlists = data;
        },
        error: function(xhr, errorType, errorMsg) {
            console.log(errorMsg)
        }
    });

    var max_elem = Math.min((page_index + 1) * items_per_page, wishlists.length);
    var newcontent = Array();

    if (wishlists.length > 0) {
        // Iterate through a selection of the content and build an HTML string
        for (var i = 0; i < max_elem; i++)
        {
            var user = JSON.parse(wishlists[i].user);
            var service = JSON.parse(wishlists[i].service);
            var service_duration = JSON.parse(wishlists[i].service_duration);
            // console.log(wishlists[i]);
            switch (wishlists[i].status_id) {
                case '4' :
                    var status = 'Confirmed';
                    break;

                case '5' :
                    var status = 'Pending Approval';
                    break;

                case '6' :
                    var status = 'Cancelled';
                    break;

                default :
                    var status = 'Pending Approval';
                    break;
            }

            newcontent.push('<tr class="recent">');
            var avtar = (typeof user.avtar_url != 'undefined' && user.avtar_url != "None" && user.avtar_url != "" && user.avtar_url != null)?user.avtar_url.replace('Media', 'Media_thumb'):'/img/profile-pic-1.jpg';
            (typeof user != 'undefined' && user != null) ? newcontent.push('<td><a href="'+OVEconfig.BASEURL+'/practitioner/view/'+user.user_id+'"><span><img src="' + avtar + '" alt="" /></span>' + user.first_name + ' ' + user.last_name + '</a></td>') : newcontent.push('<td></td>');
            newcontent.push('<td>' + service.category_name + '</td>');
            newcontent.push('<td>' + service_duration.duration + ' MIN </td>');
            newcontent.push('<td>$' + wishlists[i].current_price + '</td>');
            newcontent.push('<td>');
            newcontent.push('<div class="select-form">');
            newcontent.push('<form>');
            newcontent.push('<label for="select-all">');
            newcontent.push('<input class="checkWishlist" type="checkbox" value="' + wishlists[i].id + '"><span></span>');
            newcontent.push('</label>');
            newcontent.push('</form>');
            newcontent.push('</div><span onclick="delWishlist([' + wishlists[i].id + '])" class="delete" >D</span>');
            newcontent.push('</td>');
            newcontent.push('</tr>');
        }
    } else {
        newcontent.push('<tr class="recent"><td colspan="5"> No Records Found</td></tr>');
    }

    // Replace old content with new content
    $('div.services-data > table#wishlistTable >tbody').html(newcontent.join(''));

    // Prevent click eventpropagation
    return false;
}

function delWishlist(id) {

    if (confirm("Are you sure?")) {
        if (id != '') {
            $.ajax({
                url: OVEconfig.BASEURL + '/consumer/deleteWishlist/',
                type: 'POST',
                dataType: 'json',
                data: {id: id},
                beforeSend: function() {
                    $('.default-load').fadeIn();
                },
                success: function(data) {
                    $('.default-load').fadeOut();
                    if (data.error == false) {
                        $('input#total_wishlists').val(parseInt($('input#total_wishlists').val()) - (id.length));
                        getAllWishlists();
                        $('.error-msg').html('');
                        $('.success-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                        scrollTo('div.error-msg', 100, 'top');
                    } else {
                        $('.success-msg').html('');
                        $('.error-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                        scrollTo('div.error-msg', 100, 'top');

                    }
                },
                error: function(xhr, errorType, errorMsg) {
                    console.log
                }
            });

            return false;
        } else {
            return false;
        }
    } else {
        return false;
    }

}

function getAllWishlists()
{
    /* call to get wishlist with pagination - starts here */
    if ($('input#total_wishlists').val() > 0) {
        // wishlist pagination
        $("div#wishlist-pagination ul").pagination($('input#total_wishlists').val(), {
            callback: wishlistCallback,
            items_per_page: 5,
            num_display_entries: 10,
            num_edge_entries: 2,
            prev_text: '&lt;',
            next_text: '&gt;'
        });
    } else {
        $('div.services-data > table#wishlistTable >tbody').html('<tr class="recent"><td colspan="5"> No Records Found</td></tr>');
    }
}

/* Send Invitation code starts here */
$(document.body).on('click', 'input#sendInvitation', function() {
    $('div.error-msg, div.success-msg').slideUp();
    var email = $('input#inviteEmail').val();
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    if (pattern.test(email)) {
        $.ajax({
            url: OVEconfig.BASEURL + '/consumer/sendInvitation/',
            type: 'POST',
            data: {action: 'invite', user: $('input#consumer_id').val(), email: email},
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

/* Referal code starts here */
$(document.body).on('click', 'input#refersp', function() {
    $('div.error-msg, div.success-msg').slideUp();
    var email = $('input#referEmail').val();
    var sp_id = $('#referSp_id').val();
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    if (pattern.test(email)) {

        if (sp_id == '') {
            $('div.error-msg').html('<label>Please select a service provider to refer</label>').fadeIn('slow');
            scrollTo('div.error-msg', 100, 'top');
            return false;
        }

        $.ajax({
            url: OVEconfig.BASEURL + '/consumer/refersp/',
            type: 'POST',
            data: {action: 'refer', user: sp_id, email: email},
            beforeSend: function() {
                $('.default-load').fadeIn();
            },
            success: function(data) {
                if (data != "") {
                    data = JSON.parse(data);
                    if (data.status == '1') {
                        $('input#referEmail').val('');
                        $('div.success-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                        scrollTo('div.success-msg', 100, 'top');
                        setTimeout("$('div.success-msg').slideUp('slow')", 5000);
                    } else {
                        $('div.error-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                        scrollTo('div.error-msg', 100, 'top');
                    }
                } else {
                    $('div.error-msg').html('<label>Failed to Refer..!!</label>').fadeIn('slow');
                    scrollTo('div.error-msg', 100, 'top');
                }
                $('.default-load').fadeOut();
            },
            error: function(xhr, errorType, errorMsg) {
                console.log(errorMsg)
            }
        });

    } else {
        $('div.error-msg').html('<label>Please enter a valid email address..!!</label>').fadeIn('slow');
        scrollTo('div.error-msg', 100, 'top');
    }
    return false;
});
/* Referal code ends here */

/* Profile Save action start here */
$('.about-me .profile-edit').click(function() {
    $('div.error-msg, div.success-msg').slideUp();
    if ($(this).data('action') == 'save') {
        /* validation starts here */
        var error = [];
        error.status = false;
        $('.profileData').each(function(index, element) {
            var id = $(this).attr('id');
            if (element.tagName != 'SELECT' && element.value == '') {
                error.status = true;
                error.push('<label style="text-transform:capitalize;">' + element.placeholder + ' is required .</label>');
            }
        });
        if (error.status == false) {
            $('.default-load').fadeIn();
            $.ajax({
                url: OVEconfig.BASEURL + '/consumer/update/?' + $('form.profileForm').serialize(),
                type: 'POST',
                data: {action: 'profile'},
                success: function(data) {
                    data = JSON.parse(data);
                    if (data.status == '1') {
                        $('div.success-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                        setTimeout("$('div.success-msg').slideUp('slow')", 5000);
                        // returning new values
                        $('.profileData').each(function(index, element) {
                            var id = $(this).attr('id');
                            $(".consumerName h3").html($(".profileForm #first_name").val() + ' ' + $(".profileForm #last_name").val());
                            if (element.tagName == 'INPUT') {
                                if ($(element).attr('type') == 'text') {
                                    ($(this).val() != '') ? $('div#' + id).html($(this).val()) : $('div#' + id).html('Not Available');
                                } else if ($(element).attr('type') == 'radio') {
                                    ($('input[id=' + id + '][value="M"]').is(':checked')) ? $('div#' + id).html('Male') : $('div#' + id).html('Female');
                                }

                            } else if (element.tagName == 'SELECT') {
                                if ($(element).prop('multiple') == true) {
                                    selected = Array();
                                    $(this).children('option:selected').each(function() {
                                        selected.push($(this).text());
                                    });
                                    (selected.length > 0) ? $('div#' + id).html(selected.join(', ')) : $('div#' + id).html('Not Available');
                                } else {
                                    ($(this).children('option:selected').text() != "" && $(this).children('option:selected').text().indexOf('Select') == -1) ? $('div#' + id).html($(this).children('option:selected').text()) : $('div#' + id).html('Not Available');
                                }
                            }

                        });

                        scrollTo('div.success-msg', 100, 'top');
                        $('#profile_text, .profile-data-edit').fadeIn('slow');
                        $('#profile_form, .profile-data-save').fadeOut();

                    } else {
                        var errors = Array();
                        $.each(data.errors, function(key, value) {
                            errors.push('<label style="text-transform:capitalize;">' + key.replace('_', ' ') + ' - ' + value + '</label>');
                        });
                        $('div.error-msg').html(errors.join('')).fadeIn('slow');

                        scrollTo('div.error-msg', 100, 'top');
                    }

                    $('.default-load').fadeOut();
                },
                error: function(xhr, errorType, errorMsg) {
                    console.log
                }
            });
        } else {
            var errors = Array();
            $('div.error-msg').html(error.join('')).fadeIn('slow');
            scrollTo('div.error-msg', 100, 'top');
        }

    } else {
        // assigning current values
        $('.profileData').each(function(index, element) {
            var id = $(this).attr('id');
            if (element.tagName == 'INPUT') {
                if ($(element).attr('type') == 'text') {
                    if ($('div#' + id).html() != 'Not Available') {
                        $(this).val($('div#' + id).html());
                    }
                } else if ($(element).attr('type') == 'radio') {
                    ($('div#' + id).html() == 'Male') ? $('input[id=' + id + '][value="M"]').prop('checked', true) : $('input[id=' + id + '][value="F"]').prop('checked', true);
                }
            } else if (element.tagName == 'SELECT') {
                if ($(element).prop('multiple') == true) {
                    var selected = $('div#' + id).html().split(', ');
                    $(this).children('option').each(function() {
                        (selected.indexOf($(this).text()) != -1) ? $(this).prop('selected', true) : '';
                    });
                }
            }

        });
        $('#profile_text, .profile-data-edit').fadeOut();
        $('#profile_form, .profile-data-save').fadeIn('slow');
    }
});

/* Profile Save action ends here */

/* Address save action start here */

$('.about-me .address-edit').click(function() {
    $('div.error-msg, div.success-msg').slideUp();
    if ($(this).data('action') == 'save') {
        $('.default-load').fadeIn();
        $.ajax({
            url: OVEconfig.BASEURL + '/consumer/update/?' + $('form.address_form').serialize(),
            type: 'POST',
            data: {action: 'address'},
            success: function(data) {
                data = JSON.parse(data);
                if (data.status == '1') {
                    $('div.success-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                    setTimeout("$('div.success-msg').slideUp('slow')", 5000);
                    $('.addressData').each(function(index, element) {
                        var id = $(this).attr('id');
                        if (element.tagName == 'INPUT') {
                            if ($(element).attr('type') == 'text') {
                                ($(this).val() != '') ? $('div#' + id).html($(this).val()) : $('div#' + id).html('Not Available');
                            }
                        } else if (element.tagName == 'SELECT') {
                            ($(this).children('option:selected').text() != "" && $(this).children('option:selected').text().indexOf('Select') == -1) ? $('div#' + id).html($(this).children('option:selected').text()) : $('div#' + id).html('Not Available');
                        }

                    });

                    scrollTo('div.success-msg', 100, 'top');
                    $('#address_text, .address-data-edit').fadeIn('slow');
                    $('.address_form, .address-data-save').fadeOut();

                } else {
                    var errors = Array();
                    $.each(data.errors, function(key, value) {
                        errors.push('<label style="text-transform:capitalize;">' + key.replace('_', ' ') + ' - ' + value + '</label>');
                    });
                    $('div.error-msg').html(errors.join('')).fadeIn('slow');

                    scrollTo('div.error-msg', 100, 'top');
                }

                $('.default-load').fadeOut();
            },
            error: function(xhr, errorType, errorMsg) {
                console.log
            }
        });

    } else {
        $('.addressData').each(function(index, element) {
            var id = $(this).attr('id');
            if (element.tagName == 'INPUT') {
                if ($(element).attr('type') == 'text') {
                    if ($('div#' + id).html() != 'Not Available') {
                        $(this).val($('div#' + id).html());
                    }
                }
            } else if (element.tagName == 'SELECT') {
                var selected = $('div#' + id).html();
                $(this).children('option').each(function() {
                    ($(this).text() == selected) ? $(this).prop('selected', true) : '';
                });

            }

        });
        $('#address_text, .address-data-edit').fadeOut();
        $('.address_form, .address-data-save').fadeIn('slow');
        setTimeout("applyAutoComplete('.address-autofill', ['zip_code'])", 1000);
    }
});

/* Address save action ends here */

/* Contact save action start here */

$('.about-me .contact-edit').click(function() {
    $('div.error-msg, div.success-msg').slideUp();
    if ($(this).data('action') == 'save') {
        var error = [];
        error.status = false;
        $('.contactForm').each(function(index, element) {
            var id = $(this).attr('id');
            if (element.id == "home_phone" && element.value == '') {
                error.status = true;
                error.push('<label style="text-transform:capitalize;">' + element.placeholder + ' is required .</label>');
            }
        });
        if (error.status == false) {
            $('.default-load').fadeIn();
            $.ajax({
                url: OVEconfig.BASEURL + '/consumer/update/?' + $('form#contact_form').serialize(),
                type: 'POST',
                data: {action: 'contact'},
                success: function(data) {
                    //console.log(data)
                    data = JSON.parse(data);
                    if (data.status == '1') {
                        $('div.success-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                        setTimeout("$('div.success-msg').slideUp('slow')", 5000);
                        // returning new values
                        $('.contactForm').each(function(index, element) {
                            var id = $(this).attr('id');
                            if (element.tagName == 'INPUT') {
                                if ($(element).attr('type') == 'text') {
                                    ($(this).val() != '') ? $('div#' + id).html($(this).val()) : $('div#' + id).html('Not Available');
                                }
                            }

                        });

                        scrollTo('div.success-msg', 100, 'top');
                        $('#contact_form, .contact-data-save').fadeOut();
                        $('#contact_text, .contact-data-edit').fadeIn('slow');

                    } else {
                        var errors = Array();
                        $.each(data.errors, function(key, value) {
                            errors.push('<label style="text-transform:capitalize;">' + key.replace('_', ' ') + ' - ' + value + '</label>');
                        });
                        $('div.error-msg').html(errors.join('')).fadeIn('slow');

                        scrollTo('div.error-msg', 100, 'top');
                    }
                    $('.default-load').fadeOut();
                },
                error: function(xhr, errorType, errorMsg) {
                    console.log
                }
            });
        } else {
            var errors = Array();
            $('div.error-msg').html(error.join('')).fadeIn('slow');
            scrollTo('div.error-msg', 100, 'top');
        }
    } else {
        $('.contactForm').each(function(index, element) {
            var id = $(this).attr('id');
            if (element.tagName == 'INPUT') {
                if ($(element).attr('type') == 'text') {
                    if ($('div#' + id).html() != 'Not Available') {
                        $(this).val($('div#' + id).html());
                    }
                }
            }
        });
        $('#contact_text, .contact-data-edit').fadeOut();
        $('#contact_form, .contact-data-save').fadeIn('slow');
    }
});

/* Contact save action ends here */

/* Image Upload box show code start here*/

$(document.body).on('click', '.profile-wrapper input.black', function() {
    $('.upload-file').slideDown();
})

// hide image upload box 
$(document.body).on('click', '.upload-file span.hide', function() {
    $('.upload-file').slideUp();
})

/* Image Upload box show code start here*/

/* Image upload code start here */

$(document.body).on('click', '.fileSubmit', function() {
    $('div.error-msg, div.success-msg').slideUp();
    var valid = true;
    var errorMssg = Array();
    var validFileExtensions = [".jpg", ".jpeg", ".bmp", ".gif", ".png"];

    $(".upload-file").find('input').each(function() {

        if ($(this).attr('type') == 'file' && $(this).val() != "") {
            if (validFileExtensions.indexOf($(this).val().substr($(this).val().indexOf('.'), $(this).val().length).toLowerCase()) == -1) {
                valid = false;
                errorMssg.push('<label>Please upload a valid image.</label>');
            }

            if (this.files[0].size > 2048000) {
                errorMssg.push('<label>Please upload image less than 2MB.</label>');
                valid = false;
            }
        }
    });

    if (valid == false) {
        $('div.error-msg').html(errorMssg.join('')).fadeIn('slow');
        scrollTo('div.error-msg', 100, 'top');
        return false;
    } else {
        $('.default-load').fadeIn();
        $("#avatarForm").ajaxForm({
            success: function(data) {
                var image = JSON.parse(data);
                //console.log(image);
                if (data != "") {
                    if (image.status == '1') {
                        if (data.image_url != '') {
                            $(".profile-wrapper img").attr("src", image.image_url);
                            $('input#delete_avtar').css('opacity', 100);
                        }
                        $('.upload-file').slideUp();
                        $('div.success-msg').html('<label>' + image.msg + '</label>').fadeIn('slow');
                        setTimeout("$('div.success-msg').slideUp('slow')", 5000);
                        scrollTo('div.success-msg', 100, 'top');

                    } else {
                        $('div.error-msg').html('<label>' + image.msg + '</label>').fadeIn('slow');
                        scrollTo('div.error-msg', 100, 'top');
                    }
                } else {
                    $('div.error-msg').html('<label>Failed to upload image..!!</label>').fadeIn('slow');
                    scrollTo('div.error-msg', 100, 'top');
                }

                $('#consumer-avatar').val(""); // clearing form elements
                $('div.upload-close-btn').trigger('click');
                $('.default-load').fadeOut();
            }
        }).submit();
    }
    return false;
});
/* Image upload code end here */


/* Ratings Save action start here */
$(document.body).on('click', '.submit-ratings', function() {
    $('div.error-msg, div.success-msg').slideUp();
    var serviceProvider = $("#serviceProviderId").val();
    var serviceId = $("#serviceId").val();
    var error = true;

    if (serviceProvider != "" && serviceId != "") {
        $('.default-load').fadeIn();
        $.ajax({
            url: OVEconfig.BASEURL + '/consumer/saveRatings/?serviceProvider=' + serviceProvider + '&service_id=' + serviceId,
            type: 'POST',
            data: $('form.ratingsForm').serialize(),
            success: function(data) {
                data = JSON.parse(data);
                if (data.status == '1') {
                    $('div.success-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                    setTimeout("$('div.success-msg').slideUp('slow')", 5000);
                    $('div#back-to-practitioner').fadeIn();

                    /*$(':radio').each(function(index, value) {
                     $(this).attr('disabled', 'disabled');
                     if (!$(this).is(':checked')) {
                     $(this).next().addClass('stardisabled');
                     }
                     });*/

                    $("form input:radio").attr('disabled', 'disabled');
                    $("form .submit-rating").fadeOut();
                    scrollTo('div.success-msg', 100, 'top');
                } else {
                    var errors = Array();
                    $.each(data.errors, function(key, value) {
                        errors.push('<label style="text-transform:capitalize;">' + key.replace('_', ' ') + ' - ' + value + '</label>');
                    });
                    $('div.error-msg').html(errors.join('')).fadeIn('slow');

                    scrollTo('div.error-msg', 100, 'top');
                }
                $('.default-load').fadeOut();
            },
            error: function(xhr, errorType, errorMsg) {
                console.log
            }
        });
    }
    return false;
});
/* Ratings Save action ends here */

/* newsletter submit - starts here */
$('#newsletter-submit').on('click', function() {
    $('div.error-msg, div.success-msg').slideUp();
    if ($("input[name='newsletter-chk']:checked").val()) {
        alert($("input[name='newsletter-chk']:checked").val());
        $.ajax({
            url: OVEconfig.BASEURL + '/consumer/settings/',
            type: 'POST',
            dataType: 'json',
            data: {newletter_chk: $("input[name='newsletter-chk']:checked").val(), action: 'newletter-chk'},
            beforeSend: function() {
                $('.default-load').fadeIn();
            },
            success: function(data) {
                $('.default-load').fadeOut();
                if (data) {
                    if (data.error) {
                        $('.error-msg').html("<label>" + data.msg + "</label>").fadeIn('slow');
                        scrollTo('div.error-msg', 200, 'top');
                    } else {
                        $('.success-msg').html("<label>" + data.msg + "</label>").fadeIn('slow');
                        scrollTo('div.success-msg', 200, 'top');
                    }
                }
            },
            error: function(xhr, errorType, errorMsg) {
                console.log
            }
        });

    } else {
        $('.error-msg').html("<label>Please select atleat one option</label>").fadeIn('slow');
        scrollTo('div.error-msg', 100, 'top');
    }
    return false;
    exit;
});
/* newsletter submit - ends here */

/* Delete avtar code starts here */
$(document.body).on('click', 'input#delete_avtar', function() {
    if (confirm("Are you sure want to delete your avtar image..??")) {
        $.ajax({
            url: OVEconfig.BASEURL + '/consumer/update/',
            type: 'POST',
            data: {action: 'delete_avtar'},
            beforeSend: function() {
                $('.default-load').fadeIn();
            },
            success: function(data) {
                if (data != '') {
                    data = JSON.parse(data);
                    if (data.status == '1') {
                        $(".profile-wrapper img").attr("src", OVEconfig.BASEURL + '/img/profile-pic.jpg');
                        $('input#delete_avtar').css('opacity', 0);
                        $('.success-msg').html("<label>" + data.msg + "</label>").fadeIn('slow');
                        setTimeout("$('div.success-msg').slideUp('slow')", 5000);
                        scrollTo('div.success-msg', 100, 'top');
                    } else {
                        $('.error-msg').html("<label>" + data.msg + "</label>").fadeIn('slow');
                        scrollTo('div.error-msg', 100, 'top');
                    }
                } else {
                    $('.error-msg').html("<label>Unable to delete avtar image..!!</label>").fadeIn('slow');
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
});
/* Delete avtar code ends here */