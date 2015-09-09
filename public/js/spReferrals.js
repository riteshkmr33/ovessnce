readNotifications('reference'); // defined on common.js

/* Pagination functions starts here */
paginateNewsletter("div#referredfrom-pagination ul", referredFromCallback, $('input#referred-from').val());
paginateNewsletter("div#referredto-pagination ul", referredToCallback, $('input#referred-to').val());

if ($('input#total_recommended').val() > 0) {
    // recommended pagination
    paginateRecommended($('input#total_recommended').val());
} else {
    $('div.services-data > table.dashReview >tbody').html('<tr><td colspan="5"> No Records Found</td></tr>');
}


function paginateNewsletter(element, callbackFunction, totalNewsletters)
{
    $(element).pagination(totalNewsletters, {
        callback: callbackFunction,
        items_per_page: 5,
        num_display_entries: 10,
        num_edge_entries: 2,
        current_page: $('input#page').val(),
        prev_text: '&lt;',
        next_text: '&gt;'
    });
}

function referredFromCallback(page_index, jq)
{
    $("div.pagination-list ul li:empty").remove(); // Removing blank elements
    $('input#page').val(page_index);  // storing current page number

    var records = Array();
    var items_per_page = 5;
    var page = parseInt(page_index) + 1;

    $.ajax({
        url: OVEconfig.BASEURL + '/practitioner/referrals/',
        type: 'POST',
        async: false,
        data: {action: 'get', page: page, items: items_per_page, user: $('input#sp_id').val()},
        dataType: 'json',
        success: function(data) {
            records = data;
        },
        error: function(xhr, errorType, errorMsg) {
            console.log(errorMsg)
        }
    });

    var max_elem = Math.min((page_index + 1) * items_per_page, records.length);
    var newcontent = Array();

    if (records.length > 0) {
        // Iterate through a selection of the content and build an HTML string
        for (var i = 0; i < max_elem; i++)   //page_index*items_per_page
        {
            newcontent.push("<tr>");
            newcontent.push("<td>" + records[i].referred_by_name + "</td>");
            newcontent.push("<td>" + records[i].service + "</td>");
            //newcontent.push("<td>" + trim_words(records[i].message, 4) + "</td>");
            newcontent.push("<td><div class='select-form'>");
            newcontent.push("<form>");
            newcontent.push("<label for='select-all'>");
            newcontent.push("<input type='checkbox' class='checkReferredFrom' value='" + records[i].id + "'><span></span>");
            newcontent.push("</label>");
            newcontent.push("</form></div>");
            newcontent.push("<span class='delete' id='referredFromDelete' data-val='" + records[i].id + "'>D</span>");
            newcontent.push("</td></tr>");

        }
    } else {
        newcontent.push('<tr><td colspan="5"> No Records Found</td></tr>');
    }

    // Replace old content with new content
    $('div.services-data > table#referredFromTable >tbody').html(newcontent.join(''));

    // Prevent click eventpropagation
    return false;
}

function referredToCallback(page_index, jq)
{
    $("div.pagination-list ul li:empty").remove(); // Removing blank elements
    $('input#page').val(page_index);  // storing current page number

    var records = Array();
    var items_per_page = 5;
    var page = parseInt(page_index) + 1;

    $.ajax({
        url: OVEconfig.BASEURL + '/practitioner/referrals/',
        type: 'POST',
        async: false,
        data: {action: 'get', page: page, items: items_per_page, referred_by: $('input#sp_id').val()},
        dataType: 'json',
        success: function(data) {
            records = data;
        },
        error: function(xhr, errorType, errorMsg) {
            console.log(errorMsg)
        }
    });

    var max_elem = Math.min((page_index + 1) * items_per_page, records.length);
    var newcontent = Array();

    if (records.length > 0) {
        // Iterate through a selection of the content and build an HTML string
        for (var i = 0; i < max_elem; i++)   //page_index*items_per_page
        {
            newcontent.push("<tr>");
            newcontent.push("<td>" + records[i].user_name + "</td>");
            newcontent.push("<td>" + records[i].service + "</td>");
            //newcontent.push("<td>" + trim_words(records[i].message, 4) + "</td>");
            newcontent.push("<td><div class='select-form'>");
            newcontent.push("<form>");
            newcontent.push("<label for='select-all'>");
            newcontent.push("<input type='checkbox' class='checkReferredTo' value='" + records[i].id + "'><span></span>");
            newcontent.push("</label>");
            newcontent.push("</form></div>");
            newcontent.push("<span class='delete' id='referredToDelete' data-val='" + records[i].id + "'>D</span>");
            newcontent.push("</td></tr>");

        }
    } else {
        newcontent.push('<tr><td colspan="5"> No Records Found</td></tr>');
    }

    // Replace old content with new content
    $('div.services-data > table#referredToTable >tbody').html(newcontent.join(''));

    // Prevent click eventpropagation
    return false;
}
/* Pagination functions ends here */

