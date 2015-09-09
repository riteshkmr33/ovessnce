var graphData = Array();

function randValue() {
    return (Math.floor(Math.random() * (1 + 40 - 20))) + 20;
}

function assignData(data)
{
    graphData = Array();
    $.each(data.bookings, function(key, value){
        graphData.push([[data.consumer[key],data.service_provider[key], data.subscriptions[key], value], key])
    });
    graphData.sort(function(a,b) {
        tempDate1 = a[1].split('-')
        tempDate2 = b[1].split('-');
        firstDate = new Date(tempDate1[2] +"-"+ tempDate1[1] +"-"+ tempDate1[0]);
        secDate = new Date(tempDate2[2] +"-"+ tempDate2[1] +"-"+ tempDate2[0]);
        
        return firstDate.getTime() - secDate.getTime();
    });
    //console.log(graphData);
}

function getData(startDate, endDate, callBack)
{
    $.ajax({
        url: '/admin/admin/graph/',
        type: 'POST',
        async: false,
        dataType: 'json',
        data: {start: startDate, end: endDate},
        success: function(data) {
            callBack(data);
        },
        error: function(xhr, errorType, errorMsg) {
            console.log(errorMsg);
        },
    });
    
}

function generateGraph(startDate, endDate)
{
    $('#chart_2').html('');
    getData(startDate, endDate, assignData);
    arrayOfData = graphData;
    /*arrayOfData = new Array(
            [[14, 54, 26, 10, 20], '2007'],
            [[8, 48, 38, 10, 20], '2008'],
            [[4, 36, 57, 10, 20], '2009']
            );*/

    $('#chart_2').jqBarGraph({data: arrayOfData, // array of data for your graph
        title: false, // title of your graph, accept HTML
        barSpace: 10, // this is default space between bars in pixels
        width: '100%', // default width of your graph
        height: 200, //default height of your graph
        color: '#000000', // if you don't send colors for your data this will be default bars color
        colors: ['#D12610', '#37B7F3', '#52E136', '#FD9600'], // array of colors that will be used for your bars and legends
        lbl: '', // if there is no label in your array
        sort: false, // sort your data before displaying graph, you can sort as 'asc' or 'desc'
        position: 'bottom', // position of your bars, can be 'bottom' or 'top'. 'top' doesn't work for multi type
        prefix: '', // text that will be shown before every label
        postfix: '', // text that will be shown after every label
        animate: true, // if you don't need animated appearance change to false
        speed: 2, // speed of animation in seconds
        legendWidth: 100, // width of your legend box
        legend: true, // if you want legend change to true
        legends: ['Consumers', 'Practitioners', 'Subscriptions', 'Bookings'], // array for legend. for simple graph type legend will be extracted from labels if you don't set this
        type: 'multi', // for multi array data default graph type is stacked, you can change to 'multi' for multi bar type
        showValues: true, // you can use this for multi and stacked type and it will show values of every bar part
        showValuesColor: '#fff' // color of font for values 
    });
}

generateGraph($('input#startDate').val(), $('input#endDate').val());


$(document.body).on('click', '.generateGraph', function() {
    var startDate = $('input#startDate').val().split('-');
    var endDate = $('input#endDate').val().split('-');

    start = new Date(startDate[2] + "-" + startDate[1] + "-" + startDate[0]),
            end = new Date(endDate[2] + "-" + endDate[1] + "-" + endDate[0]),
            diff = new Date(end - start),
            days = diff / 1000 / 60 / 60 / 24;

    if (days > 14) {
        alert('Please select date range with maximum 14 days difference..!!');
    } else if (days < 2) {
        alert('Please select valid date range..!!');
    } else {
        generateGraph($('input#startDate').val(), $('input#endDate').val());
    }

});
