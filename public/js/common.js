/**
 * @author dharmendra
 */
$(window).load(function() {
    $('.flexslider').flexslider({
        animation: "slide",
        controlNav: false
    });

    imageSlider();
    videoSlider();

    if ($(window).width() > 767) {
        $('#gallery').flexslider({
            animation: "slide",
            animationLoop: false,
            itemWidth: 200,
            itemMargin: 22,
            minItems: 5,
            maxItems: 5,
            directionNav: false
        });
    } else {
        $('#gallery').flexslider({
            animation: "slide",
            animationLoop: false,
            itemWidth: 200,
            itemMargin: 22,
            minItems: 2,
            maxItems: 2,
            directionNav: false
        });
    }
    var popattr;
    $('.pop-click').on('click', function() {
        popattr = $(this).data('popup');
        $('#overlay , #' + popattr).addClass('active');
    });
    $(document.body).on('click', '.close',function() {
        var content = $('#' + popattr).html();
        $('#' + popattr).html('');
        $('#' + popattr).html(content);
        $('#overlay , #' + popattr).removeClass('active');
    });
    $('.menu-open').click(function() {
        if ($('.menu-wrapper').css('display') == 'none') {
            $('.menu-open').addClass('active');
            $('.menu-wrapper').slideDown(500)
        } else {
            $('.menu-open').removeClass('active');
            $('.menu-wrapper').slideUp(500)
        }
    });

    //TAB JS START HERE
    $('.tab-wrapper').each(function() {
        var thisChildTrigger = $(this).find('.tab-trigger');
        var thisChildTarget = $(this).find('.tab-target');
        thisChildTrigger.find('li').eq(0).addClass('active');
        thisChildTarget.eq(0).show();
        $(thisChildTrigger).find('li').click(function() {
            var thisIndex = $(this).index();
            thisChildTrigger.find('li').removeClass('active');
            $(this).addClass('active');
            thisChildTarget.hide();
            thisChildTarget.eq(thisIndex).show();
        })
    });

    $('.question-wrapper li .ques').click(function() {
        $('.question-wrapper li .ans').slideUp();
        $(this).next('.ans').slideDown();
    });

    $('.tab-menu li').click(function() {
        $('.tab-menu li').removeClass('active arow')
        $(this).addClass('active arow');
        var Tname = $(this).attr('id');
        $('.tab-content > div').hide();
        $('.' + Tname).show();
    });

    $('.tab-data li .head').click(function() {
        $('.tab-data li .display-data').slideUp();
        $(this).next('.display-data').attr('display', 'inline-block').slideDown();
    })

    $('.find-practitioner .accordion h4').click(function() {
        //$('.find-practitioner .accordion .accordion-data').slideUp();
        $(this).next('.accordion-data').slideToggle();

    })

    $('.grid-list a').click(function() {
        var linkName = $(this).attr('class');
        if (linkName == "grid active") {
            var linkname = 'grid';
            $('#list-data').attr('scrollPagination', 'disabled');
            $('#grid-data').attr('scrollPagination', 'enabled');
        } else {
            var linkname = 'list';
            $('#grid-data').attr('scrollPagination', 'disabled');
            $('#list-data').attr('scrollPagination', 'enabled');
        }
        $('.find-right .find-record').hide();
        $('#' + linkname + '-view-data').fadeIn();
        paginate();
    });

    $('.gallery-upload span').click(function() {
        $(this).parent().parent('.about-gallery').children('.upload-image-form').fadeIn();
        scrollTo('.upload-image-form', 10, 'bottom');
    });
    $

    $('.upload-image-form .upload-close-btn').click(function() {
        $(this).parent().fadeOut();
    });

    $('.add-button span').click(function() {
        $('.add-Services').hide();
        var thisID = $(this).attr('rel');
        $('#sp_edit_id').val('');
        $('#sp_action').val('add');
        $('#service-head h3').html('Add New Services')
        $("#service_id").val('');
        $("#duration").val('');
        $("#price").val('');
        $('#' + thisID).fadeIn();
        scrollTo('#' + thisID, 10, 'bottom');
    });

    $('.add-Services span').click(function() {
        /* resetting all variables on  close */
        $('#sp_edit_id').val('');
        $('#sp_action').val('add');
        $('#service-head h3').html('Add New Services')
        $("#service_id").val('');
        $("#duration").val('');
        $("#price").val('');
        $('.add-Services').hide();
    });

    $('.dashboard-nav > ul > li > a').click(function() {
        $('.dashboard-submenu').slideUp();
        $('.dashboard-nav > ul > li > a').removeClass('active');
        $(this).addClass('active');
        if ($(this).parent().find('.dashboard-submenu').is(':hidden')) {
            $(this).parent().find('.dashboard-submenu').slideDown();
        }
    });
});

