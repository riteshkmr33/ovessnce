/**
 * @author dharmendra
 */
$(window).load(function(){
var windowScroll=$(window)
	$('.parallax-image').each(function() {	
		$(this).data('speed', $(this).attr('detaSpeed'));
	});
	$('.parallax-image').each(function(){
		var dSelf=$(this),
			dOffestTop=dSelf.offset().top;
		windowScroll.scroll(function(){
			if ( (windowScroll.scrollTop() + windowScroll.height()) > (dOffestTop-100) && ( (dOffestTop + dSelf.height()) > windowScroll.scrollTop() ) ) {
				var yPosition = (windowScroll.scrollTop() / dSelf.data('speed')); 
				var positionY = '50%'+ yPosition + 'px';
				dSelf.css({ backgroundPosition: positionY });
			};
		
		});
	
	});
});