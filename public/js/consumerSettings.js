/* Password change functionality starts here.*/
$('#spchangepassword').on('submit', function() {
    $('div.error-msg, div.success-msg').slideUp();
    var old_pass = $('#old_pass').val();
    var Pass = $('#Pass').val();
    var confirm_password = $('#confirm_password').val();

    if (Pass != confirm_password) {
        $('.error-msg').html("<label>Confirm password do not match</label>").fadeIn('slow');
        scrollTo('div.error-msg', 100, 'top');
	return false;
    }

    $.ajax({
        url: OVEconfig.BASEURL+'/consumer/settings/',
        type: 'POST',
        dataType: 'json',
        data: {old_pass: old_pass, Pass: Pass, confirm_password: confirm_password, action: 'change_password'},
        beforeSend: function() {
            $('.default-load').fadeIn();
        },
        success: function(data) {
            $('.default-load').fadeOut();
            if (data) {
                if (data.error) {
                    $('.error-msg').html("<label>"+data.msg+"</label>").fadeIn('slow');
                    scrollTo('div.error-msg', 100, 'top');   
                } else {
                    $('.success-msg').html("<label>"+data.msg+"</label>").fadeIn('slow');
                    scrollTo('div.success-msg', 100, 'top');
                }
            }
        },
        error: function(xhr, errorType, errorMsg) {
            console.log
        }
    });

    return false;
    exit;

});

$('.feature_chk').on('click',function(){
	$('div.error-msg, div.success-msg').slideUp();
	if($(this).val() == 1){
		$(this).removeAttr('checked');
		$(this).val('0');
	}else{
		$(this).val('1');
		$(this).attr('checked', 'checked');
	}
	
	var feature_email = $('#feature_email').val();
	var feature_sms = $('#feature_sms').val();
	var feature_chat = $('#feature_chat').val();
	var feature_table_id = $('#feature_table_id').val();
	
	$.ajax({
		url : OVEconfig.BASEURL+'/consumer/settings/',
		type : 'POST',
		dataType : 'json',
		data : { feature_table_id : feature_table_id, feature_email : feature_email, feature_sms : feature_sms, feature_chat : feature_chat, action : 'change_features' },
		beforeSend : function(){
			$('.default-load').fadeIn();
		},
		success : function(data){ 
			$('.default-load').fadeOut();
			if(data){
				if(data.error){
					$('.error-msg').html("<label>"+data.msg+"</label>").fadeIn('slow');
                                        scrollTo('div.error-msg', 100, 'top');
				}else{
					$('.success-msg').html("<label>"+data.msg+"</label>").fadeIn('slow');
                                        scrollTo('div.success-msg', 200, 'top');
				}
			}
		},
		error : function(xhr, errorType, errorMsg){console.log}
	});
	
});

$('#newsletter-submit').on('click',function(){
    $('div.error-msg, div.success-msg').slideUp();
    if ($("input[name='newsletter-chk']:checked").val()) {
        
        $.ajax({
		url : OVEconfig.BASEURL+'/consumer/settings/',
		type : 'POST',
		dataType : 'json',
		data : { newletter_chk : $("input[name='newsletter-chk']:checked").val() , action : 'newletter-chk' },
		beforeSend : function(){
			$('.default-load').fadeIn();
		},
		success : function(data){ 
			$('.default-load').fadeOut();
			if(data){
				if(data.error){
					$('.error-msg').html("<label>"+data.msg+"</label>").fadeIn('slow');
                                        scrollTo('div.error-msg', 200, 'top');
				}else{
					$('.success-msg').html("<label>"+data.msg+"</label>").fadeIn('slow');
                                        scrollTo('div.success-msg', 200, 'top');
				}
			}
		},
		error : function(xhr, errorType, errorMsg){console.log}
	});
        
    }else{
        $('.error-msg').html("<label>Please select atleat one option</label>").fadeIn('slow');
        scrollTo('div.error-msg', 100, 'top');
    }
    return false;exit;
});

$('#close-account').on('click',function(){
    $('div.error-msg, div.success-msg').slideUp();
    var reason_id = ($('input[name=close-acc]:radio:checked').val()!=='') ? $('input[name=close-acc]:radio:checked').val() : '' ;
    var other_reason = ($('#other-reason').val()!=='') ? $('#other-reason').val() : '' ;
      
    if (!reason_id) {
        
        $('.error-msg').html("<label>Please select a reason first</label>").fadeIn('slow');
        scrollTo('div.error-msg', 200, 'top');
        return false;
        exit;
    }else if (reason_id=="5") {
        if (other_reason==='') {
            $('.error-msg').html("<label>Please provider other reason in the textarea</label>").fadeIn('slow');
            scrollTo('div.error-msg', 200, 'top');
            return false;
        }
    }
    
    if (confirm("Are you sure you want to close your account.. All your active bookings will be cancelled")) {
        $.ajax({
		url : OVEconfig.BASEURL+'/consumer/settings/',
		type : 'POST',
		dataType : 'json',
		data : { reason_id : reason_id, other_reason : other_reason, action : 'close-acc' },
		beforeSend : function(){
			$('.default-load').fadeIn();
		},
		success : function(data){ 
			$('.default-load').fadeOut();
			if(data){
				if(data.error){
					$('.error-msg').html("<label>"+data.msg+"</label>").fadeIn('slow');
                                        scrollTo('div.error-msg', 100, 'top');
				}else{
                                    // take it to the logout page 
                                    $('.success-msg').html("<label>"+data.msg+"</label>").fadeIn('slow');
                                    scrollTo('div.error-msg', 100, 'top');
                                    
                                    setTimeout('window.location = "'+OVEconfig.BASEURL+'/logout"', 5000);
				}
			}
		},
		error : function(xhr, errorType, errorMsg){console.log}
	});
    }
    
});

$('#reset-close-account').on('click',function(){
    $('input[name=close-acc]:radio').removeAttr('checked');
    $('#other-reason').val('');
});