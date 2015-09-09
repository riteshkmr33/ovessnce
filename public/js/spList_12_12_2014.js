$(document).ready(function() {

    $('.grid-list .list').addClass('active');

    $('.grid-list a').on('click', function() {
        $(this).parent().children('a.active').removeClass('active');
        $(this).addClass('active');
    });

    getList();   // call to listing practitioners

    $('.fill-type-dis ul li').on('click', function() {
        if ($(this).hasClass('active')) {
            $(this).removeClass('active')
        } else {
            $(this).attr('class', 'active');
        }
        getList();
    });

    $('.fill-star ul li').on('click', function() {
        if ($(this).hasClass('active')) {
            $(this).removeClass('active')
        } else {
            $(this).attr('class', 'active');
        }
        getList();
    });

    $(document.body).on('blur', 'input[name="practitioners_name"],input[name="company_name"],input[name="city"],input[name="zip"],input[name="q"]', function(e) {
        getList();
    });

    /* code to reset the complete filter - starts */
    $('input[name="reset_filter"]').on('click', function() {

        $('.fill-type-dis ul li').each(function() {
            if ($(this).hasClass('active')) {
                $(this).removeClass('active');
            }
        });

        $('.fill-star ul li').each(function() {
            if ($(this).hasClass('active')) {
                $(this).removeClass('active');
            }
        });

        $('form').each(function() {
            this.reset()
        }); // reset all froms
        $(".filters-tab :input[type='text']").val('');
        $(':radio').prop('checked', false); // uncheck all radio buttons
        $('#distance').val(''); // distance slider set to null
        $('#maxPrice').val(''); // distance slider set to null
        $('#minPrice').val(''); // distance slider set to null
        $('#treatmentLength').val(''); // distance slider set to null
        $('#country').select2('val', '');
        $('#state').select2('val', '');
        //Reset above search value
        $('#search_location').select2('val', '');
        $('#treatment').select2('val', '');
        $('#idorname').val(''); // distance slider set to null
        //$('#dateRangePicker').multiDatesPicker('resetDates', 'disabled');
        getList()

    });
    /* code to reset the complete filter - ends */

    /* initializing the distance,price and treatment length range sliders - starts */

    var $awesome2 = $("#awesome2").slider({range: "min", max: 20, value: 0});
    $awesome2.slider("pips", {rest: "label", handle: true, pips: true}).slider("float");

    var $awesome3 = $("#awesome3").slider({max: 500, range: true, step: 100, values: [0, 100]});
    $awesome3.slider("pips", {rest: "label", prefix: "$", handle: true, pips: true}).slider("float");

    var $awesome4 = $("#awesome4").slider({max: 120, range: "min", step: 30, values: 30});
    $awesome4.slider("pips", {rest: "label", prefix: "", handle: true, pips: true}).slider("float");
    /* initializing the distance range slider - ends */

    $("#awesome2").on("slidechange", function(event, ui) {
        var sliderValue = $("#awesome2").slider("value");
        $('#distance').val(sliderValue);
        getList(); // serching on change of distance slider 
    });

    $("#awesome3").on("slidechange", function(event, ui) {
        $('#minPrice').val($("#awesome3").slider("values", 0));
        $('#maxPrice').val($("#awesome3").slider("values", 1));
        getList();
    });

    $("#awesome4").on("slidechange", function(event, ui) {
        var treatmentLength = $("#awesome4").slider("value");
        $('#treatmentLength').val(treatmentLength);
        getList();
    });

    /**
     * @ Price Range slider Initialization
     */
    $(function() {
        /*	
         var priceRange = $("#awesome3").slider({ max: 500, range: true, step: 100, values: [ 0, 100 ] });
         $("#awesome3").slider("pips", { 
         rest: "label",  
         prefix: "$",
         handle: true, 
         pips: true
         }).slider("float");
         */
    });

    /**
     * @ Treatment Length Range slider Initialization
     */
    $(function() {
        /*	
         var priceRange = $("#awesome4").slider({ max: 120, range: "min", step: 30, values: 30 });
         $("#awesome4").slider("pips", { 
         rest: "label",  
         prefix: "",
         handle: true, 
         pips: true
         }).slider("float");
         */
    });

    $('#booking_no').on('change', function() {
        $('#price_filter').val(0);
        $('#feedback_filter').val(0);
        $(this).trigger('blur');
        getList();
    });

    $('#price_filter').on('change', function() {
        $('#booking_no').val(0);
        $('#feedback_filter').val(0);
        $(this).trigger('blur');
        getList();
    });

    $('#feedback_filter').on('change', function() {
        $('#booking_no').val(0);
        $('#price_filter').val(0);
        $(this).trigger('blur');
        getList();
    });


});

