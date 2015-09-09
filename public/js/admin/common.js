$(function() {

    /* Check all code starts here */
    $(document.body).on('click', 'input.group-checkable', function() {
        if ($(this).is(':checked')) {
            $('input.checkboxes').prop('checked', true);
        } else {
            $('input.checkboxes').prop('checked', false);
        }
    });
    /* Check all code ends here */

    /* Country and state link code starts here */
    $(document.body).on('change', '.getStates', function() {
        var country_id = $(this).val();
        var ref = $(this);
        var state_dropdown_id = $(this).data('id');

        if (country_id != "") {
            $(ref).closest('div.row').find('select#' + state_dropdown_id).select2('val', '');
            $.ajax({
                url: OVEconfig.BASEURL + '/admin/states/getstates',
                type: 'POST',
                data: {country: country_id},
                dataType: 'json',
                beforeSend: function(){$('.default-load').fadeIn();},
                success: function(data) {
                    var states = Array();
                    states.push('<option value="" > --- Choose State --- </option>');
                    $.each(data, function(key, state) {
                        states.push('<option value="' + state.id + '" >' + state.name + '</option>');
                    });
                    $(ref).closest('div.row').find('select#' + state_dropdown_id).html(states.join(''));
                    $('.default-load').fadeOut();
                },
                error: function(xhr, errorType, errorMsg) {
                    console.log(errorMsg); $('.default-load').fadeOut();
                }
            });
        }
    });
    /* Country and state link code ends here */

    /* Change status code starts here */
    $(document.body).on('click', 'button.changeStatus', function() {
        var status_id = $('select.action').val();
        var status = $('select.action  option:selected').text();
        var path = $(this).data('path');
        var rowIds = Array();
        var services = Array();  // For feedback module
        var statusClasses = Array();

        /* status classes according to lookup_status status_id field */
        statusClasses[0] = 'label-danger'; // default entry

        statusClasses[1] = 'label-success';
        statusClasses[2] = 'label-warning';
        statusClasses[3] = 'label-danger';

        statusClasses[4] = 'label-success';
        statusClasses[5] = 'label-warning';
        statusClasses[6] = 'label-danger';

        statusClasses[7] = 'label-success';
        statusClasses[8] = 'label-danger';

        statusClasses[9] = 'label-success';
        statusClasses[10] = 'label-danger';

        $('input.checkboxes:checked').each(function() {
            rowIds.push($(this).val());

            // for feedback module
            if (path.indexOf('feedback') != -1) {
                services.push($(this).data('srvc'));
            }
        });

        $('div#error').html('').removeClass('note-warning note-success note-danger').hide('slow');

        if (status != "" && rowIds.length > 0) {
            if (confirm("Are sure want to change the status of selected record..??")) {
                $.ajax({
                    url: (OVEconfig.BASEURL + path),
                    type: "POST",
                    data: {id: rowIds, status: status_id, services: services},
                    dataType: 'json',
                    beforeSend: function(){$('.default-load').fadeIn();},
                    success: function(data) {
                        $.each(rowIds, function(key, value) {

                            if (path.indexOf('feedback') != -1) {
                                $('span[id="' + value + '"][data-srvc="' + services[key] + '"]').removeClass('label-warning label-success label-danger label-default').addClass(statusClasses[status_id]).html(status);
                            } else if (isNaN(status_id)) {
                                statusClass = (status_id.indexOf('disable') == -1) ? 'label-success' : 'label-danger';
                                statusText = (status_id.indexOf('disable') == -1) ? 'Enabled' : 'Disabled';
                                $('span[id="' + value + '"][class*="' + status_id + '"]').removeClass('label-warning label-success label-danger label-default').addClass(statusClass).html(statusText);
                            } else {
                                $('span[id="' + value + '"][class*="status"]').last().removeClass('label-warning label-success label-danger label-default').addClass(statusClasses[status_id]).html(status);
                            }
                            //if (statusClasses[status_id] == 'label-success') { $('a[id="'+value+'"]').show('slow');} else {$('a[id="'+value+'"]').hide('slow');}
                        });
                        $('div#error').html(data.msg).addClass('note-success').show('slow');
                        $('.default-load').fadeOut();
                    },
                    error: function(xhr, errorType, errorMsg) {
                        console.log(errorMsg); $('.default-load').fadeOut();
                    },
                });
            }
        } else {
            $('div#error').html('Please select at least 1 record to change the status..!!').addClass('note-warning').show('slow');
        }

    });
    /* Change status code ends here */


    /* Delete all code starts here */
    $(document.body).on('click', 'button.deleteAll', function() {
        var rowIds = Array();
        var path = $(this).data('path');

        // getting all selected rows
        $('input.checkboxes:checked').each(function() {
            rowIds.push($(this).val());
        });

        $('div#error').html('').removeClass('note-warning note-success note-danger').hide('slow');

        if (rowIds.length > 0) {
            if (confirm("Are sure want to delete the selected record..??")) {
                $.ajax({
                    url: path + "deleteAll",
                    type: "POST",
                    data: {id: rowIds},
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == 'success') {
                            document.location.reload();
                        } else {
                            $('div#error').html('Unable to delete selected records..!!').addClass('note-danger').show('slow');
                        }
                    },
                    error: function(xhr, errorType, errorMsg) {
                        console.log(errorMsg);
                    },
                });
            }
        } else {
            $('div#error').html('Please select atleast 1 record to delete..!!').addClass('note-warning').show('slow');
        }

    });
    /* Delete all code ends here */

    /* Delete all code for ratings starts here */
    $(document.body).on('click', 'button.deleteAllratings', function() {
        var usrIds = Array();
        var serIds = Array();
        var crtIds = Array();
        var rtiIds = Array();
        var path = $(this).data('path');

        // getting all selected rows
        $('input.checkboxes:checked').each(function() {
            usrIds.push($(this).data('usr'));
            serIds.push($(this).data('srv'));
            crtIds.push($(this).data('crtd'));
            rtiIds.push($(this).data('rti'));
        });

        $('div#error').html('').removeClass('note-warning note-success note-danger').hide('slow');

        if (usrIds.length > 0) {
            if (confirm("Are sure want to delete the selected record..??")) {
                $.ajax({
                    url: path + "deleteAll",
                    type: "POST",
                    data: {users: usrIds, servs: serIds, creats: crtIds, rtids: rtiIds},
                    success: function(data) {
                        var table = $(data).find('table#rating-table').children('tbody').html();
                        var pagination = $(data).find('div.pagination').html();

                        $('table#rating-table').children('tbody').html(table);
                        $('div.pagination').html(pagination);

                        $('div#error').html('Records delete successfully..!!').addClass('note-success').show('slow');
                        $('input.star').rating();

                    },
                    error: function(xhr, errorType, errorMsg) {
                        console.log(errorMsg);
                    },
                });
            }
        } else {
            $('div#error').html('Please select atleast 1 record to delete..!!').addClass('note-warning').show('slow');
        }

    });
    /* Delete all code for ratings ends here */

    /* Add more fields code starts here */
    $(document.body).on('click', 'button.addMore', function() {
        var position = $(this).data('pos');
        var content = '<div class="form-group">' + $('div.form-group:eq(' + position + ')').html() + '</div>';
        $('div.form-group:eq(' + position + ')').after(content);
        $(this).data('pos', (position + 1));
        $('input.form-control:last').removeAttr('value');
        return false;
    });
    /* Add more fields code ends here */

    /* Remove fields code starts here */
    $(document.body).on('click', 'button.editable-cancel', function() {
        $(this).parent().parent().remove();
        return false;
    });
    /* Remove fields code ends here */

    /* Get services of service providers code starts here */
    $(document.body).on('change', 'select.getServices', function() {
        var sp = $(this).val();
        if (sp != "") {
            $.ajax({
                url: OVEconfig.BASEURL + '/admin/services/practitionerservices/',
                type: 'POST',
                data: {id: sp},
                dataType: 'json',
                beforeSend: function(){$('.default-load').fadeIn();},
                success: function(data) {
                    if (data.services != null && typeof data.services != 'undefined' && data.services.length > 0) {
                        var options = Array();
                        options.push('<option value="">--- choose service ---</option>');
                        $.each(data.services, function(key, value) {
                            options.push('<option value="' + value.id + '">' + value.service + '</option>');
                        });

                        if (options.length > 1) {
                            $('select.services').html(options.join('')).prop('disabled', false);
                        }
                    } else {
                        $('select.services').html('<option value="">--- choose service ---</option>').prop('disabled', true);
                    }
                    
                    if (data.addresses != null && typeof data.addresses != 'undefined' && data.addresses.length > 0) {
                        var options = Array();
                        options.push('<option value="">--- choose address ---</option>');
                        $.each(data.addresses, function(key, value) {
                            options.push('<option value="' + value.id + '">' + value.address + '</option>');
                        });

                        if (options.length > 1) {
                            $('select.address').html(options.join('')).prop('disabled', false);
                        }
                    } else {
                        $('select.address').html('<option value="">--- choose address ---</option>').prop('disabled', true);
                    }
                    
                    updateCalender(); // update caledar data
                    
                    //$('.default-load').fadeOut();
                },
                error: function(xhr, errorType, errorMsg) {
                    console.log(errorMsg); $('.default-load').fadeOut();
                },
            });
        } else {
            $('select.services').html('<option value="">--- choose service ---</option>').prop('disabled', true);
            $('select.duration').html('<option value="">--- choose duration ---</option>').prop('disabled', true);
            $('input.price').val('');
        }
    });
    /* Get services of service providers code ends here */

    /* Get duration of service provider services code starts here */
    $(document.body).on('change', 'select.getDuration', function() {
        var service_id = $(this).val();
        var sp = $('select.getServices').val();
        if (sp != "") {
            $.ajax({
                url: OVEconfig.BASEURL + '/admin/services/practitionerservices/',
                type: 'POST',
                data: {id: sp, service: service_id},
                dataType: 'json',
                beforeSend: function(){$('.default-load').fadeIn();},
                success: function(data) {
                    if (data.services != null && typeof data.services != 'undefined' && data.services.length > 0) {
                        var options = Array();
                        options.push('<option value="">--- choose duration ---</option>');
                        $.each(data.services, function(key, value) {
                            options.push('<option value="' + value.id + '">' + value.duration + '</option>');
                        });

                        if (options.length > 0) {
                            $('select.duration').html(options.join('')).prop('disabled', false);
                        }
                    } else {
                        $('select.duration').html('<option value="">--- choose duration ---</option>').prop('disabled', true);
                    }
                    
                    $('.default-load').fadeOut();
                },
                error: function(xhr, errorType, errorMsg) {
                    console.log(errorMsg); $('.default-load').fadeOut();
                },
            });
        }
    });
    /* Get duration of service provider services code ends here */

    /* Get price of service provider services code starts here */
    $(document.body).on('change', 'select.getPrice', function() {
        var duration = $(this).val();
        var service_id = $('select.getDuration').val();
        var sp = $('select.getServices').val();
        if (sp != "") {
            $.ajax({
                url: OVEconfig.BASEURL + '/admin/services/practitionerservices/',
                type: 'POST',
                data: {id: sp, service: service_id, duration: duration},
                dataType: 'json',
                beforeSend: function(){$('.default-load').fadeIn();},
                success: function(data) {

                    $('input#invoiceTotal').val(data.services.price);
                    $('input#siteCommision').val(data.services.commision);
                    $('input[name="service_provider_service_id"]').val(data.services.id);
                    updateCalender(); // update caledar data
                    $('.default-load').fadeOut();
                },
                error: function(xhr, errorType, errorMsg) {
                    console.log(errorMsg); $('.default-load').fadeOut();
                },
            });
        }
    });
    /* Get price of service provider services code ends here */

    /* Filter code starts here */
    $(document.body).on('click', 'button.search', function() {
        var form = $(this).data('form');
        var tbl = $(this).data('tbl');
        var path = $(this).data('path');
        var query = $('form#' + form).serialize();

        // reverting all sorted fields
        $('table#' + tbl + ' > thead > tr > th.sorting_asc, th.sorting_desc').removeClass('sorting_asc sorting_desc').addClass('sorting');

        if (query) {
            $.ajax({
                url: (OVEconfig.BASEURL + path).replace(document.location.search, '') + "?" + query,
                type: 'GET',
                beforeSend: function(){$('.default-load').fadeIn();},
                success: function(data) {
                    var table = $(data).find('table#' + tbl).children('tbody').html();
                    var pagination = $(data).find('div.pagination').html();
                    var action = $(data).find('div.actions').html();

                    pagination = (pagination == undefined) ? '' : pagination;
                    $('table#' + tbl).children('tbody').html(table);
                    $('div.pagination').html(pagination);
                    $('div.actions').html(action);
                    $('.popovers').popover({html: true});
                    $('.default-load').fadeOut();
                },
                error: function(xhr, errorType, errorMsg) {
                    console.log(errorMsg); $('.default-load').fadeOut();
                }
            });
        }

        return false;
    });
    /* Filter code ends here */

    /* Sorting code starts here */
    $(document.body).on('click', 'th.sorting, th.sorting_asc, th.sorting_desc', function() {
        var currentClass = $(this).attr('class');
        var field = $(this).data('field');
        var form = $(this).data('form');
        var tbl = $('table').attr('id');
        var path = $('table#' + tbl).data('path');
        var query = $('form').serialize();
        var order = 'ASC';

        // reverting all sorted fields
        $('table#' + tbl + ' > thead > tr > th.sorting_asc, th.sorting_desc').removeClass('sorting_asc sorting_desc').addClass('sorting');

        switch (currentClass) {
            case 'sorting' :
                $(this).removeClass('sorting').addClass('sorting_asc');
                order = 'ASC';
                break;

            case 'sorting_asc' :
                $(this).removeClass('sorting sorting_asc').addClass('sorting_desc');
                order = 'DESC';
                break;

            case 'sorting_desc' :
                $(this).removeClass('sorting sorting_desc').addClass('sorting_asc');
                order = 'ASC';
                break;
        }

        query = (query != "") ? query + '&sort_field=' + field + '&sort_order=' + order : 'sort_field=' + field + '&sort_order=' + order;

        // Getting sorted data
        $.ajax({
            url: (OVEconfig.BASEURL + path).replace(document.location.search, '') + "?" + query,
            type: 'GET',
            beforeSend: function(){$('.default-load').fadeIn();},
            success: function(data) {
                var table = $(data).find('table#' + tbl).children('tbody').html();
                var pagination = $(data).find('div.pagination').html();
                var action = $(data).find('div.actions').html();


                $('table#' + tbl).children('tbody').html(table);
                $('div.pagination').html(pagination);
                $('div.actions').html(action);
                $('.popovers').popover({html: true});
                $('.default-load').fadeOut();
            },
            error: function(xhr, errorType, errorMsg) {
                console.log(errorMsg); $('.default-load').fadeOut();
            }
        });
    });
    /* Sorting code ends here */

    /* Change mod code starts here */
    $(document.body).on('click', 'button.changeMod', function() {
        var status = $(this).data('sts');
        var element = $(this).data('ele');
        var elementToSelect = (status == 'false') ? ':disabled' : '';
        $(element + elementToSelect).prop('disabled', status);
    });
    /* Change mod code ends here */

    /* Child reveal code starts here */
    $(document.body).on('click', 'span.reveal', function() {
        var id = $(this).data('id');
        $(this).parent('td').parent('tr').next('tr#' + id).slideToggle("slow");

    });
    /* Child reveal code ends here */

    /* Add more address code starts here */
    $(document.body).on('click', 'button.addMoreAddress', function() {
        var total = $('div.Address').length;

        if (total < 4) {
            var content = $('div.Address:last').html();
            $('div.Address:last').after("<hr /><div class='Address'>" + content + '</div>');

        }

        return false;
    })
    /* Add more address code ends here */
    
    /* Remove address code starts here */
    $(document.body).on('click', 'button.removeAddress', function() {
        var total = $('div.Address').length;
        
        if (total > 1) {
            $(this).parent().remove();
        } else {
            $(this).parent().queue(function(){$(this).find('input, select').val('').attr('value', '')});
            $(this).parent().hide();
        }
        return false;
    })
    /* Remove address code ends here */

    /* Field toggle code starts here */
    $(document.body).on('click', 'input.fieldToggle', function() {
        switch ($(this).val()) {
            case '1' :
                $('inout[type="file"]').prop('disabled', false);
                $('textarea').prop('disabled', true);
                $('div.file').show('slow');
                $('div.textarea').hide('slow');
                break;
            case '2' :
                $('inout[type="file"]').prop('disabled', false);
                $('textarea').prop('disabled', true);
                $('div.file').show('slow');
                $('div.textarea').hide('slow');
                break;
            case '3' :
                $('inout[type="file"]').prop('disabled', true);
                $('textarea').prop('disabled', false);
                $('div.file').hide('slow');
                $('div.textarea').show('slow');
                break;
        }
    });
    /* Field toggle code ends here */
});

