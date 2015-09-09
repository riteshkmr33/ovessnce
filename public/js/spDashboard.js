/* Check user live chat status code starts here */
$.get(OVEconfig.BASEURL + '/livechat/operator/index.php?p=checkstatus&uid='+$('#sp_id').val(), function(data) {
    data = JSON.parse(data);
    if (data && data.status == '1') {
        $('div.chat-login a').removeClass('online, offline');
        $('div.chat-login a').addClass('online').html('START CHAT');
    } else {
        $('div.chat-login a').removeClass('online, offline');
        $('div.chat-login a').addClass('offline').html('LOGIN FOR CHAT');
    }
});
/* Check user live chat status code ends here */

/* Notification reading code starts here */
if (window.location.href.indexOf('review') != -1) {
    readNotifications('reviews'); // defined on common.js
}
/* Notification reading code ends here */

$(document.body).on('click', 'span.upload', function() {
    $(this).parents('div.aboutme-edit-wrapper').find('div#' + $(this).attr('rel')).fadeIn('slow');
    scrollTo(('div#' + $(this).attr('rel')), 100, 'bottom');
});

$('input[type=hidden]').each(function() {
    $(this).data('val', $(this).val());
});

/* Word count code start here */
$(document.body).on('keyup', 'textarea#description', function(e) {
    //(200  words max)
    var words = $(this).val().trim().split(/\s/);
    if (words.length < 200) {
        $('span#words').html('(' + (200 - words.length) + ' words max)');
    } else {
        $(this).val($(this).val().trim().split(/\s/, 200).join(' '))
    }
});
/* Word count code end here */