function prepareData(list, grid, data, paginate) {

    if (paginate == false) {
        list.push("<ul id='list-data'>");
        grid.push("<ul id='grid-data'>");
    }

    $.each(data, function(index, value) {
        getlistView(list, value);
        getgridView(grid, value);
    });

    if (paginate == false) {
        list.push("</ul>");
        grid.push("</ul>");
    }

    list_html = list.join("");
    grid_html = grid.join("");

    return [list_html, grid_html];
}

function getlistView(list, value) {

    var reviews = (value['reviews_count'] != '') ? value['reviews_count'] : 0;
    var bookings = (value['bookings_count'] != '') ? value['bookings_count'] : 0;
    var years_of_experience = (typeof value['years_of_experience'] === 'undefined' || value['years_of_experience'] == 'None' || value['years_of_experience'] == null) ? 'Not Available' : value['years_of_experience'];
    var specialties = (typeof value['specialties'] === 'undefined' || value['specialties'] == 'None' || value['specialties'] == null) ? 'Not Available' : value['specialties'];
    var degrees = (typeof value['degrees'] === 'undefined' || value['degrees'] == 'None' || value['degrees'] == null) ? 'Not Available' : value['degrees'];
    var work_days = (typeof value['work_days'] === 'undefined' || value['work_days'] == 'None' || value['work_days'] == null) ? 'Not Available' : value['work_days'];
    var price = (typeof value['price'] === 'undefined' || value['price'] == 'None' || value['price'] == '') ? '-NA-' : '$ ' + value['price'];
    var id = value['id'];

    list.push("<li>");
    list.push("<div class='find-profile-img' >");
    list.push("<div class='pro-pic' onclick='window.location = \"" + OVEconfig.BASEURL + "/practitioner/view/" + id + "\"'>");

    if (value['avtar_url']) {
        list.push("<img src='" + value['avtar_url'] + "' alt='' />");
    } else {
        list.push("<img src='https://s3-us-west-2.amazonaws.com/ovessence/img/profile-pic-1.jpg' alt='' />");
    }

    list.push("</div>");
    list.push("<div class='social-icon'>");
    //list.push("<div class='addthis_sharing_toolbox'data-url='http:"+OVEconfig.BASEURL+"/practitioner/view/'></div>");
    list.push("<div  data-url='" + OVEconfig.BASEURL.replace('//', '') + "/practitioner/view/" + id + "' class='addthis_sharing_toolbox' ></div>");

    list.push("</div>");
    list.push("</div>");
    list.push("<div class='find-profile-detail' onclick='window.location = \"" + OVEconfig.BASEURL + "/practitioner/view/" + id + "\"'>");
    list.push("<div class='rating-top'>");
    list.push("<div class='head'>");
    list.push("<h3>" + value['first_name'] + " " + value['last_name'] + "</h3>");
    (value['verified'] == 1) ? list.push('<span class="verify"><img src="' + OVEconfig.BASEURL + '/img/verifed-40.png" alt="" /> </span> ') : '';
    list.push("</div>");
    list.push("<div class='rating'>");
    list.push("<form>");
    if (value['rating'] == 5) {
        list.push("<input type='radio' name='rating-input-1' id='rating-input-1-5' class='rating-input' checked='checked' disabled='disabled' >");
    } else {
        list.push("<input type='radio' name='rating-input-1' id='rating-input-1-5' class='rating-input' disabled='disabled' >");
    }
    list.push("<label class='rating-star' for='rating-input-1-5'></label>");
    if (value['rating'] == 4) {
        list.push("<input type='radio' name='rating-input-1' id='rating-input-1-4' class='rating-input' checked='checked' disabled='disabled' >");
    } else {
        list.push("<input type='radio' name='rating-input-1' id='rating-input-1-4' class='rating-input' disabled='disabled' >");
    }
    list.push("<label class='rating-star' for='rating-input-1-4'></label>");
    if (value['rating'] == 3) {
        list.push("<input type='radio' name='rating-input-1' id='rating-input-1-3' class='rating-input' checked='checked' disabled='disabled' >");
    } else {
        list.push("<input type='radio' name='rating-input-1' id='rating-input-1-3' class='rating-input'  disabled='disabled' >");
    }
    list.push("<label class='rating-star' for='rating-input-1-3'></label>");
    if (value['rating'] == 2) {
        list.push("<input type='radio' name='rating-input-1' id='rating-input-1-2' class='rating-input' checked='checked' disabled='disabled' >");
    } else {
        list.push("<input type='radio' name='rating-input-1' id='rating-input-1-2' class='rating-input' disabled='disabled' >");
    }
    list.push("<label class='rating-star' for='rating-input-1-2'></label>");
    if (value['rating'] == 1) {
        list.push("<input type='radio' name='rating-input-1' id='rating-input-1-1' class='rating-input' checked='checked' disabled='disabled' >");
    } else {
        list.push("<input type='radio' name='rating-input-1' id='rating-input-1-1' class='rating-input' disabled='disabled' >");
    }
    list.push("<label class='rating-star' for='rating-input-1-1'></label>");
    list.push("</form>");
    list.push("<div class='view-all'>");
    //list.push("<a href='javascript:;'>view all rating</a>");
    list.push("</div>");
    list.push("</div>");
    list.push("<div class='booking-reviews' onclick='window.location = \"" + OVEconfig.BASEURL + "/practitioner/view/" + id + "\"'>");
    list.push("<span class='booking'>");
    list.push("<label>" + bookings + "</label>");
    list.push("<small>Booking</small>");
    list.push("</span>");
    list.push("<span class='review'>")
    list.push("<label>" + reviews + "</label>");
    list.push("<small>Reviews</small>");
    list.push("</span>");
    list.push("</div>");
    list.push("</div>");
    list.push("<div class='find-pro-detail'>");
    list.push("<div class='pro-detail'>");
    list.push("<div class='row'>");
    list.push("<span class='name-head'>Speciality</span>");
    list.push("<span class='name-text'>" + trim_words(specialties, 2) + "</span>");
    list.push("</div>");
    list.push("<div class='row'>");
    list.push("<span class='name-head'>Degrees</span>");
    list.push("<span class='name-text'>" + trim_words(degrees, 10) + "</span>");
    list.push("</div>");
    list.push("<div class='row'>");
    list.push("<span class='name-head'>Experience</span>");
    list.push("<span class='name-text'>" + trim_words(years_of_experience, 10) + "</span>");
    list.push("</div>");
    list.push("<div class='row'>");
    list.push("<span class='name-head'>Work days</span>");
    list.push("<span class='name-text'>" + trim_words(work_days, 10) + "</span>");
    list.push("</div>");
    list.push("</div>");
    list.push("<div class='book-know'>");
    list.push("<a href='" + OVEconfig.BASEURL + "/practitioner/view/" + value['id'] + "'>Book Now</a>");
    list.push("</div>");
    list.push("</div>");
    list.push("</div>");
    list.push("<div class='more-info'>");
    list.push("<ul>");
    var distance = (!isNaN(value['distance']))?value['distance']+' Miles':value['distance'];
    list.push("<li><div class='km' onclick='window.location = \"" + OVEconfig.BASEURL + "/practitioner/view/" + id + "?tab=map\"'><span>Distance from your area</span> "+distance+" </div></li>");
    list.push("<li><div class='findform'>From <span>" + price + "</span></div></li>");
    var email = "'" + value['email'] + "'"; //getContactDetails("+value['id']+","+email+","+value['cellphone']+");

    list.push("<li><div class='find-contact'><a href='javascript:void(0);' onclick=getContactDetails(" + value['id'] + "," + email + "," + value['cellphone'] + "," + value['login_user_type_id'] + "); >Contact</a></div></li>");
    list.push("<li><div class='find-profile'><a href='" + OVEconfig.BASEURL + "/practitioner/view/" + value['id'] + "'>Profile</a></div></li>");
    list.push("<li><div class='demo-profile' onclick='window.location = \"" + OVEconfig.BASEURL + "/practitioner/view/" + id + "?tab=videos\"'>Demo</div></li>");
    list.push("</ul>");
    list.push("</div>");
    list.push("</li>");
}

