$(function() {
    $('#consumers').trigger('scroll');

    /* Infinite pagination code starts here */
    $('#consumers').scrollPagination({
        'contentPage': OVEconfig.BASEURL + '/practitioner/spconsumers/', // the url you are fetching the results
        'contentData': {id: $('input#sid').val(), page: $('input#page').val(), records: 10}, // these are the variables you can pass to the request, for example: children().size() to know which page you are
        'scrollTarget': $('#scrollDiv'), // who gonna scroll? in this example, the full window
        'heightOffset': 10, // it gonna request when scroll is 10 pixels before the page ends
        'beforeLoad': function() { // before load function, you can display a preloader div
            $('.default-load').fadeIn();
            $('input#page').val(parseInt($('input#page').val()) + 1);
        },
        'afterLoad': function(elementsLoaded) { // after loading content, you can use this function to animate your new elements
            $('.default-load').fadeOut();
            var i = 0;
            $(elementsLoaded).fadeInWithDelay();
            if ($('#consumers').children().size() > 100) { // if more than 100 results already loaded, then stop pagination (only for testing)
                $('#nomoreresults').fadeIn();
                $('#consumers').stopScrollPagination();
            }
        },
        'render': function(data) {
            var content = Array();
            if (data.length > 0) {
                $.each(data, function(key, value) {
                    var avtar = (typeof value.avtar != 'undefined' && value.avtar != "None" && value.avtar != '' && value.avtar != null)?value.avtar:'/img/profile-pic-1.jpg';
                    content.push('<li>');
                    content.push('<div class="md-name-col md">');
                    content.push('<span class="md-img"><img src="' + avtar + '"></span>');
                    content.push('<span class="md-name">' + value.name + '</span>');
                    content.push('</div>');
                    content.push('<div class="md-email md">' + value.email + '</div>');
                    content.push('<div class="select-form md">');
                    content.push('<form>');
                    content.push('<label for="select-all">');
                    content.push('<input type="checkbox" name="users[]" class="checkConsumer" value="' + value.id + '"><span></span>');
                    content.push('</label>');
                    content.push('</form>');
                    content.push('</div>');
                    content.push('</li>');
                });
            }
            $('ul#consumers').append(content.join(''));
        },
        'updateData': function(data) {

            $('input#page').val(parseInt(data.page) + 1);
            data.page = $('input#page').val();
            return data;
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
    /* Infinite pagination code ends here */

    /* Newsletter send code starts here */
    $(document.body).on('click', 'input#sendNewsletter', function() {
        $('div.error-msg, div.success-msg').slideUp();
        var ids = Array();

        $('input.checkConsumer:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length > 0) {
            $.ajax({
                url: OVEconfig.BASEURL + '/practitioner/sendnewsletter/',
                type: 'POST',
                data: {users: ids, nid: $('input#nid').val()},
                success: function(data) {
                    console.log(data)
                    if (data != "") {
                        var data = JSON.parse(data);

                        if (data.status == '1') {

                            $('div.success-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                            setTimeout("$('div.success-msg').slideUp('slow')", 5000);
                            scrollTo('div.success-msg', 100, 'top');
                            setTimeout("window.location.href = '" + OVEconfig.BASEURL + "/practitioner/dashboard/';", 6000);

                        } else {
                            $('div.error-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                            scrollTo('div.error-msg', 100, 'top');
                        }
                    } else {
                        $('div.error-msg').html('<label>Failed to send newsletter..!!</label>').fadeIn('slow');
                        scrollTo('div.error-msg', 100, 'top');
                    }

                    $('.default-load').fadeOut();
                }
            });
        } else {
            $('div.error-msg').html('<label>Please select at least one user to send newsletter..!!</label>').fadeIn('slow');
            scrollTo('div.error-msg', 100, 'top');
        }

        return false;
    });
    /* Newsletter send code ends here */

    /* Check/uncheck all code starts here */
    $(document.body).on('click', 'input#checkAllConsumers', function() {
        if ($(this).is(':checked')) {
            $('input.checkConsumer').prop('checked', true);
        } else {
            $('input.checkConsumer').prop('checked', false);
        }
    });
    /* Check/uncheck all code ends here */
});
