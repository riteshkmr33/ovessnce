function scrollEvent(){
	var trigger=$('.trigger').offset().top;
	var windowHeight=parseInt($(window).height())-250;
	$('.para').each(function(){
		if($(this).offset().top<=trigger+windowHeight && $(this).hasClass('common-down')){
			$(this).removeClass('common-down');
			$(this).addClass('common-up');
		}
		// else if($(this).offset().top>=trigger+windowHeight && $(this).hasClass('common-up')){
			// $(this).removeClass('common-up');
			// $(this).addClass('common-down');
		// };
	});
};
$(window).scroll(function(){
	scrollEvent();
});
$(window).load(function(){
	scrollEvent();
	$('#banner').flexslider({
		animation: "fade",
		controlNav:false,
		directionNav:false
	});
});
