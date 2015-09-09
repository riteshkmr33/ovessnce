/**
 * @author dharmendra
 */

$(document).ready(function(){
	$('.membership-plan .green-btn').click(function(){
		var thisIndex=$(this).parent().index();
		$('.membership-plan tr').each(function(){
			$(this).children('td').removeClass('active active-bg')
			$(this).children('td').eq(thisIndex).addClass('active active-bg')
		})
		$('.membership-plan col').removeClass('active').eq(thisIndex).addClass('active')
	})
});