/* Image upload code start here */
$(document.body).on('click', '#uploadImage', function() {
    $('div.error-msg, div.success-msg').slideUp();
    var valid = true;
    var errorMssg = Array();
    var validFileExtensions = [".jpg", ".jpeg", ".bmp", ".gif", ".png"];

    $("#imageform").find('input, textarea').each(function() {
        if ($(this).val() == "" && $(this).attr('type') != 'hidden') {
            valid = false;
            errorMssg.push('<label >' + $(this).data('fieldname') + ' field should not be blank.</label>');  // style="text-transform:capitalize;"
        }

        if ($(this).attr('type') == 'file' && $(this).val() != "") {

            if (validFileExtensions.indexOf($(this).val().substr($(this).val().indexOf('.'), $(this).val().length).toLowerCase()) == -1) {
                errorMssg.push('<label>Please upload a valid image.</label>');
                valid = false;
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
    }
    $('.default-load').fadeIn();
    $("#imageform").ajaxForm({
        success: function(data) {
            //console.log(data);
            //alert(data); return false;
            if (data != "") {
                var image = JSON.parse(data);

                if (image.status == '1') {
                    var content = Array();
                    content.push('<li><a>');
                    content.push('<img src="' + image.url + '" alt="' + image.title + '" />');
                    content.push('<div class="over-lay">');
                    content.push('<span class="dProfile" id="' + image.id + '"></span>');
                    //content.push('<span class="mProfile" id="' + image.id + '"></span>');
                    content.push('<span class="eProfile" id="' + image.id + '"></span>');
                    content.push('</div></a></li>');

                    var slider = $('.about-gallery-wrapper').data('flexslider');
                    var spanId = $('input#image_id').val();
                    if (!slider) {

                        $('.about-gallery-wrapper > ul').prepend(content.join(''));
                        (spanId != "") ? $('span[id=' + spanId + '][class="eProfile"]:last').parents('li').remove() : '';
                        imageSlider(); // generate slider

                    } else {
                        if (spanId != "") {
                            var pos = $('span#' + spanId).parents('li').index();
                            slider.removeSlide(pos) // removing old slide in case of edit
                            slider.addSlide(content.join(''), pos);  // adding new slide
                        } else {
                            slider.addSlide(content.join(''), 0);  // adding new slide
                        }
                    }

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

            $('#imageform').find("input[type=text], input[type=hidden][name=id], input[type=file], textarea").val(""); // clearing form elements
            $('div.upload-close-btn').trigger('click');
            $('.default-load').fadeOut();
        }
    }).submit();
    return false;
});
/* Image upload code end here */

$(document.body).on('click', 'div.upload-close-btn', function() {
    $('#imageform, #videoform').find("input[type=text], input[type=hidden][name=id], input[type=file], textarea").val(""); // clearing form elements
})

/* Video upload code start here */
$(document.body).on('click', '#uploadVideo', function() {
    $('div.error-msg, div.success-msg').slideUp();
    var valid = true;
    var errorMssg = Array();
    var validFileExtensions = [".mp4", ".avi", ".3gp", ".mpeg", ".vob", ".flv", ".mkv", ".mov"];
    $("#videoform").find('input').each(function() {
        if ($(this).val() == "" && $(this).attr('type') != 'hidden') {
            valid = false;
            errorMssg.push('<label >' + $(this).data('fieldname') + ' field should not be blank.</label>');  // style="text-transform:capitalize;"
        }

        if ($(this).attr('type') == 'file' && $(this).val() != "") {
            if (validFileExtensions.indexOf($(this).val().substr($(this).val().indexOf('.'), $(this).val().length).toLowerCase()) == -1) {
                errorMssg.push('<label>Please upload a valid video file (i.e '+validFileExtensions.join(', ')+').</label>');
                valid = false;
            }

            if (this.files[0].size > 30720000) {
                errorMssg.push('<label>Please upload video less than 30MB.</label>');
                valid = false;
            }
        }
    });

    if (valid == false) {
        $('div.error-msg').html(errorMssg.join('')).fadeIn('slow');
        scrollTo('div.error-msg', 100, 'top');
        return false;
    }

    $('.default-load').fadeIn();
    $("#videoform").ajaxForm({
        success: function(data) {
            //console.log(data);
            if (data != "") {
                //alert(data); return false;
                var video = JSON.parse(data);

                if (video.status == '1') {
                    var slider = $('.about-video-wrapper').data('flexslider');

                    if (!slider) {
                        //$('.about-video-wrapper > ul').prepend('<li><a><video id="' + video.title + '_more" class="abc" src="' + video.url.replace('./public', OVEconfig.BASEURL) + '"></video></a><a class="comm-update-button del-video" style="margin:48px" id="'+video.id+'">Delete</a></li>');
                        $('.about-video-wrapper > ul').prepend('<li><a><video id="example_video_1" class="video-js vjs-default-skin" controls preload="auto" width="200" height="200" ><source src="' + video.url.replace('./public', OVEconfig.BASEURL) + '" type="video/mp4" /></video></a><a class="comm-update-button del-video" style="margin:48px" id="'+video.id+'">Delete</a></li>');
                        videoSlider(); // generate slider
                    } else {
                        //slider.addSlide('<li><a><video id="' + video.title + '_more" class="abc" src="' + video.url.replace('./public', OVEconfig.BASEURL) + '"></video></a><a class="comm-update-button del-video" style="margin:48px" id="'+video.id+'">Delete</a></li>', 0);
                        slider.addSlide('<li><a><video id="example_video_1" class="video-js vjs-default-skin" controls preload="auto" width="200" height="200" ><source src="' + video.url.replace('./public', OVEconfig.BASEURL) + '" type="video/mp4" /></video></a><a class="comm-update-button del-video" style="margin:48px" id="'+video.id+'">Delete</a></li>', 0);
                    }
                    
                    videojs(document.getElementById('example_video_1'), {}, function(){});
                    
                    /*$('video').each(function() {
                        var videoTitle = this.id.split('_');
                        jwplayer(this.id).setup({
                            file: this.src,
                            title: videoTitle[0],
                            width: '170',
                            height: '200',
                            aspectratio: '16:10',
                            autostart: 'false',
                            primary: 'flash'
                        });
                    });*/

                    $('div.success-msg').html('<label>' + video.msg + '</label>').fadeIn('slow');
                    setTimeout("$('div.success-msg').slideUp('slow')", 5000);
                    scrollTo('div.success-msg', 100, 'top');
                } else {
                    $('div.error-msg').html('<label>' + video.msg + '</label>').fadeIn('slow');
                    scrollTo('div.error-msg', 100, 'top');
                }
            } else {
                $('div.error-msg').html('<label>Failed to upload video file..!!</label>').fadeIn('slow');
                scrollTo('div.error-msg', 100, 'top');
            }
            $('#videoform').find("input[type=text], input[type=hidden][name=id], input[type=file], textarea").val(""); // clearing form elements
            $('div.upload-close-btn').trigger('click');
            $('.default-load').fadeOut();
        }
    }).submit();
    return false;
});
/* Video upload code end here */

/* Video delete code start here */
$(document.body).on('click', 'a.del-video', function() {
    $('div.error-msg, div.success-msg').slideUp();
    var video_id = $(this).attr('id');
    if (confirm('Are you sure want to delete this video..??')) {
        $('.default-load').fadeIn();
        $.ajax({
            url: OVEconfig.BASEURL + '/practitioner/update/',
            type: 'POST',
            data: {action: 'delete_video', video: video_id},
            success: function(data) {
                console.log(data);
                if (data != "") {
                    data = JSON.parse(data);
                    if (data.status == '1') {
                        var slider = $('.about-video-wrapper').data('flexslider');
                        if (!slider) {
                            $('a#' + video_id).parents('li').fadeOut('slow').remove();
                        } else {
                            var pos = $('a#' + video_id).parents('li').index();
                            slider.removeSlide(pos) // removing selected slide
                        }

                        $('div.success-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                        scrollTo('div.success-msg', 100, 'top');
                        setTimeout("$('div.success-msg').slideUp('slow')", 5000);
                    } else {
                        $('div.error-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                        scrollTo('div.error-msg', 100, 'top');
                    }
                } else {
                    $('div.error-msg').html('<label>Failed to delete image..!!</label>').fadeIn('slow');
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
/* Video delete code end here */

/* Edit image code start here */
$(document.body).on('click', 'span.eProfile', function() {
    $('#image_id').val($(this).attr('id'));
    $('#media_title').val($(this).data('title'));
    //$('#media_desc').val($(this).data('desc'));
    $(this).parents('div.aboutme-edit-wrapper').find('div#image_upload').fadeIn('slow');
});
/* Edit image code end here */

/* Set avtar code start here */
$(document.body).on('click', 'span.mProfile', function() {
    $('div.error-msg, div.success-msg').slideUp();
    var image_id = $(this).attr('id');
    var user = $('input#sp_id').val();
    $('.default-load').fadeIn();
    $.ajax({
        url: OVEconfig.BASEURL + '/practitioner/update/',
        type: 'POST',
        data: {action: 'avtar', image: image_id, user: user},
        success: function(data) {
            if (data != "") {
                var data = JSON.parse(data);
                if (data.image_url) {
                    $('div.profile-wrapper >img').attr('src', data.image_url);
                    $('div.success-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                    scrollTo('div.success-msg', 100, 'top');
                    setTimeout("$('div.success-msg').slideUp('slow')", 5000);
                } else {
                    $('div.error-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                    scrollTo('div.error-msg', 100, 'top');
                }
            } else {
                $('div.error-msg').html('<label>Failed to change avtar image..!!</label>').fadeIn('slow');
                scrollTo('div.error-msg', 100, 'top');
            }

            $('.default-load').fadeOut();
        },
        error: function(xhr, errorType, errorMsg) {
            console.log(errorMsg);
        }
    });
    return false;
});
/* Set avtar code start here */

/* Image delete code start here */
$(document.body).on('click', 'span.dProfile', function() {
    $('div.error-msg, div.success-msg').slideUp();
    var image_id = $(this).attr('id');
    var user = $('input#sp_id').val();
    if (confirm('Are you sure want to delete this image..??')) {
        $('.default-load').fadeIn();
        $.ajax({
            url: OVEconfig.BASEURL + '/practitioner/update/',
            type: 'POST',
            data: {action: 'delete_image', image: image_id, user: user},
            success: function(data) {
                console.log(data);
                if (data != "") {
                    data = JSON.parse(data);
                    if (data.status == '1') {
                        var slider = $('.about-gallery-wrapper').data('flexslider');
                        if (!slider) {
                            $('span#' + image_id).parents('li').fadeOut('slow').remove();
                        } else {
                            var pos = $('span#' + image_id).parents('li').index();
                            slider.removeSlide(pos) // removing selected slide
                        }

                        $('div.success-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                        scrollTo('div.success-msg', 100, 'top');
                        setTimeout("$('div.success-msg').slideUp('slow')", 5000);
                    } else {
                        $('div.error-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                        scrollTo('div.error-msg', 100, 'top');
                    }
                } else {
                    $('div.error-msg').html('<label>Failed to delete image..!!</label>').fadeIn('slow');
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
/* Image delete code end here */

/* Delete avtar code starts here */
$(document.body).on('click', 'input#delete_avtar', function() {
    if (confirm("Are you sure want to delete your avtar image..??")) {
        $.ajax({
            url: OVEconfig.BASEURL + '/practitioner/update/',
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

/* Workdays updation code starts here */
$(document.body).on('click', '#updateAvailability', function() {
    $('div.error-msg, div.success-msg').slideUp();
    $.ajax({
        url: OVEconfig.BASEURL + '/practitioner/update/?' + $('form#workdaysform').serialize(),
        type: 'POST',
        data: {action: 'workdays'},
        beforeSend: function() {
            $('.default-load').fadeIn();
        },
        success: function(data) {
            //console.log(data);
            if (data != "") {
                data = JSON.parse(data);
                if (data.status == '1') {
                    $('div#workdays').html(data.workdays);
                    $('div.success-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                    scrollTo('div.success-msg', 100, 'top');
                    setTimeout("$('div.success-msg').slideUp('slow')", 5000);
                } else {
                    $('div.error-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                    scrollTo('div.error-msg', 100, 'top');
                }
            } else {
                $('div.error-msg').html('<label>Failed to update workdays..!!</label>').fadeIn('slow');
                scrollTo('div.error-msg', 100, 'top');
            }
            $('div#rating-overlay').fadeOut('slow');
            $('.default-load').fadeOut();
        },
        error: function(xhr, errorType, errorMsg) {
            console.log
        }
    });
    return false;
});

$(document.body).on('click', '#updateWorkdays', function() {
    $('div#rating-overlay').fadeIn('slow');
    scrollTo('div#rating-overlay', 100, 'top');
});

$(document.body).on('click', '.remove', function() {
    $('div#rating-overlay').fadeOut('slow');
});
/* Workdays updation code ends here */

/* Profile updation code starts here */
$(document.body).on('click', '.head input.update', function() {
    $('.update').toggle('slow');
    $('div.error-msg, div.success-msg').slideUp();
    if ($(this).hasClass('save')) {
        $.ajax({
            url: OVEconfig.BASEURL + '/practitioner/update/',
            type: 'POST',
            data: {action: 'name', practitioner_name: $('input[name="practitioner_name"]').val(), contact_id: $('input[name="contact_id"]').data('val')},
            beforeSend: function() {
                $('.default-load').fadeIn();
            },
            success: function(data) {
                data = JSON.parse(data);
                if (data.status == '1') {
                    $('div.success-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                    setTimeout("$('div.success-msg').slideUp('slow')", 5000);
                    var practitionerName = $('.head input.update').parent('form').children('input[type="text"]').val();
                    $('.head input.update').parent('form').children('h3').html(practitionerName).show('slow');
                    $('.head input.update').parent('form').children('input[type="text"]').hide();
                    scrollTo('div.success-msg', 100, 'top');
                } else {
                    var errors = Array();
                    $.each(data.errors, function(key, value) {
                        if (key != 'detail') {
                            errors.push('<label style="text-transform:capitalize;">' + key.replace(/_/g, ' ') + ' - ' + value + '</label>');
                        } else {
                            errors.push('<label style="text-transform:capitalize;">Failed to update profile..!!</label>');
                        }
                    });
                    $('div.error-msg').html(errors.join('')).fadeIn('slow');
                    scrollTo('div.error-msg', 100, 'top');
                    //setTimeout($('div.error-msg').fadeOut('slow'), 4000);
                }
                $('.default-load').fadeOut();
            },
            error: function(xhr, errorType, errorMsg) {
                console.log
            }
        });

        return false;
    } else {
        var practitionerName = $(this).parent('form').children('h3').html();
        $(this).parent('form').children('h3').hide();
        $(this).parent('form').children('input[type="text"]').attr('value', practitionerName).show('slow');
    }
});

$(document.body).on('click', '.edit-head .comm-edit-button', function() {
    $('div.error-msg, div.success-msg').slideUp();
    var dataClass = $(this).attr('id');
    var ref = $(this);

    if ($(this).data('action') == 'save') {
        var action = $(this).parent().parent().find('.aboutme-form > form >input[name=action]').data('val');

        $.ajax({
            url: OVEconfig.BASEURL + '/practitioner/update/?' + $(this).parent().parent().find('.aboutme-form > form').serialize(),
            type: 'POST',
            data: {action: action},
            beforeSend: function() {
                $('.default-load').fadeIn();
            },
            success: function(data) {
                //console.log(data)
                if (data != '' && data != null) {
                    data = JSON.parse(data);
                    if (data.status == '1') {

                        assignData(dataClass, ref, data);

                        $('div.success-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                        setTimeout("$('div.success-msg').slideUp('slow')", 5000);
                        // returning new values
                        $('.' + dataClass).each(function(index, element) {
                            var id = $(this).attr('rel');

                            if (element.tagName == 'INPUT') {
                                if ($(element).attr('type') == 'text') {
                                    ($(this).val() != '') ? $('div#' + id).html($(this).val()) : $('div#' + id).html('Not Available');
                                } else if ($(element).attr('type') == 'radio') {
                                    ($('input[rel=' + id + '][value=1]').is(':checked')) ? $('div#' + id).html('Yes') : $('div#' + id).html('No');
                                } else if ($(element).attr('type') == 'checkbox') {
                                    var values = Array();
                                    $('input[type="checkbox"][class="' + dataClass + '"]:checked').each(function() {
                                        values.push($(this).parent().text().trim());
                                    });

                                    (values.length > 0) ? $('div#' + dataClass).html(values.join(', ')) : $('div#' + dataClass).html(values.join('Not Available'));
                                    //var values = $('div#' + dataClass).html().split(', ');
                                    //($('input[type=checkbox][class=' + dataClass + '] :checked').is(':checked')) ? $('div#' + id).html('Yes') : $('div#' + id).html('No');
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
                            } else if (element.tagName == 'TEXTAREA') {
                                console.log($(this).val());
                                ($(this).val() != '') ? $('div#' + id).html($(this).val()) : $('div#' + id).html('Not Available');
                            }

                        });

                        scrollTo('div.success-msg', 100, 'top');

                        ref.data('action', 'edit');
                        ref.html('EDIT');
                        ref.parent().parent().find('.aboutme-text').fadeIn('slow');
                        ref.parent().parent().find('.aboutme-form').fadeOut();

                    } else {
                        if (data.errors && data.errors.length > 0) {
                            var errors = Array();
                            $.each(data.errors, function(key, value) {
                                if (key != 'detail') {
                                    errors.push('<label style="text-transform:capitalize;">' + key.replace(/_/g, ' ') + ' - ' + value + '</label>');
                                } else {
                                    errors.push('<label style="text-transform:capitalize;">Failed to update profile..!!</label>');
                                }
                            });
                            $('div.error-msg').html(errors.join('')).fadeIn('slow');
                        } else {
                            $('div.error-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                        }

                        scrollTo('div.error-msg', 100, 'top');
                    }
                } else {
                    $('div.error-msg').html('<label>Unable to update profile..!!</label>').fadeIn('slow');
                    scrollTo('div.error-msg', 100, 'top');
                }

                $('.default-load').fadeOut();
            },
            error: function(xhr, errorType, errorMsg) {
                console.log(errorMsg);
                $('.default-load').fadeOut();
            }
        });

    } else {

        // assigning current values
        $('.' + dataClass).each(function(index, element) {
            var id = $(this).attr('rel');
            if (element.tagName == 'INPUT') {
                if ($(element).attr('type') == 'text') {
                    $(this).attr('value', $('div#' + id).html().replace('Not Available', ''));
                } else if ($(element).attr('type') == 'radio') {
                    ($('div#' + id).html() == 'Yes') ? $('input[rel=' + id + '][value=1]').prop('checked', true) : $('input[rel=' + id + '][value=0]').prop('checked', true);
                } else if ($(element).attr('type') == 'checkbox') {
                    var values = $('div#' + dataClass).html().split(', ');
                    (values.indexOf($(this).parent().text().trim()) != -1) ? $(this).prop('checked', true) : $(this).prop('checked', false);
                }
            } else if (element.tagName == 'SELECT') {
                if ($(element).prop('multiple') == true) {
                    var selected = $('div#' + id).html().split(', ');
                    $(this).children('option').each(function() {
                        (selected.indexOf($(this).text()) != -1) ? $(this).prop('selected', true) : '';
                    });
                } else {
                    var selected = $('div#' + id).html();
                    $(this).children('option').each(function() {
                        ($(this).text() == selected) ? $(this).prop('selected', true) : '';
                    });
                }
            } else if (element.tagName == 'TEXTAREA') {
                $(this).html($('div#' + id).html().trim().replace('Describe why people should choose you to render a treatment.', '').replace(/<br>/g, "\r\n").replace(/<br \/>/g, "\r\n"));
            }

        });

        $(this).data('action', 'save');
        $(this).html('UPDATE');
        $(this).parent().parent().find('.aboutme-text').fadeOut();
        $(this).parent().parent().find('.aboutme-form').fadeIn('slow');
        if ($('.autocomplete-suggestions').length > 0) {
            $('.address-autofill').autocomplete("destroy")
        }
        setTimeout("applyAutoComplete('.address-autofill', ['zip_code'])", 1000);
    }
});

function assignData(tab, ref, data)
{
    switch (tab) {
        case 'sp_address' :
            if (data.id) {
                ref.prev('span').data('val', data.id).attr('id', data.id);
                ref.parents('div.aboutme-edit-wrapper').find('input[name=address_id]').data('val', data.id).val(data.id);
            }
            updateAddressDropdowns(data, 'select.workdays_address');
            break;

        case 'contact_data' :
            if (data.id) {
                $('input[name=contact_id]').data('val', data.id).val(data.id);
            }
            break;

        case 'description' :
            if (data.id) {
                $('input[name=detail_id]').data('val', data.id).val(data.id);
            }
            break;

        case 'profileData' :
            if (data.data) {
                $('input[name=detail_id]').data('val', data.data.detail_id).val(data.data.detail_id);
                $('input[name=address_id]').data('val', data.data.address_id).val(data.data.address_id);
            }
            break;
    }
}

function updateAddressDropdowns(data, element)
{
    if (typeof data.addresses != 'undefined' && data.addresses != null) {
        var addresses = ['<option value="">Select</option>'];
        $.each(data.addresses, function(key, address) {
            addresses.push('<option value="' + address['id'] + '">' + address['street1_address'] + ', ' + address['city'] + ', ' + address['state_name'] + ' ' + address['zip_code'] + ', ' + address['country_name'] + '</option>');
        });
        $(element).html(addresses.join(''));
    }
}
/* Profile updation code ends here */

/* Service rendering address update code starts here */
$(document.body).on('click', 'span.btn-add-place', function() {
    var total = $('span#sp_address').length;
    if (total < 4) {
        $.ajax({
            url: OVEconfig.BASEURL + '/practitioner/update/',
            type: 'POST',
            dataType: 'json',
            data: {action: 'sp_address', layout: 'true', id: (total + 1)},
            beforeSend: function() {
                $('.default-load').fadeIn();
            },
            success: function(data) {

                if (data) {
                    if ((total + 1) == 4) {
                        $('span.btn-add-place').parent().slideUp('slow').remove();
                    }

                    if (total > 0) {
                        $('span#sp_address').last().parents('div.aboutme-edit-wrapper').after(data.layout);
                    } else {
                        $('span.btn-add-place').before(data.layout);
                    }

                    setTimeout("applyAutoComplete('.address-autofill', ['zip_code'])", 1000);

                    $('input[type=hidden]').each(function() {
                        $(this).data('val', $(this).val());
                    });
                }

                $('.default-load').fadeOut();
            },
            error: function(xhr, errorType, errorMsg) {
                console.log(errorMsg);
                $('.default-load').fadeOut();
            }
        });
    } else {
        $('div.error-msg').html('<label>You can not add more than 4 workplace address..!!</label>').fadeIn('slow');
        scrollTo('div.error-msg', 100, 'top');
    }
});

$(document.body).on('click', 'span.remove_address', function() {
    var address_id = $(this).attr('id');
    var ref = $(this);

    if (address_id != '') {
        $.ajax({
            url: OVEconfig.BASEURL + '/practitioner/update/',
            type: 'POST',
            data: {action: 'remove_sp_address', id: address_id},
            beforeSend: function() {
                $('.default-load').fadeIn();
            },
            success: function(data) {
                if (data) {
                    data = JSON.parse(data);
                    if (data.status == '1') {
                        updateAddressDropdowns(data, 'select.workdays_address');
                        ref.parents('div.aboutme-edit-wrapper').slideUp('slow').remove();
                        $('div.success-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                        scrollTo('div.success-msg', 100, 'top');
                    } else {
                        $('div.error-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                        scrollTo('div.error-msg', 100, 'top');
                    }
                } else {
                    $('div.error-msg').html('<label>Unable to delete workplace address..!!</label>').fadeIn('slow');
                    scrollTo('div.error-msg', 100, 'top');
                }
                $('.default-load').fadeOut();
            },
            error: function(xhr, errorType, errorMsg) {
                console.log(errorMsg);
                $('.default-load').fadeOut();
            }
        });
    } else {
        ref.parents('div.aboutme-edit-wrapper').slideUp('slow').remove();
    }
});
/* Service rendering address update code ends here */

$('#checkAllservices').on('click', function(event) {
    if (this.checked) {
        $('.checkServices').each(function() {
            this.checked = true;
        });
    } else {
        $('.checkServices').each(function() {
            this.checked = false;
        });
    }
});

$('#checkAllbookings').on('click', function(event) {
    if (this.checked) {
        $('input.checkBookings').each(function() {
            this.checked = true;
        });
    } else {
        $('input.checkBookings').each(function() {
            this.checked = false;
        });
    }
});

$('#checkAllnewsletter').on('click', function(event) {
    if (this.checked) {
        $('.checkNewsletter').each(function() {
            this.checked = true;
        });
    } else {
        $('.checkNewsletter').each(function() {
            this.checked = false;
        });
    }
});

$(function() {
    // get all services 
    getAllServiceName();

    $('#deleteSpservices').on('click', function() {
        if ($('.checkServices').is(':checked')) {
            var id_list = new Array()
            $('.checkServices:checked').each(function() {
                id_list.push(this.value);
            });
            deleteSpservice(id_list);
        } else {
            //$('#errorMsg').html("Please select atleat one");
            $('.error-msg').html("<label>Please select atleat one</label>").fadeIn('slow');
            scrollTo('div.error-msg', 100, 'top');
        }
        return false;
    });

    if ($('input#total_services').val() > 0) {
        // Services pagination
        paginateServices($('input#total_services').val());
    }

    if ($('input#total_bookings').val() > 0) {
        // booking pagination
        paginateBookings($('input#total_bookings').val());

    } else {
        $('div.services-data > table#bookingTable >tbody').html('<tr class="recent"><td colspan="6"> No Records Found</td></tr>');
    }

    if ($('input#total_feedbacks').val() > 0) {
        // feedback pagination
        paginateFeedbacks($('input#total_feedbacks').val());
    } else {
        $('div.services-data > table.dashReview >tbody').html('<tr><td colspan="5"> No Records Found</td></tr>');
    }


    if ($('input#total_newsletters').val() > 0) {
        // newsletter pagination
        paginateNewsletter($('input#total_newsletters').val());

    } else {
        $('div.services-data > table#newsletterTable >tbody').html('<tr><td colspan="5"> No Records Found</td></tr>');
    }

    $('#sp_id').data('spid', $('#sp_id').val())

});



/* Service code starts here*/
function getAllServiceName() {
    var services = Array();

    $.ajax({
        url: OVEconfig.BASEURL + '/practitioner/getspservices/',
        type: 'POST',
        async: false,
        data: {all: true, sp_id: $('input#sp_id').val()},
        dataType: 'json',
        success: function(data) {
            services = data.services_list;
        },
        error: function(xhr, errorType, errorMsg) {
            console.log(errorMsg)
        }
    });

    var servicename = 'No Services';

    if (services != null && services.length > 0) {
        var servicename = '';
        for (var i = 0; i < services.length; i++)
        {
            servicename += (services[i].name) ? ((i < services.length - 1) ? (services[i].name + ",") : services[i].name) : (servicename);
        }
    }
    $('#servicename').html(servicename);
}

$('#addservices').on('submit', function() {
    $('div.error-msg, div.success-msg').slideUp();
    var service_id = $('#service_id').val();
    var duration = $('#duration').val();
    var price = $('#price').val();
    var action = $('#sp_action').val();
    var sp_edit_id = $('#sp_edit_id').val();

    $.ajax({
        url: OVEconfig.BASEURL + '/practitioner/services/',
        type: 'POST',
        dataType: 'json',
        data: {service_id: service_id, duration: duration, price: price, action: action, sp_edit_id: sp_edit_id},
        beforeSend: function() {
            $('.default-load').fadeIn();
        },
        success: function(data) {

            if (data) {
                //alert(data);
                if (data.status == '1') {

                    (sp_edit_id == '') ? paginateServices(parseInt($('input#total_services').val()) + 1) : paginateServices(parseInt($('input#total_services').val()));  // regenerate pagination

                    $('.success-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                    scrollTo('div.success-msg', 100, 'top');

                } else {
                    $('.error-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                    scrollTo('div.error-msg', 100, 'top');
                }
            }
            $('.add-Services span').trigger('click');
            $('.default-load').fadeOut();
        },
        error: function(xhr, errorType, errorMsg) {
            console.log(errorMsg);
            $('.default-load').fadeOut();
        }
    });

    return false;
    exit;

});

function editSpservice(DataElement) {

    var catid = $(DataElement).data('catid');
    var duration = $(DataElement).data('duration');
    var price = $(DataElement).data('price');
    var id = $(DataElement).data('id');

    $('.add-Services').fadeIn();

    /* get form filled with the selected values */
    $('#sp_edit_id').val(id);
    $('#sp_action').val('edit');
    $('.add-head h3').html('Edit Service')
    $("select#service_id option[value='" + catid + "']").attr("selected", "selected");
    $("#duration").val(duration);
    $("#price").val(price);
    scrollTo('.add-Services', 100, 'bottom');
}

function deleteSpservice(id) {

    if (confirm("Are you sure?")) {
        if (id != '') {
            $.ajax({
                url: OVEconfig.BASEURL + '/practitioner/deleteservice/',
                type: 'POST',
                dataType: 'json',
                data: {id: id},
                beforeSend: function() {
                    $('.default-load').fadeIn();
                },
                success: function(data) {
                    $('.default-load').fadeOut();
                    if (data.error == false) {

                        paginateServices(parseInt($('input#total_services').val()) - 1);  // Regenerate pagination

                        $('.error-msg').html('');
                        $('.success-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                        scrollTo('div.error-msg', 100, 'top');
                    } else {
                        //$('#errorMsg').html(data.msg);
                        //$('#successMsg').html('');
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
/* Service code ends here*/

/* Booking change status code starts here */
$(document.body).on('click', 'input#changeStatus', function() {
    $('div.error-msg, div.success-msg').slideUp();
    var status_id = $('select#status').val();
    var ids = Array();
    var page = $('input#page').val();

    $('input.checkBookings:checked').each(function() {
        ids.push($(this).val());
    });

    changeBookingStatus(ids, status_id);

    return false;
});
/* Booking change status code ends here */

/* Newsletter save code starts here */
$(document.body).on('click', 'span[rel="add-Newsletter"]', function() {
    $('div#add-Newsletter').find('input#subject, input#newsletter_id').val('');
    tinymce.get('message').setContent('');
});

$(document.body).on('click', 'span#newsletterEdit', function() {
    $('div#add-Newsletter').fadeIn();
    $('input#newsletter_id').val($(this).data('val'));

    $.ajax({
        url: OVEconfig.BASEURL + '/practitioner/newsletters/',
        type: 'POST',
        data: {newsletter_id: $(this).data('val')},
        dataType: 'json',
        success: function(data) {

            $('div#add-Newsletter').find('h3').html('Edit Newsletter');
            $('input#subject').val(data.subject);
            tinymce.get('message').setContent(data.message);
        },
        error: function(xhr, errorType, errorMsg) {
            console.log(errorMsg);
        }
    });

    scrollTo('div#add-Newsletter', 100, 'bottom');
});

$(document.body).on('click', 'input.saveNewsletter', function() {
    $('div.error-msg, div.success-msg').slideUp();

    var buttonId = $(this).attr('id');
    $.ajax({
        url: OVEconfig.BASEURL + '/practitioner/newsletters/',
        type: 'POST',
        data: {subject: $('input#subject').val(), message: tinymce.get('message').getContent(), id: $('input#newsletter_id').val()},
        dataType: 'json',
        beforeSend: function() {
            $('.default-load').fadeIn();
        },
        success: function(data) {
            if (data.status == '1') {
                if (buttonId == 'send') {
                    window.location = OVEconfig.BASEURL + '/practitioner/sendnewsletter/' + data.id;
                } else {

                    ($('input#newsletter_id').val() == '') ? paginateNewsletter(parseInt($('input#total_newsletters').val()) + 1) : paginateNewsletter(parseInt($('input#total_newsletters').val()));    // regenerate pagination

                    $('div#add-Newsletter').find('h3').html('Add new Newsletter');
                    $('div#add-Newsletter').find('input#subject, input#newsletter_id').val('');
                    tinymce.get('message').setContent('');
                    $('div#add-Newsletter').slideUp('slow'); // close the form

                    $('div.success-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                    scrollTo('div.success-msg', 100, 'top');
                    setTimeout("$('div.success-msg').slideUp('slow')", 5000);
                }
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
            console.log(errorMsg)
        }
    });

    return false;
});
/* Newsletter save code ends here */

/* Newsletter delete code starts here */
$(document.body).on('click', 'span#newsletterDelete', function() {
    $('div.error-msg, div.success-msg').slideUp();
    var ids = Array();
    if ($(this).data('val')) {
        ids.push($(this).data('val'));
    }

    $('input.checkNewsletter:checked').each(function() {
        ids.push($(this).val());
    });

    if (ids.length > 0) {
        if (confirm('Are you sure want to delete selected records..??')) {
            $.ajax({
                url: OVEconfig.BASEURL + '/practitioner/newsletters/',
                type: 'POST',
                data: {ids: ids, delete_request: '1'},
                dataType: 'json',
                beforeSend: function() {
                    $('.default-load').fadeIn();
                },
                success: function(data) {
                    console.log(data);
                    if (data.status == '1') {

                        paginateNewsletter($('input#total_newsletters').val() - 1);  // regenerate pagination

                        $('div.success-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                        scrollTo('div.success-msg', 100, 'top');
                        setTimeout("$('div.success-msg').slideUp('slow')", 5000);
                    } else {
                        $('div.error-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                        scrollTo('div.error-msg', 100, 'top');
                    }
                    $('.default-load').fadeOut();
                },
                error: function(xhr, errorType, errorMsg) {
                    console.log(errorMsg)
                },
            });
        }
    } else {
        $('div.error-msg').html('<label>Please select at least 1 record to delete..!!</label>').fadeIn('slow');
        scrollTo('div.error-msg', 100, 'top');
    }
});
/* Newsletter delete code ends here */

/* Newsletter change status code starts here */
$(document.body).on('click', 'input#changeStatusNewsletter', function() {
    $('div.error-msg, div.success-msg').slideUp();
    var status_id = $('select#newsletterStatus').val();
    var ids = Array();
    var page = $('input#page').val();

    $('input.checkNewsletter:checked').each(function() {
        ids.push($(this).val());
    });

    if (ids.length > 0) {
        if (status_id != '') {

            $.ajax({
                url: OVEconfig.BASEURL + '/practitioner/newsletters/',
                type: 'POST',
                data: {ids: ids, status: status_id},
                beforeSend: function() {
                    $('.default-load').fadeIn();
                },
                success: function(data) {
                    console.log(data);
                    if (data != "") {
                        data = JSON.parse(data);
                        if (data.status == '1') {

                            paginateNewsletter($('input#total_newsletters').val());

                            $('div.success-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                            scrollTo('div.success-msg', 100, 'top');
                            setTimeout("$('div.success-msg').slideUp('slow')", 5000);
                        } else {
                            $('div.error-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                            scrollTo('div.error-msg', 100, 'top');
                        }
                    } else {
                        $('div.error-msg').html('<label>Failed to change status of Newsletter..!!</label>').fadeIn('slow');
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

    return false;
});
/* Newsletter change status code ends here */

/* Pagination functions start here */
function paginateServices(totalServices)
{
    $("div#services-pagination ul").pagination(totalServices, {
        callback: servicesCallback,
        items_per_page: 5,
        num_display_entries: 10,
        num_edge_entries: 2,
        current_page: $('input#page').val(),
        prev_text: '&lt;',
        next_text: '&gt;'
    });

}

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

function paginateFeedbacks(totalFeedbacks)
{
    $("div#feedback-pagination ul").pagination(totalFeedbacks, {
        callback: feedbackCallback,
        items_per_page: 5,
        num_display_entries: 10,
        num_edge_entries: 2,
        current_page: $('input#page').val(),
        prev_text: '&lt;',
        next_text: '&gt;'
    });
}

function paginateNewsletter(totalNewsletters)
{
    $("div#newsletter-pagination ul").pagination(totalNewsletters, {
        callback: newsletterCallback,
        items_per_page: 5,
        num_display_entries: 10,
        num_edge_entries: 2,
        current_page: $('input#page').val(),
        prev_text: '&lt;',
        next_text: '&gt;'
    });
}



function servicesCallback(page_index, jq)
{
    $("div.pagination-list ul li:empty").remove(); // Removing blank elements
    $('input#page').val(page_index);  // storing current page number

    var services = Array();
    var items_per_page = 5;
    var page = parseInt(page_index) + 1;

    $.ajax({
        url: OVEconfig.BASEURL + '/practitioner/getspservices/',
        type: 'POST',
        async: false,
        data: {page: page, items: items_per_page, sp_id: $('input#sp_id').val()},
        dataType: 'json',
        success: function(data) {
            services = data.services_list;
        },
        error: function(xhr, errorType, errorMsg) {
            console.log(errorMsg)
        }
    });

    var max_elem = (services != null) ? Math.min((page_index + 1) * items_per_page, services.length) : 0;
    var newcontent = Array();

    if (services != null && services.length > 0) {
        //$('#total_services').val(services.length);
        // Iterate through a selection of the content and build an HTML string

        for (var i = 0; i < max_elem; i++)   //page_index*items_per_page
        {

            if (services[i].status_id == "1") {
                var status = 'Active';
            } else if (services[i].status_id == "2") {
                var status = 'Inactive';
            } else {
                var status = 'Inactive';
            }


            newcontent.push("<tr>");
            newcontent.push("<td>" + services[i].name + "</td>");
            newcontent.push("<td>" + services[i].duration + " Mins</td>");
            newcontent.push("<td>$" + services[i].price + "</td>");
            newcontent.push("<td>" + status + "</td>");
            newcontent.push("<td><div class='select-form'><form style='display:inline'><label for='select-all'>");
            newcontent.push("<input class='checkServices' type='checkbox' value=" + services[i].id + " data-duration=" + services[i].duration + " data-catid=" + services[i].category_id + "><span></span></label></form>");
            //newcontent.push("<span onclick='deleteSpservice([" + services[i].id + "])' class='delete'>D</span>");
            //newcontent.push("<span data-duration=" + services[i].duration + " data-price=" + services[i].price + " data-id=" + services[i].id + " data-catid=" + services[i].category_id + " onclick='editSpservice(this)' class='edit' ></span>");
            newcontent.push("</td></tr>");
        }

    } else {
        newcontent.push('<tr class="recent"><td colspan="5"> No Records Found</td></tr>');
    }

    // Replace old content with new content

    $('div.services-data > table#tbl_Splist >tbody').html(newcontent.join(''));

    // Prevent click eventpropagation
    return false;
}

function bookingCallback(page_index, jq) {

    $("div.pagination-list ul li:empty").remove(); // Removing blank elements
    $('input#page').val(page_index);  // storing current page number

    var bookings = Array();
    var items_per_page = 5;
    var page = parseInt(page_index) + 1;

    $.ajax({
        url: OVEconfig.BASEURL + '/booking/getbooking/',
        type: 'POST',
        async: false,
        data: {page: page, items: items_per_page, user_id: $('input#sp_id').val()},
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

    if (bookings != null && bookings.length > 0) {
        // Iterate through a selection of the content and build an HTML string
        for (var i = 0; i < max_elem; i++)
        {
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

                default :
                    var status = 'Pending Approval';
                    break;
            }

            var booked_date = new Date(bookings[i].booking_status.booking_time.replace('+00:00', '').replace(/-/g, '/'));
            var booked_timestamp = booked_date.getTime() / 1000;
            var n = booked_date;
            n.setDate(n.getDate() - 2);
            var locked_date = new Date(n);
            var locked_timestamp = locked_date.getTime() / 1000;
            var d = new Date();
            var current_timestamp = d.getTime() / 1000;

            var avatar_url = (typeof bookings[i].consumer_avtar_url == 'undefined' || bookings[i].consumer_avtar_url == "None" || bookings[i].consumer_avtar_url == "" || bookings[i].consumer_avtar_url == null) ? '/img/profile-pic.jpg' : bookings[i].consumer_avtar_url.replace('Media', 'Media_thumb');

            newcontent.push('<tr class="recent">');
            newcontent.push('<td>' + bookings[i].id + '</td>');
            newcontent.push('<td><span class="profile"><img src="' + avatar_url + '" alt="" /></span>' + bookings[i].consumer_first_name + ' ' + bookings[i].consumer_last_name + '</td>');
            newcontent.push('<td><div class="bookingTime">' + formatDate(bookings[i].booking_status.booking_time, 'Day d/m/Y h:i A') + '</div>');
            newcontent.push('<input class="datetimepicker" readonly="" style="display: none;" />');
            newcontent.push('</td>');
            newcontent.push('<td>' + bookings[i].category_name + '</td>');
            newcontent.push('<td>' + status + '</td>');
            newcontent.push('<td>');
            /*newcontent.push('<div class="select-form">');
             newcontent.push('<form>');
             newcontent.push('<label for="select-all">');
             newcontent.push('<input type="checkbox" class="checkBookings" value="' + bookings[i].id + '"><span></span>');
             newcontent.push('</label>');
             newcontent.push('</form></div>');*/

            if ((bookings[i].booking_status.user_id != bookings[i].service_provider_id || bookings[i].booking_status.status_id == '4') && bookings[i].booking_status.confirmations < 3 && bookings[i].booking_status.status_id != '6' && (current_timestamp <= locked_timestamp || bookings[i].booking_status.status_id == '5') && current_timestamp < booked_timestamp) {
                //if ((bookings[i].booking_status.user_id != bookings[i].service_provider_id || bookings[i].booking_status.status_id == '4') && bookings[i].booking_status.confirmations < 3 && bookings[i].booking_status.status_id != '6' && current_timestamp <= locked_timestamp) {
                newcontent.push('<div class="update reschedule">');
                newcontent.push('<span class="btn-rating btn-reschedule reschedule" id="' + bookings[i].id + '" data-durtn="' + bookings[i].duration + '" data-sp="' + bookings[i].service_provider_id + '" data-address="' + bookings[i].service_address_id + '">New Date & Time</span>');
                (bookings[i].booking_status.status_id == '5') ? newcontent.push('<span class="btn-rating btn-ok bookingConfirm" id="' + bookings[i].id + '">Confirm</span>') : '';
                (bookings[i].booking_status.status_id == '5') ? newcontent.push('<span class="btn-rating btn-cancel bookingCancel" id="' + bookings[i].id + '">Cancel</span>') : '';
                newcontent.push('</div>');
                newcontent.push('<div class="update send" style="display:none;">');
                newcontent.push('<span class="btn-rating btn-ok bookingReschedule" id="' + bookings[i].id + '" >Confirm</span>');
                newcontent.push('<span class="btn-rating btn-cancel cancel" id="' + bookings[i].id + '">Cancel</span>');
                newcontent.push('</div>');
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

function feedbackCallback(page_index, jq)
{
    $("div.pagination-list ul li:empty").remove(); // Removing blank elements
    $('input#page').val(page_index);  // storing current page number

    var feedbacks = Array();
    var items_per_page = 5;
    var page = parseInt(page_index) + 1;

    $.ajax({
        url: OVEconfig.BASEURL + '/practitioner/feedbacks/',
        type: 'POST',
        async: false,
        data: {page: page, items: items_per_page},
        dataType: 'json',
        success: function(data) {
            feedbacks = data;
        },
        error: function(xhr, errorType, errorMsg) {
            console.log(errorMsg)
        }
    });

    var max_elem = Math.min((page_index + 1) * items_per_page, feedbacks.length);
    var newcontent = Array();

    if (feedbacks.length > 0) {
        // Iterate through a selection of the content and build an HTML string
        for (var i = 0; i < max_elem; i++)   //page_index*items_per_page
        {
            switch (feedbacks[i].status_id) {
                case '9' :
                    var status = 'approved';
                    break;

                case '10' :
                    var status = 'decline';
                    break;

                default :
                    var status = 'decline';
                    break;
            }
            
            var avtar = (typeof feedbacks[i].avtar_url != 'undefined' && feedbacks[i].avtar_url != "" && feedbacks[i].avtar_url != "None" && feedbacks[i].avtar_url != null)?feedbacks[i].avtar_url:"/img/profile-pic.jpg";
            
            newcontent.push("<tr>");
            newcontent.push("<td><div class='review-img'><img alt='' src='" + avtar + "'></div></td>");
            newcontent.push("<td>" + feedbacks[i].comments);
            newcontent.push("<p>By <span>" + feedbacks[i].first_name + " " + feedbacks[i].last_name + "</span></p></td>");
            newcontent.push("<td>");
            /*newcontent.push("<div class='select-form'><form>");
             newcontent.push("<label for='select-all'>");
             newcontent.push("<input type='checkbox' value='yes'><span></span>");
             newcontent.push("</label>");
             newcontent.push("</form>");
             newcontent.push("</div><span class='delete'>D</span>");*/
            newcontent.push("</td></tr>");

        }
    } else {
        newcontent.push('<tr class="recent"><td colspan="4"> No Records Found</td></tr>');
    }

    // Replace old content with new content
    $('div.services-data > table.dashReview >tbody').html(newcontent.join(''));

    // Prevent click eventpropagation
    return false;
}

function newsletterCallback(page_index, jq)
{
    $("div.pagination-list ul li:empty").remove(); // Removing blank elements
    $('input#page').val(page_index);  // storing current page number

    var newsletters = Array();
    var items_per_page = 5;
    var page = parseInt(page_index) + 1;

    $.ajax({
        url: OVEconfig.BASEURL + '/practitioner/newsletters/',
        type: 'POST',
        async: false,
        data: {page: page, items: items_per_page},
        dataType: 'json',
        success: function(data) {
            newsletters = data;
        },
        error: function(xhr, errorType, errorMsg) {
            console.log(errorMsg)
        }
    });

    var max_elem = Math.min((page_index + 1) * items_per_page, newsletters.length);
    var newcontent = Array();

    if (newsletters.length > 0) {
        // Iterate through a selection of the content and build an HTML string
        for (var i = 0; i < max_elem; i++)   //page_index*items_per_page
        {
            switch (newsletters[i].status_id) {
                case 1 :
                    var status = 'Active';
                    break;

                case 2 :
                    var status = 'Inactive';
                    break;

                default :
                    var status = 'Inactive';
                    break;
            }

            send_date = (newsletters[i].send_date != null) ? newsletters[i].send_date : 'Not Set';

            newcontent.push("<tr>");
            newcontent.push("<td>" + newsletters[i].subject + "</td>");
            newcontent.push("<td>" + trim_words($(newsletters[i].message).text(), 4) + "</td>");
            newcontent.push("<td>" + send_date + "</td>");
            //newcontent.push("<td>" + status + "</td>");
            newcontent.push("<td><div class='select-form'>");
            newcontent.push("<form>");
            newcontent.push("<label for='select-all'>");
            //newcontent.push("<input type='checkbox' class='checkNewsletter' value='" + newsletters[i].id + "'><span></span>");
            newcontent.push("</label>");
            newcontent.push("</form></div>");
            newcontent.push("<span class='delete' id='newsletterDelete' data-val='" + newsletters[i].id + "'>D</span>");
            newcontent.push("<span class='edit' id='newsletterEdit' data-val='" + newsletters[i].id + "'>D</span>");
            newcontent.push("<span class='edit' id='newsletterSend' onclick='window.location.href=\"" + OVEconfig.BASEURL + "/practitioner/sendnewsletter/" + newsletters[i].id + "\"'>D</span>");
            newcontent.push("</td></tr>");

        }
    } else {
        newcontent.push('<tr><td colspan="5"> No Records Found</td></tr>');
    }

    // Replace old content with new content
    $('div.services-data > table#newsletterTable >tbody').html(newcontent.join(''));

    // Prevent click eventpropagation
    return false;
}
/* Pagination functions end here */



/* Change status for service code starts here */
$(document.body).on('change', 'select#service_status', function() {
    if ($(this).val() != '') {
        var service_ids = Array();
        $('input.checkServices:checked').each(function() {
            //service_ids.push($(this).val());
            service_ids[$(this).val()] = [$(this).data('duration'), $(this).data('catid')];
        });

        if (service_ids.length > 0) {
            if (confirm('Are you sure want to change status to ' + $(this).children('option:selected').text() + ' of selected records??')) {
                $.ajax({
                    url: OVEconfig.BASEURL + '/practitioner/services/',
                    type: 'POST',
                    data: {action: 'change_status', ids: service_ids, status_id: $(this).val()},
                    beforeSend: function() {
                        $('.default-load').fadeIn();
                    },
                    success: function(data) {
                        console.log(data);

                        if (data != '') {
                            data = JSON.parse(data);
                            if (data.status == '1') {
                                $('#checkAllservices').prop('checked', false);
                                paginateServices($('input#total_services').val());
                                $('div.success-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                                setTimeout("$('div.success-msg').slideUp('slow')", 5000);
                                scrollTo('div.success-msg', 100, 'top');
                            } else {
                                $('div.error-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                                scrollTo('div.error-msg', 100, 'top');
                            }
                        } else {
                            $('div.error-msg').html('<label>Unable to change status..!!</label>').fadeIn('slow');
                            scrollTo('div.error-msg', 100, 'top');
                        }

                        $('.default-load').fadeOut();
                    }
                });
            }
        } else {
            $('div.error-msg').html('<label>Please select at least one record to update..!!</label>').fadeIn('slow');
            scrollTo('div.error-msg', 100, 'top');
        }
    } else {
        $('div.error-msg').html('<label>Please select status to change..!!</label>').fadeIn('slow');
        scrollTo('div.error-msg', 100, 'top');
    }

    return false;
});
/* Change status for service code ends here */

