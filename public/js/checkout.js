/* Payment code starts here */
$(document.body).on('click', 'input#pay_now', function() {
    $('div.error-msg, div.success-msg').slideUp();
    
    var user_name = $('input#name_on_card').val();
    var user_email = $('input#emailid').val();
    var card_no = $('input#card_no').val(); 
    var month = $('select#month').val();
    var year = $('select#year').val();
    var cvv_no = $('input#cvv_no').val(); 
    var card_type = $('select#card_type').val();
    var rememberme = 0;
    var useforrenew = 0;
    
    if (user_name == "") {
        $('div.error-msg').html('<label>Please enter the name mentioned on card..!!</label>').fadeIn('slow');
        scrollTo('div.error-msg', 100, 'top');
        return false;
    }
    
    if (user_email == "") {
        $('div.error-msg').html('<label>Please enter email address..!!</label>').fadeIn('slow');
        scrollTo('div.error-msg', 100, 'top');
        return false;
    }/* else if () {
        
    }*/
    
    if (card_type == "") {
        $('div.error-msg').html('<label>Please select your card type..!!</label>').fadeIn('slow');
        scrollTo('div.error-msg', 100, 'top');
        return false;
    }
    
    if (card_no == "") {
        $('div.error-msg').html('<label>Please enter your card number..!!</label>').fadeIn('slow');
        scrollTo('div.error-msg', 100, 'top');
        return false;
    } else if (isNaN(card_no) || card_no.length < 16 || card_no.length > 16) {
        $('div.error-msg').html('<label>Please enter a valid 16 digit card number..!!</label>').fadeIn('slow');
        scrollTo('div.error-msg', 100, 'top');
        return false;
    }
    
    if (month == "") {
        $('div.error-msg').html('<label>Please select your card expiration month..!!</label>').fadeIn('slow');
        scrollTo('div.error-msg', 100, 'top');
        return false;
    }
    
    if (year == "") {
        $('div.error-msg').html('<label>Please select your card expiration year..!!</label>').fadeIn('slow');
        scrollTo('div.error-msg', 100, 'top');
        return false;
    }
    
    if (cvv_no == "") {
        $('div.error-msg').html('<label>Please enter CVV number..!!</label>').fadeIn('slow');
        scrollTo('div.error-msg', 100, 'top');
        return false;
    } else if (isNaN(cvv_no) || cvv_no.length < 3 || cvv_no.length > 4) {
        $('div.error-msg').html('<label>Please enter a valid 3 or 4 digit CVV number..!!</label>').fadeIn('slow');
        scrollTo('div.error-msg', 100, 'top');
        return false;
    }
    
    /* Condition for booking form starts here */
    if ($('#termCondition').length > 0) {
        if (!$('#termCondition').is(':checked')) {
            $('#termCondition').focus();
            $('div.error-msg').html('<label>Please accept the Terms & Conditions to proceed..!!</label>').fadeIn('slow');
            scrollTo('div.error-msg', 100, 'top');
            return false;
        }
    }
    /* Condition for booking form ends here */
    
    /* Condition for subscription form starts here */
    if ($('.useforrenew').length > 0) {
        /*if ($('.rememberme:checked').val() == "1" && $('#cardtermCondition:checked').val() != "1") {
            $('#cardtermCondition').focus();
            $('div.error-msg').html('<label>Please accept the Terms & Conditions to proceed..!!</label>').fadeIn('slow');
            scrollTo('div.error-msg', 100, 'top');
            return false;
        } else {
            rememberme = 1;
        }*/
            
        //if ($('.useforrenew:checked').val() == "1" && $('#autorenewtermCondition:checked').val() != "1") {
        if ($('#autorenewtermCondition:checked').val() != "1") {
            $('#autorenewtermCondition').focus();
            $('div.error-msg').html('<label>Please accept the Terms & Conditions to proceed..!!</label>').fadeIn('slow');
            scrollTo('div.error-msg', 100, 'top');
            return false;
        } else {
            useforrenew = 1;
        }
    }
    /* Condition for subscription form ends here */
    
    var formAction = $('form.horizontal').attr('action');
        
    $.ajax({
        //url: OVEconfig.BASEURL + '/booking/payment/',
        url: OVEconfig.BASEURL + formAction,
        type: 'POST',
        data: {name_on_card: user_name, emailid: user_email, card_no: card_no, month: month, year: year, cvv_no: cvv_no, card_type: card_type, rememberme: rememberme, use_for_renew: useforrenew},
        beforeSend: function() {
            $('div.success-msg').html('<label>Please wait while your transaction is being processed. <br />DO NOT TRY TO RELOAD THIS PAGE DURING PROCESS..!!</label>').fadeIn('slow');
            scrollTo('div.success-msg', 100, 'top');
            $('.default-load').fadeIn();
        },
        success: function(data) {
            $('div.error-msg, div.success-msg').slideUp();
            console.log(data);
            if (data != '') {
                data = JSON.parse(data);
                if (data.status == '1') {
                    $('div.success-msg').html('<label>'+data.msg+'</label>').fadeIn('slow');
                    scrollTo('div.success-msg', 100, 'top');
                    if (data.subscription_id) {
                        setTimeout('window.location = "'+OVEconfig.BASEURL+'/membership/invoice/'+data.subscription_id+'"', 3000);
                    } else {
                        setTimeout('window.location = "'+OVEconfig.BASEURL+'/booking/invoice/'+data.booking_id+'"', 3000);
                    }
                } else {
                    console.log(data.errors);
                    $('div.error-msg').html('<label>'+data.msg+'</label>').fadeIn('slow');
                    scrollTo('div.error-msg', 100, 'top');
                }
            } else {
                $('div.error-msg').html('<label>Unable to process your request. Please try again later..!!</label>').fadeIn('slow');
                scrollTo('div.error-msg', 100, 'top');
            }
            
            $('.default-load').fadeOut();
        },
        error: function(xhr, errorType, errorMsg) {
            console.log(errorMsg);
            $('.default-load').fadeOut();
        }
    });
    return false;
});
/* Payment code ends here */

/* Accept terms and condition code starts here */
$(document.body).on('change', '.rememberme', function(){
    if ($(this).val() == '1') {
        $('.renew').slideDown('slow');
    } else {
        $('.renew').slideUp('slow');
    }
});
/* Accept terms and condition code ends here */