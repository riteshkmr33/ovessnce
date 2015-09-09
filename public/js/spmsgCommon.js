$(function() {
    var user_type = (location.href.indexOf('practitioner') != -1) ? 'practitioner' : 'consumer';
    var action = $('#action').val();
    if (action != '') {
        getList(action, user_type);
    }
});

function paginateMsgs(pageElement)
{
    var page = $(pageElement).data('url');
    var user_type = (location.href.indexOf('practitioner') != -1) ? 'practitioner' : 'consumer';
    $('#page').val(page);
    var action = $('#action').val();
    if (action != '') {
        getList(action, user_type);
    }
}

function getList(actn, user_type)
{
    var page = $('#page').val();
    var pageLength = 10;

    $.ajax({
        url: OVEconfig.BASEURL + '/' + user_type + '/' + actn + '/',
        type: 'POST',
        dataType: 'json',
        data: {page: page, no_of_records: pageLength},
        beforeSend: function() {
            $('.default-load').fadeIn();
        },
        success: function(data) {
            $('.default-load').fadeOut();

            var data = JSON.stringify(data);
            var allData = $.parseJSON(data);
            list = new Array();

            /* this is for pagination */
            var next = (allData.next && typeof allData.next != 'undefined' && allData.next != null)?allData.next.split('&'):false;
            var previous = (allData.previous && typeof allData.previous != 'undefined' && allData.previous != null)?allData.previous.split('&'):false;
            var count = (allData.count && typeof allData.count != 'undefined' && allData.count != null)?allData.count:0;
            var resLength = (allData.results && typeof allData.results != 'undefined' && allData.results != null)?allData.results.length:0;

            if (next) {
                
                if (actn == "trash") {
                    var naxtpage_val = (next[(next.length-1)] && typeof next[(next.length-1)] != 'undefined' && next[(next.length-1)] != null)?next[(next.length-1)].split('=')[1]:0;
                } else {
                    var naxtpage_val = (next[(next.length-1)] && typeof next[(next.length-1)] != 'undefined' && next[(next.length-1)] != null)?next[(next.length-1)].split('=')[1]:0;
                }
                $('.next').show();
                $('.next').data('url', naxtpage_val);
            } else {
                naxtpage_val = '';
                $('.next').hide();
            }

            if (previous) {
                if (actn == "trash") {
                    var prevpage_val = (previous[(previous.length-1)] && typeof previous[(previous.length-1)] != 'undefined' && previous[(previous.length-1)] != null)?previous[(previous.length-1)].split('=')[1]:0;
                } else {
                    var prevpage_val = (previous[(previous.length-1)] && typeof previous[(previous.length-1)] != 'undefined' && previous[(previous.length-1)] != null)?previous[(previous.length-1)].split('=')[1]:0;
                }
                $('.prev').show();
                $('.prev').data('url', prevpage_val);
            } else {
                prevpage_val = '';
                $('.prev').hide();
            }

            var start = pageLength * (page - 1) + 1;
            (count == 0) ? start = 0 : start;
            var to = (page - 1) * pageLength + resLength;
            var pageCountDisplay = start + " To " + to + " 0f " + count;

            $('.count').html(pageCountDisplay);
            //console.log(pageCountDisplay);
            /* this is for pagination */

            if (resLength != '') {
                $.each(allData.results, function(index, value) {

                    var user_details = $.parseJSON(value.to_user_details);
                    var to_name = ((user_details != null && typeof user_details != 'undefined') && (user_details.first_name != '' || user_details.last_name)) ? user_details.first_name + ' ' + user_details.last_name : '';
                    var to_avatar_url = (user_details != null && typeof user_details != 'undefined' && user_details.avtar_url != '') ? user_details.avtar_url : '';

                    var date = new Date(value.created_date);
                    var Name = '';

                    if (actn == "inbox") {
                        if (value.from_avtar_url != null) {
                            var avatar_url = value.from_avtar_url;
                        } else {
                            var avatar_url = OVEconfig.BASEURL + '/img/testimonials-img.jpg'; // default image
                        }
                        Name = value.from_name;
                    } else if (actn == "sent") {
                        if (value.to_avtar_url != null) {
                            var avatar_url = value.to_avtar_url;
                        } else {
                            var avatar_url = OVEconfig.BASEURL + '/img/testimonials-img.jpg'; // default image
                        }
                        Name = to_name;
                    }


                    if (user_type == "practitioner") {
                        var readclass = (value.readFlag_p) ? '' : 'unreadmsg';
                    } else {
                        var readclass = (value.readFlag_c) ? '' : 'unreadmsg';
                    }


                    list.push("<tr class=" + readclass + ">");
                    list.push("<td style='width:8%'>");
                    list.push("<div class='select-form'>");
                    list.push("<label for='select-all'><input type='checkbox' value='" + value.id + "' class='checkMsg'><span></span></label>");
                    list.push("<span class='star select'>Star</span></div>");
                    list.push("</td>");
                    list.push("<td>");
                    list.push("<span class='sender-img'>");
                    if (actn == "inbox" || actn == "sent") {
                        list.push("<img src='" + avatar_url + "' alt='' />");
                    }
                    //list.push("</span>"+value.from_name+"");
                    list.push("</span>" + Name + "");
                    list.push("</td>");
                    if (actn == "inbox") {
                        list.push("<td><a href='" + OVEconfig.BASEURL + '/' + user_type + '/viewmessage/' + value.id + "'>" + value.message.substr(0, 50) + "</a></td>");
                    } else {
                        list.push("<td><a href='" + OVEconfig.BASEURL + '/' + user_type + '/readmessage/' + value.id + "'>" + value.subject + " : " + value.message.substr(0, 50) + "</a></td>");
                    }
                    //list.push("<td><a href='"+OVEconfig.BASEURL+'/practitioner/viewmessage/'+value.id+"'>"+value.message.substr(0, 50) +"</a></td>");
                    list.push("<td>" + date.toLocaleDateString() + " : " + date.toLocaleTimeString() + "</td>");
                    list.push("</tr>");
                });
            } else {
                list.push("<tr><td colspan='4'>No new messages</td></tr>");
            }
            $('#sp_' + actn + '_messages').children('tbody').html(list.join(""));
            return false;
        },
        error: function(xhr, errorType, errorMsg) {
            console.log
        }
    });

}