/* Check all code starts here*/
$(document.body).on('click', 'input#checkAllreferredFrom', function() {
    if ($(this).is(':checked')) {
        $('input.checkReferredFrom').prop('checked', true);
    } else {
        $('input.checkReferredFrom').prop('checked', false);
    }
});

$(document.body).on('click', 'input#checkAllreferredTo', function() {
    if ($(this).is(':checked')) {
        $('input.checkReferredTo').prop('checked', true);
    } else {
        $('input.checkReferredTo').prop('checked', false);
    }
});
/* Check all code ends here*/

/* Add reference code starts here */
function getPractitioner(data) {
    $('input[name="practitioner"]').val(data.key);
}

$(document.body).on('click', 'span.btn-add', function() {
    $('div#add-Reference').fadeIn('slow');
    setTimeout(applyAutoCompleteName('#idorname', getPractitioner), 1000);
});

$(document.body).on('click', 'div.add-head > span', function() {
    $('select.required, input#referral_id, input#message').val(''); // reset the form
    $('div#add-Reference').slideUp('slow');  // close the form
});

$(document.body).on('click', 'input#addReference', function() {
    $('div.error-msg, div.success-msg').slideUp();
    valid = true;

    $('select.required, input.required').each(function() {
        if ($(this).val() == "") {
            valid = false;
        }
    });

    if (valid == true) {
        $.ajax({
            url: OVEconfig.BASEURL + '/practitioner/referrals/',
            type: 'POST',
            data: {action: $('input#action').val(), service: $('select#service').val(), practitioner: $('input#practitioner').val(), id: $('input#referral_id').val()},
            beforeSend: function() {
                $('.default-load').fadeIn();
            },
            success: function(data) {
                if (data != "") {
                    data = JSON.parse(data);
                    if (data.status == '1') {

                        paginateNewsletter("div#referredto-pagination ul", referredToCallback, parseInt($('input#referred-to').val()) + 1);  // generate pagination
                        $('select.required, input#referral_id, input#message').val(''); // reset the form
                        $('div#add-Reference').slideUp('slow');  // close the form

                        $('div.success-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                        scrollTo('div.success-msg', 100, 'top');
                        setTimeout("$('div.success-msg').slideUp('slow')", 5000);
                    } else {
                        $('div.error-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                        scrollTo('div.error-msg', 100, 'top');
                    }
                } else {
                    $('div.error-msg').html('<label>Unable to refer selected practitioner..!!</label>').fadeIn('slow');
                    scrollTo('div.error-msg', 100, 'top');
                }
                $('.default-load').fadeOut();
            },
            error: function(xhr, errorType, errorMsg) {
                console.log(errorMsg)
            },
        });
    } else {
        $('div.error-msg').html('<label>Please select the service and practitioner to refer..!!</label>').fadeIn('slow');
        scrollTo('div.error-msg', 100, 'top');
    }

    return false;
});
/* Add reference code ends here */

/* Delete reference code starts here */
$(document.body).on('click', 'span#referredToDelete, input#deleteAllReferTo', function() {
    $('div.error-msg, div.success-msg').slideUp();
    var ids = Array();

    if ($(this).data('val')) {
        ids.push($(this).data('val'));
    } else {
        $('input.checkReferredTo:checked').each(function() {
            ids.push($(this).val());
        });
    }

    if (ids.length > 0) {
        if (confirm('Are you sure want to delete selected record(s)..??')) {
            deleteReference(ids, 'referTo')
        }

    } else {
        $('div.error-msg').html('<label>Please select at least one record to delete..!!</label>').fadeIn('slow');
        scrollTo('div.error-msg', 100, 'top');
    }

    return false;
});

$(document.body).on('click', 'span#referredFromDelete, input#deleteAllReferFrom', function() {
    $('div.error-msg, div.success-msg').slideUp();
    var ids = Array();

    if ($(this).data('val')) {
        ids.push($(this).data('val'));
    } else {
        $('input.checkReferredFrom:checked').each(function() {
            ids.push($(this).val());
        });
    }

    if (ids.length > 0) {
        if (confirm('Are you sure want to delete selected record(s)..??')) {
            deleteReference(ids, 'referFrom')
        }

    } else {
        $('div.error-msg').html('<label>Please select at least one record to delete..!!</label>').fadeIn('slow');
        scrollTo('div.error-msg', 100, 'top');
    }

    return false;
});

function deleteReference(ids, tab)
{
    $.ajax({
        url: OVEconfig.BASEURL + '/practitioner/referrals/',
        type: 'POST',
        data: {action: 'delete', ids: ids},
        beforeSend: function() {
            $('.default-load').fadeIn();
        },
        success: function(data) {
            if (data != "") {
                data = JSON.parse(data);
                if (data.status == '1') {

                    if (tab == 'referTo') {
                        paginateNewsletter("div#referredto-pagination ul", referredToCallback, parseInt($('input#referred-to').val()) - 1);  // generate pagination
                    } else {
                        paginateNewsletter("div#referredfrom-pagination ul", referredFromCallback, parseInt($('input#referred-from').val()) - 1); // generate pagination
                    }

                    $('div.success-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                    scrollTo('div.success-msg', 100, 'top');
                    setTimeout("$('div.success-msg').slideUp('slow')", 5000);
                } else {
                    $('div.error-msg').html('<label>' + data.msg + '</label>').fadeIn('slow');
                    scrollTo('div.error-msg', 100, 'top');
                }
            } else {
                $('div.error-msg').html('<label>Unable to refer selected practitioner..!!</label>').fadeIn('slow');
                scrollTo('div.error-msg', 100, 'top');
            }
            $('.default-load').fadeOut();
        },
        error: function(xhr, errorType, errorMsg) {
            console.log(errorMsg)
        },
    });
}
/* Delete reference code ends here */

function paginateRecommended(totalRecommended)
{
    $("div#recommended-pagination ul").pagination(totalRecommended, {
        callback: recommendedCallback,
        items_per_page: 5,
        num_display_entries: 10,
        num_edge_entries: 2,
        current_page: $('input#page').val(),
        prev_text: '&lt;',
        next_text: '&gt;'
    });
}

function recommendedCallback(page_index, jq)
{
    $("div.pagination-list ul li:empty").remove(); // Removing blank elements
    $('input#page').val(page_index);  // storing current page number

    var records = Array();
    var items_per_page = 5;
    var page = parseInt(page_index) + 1;

    $.ajax({
        url: OVEconfig.BASEURL + '/practitioner/referrals/',
        type: 'POST',
        async: false,
        data: {action: 'get', page: page, items: items_per_page, user: $('input#sp_id').val()},
        dataType: 'json',
        success: function(data) {
            records = data;
        },
        error: function(xhr, errorType, errorMsg) {
            console.log(errorMsg)
        }
    });

    var max_elem = Math.min((page_index + 1) * items_per_page, records.length);
    var newcontent = Array();

    if (records.length > 0) {
        // Iterate through a selection of the content and build an HTML string
        for (var i = 0; i < max_elem; i++)   //page_index*items_per_page
        {
            var avatar_url = (typeof records[i].referred_by_avtar != "undefined" && records[i].referred_by_avtar != "" && records[i].referred_by_avtar != "None" && records[i].referred_by_avtar != null)?records[i].referred_by_avtar:"/img/profile-pic.jpg";
            newcontent.push("<tr>");
            newcontent.push('<td><a href="'+OVEconfig.BASEURL+'/practitioner/view/'+records[i].referred_by_id+'"><span class="profile"><img src="' + avatar_url + '" alt="" /></span>' + records[i].referred_by_name + "</td>");
            newcontent.push("<td>" + records[i].service + "</td>");
            newcontent.push("</tr>");

        }
    } else {
        newcontent.push('<tr><td colspan="5"> No Records Found</td></tr>');
    }

    // Replace old content with new content
    $('div.services-data > table#recommendedTable >tbody').html(newcontent.join(''));

    // Prevent click eventpropagation
    return false;
}
/* Pagination functions end here */
