
// Make a fucntion get give a verification code form
makefun = {
	commonfield: function(){
					var html = '<div id="verifycode" class="email-verification">';
					html    += '<input type="text" value="" id="verifycodeval" name="verifycodeval">';																					
					html    += '<input type="submit" class="black verifycode" value="verifiy" />';
					html    += '</div>';
					$("#from_container").html(html);
				}
 }

// Show option verified through mail or number
$(document).on('click','.verification_type',function(){ 
	var value = $(this).val();
	$.ajax({
			url : OVEconfig.BASEURL+'/verification/getdetail',
			type : 'POST',
			dataType: 'json',
			data : {type:value},
			beforeSend : function(){ $('.default-load').fadeIn();},
			success: function(data) { 
				
				// email form
				if(data.divtype==3)	{								
					var html = '<div id="linkverification" class="email-verification">';
					html    += '<input type="text" value="'+data.fieldvalue+'" readonly="readonly" id="emailid" name="emailid">';																					
					html    += '<input type="submit" value="Send me link" class="black sendlink">';
					html    += '</div>';
				
					$("#from_container").html(html);
					$("#msg").empty();
				}
				// contact number form
				if(data.divtype==4)	{								
					var html = '<div id="numberverification" class="email-verification">';
					html    += '<input id="mobile-number" type="tel" placeholder="e.g. +1 702 123 4567">';
					//html    += '<input type="text" value="'+data.fieldvalue+'" id="mobile_no" name="mobile_no" placeholder="Mobile Number" minilenght="10">';																					
					html    += '<input type="submit" class="black sendcode" value="Send Me code" />';
					html    += '</div>';
					html    += '<script>$("#mobile-number").intlTelInput();</script>';
					$("#from_container").html(html);
					$("#msg").empty();
				}
				// verify code
				if(data.divtype==2)	{								
					/*var html = '<div id="verifycode" class="email-verification">';
					html    += '<input type="text" value="" id="verifycodeval" name="verifycodeval">';																					
					html    += '<input type="submit" class="black verifycode" value="verifiy" />';
					html    += '</div>';
					$("#from_container").html(html);*/
					makefun.commonfield();
					var msg = '<div id="response" class="'+data.class+'"> <label id="msgVal">'+data.msg+'</label></div>';
					$("#msg").html(msg);
				}
				// Already verified
				if(data.divtype==1)	{ 							
					var msg = '<div id="response" class="'+data.class+'"> <label id="msgVal">'+data.msg+'</label></div>';
					$("#msg").html(msg);
					$("#from_container").empty();
				}
				
						// Show new email form
						/*if(data.divtype==3){
								$('#from_container').append(data.div);
							}*/
				
				/*	if(value==1){
						$('#email_container,#from_container').removeClass('hide-div');
						$('#email_container').append("<span>test</span>");
					}
					else{
						$('#number_container,#from_container').removeClass('hide-div');
					}*/
					/*if(data){ 
						if(value==1){
							$('#linkverification').removeClass('hide-div');
							$('#emailid').val(data);
						}
						else{
							$('#numberverification').removeClass('hide-div');
						}
					}
					else{ 
						$('#response').removeClass();
						$('#response').addClass('success-msg')
						$('#msg').html("Mail already send on your registered mail id.Please check it to get verify code");
						$('#verifycode').removeClass('hide-div');
					}*/
				
				$('.default-load').fadeOut();
			},
		error: function(xhr, errorType, errorMsg) {console.log(errorMsg)},
		}); 
	//return false;
});

//Send verify code on number
$(document).on('click','.sendcode', function(){
		var number = $('#mobile-number').val();
		 //var phoneno =/^\+?([0-9]{2})\)?[-. ]?([0-9]{4})[-. ]?([0-9]{4})$/;  
		 var phoneno =/^[\s()+-]*([0-9][\s()+-]*){6,20}$/
		 var getval = number.match(phoneno);
		 if(!number.match(phoneno)){
			var msg = '<div id="response" > <label id="msgVal"></label></div>';
			$("#msg").html(msg);
			$('#response').removeClass();
			$('#response').addClass('error-msg');
			$('#msgVal').html("Number must be numeric");
			return false;  
        }
		
		$.ajax({
			url : OVEconfig.BASEURL+'/verification/sendmsg',
			type : 'POST',
			dataType: 'json',
			data : {number:number},
			beforeSend : function(){ $('.default-load').fadeIn();},
			success: function(data) {
			
				var msg = '<div id="response" > <label id="msgVal">'+data.msg+'</label></div>';
				$("#msg").html(msg);
				$('#response').removeClass();
				if(data.error){
					 $('#response').addClass('error-msg');
				}
				else{
					makefun.commonfield();
					$('#response').addClass('success-msg');
				}
				$('#msgVal').html(data.msg);	
				$('.default-load').fadeOut();		
			},
			error: function(xhr, errorType, errorMsg) {console.log(errorMsg)},
		});
	return false;
});

//Send verify code mail
$(document).on('click', '.sendlink' ,function(){ 
	var email = $('#emailid').val();
	$.ajax({
			url : OVEconfig.BASEURL+'/verification/sendmail',
			type : 'POST',
			dataType: 'json',
			data : {email:email},
			beforeSend: function(){ $('.default-load').fadeIn();},
			success: function(data) {
			
				var msg = '<div id="response" > <label id="msgVal">'+data.msg+'</label></div>';
				$("#msg").html(msg);
				
				$('#response').removeClass();
				if(data.error){
					 $('#response').addClass('error-msg');
				}
				else{
					makefun.commonfield();
					$('#response').addClass('success-msg');
				}
				
				$('#msgVal').html(data.msg);			
				$('.default-load').fadeOut();
			},
			error: function(xhr, errorType, errorMsg) {console.log(errorMsg)},
		});
	return false;
});
/*	
//Send verify code mail
$('.sendlink').on('click',function(){
	$('#response,#verifycode').addClass('hide-div');
	$.ajax({
			url : OVEconfig.BASEURL+'/verification/sendmail',
			type : 'POST',
			dataType: 'json',
			data : {},
			success: function(data) { 
				$('#response').removeClass();
				if(data.error){ 
					$('#response').addClass('error-msg');
				}else{
					$('#response').addClass('success-msg');
					$('#linkverification').addClass('hide-div');
					$('#verifycode').removeClass('hide-div');
				}
					$('#msg').html(data.msg);			
			},
			error: function(xhr, errorType, errorMsg) {console.log(errorMsg)},
		});
	return false;
});

*/
// Verify enter code 
$(document).on('click', '.verifycode' ,function(){
	var code = $('#verifycodeval').val();
	$.ajax({
			url : OVEconfig.BASEURL+'/verification/verifycode',
			type : 'POST',
			dataType: 'json',
			data : {code:code},
			beforeSend: function(){ $('.default-load').fadeIn();},
			success: function(data) { 
				$('#response').removeClass();
				if(data.error){ 
					$('#response').addClass('error-msg');
				}else{
					$('#response').addClass('success-msg');
					$("#from_container").empty();
				}
					$('#msgVal').html(data.msg);
					$('.default-load').fadeOut();
			},
			error: function(xhr, errorType, errorMsg) {console.log(errorMsg)},
		});
	return false;
});

/*
// only number key pressed 
$(document).on('keydown','#mobile-number' ,function(e){
	     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && (e.which < 96 || e.which > 105)) {
           e.preventDefault();
        }
        var value = $('#mobile-number').val();
        if(value.toString().length>9){ 
			if (e.which != 8 && e.which != 0){
				 e.preventDefault();
			}
		}
});
* */