$('#checkAllmsgs').on('click', function(event) {
    if (this.checked) {
        $('.checkMsg').each(function() {
            this.checked = true;
        });
    } else {
        $('.checkMsg').each(function() {
            this.checked = false;
        });
    }
});

$('#msg-action-form').on('submit', function() {

    var msg_action = $("#msg-action").val();

    var id_list = new Array()
    $('.checkMsg:checked').each(function() {
        id_list.push(this.value);
    });

    if (id_list.length == 0) {
        $('.error-msg').html('<label>please select atleast one message</label>');
        $('.error-msg').fadeIn(1000, function() {
            $(".error-msg").fadeOut(4000);
        });
        return false;
    }

    if (msg_action != '0') {
        msgActions(id_list, msg_action);
    } else {

        $('.error-msg').html('<label>Please select an option first</label>');
        $('.error-msg').fadeIn(1000, function() {
            $(".error-msg").fadeOut(4000);
        });
        return false;

    }

    return false;
});

function msgActions(ids, msg_action)
{
    var action = $('#action').val();
    var user_type = (location.href.indexOf('practitioner') != -1) ? 'practitioner' : 'consumer';

    if (msg_action == "delete") {
        var confirm_msg = "Are you sure? Message will be permently deleted and cannot be recovered later";
    } else {
        var confirm_msg = "Are you sure?";
    }

    if (confirm(confirm_msg)) {

        $.ajax({
            url: OVEconfig.BASEURL + '/' + user_type + '/actionmsgs/',
            type: 'POST',
            dataType: 'json',
            data: {ids: ids, msg_action: msg_action},
            beforeSend: function() {
                $('.default-load').fadeIn();
            },
            success: function(data) {
                $('.default-load').fadeOut();
                if (data.error == false) {
                    $('.error-msg').hide();
                    $('.success-msg').html("<label>" + data.msg + "</label>");
                    $('.success-msg').fadeIn(1000, function() {
                        $(".success-msg").fadeOut(4000);
                    });
                } else {
                    $('.error-msg').html("<label>" + data.msg + "</label>");
                    $('.success-msg').hide();
                    $('.error-msg').fadeIn(1000, function() {
                        $(".error-msg").fadeOut(4000);
                    });
                }
                /* update notifications - starts */
                $('#message_count').html(data.notifications.inbox);
                $('#inbox_count a').html("Inbox  ( " + data.notifications.inbox + " ) ");
                /* update notifications - ends */

            },
            error: function(xhr, errorType, errorMsg) {
                console.log
            }
        });
        getList(action, user_type);
        return false;

    } else {
        return false;
    }
}