function imageSlider()
{
    if ($('.about-gallery-wrapper >ul >li').length > 4) {
        $('.about-gallery-wrapper').flexslider({
            animation: "slide",
            animationLoop: true,
            itemWidth: 135,
            itemMargin: 20,
            minItems: 4,
            maxItems: 4,
            controlNav: false,
            directionNav: false
        });
    }
}

function videoSlider()
{
    if ($('.about-video-wrapper >ul >li').length > 4) {
        $('.about-video-wrapper').flexslider({
            animation: "slide",
            animationLoop: true,
            itemWidth: 230,
            itemMargin: 20,
            minItems: 3,
            maxItems: 3,
            controlNav: false,
            directionNav: false
        });
    }
}

/* Function to trim content by words */
function trim_words(theString, numWords) {
    if (theString != null) {
        expString = theString.split(/\s+/, numWords);
        expFullString = theString.split(/\s+/);
        theNewString = (expFullString.length > numWords) ? expString.join(" ") + '...' : expString.join(" ");
        theNewString = (expString.length > 0) ? theNewString : theString;
        theNewString = (theNewString.length > (numWords * 15)) ? theNewString.substr(0, (numWords * 15)) + '...' : theNewString;
        return theNewString;
    } else {
        return theString;
    }
}

/* Window scroll code starts here */
function scrollTo(element, height, direction)
{
    if (direction == 'top') {
        $('html,body').animate({
            scrollTop: $(element).offset().top - height
        }, 1000);
    } else if (direction == 'bottom') {
        $('html,body').animate({
            scrollTop: $(element).offset().top - $(element).height() - height
        }, 1000);
    }
}
/* Window scroll code ends here */

/*	
 function showWhislist(){
 var user_id = $("input#user_id").val();
 
 if(user_id){
 $('div#wishlist-overlay').fadeIn('slow');	
 }
 else{
 $('input#last_url').val(OVEconfig.BASEURL+"/practitioner/view/"+user_id);
 window.location= OVEconfig.BASEURL+"/login";
 }
 return false;
 }
 */
/*
 $(document).ready(function(){
 $.ajax({
 url : OVEconfig.BASEURL+'/partners/index/',
 type : 'POST',
 success: function(data) {
 if( data.length > 0 ){
 $('#partners_list').html(data);
 }
 },
 error: function(xhr, errorType, errorMsg) {console.log(errorMsg)},
 });
 });
 */

/* Function to get states according to country */
function getStates(countryId, element)
{
    $.ajax({
        url: OVEconfig.BASEURL + '/practitioner/getstates/',
        type: 'POST',
        data: {country_id: countryId},
        dataType: 'json',
        beforeSend: function() {
            $('.default-load').fadeIn();
        },
        success: function(data) {
            var states = Array();
            states.push('<option value="">-Select state-</option>');
            
            $.each(data, function(key, value) {
                states.push('<option value="' + value.id + '">' + value.value + '</option>');
            });

            $(element).html(states.join(''));

            $('.default-load').fadeOut();
        },
        error: function(xhr, errorType, errorMsg) {
            console.log(errorMsg);
            $('.default-load').fadeOut();
        }
    });
}

/* Function to format date */
var weekday = new Array(7);
weekday[0] = "Sunday";
weekday[1] = "Monday";
weekday[2] = "Tuesday";
weekday[3] = "Wednesday";
weekday[4] = "Thursday";
weekday[5] = "Friday";
weekday[6] = "Saturday";