function getgridView(grid, value) {
    var bookings = (value['bookings_count'] != '') ? value['bookings_count'] : 0;
    var years_of_experience = (typeof value['years_of_experience'] === 'undefined' || value['years_of_experience'] == 'None' || value['years_of_experience'] == null) ? 'Not Available' : value['years_of_experience'];
    var specialties = (typeof value['specialties'] === 'undefined' || value['specialties'] == 'None' || value['specialties'] == null) ? 'Not Available' : value['specialties'];
    var degrees = (typeof value['degrees'] === 'undefined' || value['degrees'] == 'None' || value['degrees'] == null) ? 'Not Available' : value['degrees'];
    var work_days = (typeof value['work_days'] === 'undefined' || value['work_days'] == 'None' || value['work_days'] == null) ? 'Not Available' : value['work_days'];
    var id = value['id'];

    grid.push("<li>");
    grid.push("<div class='find-profile-img'>");
    grid.push("<div class='pro-pic' onclick='window.location = \"" + OVEconfig.BASEURL + "/practitioner/view/" + id + "\"'>");

    if (value['avtar_url']) {
        grid.push("<img src='" + value['avtar_url'] + "' alt='' />");
    } else {
        grid.push("<img src='https://s3-us-west-2.amazonaws.com/ovessence/img/profile-pic-1.jpg' alt='' />");
    }

    grid.push("</div>");
    grid.push("<div class='social-icon'>");
    grid.push("<div  data-url='" + OVEconfig.BASEURL.replace('//', '') + "/practitioner/view/" + id + "' class='addthis_sharing_toolbox' ></div>");
    /*grid.push("<ul>");
     grid.push("<li><a href='javascript:;' class='facebook'></a></li>");
     grid.push("<li><a href='javascript:;' class='twitter'></a></li>");
     grid.push("<li><a href='javascript:;' class='google'></a></li>");
     grid.push("<li><a href='javascript:;' class='linkdin'></a></li>");
     
     grid.push("</ul>");*/
    grid.push("</div>");
    grid.push("</div>");
    grid.push("<div class='find-profile-detail' onclick='window.location = \"" + OVEconfig.BASEURL + "/practitioner/view/" + id + "\"'>");
    grid.push("<div class='rating-top'>");
    grid.push("<div class='head'>");
    grid.push("<h3><a href='" + OVEconfig.BASEURL + "/practitioner/view/" + value['id'] + "'>" + value['first_name'] + " " + value['last_name'] + "</a></h3>");
    (value['verified'] == 1) ? grid.push('<span class="verify"><img src="' + OVEconfig.BASEURL + '/img/verifed-40.png" alt="" /> </span> ') : '';
    grid.push("</div>");
    grid.push("<div class='rating'>");
    grid.push("<form>");
    if (value['rating'] == 5) {
        grid.push("<input type='radio' name='rating-input-1' id='rating-input-1-5' class='rating-input' checked='checked' disabled='disabled'>");
    } else {
        grid.push("<input type='radio' name='rating-input-1' id='rating-input-1-5' class='rating-input' disabled='disabled'>");
    }
    grid.push("<label class='rating-star' for='rating-input-1-5'></label>");
    if (value['rating'] == 4) {
        grid.push("<input type='radio' name='rating-input-1' id='rating-input-1-4' class='rating-input' checked='checked' disabled='disabled'>");
    } else {
        grid.push("<input type='radio' name='rating-input-1' id='rating-input-1-4' class='rating-input'  disabled='disabled'>");
    }
    grid.push("<label class='rating-star' for='rating-input-1-4'></label>");
    if (value['rating'] == 3) {
        grid.push("<input type='radio' name='rating-input-1' id='rating-input-1-3' class='rating-input' checked='checked'  disabled='disabled'>");
    } else {
        grid.push("<input type='radio' name='rating-input-1' id='rating-input-1-3' class='rating-input'  disabled='disabled'>");
    }
    grid.push("<label class='rating-star' for='rating-input-1-3'></label>");
    if (value['rating'] == 2) {
        grid.push("<input type='radio' name='rating-input-1' id='rating-input-1-2' class='rating-input' checked='checked'  disabled='disabled'>");
    } else {
        grid.push("<input type='radio' name='rating-input-1' id='rating-input-1-2' class='rating-input'  disabled='disabled'>");
    }
    grid.push("<label class='rating-star' for='rating-input-1-2'></label>");
    if (value['rating'] == 1) {
        grid.push("<input type='radio' name='rating-input-1' id='rating-input-1-1' class='rating-input' checked='checked'  disabled='disabled'>");
    } else {
        grid.push("<input type='radio' name='rating-input-1' id='rating-input-1-1' class='rating-input'  disabled='disabled'>");
    }
    grid.push("<label class='rating-star' for='rating-input-1-1'></label>");
    grid.push("</form>");
    grid.push("<div class='view-all'>");
    //grid.push("<a href='javascript:;'>view all rating</a>");
    grid.push("</div>");
    grid.push("</div>");
    grid.push("</div>");
    grid.push("<div class='find-pro-detail'>");
    grid.push("<div class='pro-detail'>");
    grid.push("<div class='row'>");
    grid.push("<span class='name-head'>Speciality</span>");
    grid.push("<span class='name-text'>" + trim_words(specialties, 2) + "</span>");
    grid.push("</div>");
    grid.push("<div class='row'>");
    grid.push("<span class='name-head'>Degrees</span>");
    grid.push("<span class='name-text'>" + trim_words(degrees, 10) + "</span>");
    grid.push("</div>");
    grid.push("<div class='row'>");
    grid.push("<span class='name-head'>Experience</span>");
    grid.push("<span class='name-text'>" + trim_words(years_of_experience, 10) + "</span>");
    grid.push("</div>");
    grid.push("<div class='row'>");
    grid.push("<span class='name-head'>Work days</span>");
    grid.push("<span class='name-text'>" + trim_words(work_days, 10) + "</span>");
    grid.push("</div>");
    grid.push("<div class='row'>");
    grid.push("<span class='name-head'>Booking</span>");
    grid.push("<span class='book-text'>" + bookings + "</span>");
    grid.push("</div>");
    grid.push("</div>");
    grid.push("<div class='book-know'>");
    grid.push("<a href='" + OVEconfig.BASEURL + "/practitioner/view/" + value['id'] + "'>Book Now</a>");
    grid.push("</div>");
    grid.push("</div>");
    grid.push("</div>");
    grid.push("</li>");
}

