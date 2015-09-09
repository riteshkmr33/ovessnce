<?php include_once APP_PATH.'operator/template/header.php';?>

<?php if (LS_NV_ALERT && $LS_SPECIALACCESS && LS_VC_STATUS && LS_VC_LSBVERSION > LS_VERSION) { ?>

<div class="alert">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <h4><?php echo LS_VC_LSBNEWS;?></h4>
  <?php echo str_replace("%s", LS_VC_LSBVERSION, $tl["general"]['g150']);?><a href="<?php echo LS_VC_LSBURL;?>"><?php echo LS_VC_LSBURL;?></a>.
</div>

<?php } ?>

<div class="row">

<div class="col-md-8">

<!-- Chat Output -->
<h5><a href="javascript:void(0)" id="user-info-hide"><span class="glyphicon glyphicon-user"></span></a> <?php echo $tl["general"]["g6"];?></h5>

<!-- Operator transfer -->
<div id="operatori"><?php echo $operatori;?></div>

<!-- user info output -->
<div id="user-info" class="table-responsive"></div>

<!-- chat output -->
<div id="chatOutput"></div>

<hr>

<!-- Error MSG -->
<div id="msgError"></div>

<!--- Input form -->
	<form role="form" name="messageInput" class="hidden" id="MessageInput" action="javascript:sendInput(ls.activeConv);">
	
	<?php if ($lsuser->getVar("responses") && isset($LV_RESPONSES) && is_array($LV_RESPONSES)) { ?>
	
	<div class="form-group">
	<label class="control-label" for="response"><?php echo $tl["general"]["g7"];?></label>
	  <select name="standard" id="response" class="form-control">
	  <option value="0"><?php echo $tl["general"]["g7"];?></option>
	
	<?php foreach($LV_RESPONSES as $r) { ?>
	
	<option value="<?php echo $r["id"];?>"><?php echo $r["title"];?></option>
	
	<?php } ?>
	
	</select>
	</div>
	
	<?php } ?>
	
	<div class="form-group">
	<label class="control-label" for="message"><?php echo $tl["general"]["g135"];?></label>
	<textarea name="message" id="message" class="form-control" rows="5"></textarea>
	</div>
	
	<button name="sendMSG" class="btn btn-primary btn-block"><?php echo $tl["general"]["g4"];?></button>
	
	<input type="hidden" name="userID" id="userID" value="<?php echo $lsuser->getVar("id");?>" />
	<input type="hidden" name="userName" id="userName" value="<?php echo $lsuser->getVar("username");?>" />
	<input type="hidden" name="operatorName" id="operatorName" value="<?php echo $lsuser->getVar("name");?>" />
	<input type="hidden" name="operatorChat" id="operatorChat" value="<?php echo $lsuser->getVar("operatorchat");?>" />
	<input type="hidden" name="operatorList" id="operatorList" value="<?php echo $lsuser->getVar("operatorlist");?>" />
	<input type="hidden" name="operatorDep" id="operatorDep" value="<?php echo $_SESSION['usr_department'];?>" />
	<input type="hidden" name="oidhash" id="oidhash" value="<?php echo $_SESSION['lc_idhash'];?>" />
	<input type="hidden" name="oplang" id="oplang" value="<?php echo $_SESSION['lc_ulang'];?>" />
	<input type="hidden" name="convID" id="convID" value="" />
				
	</form>
	
	<?php if ($lsuser->getVar("files")) { ?>
	
	<div class="clearfix"></div>
	
	<div id="upload_forms" class="hidden">
	
	<h4><?php echo $tl["general"]["g127"];?></h4>
	
	<form class="dropzone" id="cUploadDrop" enctype="multipart/form-data">
	  <div class="fallback">
	    <input name="file" type="file" multiple />
	  </div>
	  <input type="hidden" name="convID" id="convIDI" value="" />
	  <input type="hidden" name="userIDU" value="<?php echo $lsuser->getVar("id");?>" />
	  <input type="hidden" name="base_url" value="<?php echo BASE_URL_ORIG;?>" />
	  <input type="hidden" name="operatorNameU" value="<?php echo $lsuser->getVar("name");?>" />
	  <input type="hidden" name="operatorLanguage" value="<?php echo $USER_LANGUAGE;?>" />
	</form>
	
	<?php if (isset($LV_FILES) && is_array($LV_FILES)) { ?>
	
	<div class="standard_files form-inline">

	<select name="files" id="files" class="form-control">
	<option value="0"><?php echo $tl["general"]["g8"];?></option>
	
	<?php foreach($LV_FILES as $f) { ?>
	
	<option value="<?php echo $f["id"];?>"><?php echo $f["name"];?></option>
	
	<?php } ?>
	
	</select>
	
	<button name="sendFile" id="sendFile" class="btn btn-success"><?php echo $tl["general"]["g4"].' '.$tl["general"]["g9"];?></button>
	
	</div>
	
	<?php } ?>
	
	</div>
	
	<?php } ?>

