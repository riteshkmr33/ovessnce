/**
 * @author dharmendra
 */

$(function(){
	getList();   // call to listing testimonial
    /* Infinite pagination code starts here */
	//$('#ts-main-list').trigger('scroll');
});

$(window).load(function(){ 
	$('#testimonial').flexslider({
		directionNav:false,
		selector: ".slides > section"
	});
});

/*
 *  Function called when filter data changes every time. Code start here.
 *  */

function getList() {
	var next = $('input[name=page]').val(); 
	
	
    $.ajax({
        url: OVEconfig.BASEURL + '/testimonials/index/',
        type: 'POST',
        dataType: 'json',
        data:{page: next},
        beforeSend: function() {
          //  $('#loading').fadeIn();
           // $('.default-load').fadeIn();
        },
        success: function(data) {
			
           var viewFormate = '';
           /*
           var maxTime = (data.count>3)?3:data.count;
           if(data.count>0){
			   for(var i=0;i<maxTime;i++) { 
					
					var url = (data[i]['img_url']!=0) ? data[i]['img_url'] : './img/profile-pic.jpg'; 
					viewFormate +='	<section> ';
					viewFormate +='<figure> <img src="'+url+'" alt=""> </figure>';
					viewFormate +='<div class="testimonial-text text-align"> <p>';
					viewFormate += (data[i]['text']!='') ? data[i]['text'] : ' Sorry no text..!!';
					viewFormate +='</p> <a href="'+OVEconfig.BASEURL+'/practitioner/view/'+data[i]['user_id']+'" class="name"> written by - <em>';
					viewFormate += data[i]['user_name']+'</em>	</a> </div>	</section>';
				}// End for loop 
				$('#sliderview').html(viewFormate);
			}
           */
            var viewFormate = '';
           //var maxTime = (data.count>3)?data.count:0;
           var maxTime = (data.count>0)?data.count:0;
           if(maxTime>0){
			   for(var i=0;i<maxTime;i++) { 
				   
				    if(data[i]['text']!='') {
						stringCut = ((data[i]['text'].length) > 120) ? data[i]['text'].substr(0, 120)+'....<a class="read-more" href="javascript:void(0);" onClick="blockContent('+data[i]['id']+');" >Read More</a>': data[i]['text'];
					} else { stringCut="Sorry no text..!!";}
				    
					var url = (data[i]['img_url']!=0) ? data[i]['img_url'] : './img/profile-pic.jpg'; 
					viewFormate +='<li> <article> <div class="more-content" id ="more-content'+data[i]['id']+'">'+data[i]['text']+'<span class="close" onclick="noneContent('+data[i]['id']+');">X</span></div> <p>';
					viewFormate += stringCut;
					viewFormate +='</p> <a href="'+OVEconfig.BASEURL+'/practitioner/view/'+data[i]['user_id']+'" class="name"> written by - <em>';
					viewFormate += data[i]['user_name']+'</em>	</a> ';
					viewFormate += '<span class="post-time">- <em>'+data[i]['created_on']+'</em> </span> </article> </li>';
				}// End for loop 
		   $('#ts-main-list').html(viewFormate);
           $('input#page').val(parseInt($('input#page').val())+1)
           applyPagination();
           } else { $('#nomoreresults').fadeIn();
			    //$('#testimonial-error').html('Sorry no testimonial avialable..!!');
			    //$('#nomoreresults').html('Sorry no testimonial avialable..!!');
			   }
           // $('.default-load').fadeOut();
           // $('#loading').fadeOut();
           
        },
        error: function(xhr, errorType, errorMsg) {
            console.log(errorMsg)
        },
    });
    return false;
}

/*
 *  Function called when filter data changes every time. Code end here.
 *  
 *  */
 
function applyPagination()
{
	$('#ts-main-list').scrollPagination({ 
		'contentPage': OVEconfig.BASEURL+'/testimonials/index/', // the url you are fetching the results
		'contentData': {page: $('input#page').val()}, // these are the variables you can pass to the request, for example: children().size() to know which page you are
		//'scrollTarget': $('#scrolldiv'), // who gonna scroll? in this example, the full window
		'scrollTarget': $(window), // who gonna scroll? in this example, the full window
		'heightOffset': 12, // it gonna request when scroll is 10 pixels before the page ends
		'beforeLoad': function(){ // before load function, you can display a preloader div
			$('.default-load').fadeIn();
			//$('input#page').val(parseInt($('input#page').val())+1);
		},
		'afterLoad': function(elementsLoaded){ // after loading content, you can use this function to animate your new elements
			 $('.default-load').fadeOut();
			 var i = 0;
			 $(elementsLoaded).fadeInWithDelay();
			 if ($('#ts-main-list').children().size() > 100){ // if more than 100 results already loaded, then stop pagination (only for testing)
			 	$('#nomoreresults').fadeIn();
				$('#ts-main-list').stopScrollPagination();
			 }
		},
		'render' : function(data) {
			var content = Array();
			if (data.count > 0) { 
				 for(var i=0;i<data.count;i++) {
					if(data[i]['text']!='') {
						stringCut = ((data[i]['text'].length) > 120) ? data[i]['text'].substr(0, 120)+'....<a class="read-more" href="javascript:void(0);" onClick="blockContent('+data[i]['id']+');">Read More</a>': data[i]['text'];
					} else { stringCut="Sorry no text..!!";}
					content.push('<li> <article> <div class="more-content" id ="more-content'+data[i]['id']+'">'+data[i]['text']+'<span class="close" onclick="noneContent('+data[i]['id']+');">X</span></div> <p>');
					
					content.push(stringCut);
					content.push('</p> <a href="'+OVEconfig.BASEURL+'/practitioner/view/'+data[i]['user_id']+'" class="name"> written by - <em>');
					content.push(data[i]['user_name']+'</em></a> ');
					content.push('<span class="post-time">- <em>'+data[i]['created_on']+'</em> </span> ');
					content.push('</article> </li>');
				};
			} else {
				$('#nomoreresults').fadeIn();
				$('#ts-main-list').stopScrollPagination();
			}
			$('ul#ts-main-list').append(content.join(''));
		},
		'updateData' : function(data) {
			
			$('input#page').val(parseInt(data.page)+1);
			data.page = $('input#page').val();
			return data;
		}
	});
	
	// code for fade in element by element
	$.fn.fadeInWithDelay = function(){
		var delay = 0;
		return this.each(function(){
			$(this).delay(delay).animate({opacity:1}, 200);
			delay += 100;
		});
	};
	/* Infinite pagination code ends here */
}
 
function blockContent(id)
{
	$("#more-content"+id).css("display","block");	 
}
function noneContent(id)
{
	$("#more-content"+id).css("display","none");	 
}
 
	