function paginate()
{
    // which style is active i.e 'list' or 'grid'
    if ($('#list-view-data').is(':visible')) {
        var style = 'list';
    } else if ($('#grid-view-data').is(':visible')) {
        var style = 'grid';
    }

    ($("input[name='next']").val() == '') ? paginateflag = false : '';

    $('#' + style + '-data').scrollPagination({
        'contentPage': OVEconfig.BASEURL + '/practitioner/list', // the url you are fetching the results
        'contentData': {}, // these are the variables you can pass to the request, for example: children().size() to know which page you are
        /* 'scrollTarget': $('#'+style+'-view-data'), // who gonna scroll? in this example, the full window */
        'scrollTarget': $(window), // who gonna scroll? in this example, the full window 
        'heightOffset': 400, // it gonna request when scroll is 10 pixels before the page ends
        'beforeLoad': function() { // before load function, you can display a preloader div
            $('#loading').fadeIn();
        },
        'afterLoad': function(elementsLoaded) { // after loading content, you can use this function to animate your new elements
            $('#loading').fadeOut();
            $(elementsLoaded).fadeInWithDelay();
        }
    });

    // code for fade in element by element
    $.fn.fadeInWithDelay = function() {
        var delay = 0;
        return this.each(function() {
            $(this).delay(delay).animate({opacity: 1}, 200);
            delay += 100;
        });
    };

}

