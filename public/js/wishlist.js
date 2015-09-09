$('form#wishlist').on('submit', function() {
    var price = $('#priceDel').val().replace('CAD', '').replace('USD', '').replace('$', '');
    var sp_id = $('#sp_id').val();
    var service_id = $('select#services:last').val();
    var duration = $('select#duration_list:last').val();

    $.ajax({
        url: OVEconfig.BASEURL + '/booking/wishlist/',
        type: 'POST',
        data: {priceDel: price, sp_id: sp_id, service_id: service_id, duration: duration},
        beforeSend: function() {
            $('.default-load').fadeIn();
        },
        success: function(data) {
            if (data.length > 0) {
                $("div#mesg").css({'display': "block"});
                $("div#mesg").html(data);
                setTimeout("$('div#mesg').slideUp('slow')", 5000);
                $('.default-load').fadeOut();
            }

        },
        error: function(xhr, errorType, errorMsg) {
            console.log(errorMsg)
        },
    });
    return false;
});

$('form#Review').on('submit', function() {

    var service_id = $('#service_id').val();
    var comment = $('textarea[name=comment]').val();

    if (service_id != '' && comment != '') {
        $('#service_id').removeAttr('disabled');

    } else {
        return false;
    }

    /*
     var service_id = $('#service_id').val();
     var comment = $('textarea[name=comment]').val();
     
     
     if(service_id!='' && comment!=''){
     
     $.ajax({
     url : OVEconfig.BASEURL+'/practitioner/addreview/',
     type : 'POST',
     data : {  formData : $('form#Review').serialize() , sp_id : $('#sp_id').data('spid') },
     success: function(data) { 
     alert(data);
     return false;
     exit;
     
     if( data.length > 0 ){
     $("div#mesg").css({'display':"block"});
     $("div#mesg").html(data);
     }
     
     service_id.attr('disabled','disabled');
     return false;
     exit;
     
     },
     error: function(xhr, errorType, errorMsg) {console.log(errorMsg)},
     });
     return false;
     }else{
     alert('inside else');
     return false;
     exit;
     }
     
     return false;
     exit;
     */
});
