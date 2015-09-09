<!--- Container -->
<div class="container">

	<div class="jrc_chat_form_slide">
	
		<!--- Chat output -->
		<div id="jrc_chat_output" style="height: 230px;">
			<ul class="list-group">
				<?php if ($proactivemsg) { ?>
				<li  class="list-group-item admin"><span class="response_sum"><?php echo $tl["general"]["g52"];?>:</span><br /><?php echo $proactivemsg;?></li>
				<?php } ?>
				<li  class="list-group-item admin"><span class="response_sum"><?php echo $tl["general"]["g56"];?>:</span><br /><?php echo $tl["general"]["g60"];?></li>
			</ul>
		</div>
		
		<form role="form" class="ls-ajaxform" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
			
			<div class="form-group">
				<label class="sr-only" for="message"><?php echo $tl["general"]["g6"];?></label>
				<textarea name="message" id="message" rows="2" class="form-control chat_txt_msg"></textarea>
			</div>
			
			<input type="hidden" name="start_chat" value="1" />
			<input type="hidden" name="slide_chat" value="<?php echo $_GET['slide'];?>" />
			
		</form>
	</div>
	
	<hr>
	
	<footer><a href="http://www.livesupportrhino.com" target="_blank">Live Chat Rhino</a></footer>
</div>