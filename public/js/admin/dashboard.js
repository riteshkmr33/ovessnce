$(function() {
    var tabs = Array();
    $('a[data-toggle="tab"]').each(function() {
        if ($(this).attr('href').indexOf('day') != -1) {
            tabs.push($(this).attr('href'));
        }
    });

    $.each(tabs, function(key, tab) {
        getDashboardData(tab);
    });
});

$(document.body).on('click', 'a[data-toggle="tab"]', function() {
    getDashboardData($(this).attr('href'));
});

/* Function to fetch dynamic data starts here */
function getDashboardData(tab)
{
    if (tab != "") {
        var divId = tab.replace('#', '');
        var tabDetails = divId.split("_");

        if (tabDetails[0] != "" && tabDetails[1] != "") {
            var records = null;

            $.ajax({
                url: document.location.href,
                type: 'POST',
                async: false,
                data: {module: tabDetails[0], tab: tabDetails[1]},
                dataType: 'json',
                success: function(data) {
                    records = data;
                },
                error: function(xhr, errorType, errorMsg) {
                    console.log(errorMsg)
                },
            });


            var content = Array();
            var time = (tabDetails[1] == 'day') ? 'today' : 'this ' + tabDetails[1];

            switch (tabDetails[0]) {
                case 'subscriptions' :
                    if (records.length > 0) {
                        $.each(records, function(key, record) {
                            content.push('<tr><td>' + record.state_name + '</td><td>' + record.total + '</td><td>' + record.total_percentage + '%</td><td>' + record.growth + '%</td></tr>');
                        });
                        $('div#' + divId + '').find('tbody').html(content.join(''));
                    } else {
                        $('div#' + divId + '').find('tbody').html('<tr><td colspan="4" > No Subscription sold ' + time + '</td></tr> ');
                    }

                    break;

                case 'bookings' :
                    if (records.length > 0) {
                        $.each(records, function(key, record) {
                            content.push('<tr><td>' + record.state_name + '</td><td>' + record.total + '</td><td>' + record.total_percentage + '%</td><td>' + record.growth + '%</td></tr>');
                        });
                        $('div#' + divId + '').find('tbody').html(content.join(''));
                    } else {
                        $('div#' + divId + '').find('tbody').html('<tr><td colspan="4" > No Bookings sold ' + time + '</td></tr> ');
                    }
                    break;

                case 'revenue' :
                    /* Revenue subscription starts */
                    content.push('<tr><td> Subscriptions </td><td></td><td></td><td></td><td></td></tr>');
                    if (records.subscriptions.length > 0) {
                        $.each(records.subscriptions, function(key, record) {
                            content.push('<tr><td>' + record.name + '</td><td>' + record.total + '</td><td>' + record.cancelled + '</td><td>' + record.total_revenue + '</td><td>' + record.growth + '%</td></tr>');
                        });
                        $('div#' + divId + '').find('tbody:first').html(content.join(''));
                    } else {
                        content.push('<tr><td colspan = "5" > No Subscription sold ' + time + '</td></tr>');
                        $('div#' + divId + '').find('tbody:first').html(content.join(''));
                    }
                    /* Revenue subscription ends */

                    /* Revenue Bookings starts */
                    $('div#' + divId + '').find('tbody:last').html('<tr><td> Commissions from practitioners </td><td>' + records.bookings.total + '</td><td>' + records.bookings.avg_commision + '</td><td>' + records.bookings.total_revenue + '</td><td>' + records.bookings.growth + '%</td></tr>');
                    /* Revenue Bookings ends */

                    $('div#' + divId + '').find('span:first').html('<strong>Total Revenue</strong> &nbsp;&nbsp;&nbsp;&nbsp; ' + records.revenue);

                    break;

                case 'message' :
                    if (records.total > 0) {
                        $('div#' + divId + '').find('div.value').html(records.total);
                    } else {
                        $('div#' + divId + '').find('div.value').html('No new messages sent ' + time);
                    }
                    break;

                case 'registration' :
                    $('div#' + divId + '').find('tbody >tr:first').html('<td> Practitioner\'s registration </td><td>' + records.practitioner_registered + '</td><td>' + records.practitioner_cancelled + '</td><td>' + records.practitioner_growth + '%</td>');
                    $('div#' + divId + '').find('tbody >tr:last').html('<td> Customer\'s registration </td><td>' + records.consumer_registered + '</td><td>' + records.consumer_cancelled + '</td><td>' + records.consumer_growth + '%</td>');
                    break;

                case 'video' :
                    if (records.view_count > 0) {
                        $('div#' + divId + '').find('div.value:last').html(records.view_count);
                    } else {
                        $('div#' + divId + '').find('div.value:last').html('No video viewed ' + time);
                    }

                    if (records.upload_count > 0) {
                        $('div#' + divId + '').find('div.value:first').html(records.upload_count);
                    } else {
                        $('div#' + divId + '').find('div.value:first').html('No video uploaded ' + time);
                    }
                    break;

                case 'PRtop10' :
                    if (records.length > 0) {
                        $.each(records, function(key, record) {
                            content.push('<tr><td>' + record.state_name + '</td><td>' + record.users_count + '</td><td>' + record.total + '%</td><td>' + record.growth + '%</td></tr>');
                        });
                        $('div#' + divId + '').find('tbody').html(content.join(''));
                    } else {
                        $('div#' + divId + '').find('tbody').html('<tr><td colspan="4" > No Practitioner registered ' + time + '</td></tr> ');
                    }
                    break;

                case 'CRtop10' :
                    if (records.length > 0) {
                        $.each(records, function(key, record) {
                            content.push('<tr><td>' + record.state_name + '</td><td>' + record.users_count + '</td><td>' + record.total + '%</td><td>' + record.growth + '%</td></tr>');
                        });
                        $('div#' + divId + '').find('tbody').html(content.join(''));
                    } else {
                        $('div#' + divId + '').find('tbody').html('<tr><td colspan="4" > No Consumer registered ' + time + '</td></tr> ');
                    }
                    break;

                case 'CPCRtop10' :
                    if (records.length > 0 || records.length == undefined) {
                        $.each(records, function(key, record) {
                            content.push('<tr><td>' + key + '</td><td>' + record.consumer_count + '</td><td>' + record.practitioner_count + '</td><td>' + record.total + '%</td><td>' + record.growth + '%</td></tr>');
                        });
                        $('div#' + divId + '').find('tbody').html(content.join(''));
                    } else {
                        $('div#' + divId + '').find('tbody').html('<tr><td colspan="5" > No Cancellation ' + time + '</td></tr> ');
                    }
                    break;
            }

        }
    }
}
/* Function to fetch dynamic data ends here */