</div>

<div class="col-md-4">

<div id="showclock" data-date=""></div>

<!-- User currently active in the chat -->
<h5><span class="glyphicon glyphicon-inbox"></span> <?php echo $tl["general"]["g5"];?></h5>
<div id="currentConv"></div>

<hr>

<!-- User Online on Website -->
<h5><a href="javascript:void(0)" class="rhino-help-bubble" title="<?php echo $tl["help"]["h5"];?>"><span class="glyphicon glyphicon-star"></span></a> <a href="index.php?p=uonline"><?php echo $tl["general"]["g122"];?></a></h5>
<div id="userOnline"></div>

<?php if ($lsuser->getVar("operatorlist") || $lsuser->getVar("operatorchat")) { ?>

<hr>

<!-- Operators Online on Website -->
<h5><span class="glyphicon glyphicon-share"></span> <?php echo $tl["general"]["g134"];?></h5>
<div id="operatorOnline"></div>

<?php } ?>

</div>

</div>

<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
<script type="text/javascript" src="../js/dropzone.js"></script>
<script type="text/javascript" src="js/index.ajax.js"></script>

<script type="text/javascript">
$(document).ready(function(){

	//dropzone config
	Dropzone.options.cUploadDrop = {
	    dictResponseError: "SERVER ERROR",
	    paramName: "uploadpp", // The name that will be used to transfer the file
	    addRemoveLinks: true,
	    maxFilesize: 3,
	    maxFiles: 3,
	    acceptedFiles: "<?php echo LS_ALLOWEDO_FILES;?>",
	    url: "../uploader/uploadero.php",
	    init: function () {
	        this.on("complete", function (file) {
	          if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
	            loadchat = true;
	            scrollchat = true;
	            getInput(ls.activeConv);
	          }
	        });
	      }
	};
		
		<?php if ($operatori) { ?>
			$("#operatori").fadeIn(200).delay(10000).fadeOut(200);
		<?php } ?>
	
});

		ls.main_url = "<?php echo BASE_URL_ADMIN;?>";
		ls.socket_url = "<?php echo SOCKET_PROTOCOL;?>";
		ls.main_lang = "<?php echo LS_LANG;?>";
		ls.ls_submit = "<?php echo $tl['general']['g69'];?>";
		ls.ls_submitwait = "<?php echo $tl['general']['g70'];?>";
		ls.ls_transfer = "<?php echo $tl['general']['g110'];?>";
		// by default we want to retrieve dashboard
		ls.activeConv = "open";
		
</script>

<?php if ($lsuser->getVar("operatorchat") == 1){;?>

<script type="text/javascript" src="js/operator.chat.js"></script>

<!-- reopen old opened chatboxes with the last state-->
<?php if (isset($_SESSION['chatbox_status'])) {
	echo '<script type="text/javascript">';
	echo '$(function() {';
	foreach ($_SESSION['chatbox_status'] as $openedchatbox) {
		echo 'PopupChat('.$openedchatbox['partner_id'].',"'.$openedchatbox['partner_username'].'",'.$openedchatbox['chatbox_status'].');';
	}
	echo "});";
	echo '</script>';
	}
?>

<?php } include_once APP_PATH.'operator/template/footer.php';?>