/* Change user type on newsletter subscriber page code starts here */
$(document.body).on('change', '#userType', function(){
    window.location = OVEconfig.BASEURL + "/admin/newslettersubscribers/?usertype=" + $(this).val();
});
/* Change user type on newsletter subscriber page code ends here */

function filterConsumers() {

    var name = $('[name="name"]').val();
    var user_name = $('[name="user_name"]').val();
    var email = $('[name="email"]').val();
    var created_on = $('[name="created_on"]').val();
    var city = $('[name="city"]').val();
    var state = $('[name="state"]').val();
    var country = $('[name="country"]').val();
    var status_id = $('[name="status_id"]').val();


    $.ajax({
        type: "post",
        url: OVEconfig.BASEURL + "/admin/consumers/",
        data: {name: name, user_name: user_name, email: email, created_on: created_on, city: city, state: state, country: country, status_id: status_id, action: 'filter'},
        beforeSend: function(){$('.default-load').fadeIn();},
        success: function(data) {

            var table = $(data).find('#consumers-table').children('tbody').html();
            var pagination = $(data).find('#pagination').html();

            if (table) {
                $('#consumers-table').children('tbody').html(table);
                $('#pagination').html(pagination);
            }
            $('.default-load').fadeOut();
        },
        error: function(xhr, type, message) {
            console.log(message); $('.default-load').fadeOut();
        },
    });

}

