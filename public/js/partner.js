/**
 * @author dharmendra
 */

$(document).ready(function(){
	$('.partner-list li').hover(function(){
		var windowWidth=$(window).width();
		var thisWidth=$(this).find('.partner-dfn').outerWidth();
		var this_Width=$(this).outerWidth();
		var thisOffset=$(this).find('.partner-dfn').offset().left;
		if(windowWidth>(thisWidth+thisOffset)){
			
		}else if(windowWidth<(thisWidth+thisOffset) && windowWidth > (thisWidth+this_Width)){
			$(this).find('.partner-dfn').addClass('right-position');
			}else{
			$(this).find('.partner-dfn').addClass('bottom-position');
			}
	})
})