function formatDate(currentDate, format)
{
    currentDate = (typeof currentDate == 'string' && currentDate.indexOf('+00:00') != -1) ? currentDate.replace('+00:00', '') : currentDate;
    currentDate = (typeof currentDate == 'string' && currentDate.indexOf('-') != -1) ? currentDate.replace(/-/g, '/') : currentDate;
    if (typeof currentDate == 'string' && currentDate.indexOf('day') != -1) {

        var dateParts = currentDate.split(' ');
        var tempDate = dateParts[1].split('/');
        currentDate = Array();
        currentDate.push(dateParts[0]);
        currentDate.push(tempDate[2] + '/' + tempDate[1] + '/' + tempDate[0]);
        currentDate.push(dateParts[2]);
        currentDate.push(dateParts[3]);
        currentDate = currentDate.join(' ');
    }

    var new_date = (typeof currentDate == 'object') ? currentDate : new Date(currentDate);
    var h = new_date.getHours() % 12 || 12;
    h = (h < 10) ? '0' + h : h;
    var ms = new_date.getMinutes();
    ms = (ms < 10) ? '0' + ms : ms;
    var s = new_date.getSeconds();
    var A = (new_date.getHours() < 12) ? 'AM' : 'PM';

    format = format.replace(/Y/g, new_date.getFullYear());
    format = format.replace(/m/g, (new_date.getMonth() + 1));
    format = format.replace(/d/g, new_date.getDate());
    format = format.replace(/h/g, h);
    format = format.replace(/i/g, ms);
    format = format.replace(/s/g, s);
    format = format.replace(/A/g, A);
    format = format.replace(/Day/g, weekday[new_date.getDay()]);

    return format;
}

