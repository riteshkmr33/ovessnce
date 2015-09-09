<?php include_once APP_PATH.'operator/template/header.php';?>

<?php if ($LS_SPECIALACCESS && LS_VC_STATUS && LS_VC_LSLVERSION > LS_VERSION) { ?>

<div class="alert">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <h4><?php echo LS_VC_LSLNEWS;?></h4>
  <?php echo str_replace("%s", LS_VC_LSLVERSION, $tl["general"]['g150']);?><a href="<?php echo LS_VC_LSLURL;?>"><?php echo LS_VC_LSLURL;?></a>.
</div>

<?php } ?>

<div class="row">

<div class="span8">

<!-- user info output -->
<div id="jrc_clientinfo"></div>

<!-- chat output -->
<div id="chatOutput"></div>

<hr>

<!-- Error MSG -->
<div id="msgError"></div>

<select name="standard" id="response">
<option value="0"><?php echo $tl["general"]["g7"];?></option>
	
<?php if (isset($LV_RESPONSES) && is_array($LV_RESPONSES)) foreach($LV_RESPONSES as $r) { ?>
	
<option value="<?php echo $r["id"];?>"><?php echo $r["title"];?></option>
	
<?php } ?>
	
</select>

<div class="input-append">
<select name="files" id="files">
<option value="0"><?php echo $tl["general"]["g8"];?></option>
	
<?php if (isset($LV_FILES) && is_array($LV_FILES)) foreach($LV_FILES as $f) { ?>
	
<option value="<?php echo $f["id"];?>"><?php echo $f["name"];?></option>
	
<?php } ?>

</select>
<button name="sendFile" value="send" id="sendFile" class="btn" onclick="javascript:sharedFiles();"><?php echo $tl["general"]["g4"].' '.$tl["general"]["g9"];?></button>

</div>

<!--- Input form -->
<form name="messageInput" id="MessageInput" action="javascript:jrc_sendInput(ls.activeConv);">

<textarea name="message" id="message" class="span8" rows="5"></textarea>

<div class="form-actions">
<button type="submit" class="btn btn-primary pull-right" id="chat_s_button"><?php echo $tl["general"]["g4"];?></button>
</div>
	
<input type="hidden" name="userID" id="userID" value="<?php echo $lsuser->getVar("id");?>" />
<input type="hidden" name="userName" id="userName" value="<?php echo $lsuser->getVar("username");?>" />
<input type="hidden" name="operatorName" id="operatorName" value="<?php echo $lsuser->getVar("name");?>" />
				
</form>

</div>

<div class="span4">

<!-- Company Name -->
<h3><?php echo LS_TITLE;?></h3>

<hr class="soften">

<!-- Button -->
<button type="button" class="btn<?php if ($lsuser->getVar("available") == 0) { echo ' btn-danger'; } else { echo ' btn-success';}?> rhino-help-bubble" id="available_user" data-toggle="button" title="<?php echo $tl["help"]["h7"];?>"><?php if ($lsuser->getVar("available") == 0) { echo '<i class="icon-off"></i> '.$tl["general"]["g1"]; } else { echo '<i class="icon-ok"></i> '.$tl["general"]["g"]; } ?></button>

<button type="button" class="btn btn-success rhino-help-bubble" id="sound_alert" data-toggle="button" title="<?php echo $tl["help"]["h8"];?>"><i class="icon-volume-up"></i> <?php echo $tl["general"]["g2"];?></button>

<hr class="soften">

<!-- User currently active in the chat -->
<h5><i class="icon-inbox"></i> <?php echo $tl["general"]["g5"];?></h5>
<div id="jrc_conversations"></div>

</div>

</div>

<!-- Archive, Delete Transfer -->
<div id="inchatModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="inchatModal" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="inchatModalLabel"><?php echo LS_TITLE;?></h3>
  </div>
  <div class="modal-body">
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo $tl["general"]["g99"];?></button>
  </div>
</div>

<script type="text/javascript" src="js/index.ajax.js"></script>

<script type="text/javascript">
$(document).ready(function(){
	jrc_conversation(1);
		setInterval("jrc_conversation(1);", 3000);
	jrc_setTimer(<?php echo $lsuser->getVar("id");?>);
	        setInterval("jrc_setTimer(<?php echo $lsuser->getVar("id");?>);", 120000);
	        
		$('#inchatModal').on('hidden', function () {
		  $(this).removeData();
		});
});

		ls.main_url = "<?php echo BASE_URL_ADMIN;?>";
		ls.main_lang = "<?php echo LS_LANG;?>";
		ls.ls_submit = "<?php echo $tl['general']['g69'];?>";
		ls.ls_submitwait = "<?php echo $tl['general']['g70'];?>";
		ls.ls_online = "<i class='icon-ok'></i> <?php echo $tl['general']['g'];?>";
		ls.ls_offline = "<i class='icon-off'></i> <?php echo $tl['general']['g1'];?>";
		ls.ls_alert = "<i class='icon-volume-up'></i> <?php echo $tl['general']['g2'];?>";
		ls.ls_alerton = "<i class='icon-volume-off'></i> <?php echo $tl['general']['g2'];?>";
		// set refresh rate of chat window 
		ls.chatRefresh = 3000;
		// by default we want to retrieve dashboard
		ls.activeConv = "open";
		// User stat
		ls.usrAvailable = <?php echo $lsuser->getVar("available");?>;
		// set up auto refresh to pull new entries into chat window
		ls.intervalID = setInterval("jrc_getInput(ls.activeConv);", ls.chatRefresh);
</script>

<?php include_once APP_PATH.'operator/template/footer.php';?>