function getStates(id)
{

    getList(); // call to get list start serching on country basis till then

    var list = new Array();

    if (id != '') {

        $.ajax({
            url: OVEconfig.BASEURL + '/practitioner/getstates/',
            type: 'POST',
            dataType: 'json',
            data: {country_id: id},
            beforeSend: function() {
                //$('#loading').fadeIn();
            },
            success: function(data) {
                var data = JSON.stringify(data);
                if (data.length != 0) {

                    var allData = $.parseJSON(data);
                    list.push("<option value='0'>Select State</option>")
                    $.each(allData, function(index, value) {
                        list.push("<option value=" + index + ">" + value + "</option>");
                    });
                } else {
                    list.push("<option value=" + 0 + ">NO data</option>")
                }

                list.join("");
                $('#state').html(list);

            },
            error: function(xhr, errorType, errorMsg) {
                console.log(errorMsg)
            },
        });

    }
}

/*
 *  Function called when filter data changes every time. Code start here.
 *  */
function applyAddThis()
{
    var script = '//s7.addthis.com/js/300/addthis_widget.js#domready=1';
    if (window.addthis) {
        window.addthis = null;
        window._ate = null;
    }
    $.getScript(script);
}

function getList() {
    var data = getDataForList();
    $.ajax({
        url: OVEconfig.BASEURL + '/practitioner/list/',
        type: 'POST',
        dataType: 'json',
        data: data,
        beforeSend: function() {
            $('#loading').fadeIn();
            $('.default-load').fadeIn();
        },
        success: function(data) {
            renderData(data);
            applyAddThis();
            $('.default-load').fadeOut();
            $('#loading').fadeOut();
        },
        error: function(xhr, errorType, errorMsg) {
            console.log(errorMsg)
        },
    });
}

