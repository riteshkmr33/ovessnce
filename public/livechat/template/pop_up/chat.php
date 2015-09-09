<div class="navbar navbar-default">
	<div class="container">
    	<div class="navbar-header">
        	<a class="navbar-brand" href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']);?>">
                    <!--<img src="img/logo.png" alt="logo" /> <?php echo $tl["general"]["g"];?> - <?php echo LS_TITLE;?>-->
                    <img src="https://s3-us-west-2.amazonaws.com/ovessence/img/logo.png" alt="logo" style="width:28%" /> <?php echo $tl["general"]["g"];?> - <?php echo LS_TITLE;?>
                </a>
    	</div>
	</div>
</div>

<!--- Container -->
<div class="container">
	
	<div class="row">
		<div class="col-sm-8">
		
			<!--- Chat output -->
			<div id="jrc_chat_output"></div>
			
			<div id="client_input_container">
			<!-- Client Input -->
				<form action="javascript:sendInput();" name="messageInput" id="MessageInput">
					
					<!-- Operator is typing -->
					<div id="jrc_typing"></div>
					
					<textarea name="message" id="message" rows="2" class="form-control chat_txt_msg"></textarea>
					<button name="sendMSG" class="btn btn-primary btn-block"><?php echo $tl["general"]["g11"];?></button>
					
					<input type="hidden" name="userID" id="userID" value="<?php echo $_SESSION['jrc_userid'];?>" />
					<input type="hidden" name="userName" id="userName" value="<?php echo $_SESSION['jrc_name'];?>" />
					<input type="hidden" name="convID" id="convID" value="<?php echo $_SESSION['convid'];?>" />
					
				</form>
				
				<span id="audio_alert"></span>
				<div id="rhino_update"></div>
				<div id="msgError" class="alert alert-danger"></div>
			
			</div>
			
		</div>
		<div class="col-sm-4 sidebar">
		
			<h4><?php echo $tl["general"]["g52"];?></h4>
			
			<!-- show most content only if operator is connected -->
			<div id="operator_connected">
			
			<!-- Display Operator Image -->
			<div id="operator"></div>
			
			<!-- Display Operator Name -->
			<div id="oname" class="alert alert-block"></div>
			
			<hr>
			
			<form action="<?php echo $parseurl;?>" method="post">
			
			<?php if (LS_CRATING && !LS_FEEDBACK) { ?>
			
			<!-- Rate Conversation -->
				<div id="starify" class="rating_inline">
					<label for="vote1"><input type="radio" name="fbvote" id="vote1" value="1" title="Poor" /> Poor</label>
					<label for="vote2"><input type="radio" name="fbvote" id="vote2" value="2" title="Fair" /> Fair</label>
					<label for="vote3"><input type="radio" name="fbvote" id="vote3" value="3" title="Average" /> Average</label>
					<label for="vote4"><input type="radio" name="fbvote" id="vote4" value="4" title="Good" /> Good</label>
					<label for="vote5"><input type="radio" name="fbvote" id="vote5" value="5" title="Excellent" checked="checked" /> Excellent</label>
				</div>
				
			<div class="clearfix"></div>
				
			<hr>
			
			<?php } ?>
			
			<h4><?php echo $tl["general"]["g53"];?></h4>
			
			<!-- Print Conversation -->
			<p><a href="#" id="print_transcript" class="btn btn-info btn-sm"><span class="glyphicon glyphicon-print"></span></a>
			
			
			<!-- Close Window -->
			<button type="submit" class="btn btn-danger btn-sm" onclick="if(!confirm('<?php echo $tl["general"]["g40"];?>'))return false;"><span class="glyphicon glyphicon-off"></span> <?php echo $tl["general"]["g15"];?></button></p>
			
			</form>
			
			<div id="uploadpp_wrapper">
			<hr>
			<form class="dropzone small" id="cUploadDrop" enctype="multipart/form-data">
			  <div class="fallback">
			    <input name="file" type="file" multiple />
			  </div>
			  <input type="hidden" name="convID" value="<?php echo $_SESSION['convid'];?>" />
			  <input type="hidden" name="base_url" value="<?php echo BASE_URL;?>" />
			</form>
			</div>
			
		</div>
		
		</div>
	</div>
			
	<hr>
	
	<footer><a href="//ovessence.com" target="_blank">Live Chat Ovessence</a></footer>
</div>