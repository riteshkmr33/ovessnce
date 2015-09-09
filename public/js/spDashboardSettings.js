/* Password change functionality starts here.*/
$('#spchangepassword').on('submit', function() {
    $('div.error-msg, div.success-msg').slideUp();
    var old_pass = $('#old_pass').val();
    var Pass = $('#Pass').val();
    var confirm_password = $('#confirm_password').val();

    if (Pass != confirm_password) {
        $('.error-msg').html("<label>Confirm password do not match</label>").fadeIn('slow');
        scrollTo('div.error-msg', 100, 'top');
        return false;
    }

    $.ajax({
        url: OVEconfig.BASEURL + '/practitioner/settings/',
        type: 'POST',
        dataType: 'json',
        data: {old_pass: old_pass, Pass: Pass, confirm_password: confirm_password, action: 'change_password'},
        beforeSend: function() {
            $('.default-load').fadeIn();
        },
        success: function(data) {
            $('.default-load').fadeOut();
            if (data) {
                if (data.error) {
                    $('.error-msg').html("<label>" + data.msg + "</label>").fadeIn('slow');
                    scrollTo('div.error-msg', 100, 'top');
                } else {
                    $('.success-msg').html("<label>" + data.msg + "</label>").fadeIn('slow');
                    scrollTo('div.success-msg', 100, 'top');
                }
            }
        },
        error: function(xhr, errorType, errorMsg) {
            console.log
        }
    });

    return false;
    exit;

});

