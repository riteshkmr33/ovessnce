/*======================================================================*\
|| #################################################################### ||
|| # Rhino Socket 2.0                                                 # ||
|| # ---------------------------------------------------------------- # ||
|| # Copyright 2014 Rhino All Rights Reserved.                        # ||
|| # This file may not be redistributed in whole or significant part. # ||
|| #   ---------------- Rhino IS NOT FREE SOFTWARE ----------------   # ||
|| #                  http://www.livesupportrhino.com                 # ||
|| #################################################################### ||
\*======================================================================*/

/* Style Changer */
$(document).ready(function(){

	$("#pcolor, #pheadcontent, #pfont, #phead, #icont, #pafont").minicolors({theme: "bootstrap"});
		
	$('.styleChanger .stCols span').click(function(){
		var bgCol = $(this).css('background-color');
		$('.styleChanger .stCols span').removeClass('current');
		$(this).addClass('current');
		$('#pcolor').val(rgb2hex(bgCol));
	});
});

var hexDigits = new Array
        ("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f"); 

//Function to convert rgb format to a hex color
function rgb2hex(rgb) {
 rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
 return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
}

function hex(x) {
  return isNaN(x) ? "00" : hexDigits[(x - x % 16) / 16] + hexDigits[x % 16];
 }