function getServiceduration(user_id, id) {
    $('input#service_date').val('- Select date -');
    if (id != '' && user_id != '') {

        $.ajax({
            url: OVEconfig.BASEURL + '/practitioner/serviceduration/',
            type: 'POST',
            data: {id: user_id, service_id: id},
            dataType: 'json',
            beforeSend: function() {
                $('div.error-msg, div.success-msg').slideUp();
                $('.default-load').fadeIn();
            },
            success: function(data) {
                if (data != '') {
                    if (data.status == '1') {
                        var options = Array();
                        options.push('<option value="">--- choose duration ---</option>');
                        $.each(data.durations, function(key, value) {
                            options.push('<option value="' + value.id + '">' + value.duration + '</option>');
                        });

                        if (options.length > 0) {
                            $('select#duration_list').html(options.join('')).prop('disabled', false);
                        }
                    } else {
                        $('select#duration_list').html('<option value="">--- choose duration ---</option>').prop('disabled', true);
                        $('div.error-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                        scrollTo('div.error-msg', 100, 'top');
                    }
                }

                $('.default-load').fadeOut();
            },
            error: function(xhr, errorType, errorMsg) {
                console.log(errorMsg);
                $('.default-load').fadeOut();
            },
        });

    } else {
        $('select#duration_list').html('<option value="">--- choose duration ---</option>').prop('disabled', true);
    }
}

function getprice(id, element)
{	//var id = $(element).next('select#duration_list').val();
    $('input#service_date').val('- Select date -');
    var divId = $(element).data('div');

    var duration = ($('select#duration_list:first').val() != '') ? $('select#duration_list:first').find('option:selected').text().replace(' Mins', '') : 0;
    (duration != 0) ? $('input#service_date').prop('disabled', false) : $('input#service_date').prop('disabled', true);
    $('div.xdsoft_datetimepicker').data({duration: duration});

    if (id != '') {

        var serviceDuration = $(element).find('option:selected').text().replace(' Mins', '');

        $.ajax({
            url: OVEconfig.BASEURL + '/practitioner/getserviceprice/',
            type: 'POST',
            data: {id: id},
            dataType: 'json',
            beforeSend: function() {
                $('.default-load').fadeIn();
            },
            success: function(data) {

                if (data) {
                    $('div#' + divId).html(data);
                    $('#priceDel').val(data);
                }
                $('.default-load').fadeOut();
            },
            error: function(xhr, errorType, errorMsg) {
                console.log(errorMsg);
                $('.default-load').fadeOut();
            },
        });

    }
}

/* Contact to practitioner code starts here */
function getContactDetails(practitionerId, emailId, number, type) {

    if (type != 3) {
        $("div#contact-overlay").fadeIn("slow");
        $("#id").val(practitionerId);
        $("#emailId").val(emailId);
        $("#number").val(number);
    }
    return false;

}
$('#contactdetail').on('click', function() {

    var content = $("#message").val();
    if (content == '') {
        $('div#mesg').html('<div class=error-msg ><label> Please write your message..!!</label></div>').fadeIn('slow');
        setTimeout("$('div#mesg').slideUp('slow')", 5000);
        return false;
    }
    var emailId = $("#emailId").val();
    var number = $("#number").val();
    var id = $("#id").val();
    if (emailId != '') {
        $.ajax({
            type: "post",
            url: OVEconfig.BASEURL + '/practitioner/contactPractitioner/',
            dataType: "json",
            data: {emailId: emailId, content: content, number: number, id: id},
            beforeSend: function() {
                $('.default-load').fadeIn();
            },
            success: function(data) {

                if (data.error == false) {
                    $('div#mesg').html('<div class=success-msg ><label>' + data.msg + '</label></div>').fadeIn('slow');
                    setTimeout("$('div#mesg').slideUp('slow')", 5000);
                }
                else {
                    $('div#mesg').html('<div class=error-msg ><label>' + data.msg + '</label></div>').fadeIn('slow');
                    setTimeout("$('div#mesg').slideUp('slow')", 5000);
                }
                $('.default-load').fadeOut();
            },
            error: function(xhr, errorType, errorMsg) {
                console.log(errorMsg)
            },
        });
    }
});
/* Contact to practitioner code ends here */

/* Function to readNotification starts here */
function readNotifications(tab)
{
    $.ajax({
        type: "post",
        url: OVEconfig.BASEURL + '/practitioner/readnotifications/',
        dataType: "json",
        data: {tab: tab},
        beforeSend: function() {
            $('.default-load').fadeIn();
        },
        success: function(data) {
            if (data && data.status == '1') {
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

                if (data.notifications.referrals > 0) {
                    $('li#referral_notification').html('<a href="' + OVEconfig.BASEURL + '/practitioner/referrals/" >' + data.notifications.referrals + ' Recommendations</a>');
                } else {
                    $('li#referral_notification').html('<a href="' + OVEconfig.BASEURL + '/practitioner/referrals/">No Recommendations</a>');
                }

                if (data.notifications.reviews > 0) {
                    $('li#review_notification').html('<a href="' + OVEconfig.BASEURL + '/practitioner/referrals/" >' + data.notifications.reviews + ' New Review</a>');
                } else {
                    $('li#review_notification').html('<a href="' + OVEconfig.BASEURL + '/practitioner/referrals/">No New Review</a>');
                }
                /* Notifications updating code ends here */
            }
            $('.default-load').fadeOut();
        },
        error: function(xhr, errorType, errorMsg) {
            console.log(errorMsg)
        },
    });
}
/* Function to readNotification ends here */

/* Code to fetch faq details starts here */
$(document).ready(function(){
    $('div.helpCenteAnwser').css('min-height', $('div.helpCenterQuestion').height());
});

$(document.body).on('click', 'a.fetch_faq', function() {
    var id = $(this).data('id');

    $.ajax({
        url: OVEconfig.BASEURL + '/helpcenter/faqdetails/',
        type: 'POST',
        dataType: 'json',
        data: {id: id},
        beforeSend: function() {
            $('.default-load').fadeIn();
        },
        success: function(data) {
            if (data && data != null && typeof data != 'undefined') {
                $('div.helpCenteAnwser').find('h4').html(data.question.replace(/\\/g, ''));
                $('div.helpCenteAnwser').find('div.questionWrap').html(data.answer.replace(/\\/g, ''));
                $('.default-load').fadeOut();
            }
        },
        error: function(xhr, errorType, errorMsg) {
            console.log(errorMsg);
            $('.default-load').fadeOut();
        },
    });
});
/* Code to fetch faq details ends here */

/* Function to appy slider starts here*/
function applySlider(height, width)
{
    $('.add-image').bjqs({
            animtype      : 'slide',
            height        : height,
            width         : width,
            responsive    : true,
            randomstart   : true,
            showcontrols : false, // show next and prev controls
            centercontrols : true, // center controls verically
            nexttext : 'Next', // Text for 'next' button (can use HTML)
            prevtext : 'Prev', // Text for 'previous' button (can use HTML)
            showmarkers : false, // Show individual slide markers
            centermarkers : true, // Center markers horizontally
            usecaptions : false, // show captions for images using the image title tag
                            
            // animation values
            animduration : 450, // how fast the animation are
            animspeed : 5000, // the delay between each slide
            automatic : true, // automatic

            // interaction values
            keyboardnav : true, // enable keyboard navigation
            hoverpause : true, // pause the slider on hover
        });
}