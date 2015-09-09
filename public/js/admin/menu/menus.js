$(function() {
	/* toogle */
	$(document).on('click',"h3.hndle",function(){
		
        $(this).next().toggle();
    
			if($(this).children('span').next().html() == "+") {
				
                $(this).children('span').next().html('-');
                  
            }else{
				
				$(this).children('span').next().html('+');
            
            }     
            
    });
    
    /* to save menu */        
	$(document).on('click',"#save_menu",function(){
		var murl = $("#menu_url").val();
		var mlevel = $("#menu_level").val();
		var menuMangerId =   $('#tabs').children('.active').children('a').attr('href');
			menuMangerId = menuMangerId.replace('#tab','');
		if(murl=='' || mlevel =='' ){
			alert( 'please enter value');
			return false;
		} 

		$.post('/admin/Menus/Savemenu', { murl: murl, mlevel: mlevel ,menuMangerId : menuMangerId})
			.done(function(data) {
			   var obj = jQuery.parseJSON(data);
				$(".alert-error").remove();
				$("#menu_url").val('http://');
				$("#menu_level").val('');
				$(".wrapper").prepend('<div class="alert '+obj.class+'">'+obj.msg+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				if(obj.class == 'alert-success'){
					$("#nestable ol").first().append('<li class="dd-item" data-id="'+obj.id+'"><div class="dd-handle">'+obj.label+'</div><span class="myTrig" id="cont'+obj.id+'"><i>Custom</i><b>+</b></span><div id="cont'+obj.id+'" class="Vcontents cont'+obj.id+' clearfix"><ul><li><label> Label : </label><input type="text" id="label_'+obj.id+'" name="label_'+obj.id+'" value="'+obj.label+'"></li><li><label> Title : </label><input type="text" name="title_'+obj.id+'" id="title_'+obj.id+'" value=""></li><li class="wd100 "><label> Url :</label><input type="text" id="label_'+obj.id+'" name="label_'+obj.id+'" value="'+obj.url+'"><input type="hidden" name="hurl_'+obj.id+'" id="hurl_'+obj.id+'" value="'+obj.url+'"><input type="hidden" name="hlabel_'+obj.id+'" id="hlabel_'+obj.id+'" value="'+obj.label+'"><input type="hidden" name="htitle_'+obj.id+'" id="htitle_'+obj.id+'" value=""></li></ul><div class=" linkurl"><a herf="#" class="removeme" >Remove</a> | <a herf="#" class="Rcancel" >Cancle</a> </div></div></li>');
				}else{
					return false;
				}
			})
			.fail(function() { 
			})
	});
        
    /* to add page to menu */    
	$(document).on('click',"#save_page",function(){

		$('input:checkbox.pagecheckbox').each(function () {
			
			var sThisVal = (this.checked ? $(this).val() : "");   
			var menuMangerId =   $('#tabs').children('.active').children('a').attr('href');
			menuMangerId = menuMangerId.replace('#tab','');
			if(sThisVal && menuMangerId){
				$.post('/admin/Menus/Savepage', { pageid: sThisVal ,menuMangerId : menuMangerId })
					.done(function(data) {
					var obj = jQuery.parseJSON(data);
					$(".alert-error").remove();
					$("#page_"+sThisVal).prop('checked', false);
					$(".wrapper").prepend('<div class="alert '+obj.class+'">'+obj.msg+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
					if(obj.class == 'alert-success'){
						$("#nestable ol").first().append('<li class="dd-item" data-id="'+obj.id+'"><div class="dd-handle">'+obj.label+'</div><span class="myTrig" id="cont'+obj.id+'"><i>Page</i><b>+</b></span><div id="cont'+obj.id+'" class="Vcontents cont'+obj.id+' clearfix"><ul><li><label> Label : </label><input type="text" id="label_'+obj.id+'" name="label_'+obj.id+'" value="'+obj.label+'"></li><li><label> Title : </label><input type="text" id="title_'+obj.id+'" name="title_'+obj.id+'" value=""></li><li class="wd100 "><label> Url :</label><input type="text" id="url_'+obj.id+'" name="url_'+obj.id+'" value="'+obj.url+'"><input type="hidden"  name="hurl_'+obj.id+'" id="hurl_'+obj.id+'" value="'+obj.url+'"><input type="hidden" name="hlabel_'+obj.id+'" id="hlabel_'+obj.id+'" value="'+obj.label+'"><input type="hidden" name="htitle_'+obj.id+'" id="htitle_'+obj.id+'" value=""></li></ul><div class=" linkurl"><a herf="#" class="removeme" >Remove</a> | <a herf="#" class="Rcancel" >Cancle</a> </div></div></li>');
					}
				})
				.fail(function() { })
			}
		
		});
		
	});

    $(document).on("click",".myTrig",function(){
		
		var mclass = $(this).attr('id');
		$("."+mclass).toggle();
			
		if($("."+mclass).is(":visible")==true){
			$(this).children('b').html("-");
		}
		else
		{
			$(this).children('b').html("+");
		}
		return false;
	});
	
	var updateOutput = function(e)
    {
        var list   = e.length ? e : $(e.target),output = list.data('output');
       
		if (window.JSON) 
		{
            output.val(window.JSON.stringify(list.nestable('serialize')));//, null, 2));
        } 
		else 
		{
            output.val('JSON browser support required for this demo.');
        }
    };

	// activate Nestable for list 1
    $('#nestable').nestable({
        group: 1
    })
    .on('change', updateOutput);
           
	// expand collape list 
	$(document).on('click',"#nestable-menu",function(e)
    {
        var target = $(e.target),
            action = target.data('action');
        if (action === 'expand-all') {
            $('.dd').nestable('expandAll');
        }
        if (action === 'collapse-all') {
            $('.dd').nestable('collapseAll');
        }
    });
	
	// Lets update output
    updateOutput($('#nestable').data('output', $('#nestable-output')));
	
	$(document).on('click',".removeme",function(){
		var id =  $(this).closest('li').attr("data-id");
		var me = $(this).closest('li');
		
		if(id){
			$.post('/admin/Menus/delete', { id: id})
				.done(function(data) {
				me.remove();
				var obj = jQuery.parseJSON(data);                    
				$(".wrapper").prepend('<div class="alert '+obj.class+'">'+obj.msg+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');					   
				}).fail(function() { })
		}
        updateOutput($('#nestable').data('output', $('#nestable-output')));
	});
	
	$(document).on('click',".Rcancel",function(){
		
		var id =  $(this).closest('li').attr("data-id");
		var oldLabel = $('#hlabel_'+id).val(); 
		var oldTitle = $('#htitle_'+id).val(); 
		var oldUrl = $('#hurl_'+id).val(); 
		$('#label_'+id).val(oldLabel);
		$('#title_'+id).val(oldTitle); 
		$('#url_'+id).val(oldUrl);
		
	});   
   
   
	$(document).on('click',".tabclass",function(){
	var id = $(this).attr('href');
	id = id.replace('#tab','');
	$('#menu_manager_id').val(id);		  
		$.ajax({
			type : "post",
			url :  "/admin/Menus/Tabmenu/menuid/"+id,
			success: function(data) {
			  $('#nestable').html(data);
				$.ajax({
				  type : "post",
				  url :  "/admin/Menus/Ajaxrequest/menuid/"+id,
					success: function(data) {
					  $('#nestable-output').val(data);
					}
				}); 
			}
		});
	});
  
   $(document).on('click',"#saveBtn",function(){
       $('#myform').submit();
   });

});   