function ResetConsumerFilter() {

    $('[name="name"]').val('');
    $('[name="user_name"]').val('');
    $('[name="email"]').val('');
    $('[name="created_on"]').val('');
    $('[name="city"]').val('');
    $('[name="state"]').val('');
    $('[name="country"]').val('');
    $('[name="status_id"]').val('');

    filterConsumers();

}

function callImage(title, url)
{
	$('body').find('h4.image:hidden').last().html(title); 
	$('body').find('img:hidden').last().attr('src', url);
}

function callVideo(title, url, player)
{
	if (player == 'vimeo') {
		/*$('div#jwplayer, div#jwplayer_wrapper').hide();
		$('div#jwplayer, div#jwplayer_wrapper').prev('div').show();*/
                $('div#jwplayer').prev('div').show();
                $('div#jwplayer').html('').hide();
		$('body').find('h4.video:hidden').last().html(title);
		$('body').find('iframe:hidden').last().attr({'src':'https://player.vimeo.com/video/'+url+'?api=1&amp;player_id=player_1', 'width': '540', 'height':'304'});
		
	} else {
		/*$('div#jwplayer, div#jwplayer_wrapper').show();
		$('div#jwplayer, div#jwplayer_wrapper').prev('div').hide();*/
                $('div#jwplayer').html('').show();
                $('div#jwplayer').prev('div').hide();
		$('body').find('h4.video:hidden').last().html(title);
                $('div#jwplayer').html('<video id="example_video_1" class="video-js vjs-default-skin" controls preload="auto" width="800" height="400" ><source src="'+url.replace('./public', '')+'" type="video/mp4" /></video>')  // data-setup=\'{ "plugins": { "zoomrotate": { "rotate": "90", "zoom": "1" } } }\'
                videojs(document.getElementById('example_video_1'), {}, function(){});
		/*jwplayer('jwplayer').setup({
			file: url.replace('./public', ''),
			title: title,
			width: '100%',
			aspectratio: '16:10',
			autostart: 'false',
			primary: 'flash'	
		});*/
	}
}
