/**
 * @author Shivani
 */
 
/*$(function() {
	$( "#slider-range" ).slider({
		range: "min",
		decimal:true,
		min: 0.0,
		max: 100,
		value: 60.02,
		slide: function( event, ui ) {
			$( "#distance" ).val( ui.value );
		}
	});
});*/

$(function() {
			/*
			var $awesome2 = $("#awesome2").slider({ range:"min",max: 20 , value: 4 });
			$awesome2.slider("pips", { rest: "label",handle: true, pips: true }).slider("float");
			*/ 
			$( "#awesome2" ).on( "slidechange", function( event, ui ) { 
				var sliderValue = $( "#awesome2" ).slider( "value");
			});
			
			});