/*
 *  Function called when filter data changes every time. Code end here.
 *  
 *  */

/* 
 * Date and Time Range picker initialization code start here 
 * */

$(function() {

    $("#dateRangePicker").multiDatesPicker({
        'minDate': 0
    });

    $('#datepairExample .time').timepicker({
        'showDuration': true,
        'timeFormat': 'g:i A',
        'stepMinute': 15
    });
});

/* 
 * Date and Time Range picker initialization code ends here 
 * */

/*
 * Get practitioner list by Availability code start here 
 * */

$('#dateTimeRange').on('click', function() {
    var data = getDataForList();

    if (data.weeDay.length > 0) {
        $.ajax({
            type: "post",
            url: OVEconfig.BASEURL + '/practitioner/listBYDateTime/',
            dataType: "json",
            data: data,
            beforeSend: function() {
                $('#loading').fadeIn();
                $('.default-load').fadeIn();
            },
            success: function(data) {
                renderData(data);
                $('#loading').fadeOut();
                $('.default-load').fadeOut();
            },
            error: function(xhr, errorType, errorMsg) {
                console.log(errorMsg);
            },
        });
    }
});

/*
 * Get practitioner list by Availability code end here here 
 * */

/*
 * Convert Time (12H) to (24h) format code start here
 * */

function convertTimeString(time) {
    var hours = Number(time.match(/^(\d+)/)[1]);
    var minutes = Number(time.match(/:(\d+)/)[1]);
    var AMPM = time.match(/\s(.*)$/)[1];
    if (AMPM == "PM" && hours < 12)
        hours = hours + 12;
    if (AMPM == "AM" && hours == 12)
        hours = hours - 12;
    var sHours = hours.toString();
    var sMinutes = minutes.toString();
    if (hours < 10)
        sHours = "0" + sHours;
    if (minutes < 10)
        sMinutes = "0" + sMinutes;
    return (sHours + ':' + sMinutes + ':00');
}

/*
 * Convert Time (12H) to (24h) format code end here 
 * */

/*
 * Function to providing data for generating the list of practitioners 
 * with filtering
 * */

