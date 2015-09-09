<!--- Container -->
<div class="container">

	<div id="operator_connected" class="jrc_chat_form_slide">
	
			<div class="pull-left">
	
			<!-- Display Operator Name -->
			<span id="oname" class="label label-warning"></span></div>
			
			<div class="pull-right">
			
			<!-- Print Conversation -->
			<a href="#" id="print_transcript" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-print"></span></a>
			
			<!-- Close Window -->
			<a href="<?php echo $parseurl;?>" class="btn btn-xs btn-danger" onclick="if(!confirm('<?php echo $tl["general"]["g40"];?>'))return false;"><span class="glyphicon glyphicon-off"></span></a>
			
			</div>
			
			<div class="clearfix"></div>
			<div id="uploadpp_wrapper">
			<form class="dropzone small" id="cUploadDrop" enctype="multipart/form-data">
			  <div class="fallback">
			    <input name="file" type="file" multiple />
			  </div>
			  <input type="hidden" name="convID" value="<?php echo $_SESSION['convid'];?>" />
			  <input type="hidden" name="base_url" value="<?php echo BASE_URL;?>" />
			</form>
			</div>
	</div>

	<div class="jrc_chat_form_slide">
		
		<!--- Chat output -->
		<div id="jrc_chat_output" style="height: 230px;"></div>
			
		<div id="client_input_container">
		<!-- Client Input -->
			<form action="javascript:sendInput();" name="messageInput" id="MessageInput">
				<div id="msgError" class="alert alert-danger"></div>
				
				<!-- Operator is typing -->
				<div id="jrc_typing"></div>
					
				<textarea name="message" id="message" rows="3" class="form-control chat_txt_msg"></textarea>
					
				<input type="hidden" name="userID" id="userID" value="<?php echo $_SESSION['jrc_userid'];?>" />
				<input type="hidden" name="userName" id="userName" value="<?php echo $_SESSION['jrc_name'];?>" />
				<input type="hidden" name="convID" id="convID" value="<?php echo $_SESSION['convid'];?>" />
					
			</form>
				
			<span id="audio_alert"></span>
			<div id="rhino_update"></div>
			
		</div>
	</div>
	
	<hr>
	
	<footer><a href="//ovessence.com" target="_blank">Live Chat Ovessence</a></footer>
</div>