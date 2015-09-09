<div class="navbar navbar-default">
	<div class="container">
    	<div class="navbar-header">
        	<a class="navbar-brand" href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']);?>">
                    <img src="https://s3-us-west-2.amazonaws.com/ovessence/img/logo.png" alt="logo" style="width:28%" /> <?php echo $tl["general"]["g"];?> - <?php echo LS_TITLE;?>
                </a>
    	</div>
	</div>
</div>
 
 <!--- Container -->
 <div class="container">
 	
 	<div class="row">
 		<div class="col-md-8">
 		
 			<!--- Chat output -->
 				<div id="jrc_chat_output">
 					<ul>
 						<?php if ($proactivemsg) { ?>
 						<li class="admin"><span class="response_sum"><?php echo $tl["general"]["g52"];?>:</span><br /><?php echo $proactivemsg;?></li>
 						<?php } ?>
 						<li class="admin"><span class="response_sum"><?php echo $tl["general"]["g56"];?>:</span><br /><?php echo $tl["general"]["g60"];?></li>
 					</ul>
 				</div>
 				
 				<div id="client_input_container">
 				
 				<form class="ls-ajaxform" class="form-inline" method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']);?>">
 					
 					<div class="form-group">
 						<label class="sr-only" for="message"><?php echo $tl["general"]["g6"];?></label>
 						<textarea name="message" id="message" rows="2" class="form-control chat_txt_msg"></textarea>
 					</div>
 					<button name="sendMSG" class="btn btn-primary btn-block ls-submit"><?php echo $tl["general"]["g10"];?></button>
 					
 					<input type="hidden" name="start_chat" value="1" />
 					<input type="hidden" name="slide_chat" value="<?php echo $_GET['slide'];?>" />
 					<input type="hidden" name="lang" value="<?php echo $_GET['lang'];?>" />
 					
 				</form>
 			</div>
 			
 			</div>
 			
 		<div class="col-md-4 sidebar">
 		
 			<h4><?php echo $tl["general"]["g52"];?></h4>
 			
 			<div class="alert alert-block"><?php echo $tl["general"]["g60"];?></div>
 			
 		</div>
 		
 		</div>
 		
 		<hr>
 		
 		<footer><a href="//ovessence.com" target="_blank">Live Chat Ovessence</a></footer>
 		
 	</div>