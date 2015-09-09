$(function() {

    $('form#composemessage').on('submit', function() {

        var to = $('#to_user').val();
        var subject = $('#subject').val();
        var message = $('#message').val();
        var user_type = (location.href.indexOf('practitioner') != -1) ? 'practitioner' : 'consumer';

        if (user_type != '') {

            $.ajax({
                url: OVEconfig.BASEURL + '/' + user_type + '/compose/',
                type: 'POST',
                dataType: 'json',
                data: {to: to, subject: subject, message: message},
                beforeSend: function() {
                    $('.default-load').fadeIn();
                },
                success: function(data) {
                    $('.default-load').fadeOut();
                    if (data) {
                        if (data.error) {
                            $('.error-msg').html(data.msg);
                            $('.success-msg').html('');
                        } else {
                            //window.location.href = OVEconfig.BASEURL + "/" + user_type + "/sent";
                            $('#error-msg').html('');
                            $('#success-msg').html(data.msg);
                        }
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
    });
});