function getDataForList() {

    var id = ($("#idorname").val() != '') ? ($.isNumeric($("#idorname").val()) ? $("#idorname").val() : 0) : 0;

    var booking_no = ($("#booking_no").val() != 0) ? $("#booking_no").val() : ''; // filter by booking no 

    var price_filter = ($("#price_filter").val() != 0) ? $("#price_filter").val() : ''; // filter by price range

    var feedback_filter = ($("#feedback_filter").val() != 0) ? $("#feedback_filter").val() : ''; // feedback 

    var minPrice = ($("#minPrice").val() != 0) ? $("#minPrice").val() : '';

    var maxPrice = ($("#maxPrice").val() != 0) ? $("#maxPrice").val() : '';

    var treatmentLength = ($("#treatmentLength").val() != 0) ? $("#treatmentLength").val() : '';

    var feedback_filter = ($("#feedback_filter").val() != 0) ? $("#feedback_filter").val() : '';
    /*	
     var practitioners_name = ($('input[name="practitioners_name"]').val()!='') ? $('input[name="practitioners_name"]').val() : '' ; // practitioner name 
     */
    var name = ((!$.isNumeric($("#idorname").val())) && ($("#idorname").val() != '')) ? $("#idorname").val() : '';
    var practitioners_name = ($('input[name="company_name"]').val() != '') ? $('input[name="company_name"]').val() : name; // Search param for company name	
    var company_name = ($('input[name="company_name"]').val() != '') ? $('input[name="company_name"]').val() : ''; // practitioner company name 

    var auth_to_issue_insurence_rem_receipt = ($('input[name="insurance"]').is(':checked')) ? $('input[name="insurance"]:checked').val() : ''; // insurance reciept 

    var treatment_for_physically_disabled_person = ($('input[name="disability"]').is(':checked')) ? $('input[name="disability"]:checked').val() : ''; // Disablity 

    var association_member = ($('input[name="association_member"]').is(':checked')) ? $('input[name="association_member"]:checked').val() : ''; // associated member 
    var address = ($('.address-autofill').last().val() != '') ? $('.address-autofill').last().val().split(',') : $('#search_location').val().split(',');

    if (address.length == 4) {
        var county = '';
        var city = (typeof address[0] != 'undefined') ? address[0].trim() : '';
        var state = (typeof address[1] != 'undefined') ? address[1].trim() : '';
        var zip_code = (typeof address[2] != 'undefined') ? address[2].trim() : '';
        var country = (typeof address[3] != 'undefined') ? address[3].trim() : '';
    } else {
        var city = (typeof address[0] != 'undefined') ? address[0].trim() : '';
        var county = (typeof address[1] != 'undefined') ? address[1].trim() : '';
        var state = (typeof address[2] != 'undefined') ? address[2].trim() : '';
        var zip_code = (typeof address[3] != 'undefined') ? address[3].trim() : '';
        var country = (typeof address[4] != 'undefined') ? address[4].trim() : '';
    }

    /*var country_id = ($('#country').val() != '0') ? $('#country').val() : ''; // country 
     
     //var state_id = ($('#state').val() != '0') ? $('#state').val() : ''; // state 
     var state_id = ($('#state').val() != '0') ? $('#state').val() : (($('#search_location').val()!='0') ? $('#search_location').val():''); // state 
     
     var city = ($('input[name="city"]').val() != '') ? $('input[name="city"]').val() : ''; // city 
     
     var zip_code = ($('input[name="zip"]').val() != '') ? $('input[name="zip"]').val() : ''; // zip*/

    var distance = ($('#distance').val() != '') ? $('#distance').val() : ''; // distance 

    /* avg rating */
    var avg_rating = new Array();
    if ($('#avg_rating ul').find('li.active').length != 0) {
        $('#avg_rating ul').find('li.active').each(function(i, selected) {
            avg_rating[i] = $(selected).data('value');
        });
    } else {
        avg_rating = '';
    }

    /* service category */
    var service_id = new Array();
    if ($('#type_of_treatment ul').find('li.active').length != 0) {
        $('#type_of_treatment ul').find('li.active').each(function(i, selected) {
            service_id[i] = $(selected).data('value');
        });
    } else {
        ($('#treatment').val() != '') ? (service_id[0] = $('#treatment').val()) : (service_id = '');
    }

    /* days */
    var days_id = new Array();
    if ($('#weekdays_id ul').find('li.active').length != 0) {

        $('#weekdays_id ul').find('li.active').each(function(i, selected) {
            days_id[i] = $(selected).data('value');
        });

    } else if ($('#weekend_id ul').find('li.active').length != 0) {

        $('#weekend_id ul').find('li.active').each(function(i, selected) {
            days_id[i] = $(selected).data('value');
        });

    } else {
        days_id = '';
    }

    /* Location Type */
    var locationType = new Array();
    if ($('#location_type ul').find('li.active').length != 0) {
        $('#location_type ul').find('li.active').each(function(i, selected) {
            locationType[i] = $(selected).data('value');
        });
    } else {
        locationType = '';
    }

    /* Get Time Data*/
    var weekDay = '';
    var dateRange = ($('#dateRangePicker').val() != '') ? ($('#dateRangePicker').val()) : ($('#datetime').val());
    var startTime = ($("#startTime").val() != '') ? convertTimeString($("#startTime").val()) : '';
    var endTime = ($("#startTime").val() != '') ? convertTimeString($("#endTime").val()) : '';
    var weekdayList = [7, 1, 2, 3, 4, 5, 6];

    if (dateRange != '') {
        var dateArray = dateRange.split(',');
        var days = new Array();
        $.each(dateArray, function(index, value) {
            days.push(weekdayList[new Date(value).getDay()]);
        });
        weekDay = days.join();
    }

    /*return data = {next: '1', paginate: false, booking_no: booking_no, service_id: service_id, auth_to_issue_insurence_rem_receipt: auth_to_issue_insurence_rem_receipt,
     days_id: days_id, avg_rating: avg_rating, practitioners_name: practitioners_name, company_name: company_name, price_filter: price_filter,
     feedback_filter: feedback_filter, country_id: country_id, state_id: state_id, city: city, distance: distance, association_member: association_member,
     minPrice: minPrice, maxPrice: maxPrice, zip_code: zip_code, treatmentLength: treatmentLength, treatment_for_physically_disabled_person: treatment_for_physically_disabled_person,
     id: id, locationType: locationType, weeDay: weekDay, startTime: startTime, endTime: endTime};*/

    return data = {next: '1', paginate: false, booking_no: booking_no, service_id: service_id, auth_to_issue_insurence_rem_receipt: auth_to_issue_insurence_rem_receipt,
        days_id: days_id, avg_rating: avg_rating, practitioners_name: practitioners_name, company_name: company_name, price_filter: price_filter,
        feedback_filter: feedback_filter, distance: distance, city: city, county: county, state: state, zip_code: zip_code, country: country, association_member: association_member,
        minPrice: minPrice, maxPrice: maxPrice, treatmentLength: treatmentLength, treatment_for_physically_disabled_person: treatment_for_physically_disabled_person,
        id: id, locationType: locationType, weeDay: weekDay, startTime: startTime, endTime: endTime};
}

