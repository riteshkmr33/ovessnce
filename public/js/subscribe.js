/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* Send email for subscribtion code starts here */
$(document.body).on('click', 'input#sendSubscribe', function() {
    $('div.error-msg-subs, div.success-msg-subs').slideUp();
    var email = $('input#subscribeEmail').val();
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    if (pattern.test(email)) {
        $.ajax({
            url: OVEconfig.BASEURL + '/contact/subscribeNewsletter/',
            type: 'POST',
            data: {email: email},
            beforeSend: function() {
                $('.default-load').fadeIn();
            },
            success: function(data) {
                if (data != "") {
                    data = JSON.parse(data);
                    if (data.status == '1') {
                        $('input#subscribeEmail').val('');
                        $('div.success-msg-subs').html('<label>' + data.msg + '</label>').fadeIn('slow');
                        scrollTo('div.success-msg-subs', 100, 'top');
                        setTimeout("$('div.success-msg-subs').slideUp('slow')", 5000);
                    } else {
                        //$('div.error-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                        //scrollTo('div.error-msg', 100, 'top');
                        var errors = Array();
                        $.each(data.errors, function(key, value) {
                            errors.push('<label style="text-transform:capitalize;">' + key.replace('_', ' ') + ' - ' + value + '</label>');
                        });
                        $('div.error-msg-subs').html(errors.join('')).fadeIn('slow');

                        scrollTo('div.error-msg-subs', 100, 'top');
                    }
                    $('.default-load').fadeOut();
                } else {
                    $('div.error-msg-subs').html('<label>Failed to send invitation..!!</label>').fadeIn('slow');
                    scrollTo('div.error-msg-subs', 100, 'top');
                }
                $('.default-load').fadeOut();
            },
            error: function(xhr, errorType, errorMsg) {
                console.log(errorMsg)
            }
        });
    }else {
        $('div.error-msg-subs').html('<label>Please provide a valid email address..!!</label>').fadeIn('slow');
        scrollTo('div.error-msg-subs', 100, 'top');
    }
    return false;
});

/* Send email for subscribtion code ends here */