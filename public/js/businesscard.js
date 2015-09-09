/* Send a business cars*/
$(document).on('click', '#sendBusinesscard', function() {
    $('div.error-msg, div.success-msg').slideUp();
    var email = $('input#cardEmail').val();
    var imageUrl = $('#bg_name').val();
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    if (pattern.test(email)) {
        $.ajax({
            url: OVEconfig.BASEURL + '/practitioner/mailbusinesscard/',
            type: 'POST',
            data: {servicename: $('div#servicename').text(), email: email, imageUrl: imageUrl},
            dataType: 'json',
            beforeSend: function() {
                $('.default-load').fadeIn();
            },
            success: function(data) {
                if (data != "") {
                    //data = JSON.parse(data);
                    if (data.status == '1') {
                        $('.send-business-card').empty();
                        $('input#cardEmail').val('');
                        $('div.bc-msg').html('<div class="success-msg" ><label>' + data.msg + '</label></div>').fadeIn('slow');
                        //scrollTo('div.bc-msg', 50, 'top');
                        setTimeout("$('div.bc-msg').slideUp('slow')", 5000);
                    } else {
                        $('div.bc-msg').html('<div class="error-msg" ><label>' + data.msg + '</label> </div>').fadeIn('slow');
                        //scrollTo('div.bc-msg', 50, 'top');
                    }
                } else {
                    $('div.bc-msg').html('<div class="error-msg" ><label>Failed to send business card..!!</label> </div>').fadeIn('slow');
                    //scrollTo('div.bc-msg', 50, 'top');
                }
                $('.default-load').fadeOut();
            },
            error: function(xhr, errorType, errorMsg) {
                console.log(errorMsg)
            }
        });
    } else {
        $('div.bc-msg').html('<div class="error-msg" > <label>Please enter a valid email address to send business card..!!</label></div>').fadeIn('slow');
        //scrollTo('div.bc-msg', 50, 'top');
    }
    return false;
});
/*End:- Send a business cars*/

$('#businessmail').on('click', function() {

    var showhtml = '<form> <input type="text" id="cardEmail" placeholder="Email :" />';
    showhtml += '<input type="submit" class="black" id="sendBusinesscard" value="Send" /></form>';
    $(".send-business-card").html(showhtml);
});

$('#viewBusinessCard').on('click', function() {
    var bg_name = $('#bg_name').val();
    /*(bg_name == 'verso') ? $(".card-container").css("background", "url(../../img/bg_recto.jpg)  no-repeat center center") : $(".card-container").css("background", "url(../../img/bg_verso.jpg)  no-repeat center center");*/
    (bg_name == 'verso') ? $('#bg_name').val('recto') : $('#bg_name').val('verso');
    (bg_name == 'recto') ? $('#viewBusinessCard').text('view recto') : $('#viewBusinessCard').text('view verso');
    $('div.card-container').toggle();
    $('#savepdf').attr('href', function() {
        $newurl = this.href.split('?');
        $('#savepdf').attr('href', $newurl[0] + "?imgurl=" + bg_name);
    });
});

// toggle
/* 
 $(".business-card-toggle" ).click(function() {
 $( "#bcard-container" ).toggle( "slow" );
 }); */