$('.feature_chk').on('click', function() {
    $('div.error-msg, div.success-msg').slideUp();
    if ($(this).val() == 1) {
        $(this).removeAttr('checked');
        $(this).val('0');
    } else {
        $(this).val('1');
        $(this).attr('checked', 'checked');
    }

    var feature_email = $('#feature_email').val();
    var feature_sms = $('#feature_sms').val();
    var feature_chat = $('#feature_chat').val();
    var feature_table_id = $('#feature_table_id').val();

    $.ajax({
        url: OVEconfig.BASEURL + '/practitioner/settings/',
        type: 'POST',
        dataType: 'json',
        data: {feature_table_id: feature_table_id, feature_email: feature_email, feature_sms: feature_sms, feature_chat: feature_chat, action: 'change_features'},
        beforeSend: function() {
            $('.default-load').fadeIn();
        },
        success: function(data) {
            $('.default-load').fadeOut();
            if (data) {
                if (data.error) {
                    $('.error-msg').html("<label>" + data.msg + "</label>").fadeIn('slow');
                    scrollTo('div.error-msg', 100, 'top');
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

});

$('#newsletter-submit').on('click', function() {
    $('div.error-msg, div.success-msg').slideUp();
    if ($("input[name='newsletter-chk']:checked").val()) {

        $.ajax({
            url: OVEconfig.BASEURL + '/practitioner/settings/',
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

$('#close-account').on('click', function() {
    $('div.error-msg, div.success-msg').slideUp();
    var reason_id = ($('input[name=close-acc]:radio:checked').val() !== '') ? $('input[name=close-acc]:radio:checked').val() : '';
    var other_reason = ($('#other-reason').val() !== '') ? $('#other-reason').val() : '';

    if (!reason_id) {

        $('.error-msg').html("<label>Please select a reason first</label>").fadeIn('slow');
        scrollTo('div.error-msg', 200, 'top');
        return false;
        exit;
    } else if (reason_id == "5") {
        if (other_reason === '') {
            $('.error-msg').html("<label>Please provider other reason in the textarea</label>").fadeIn('slow');
            scrollTo('div.error-msg', 200, 'top');
            return false;
        }
    }

    if (confirm("Are you sure you want to close your account")) {
        $.ajax({
            url: OVEconfig.BASEURL + '/practitioner/settings/',
            type: 'POST',
            dataType: 'json',
            data: {reason_id: reason_id, other_reason: other_reason, action: 'close-acc'},
            beforeSend: function() {
                $('.default-load').fadeIn();
            },
            success: function(data) {
                $('.default-load').fadeOut();
                if (data) {
                    if (data.error) {
                        $('.error-msg').html("<label>" + data.msg + "</label>").fadeIn('slow');
                        scrollTo('div.error-msg', 100, 'top');
                    } else {
                        // take it to the logout page 
                        $('.success-msg').html("<label>" + data.msg + "</label>").fadeIn('slow');
                        scrollTo('div.success-msg', 100, 'top');

                        setTimeout('window.location = "' + OVEconfig.BASEURL + '/logout"', 5000);
                    }
                }
            },
            error: function(xhr, errorType, errorMsg) {
                console.log
            }
        });
    }

});

$('#reset-close-account').on('click', function() {
    $('input[name=close-acc]:radio').removeAttr('checked');
    $('#other-reason').val('');
});

$('#auto-renew').on('click', function() {
    $('div.error-msg, div.success-msg').slideUp();
    var auto_renewal = ($('#auto-renew').is(':checked')) ? "1" : "0";
    var subscription_id = $('#subscription_id').val();

    if (subscription_id === '') {
        $('.error-msg').html("<label>You have not subscribed to any subscription plan, Please subscribe to a plan first</label>").fadeIn('slow');
        scrollTo('div.error-msg', 200, 'top');
        return false;
    }

    $.ajax({
        url: OVEconfig.BASEURL + '/practitioner/settings/',
        type: 'POST',
        dataType: 'json',
        data: {auto_renewal: auto_renewal, subscription_id: subscription_id, action: 'auto-renewal'},
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

});

$('#unsubscribe').on('click', function() {
    $('div.error-msg, div.success-msg').slideUp();
    var subscription_id = $('#subscription_id').val();

    if (subscription_id === '') {
        $('.error-msg').html("<label>You have not subscribed to any subscription plan, Please subscribe to a plan first</label>").fadeIn('slow');
        scrollTo('div.error-msg', 200, 'top');
        return false;
    }

    if (confirm("Are you sure you want to unsubscribe , Once confirmed you will not be able to revet this")) {
        $.ajax({
            url: OVEconfig.BASEURL + '/practitioner/settings/',
            type: 'POST',
            dataType: 'json',
            data: {subscription_id: subscription_id, action: 'unsubscribe'},
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
                        $('.subs').html("<label>You have unsubscribed successfully <a href='" + OVEconfig.BASEURL + "/membership/'>Click Here</a> to sunscribe to a new plan </label>");
                        /*
                         $('.success-msg').html("<label>" + data.msg + "</label>").fadeIn('slow');
                         scrollTo('div.success-msg', 200, 'top');
                         */
                    }
                }
            },
            error: function(xhr, errorType, errorMsg) {
                console.log(errorMsg)
            }
        });
    }

    return false;
    exit;
});

/* Update card details functionality starts here */
$(document.body).on('click', '#update_card', function(){
    $('div.error-msg, div.success-msg').slideUp();
    if ($('#name_on_card').val() == '') {
        $('.error-msg').html("<label> Please enter the name mentioned on card..!!</label>").fadeIn('slow');
        scrollTo('div.error-msg', 200, 'top');
        return false;
    }
    
    if ($('#card_no').val() == '') {
        $('.error-msg').html("<label> Please enter your card number..!!</label>").fadeIn('slow');
        scrollTo('div.error-msg', 200, 'top');
        return false;
    } else if (isNaN($('#card_no').val()) || $('#card_no').val().length < 12 || $('#card_no').val().length > 19) {
        $('.error-msg').html("<label> Please enter valid 12-19 digit card number..!!</label>").fadeIn('slow');
        scrollTo('div.error-msg', 200, 'top');
        return false;
    }
    
    if ($('#month').val() == '') {
        $('.error-msg').html("<label> Please select card expiration month..!!</label>").fadeIn('slow');
        scrollTo('div.error-msg', 200, 'top');
        return false;
    }
    
    if ($('#year').val() == '') {
        $('.error-msg').html("<label> Please select card expiration year..!!</label>").fadeIn('slow');
        scrollTo('div.error-msg', 200, 'top');
        return false;
    }
    
    if ($('#cvv').val() == '') {
        $('.error-msg').html("<label> Please enter CVV number mentioned on card..!!</label>").fadeIn('slow');
        scrollTo('div.error-msg', 200, 'top');
        return false;
    } else if (isNaN($('#cvv').val())) {
        $('.error-msg').html("<label> Please enter valid numeric CVV number..!!</label>").fadeIn('slow');
        scrollTo('div.error-msg', 200, 'top');
        return false;
    }
    
    $.ajax({
        url: OVEconfig.BASEURL + '/practitioner/settings/',
        type: 'POST',
        data: {card_data: $('form#card_details').serialize(), action: 'update_card'},
        beforeSend: function() {$('.default-load').fadeIn();},
        success: function(data) {
            //console.log(data);
            if (data) {
                data = JSON.parse(data);
                if (data.status == '1') {
                    $('.success-msg').html("<label> "+data.msg+" </label>").fadeIn('slow');
                    setTimeout("$('div.success-msg').slideUp('slow')", 5000);
                    scrollTo('div.success-msg', 200, 'top');
                } else {
                    $('.error-msg').html("<label> "+data.msg+" </label>").fadeIn('slow');
                    scrollTo('div.error-msg', 200, 'top');
                }
            } else {
                $('.error-msg').html("<label> Unable to update card details..!!</label>").fadeIn('slow');
                scrollTo('div.error-msg', 200, 'top');
            }
            $('.default-load').fadeOut();
        },
        error: function(xhr, errorType, errorMsg) {
            console.log(errorMsg); $('.default-load').fadeOut();
        }
    });
    
    return false;
});
/* Update card details functionality ends here */