/*
 * Function to providing data for generating the list of practitioners 
 * with filtering
 * */

/*
 * Function for rendring providing data for generating the list of practitioners 
 * with filtering
 * */

function renderData(data) {

    dataCount(data);
    $('.default-load').fadeOut();
    if (data) {
        $('#loading').fadeOut();
        var data = JSON.stringify(data);

        if (data.length != 0) {
            var list = new Array();
            var grid = new Array();

            var allData = $.parseJSON(data);

            var next = allData.next;
            if (next != '') {
                //var page = next.split('?')[1].split('=')[1];
                var page = next;
            } else {
                var page = '';
            }
            $('input[name=next]').val(page);

            if (allData['result'].length > 0) {

                var result = prepareData(list, grid, allData['result'], false);
                list_html = result[0];
                grid_html = result[1];

            } else {
                list_html = '';
                grid_html = '';
                $('#nomoreresults').fadeIn();
            }

            $('#list-view-data').html(list_html);
            $('#grid-view-data').html(grid_html);

            paginateflag = true;
            paginate();

        } else {

            return false;
        }

    } else {

        $('#list-view-data').html('');
        $('#grid-view-data').html('');

        if ($('#loading').is(':visible')) {
            $('#loading').fadeOut();
        } else {
            $('#loading').fadeIn();
        }

        $('#nomoreresults').fadeIn();
    }
}

/*
 * Function for rendring providing data for generating the list of practitioners  
 * with filtering
 * */

/*
 * Function for counting no of records while generating the list of practitioners  
 * with filtering start here
 * */

function dataCount(data) {
    var recordCount = $("#paginateDataCount").val();
    
    if (data != '') {
        if (data.result != '') {
            
            var noOfResults = data.result.length;
            var totalRecords = data.count;
            if (recordCount == 0 || recordCount < 10 || noOfResults == totalRecords) {
                var resultCount = noOfResults;
            } else if (recordCount > 0) {
                var resultCount = Number(recordCount) + Number(noOfResults);
            }

            $("#paginateDataCount").val(resultCount);
            $(".short-history .short").html(resultCount + " out of " + totalRecords);
        } else {
            $(".short-history .short").html("No Records");
            $("#paginateDataCount").val(0);
        }
    } else {
        $(".short-history .short").html("No Records");
        $("#paginateDataCount").val(0);
    }
}

/*
 * Function for counting no of records while generating the list of practitioners  
 * with filtering ends here
 * */
