<?php include_once APP_PATH.'operator/template/header.php';?>

<h3><?php echo $tl["menu"]["m5"];?></h3>

<?php if ($errors) { ?>
<div class="alert alert-danger"><?php echo $errors["e"].$errors["e1"].$errors["e2"].$errors["e3"].$errors["e4"].$errors["e5"].$errors["e6"].$errors["e7"];?></div>
<?php } if ($success) { ?>
<div class="alert alert-success fade in">
	<?php echo $success["e"];?>
</div>
<?php } ?>
<form method="post" class="jak_form" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<table class="table table-striped">
<thead>
<tr>
<th colspan="2"><?php echo $tl["general"]["g15"];?></th>
</tr>
</thead>
<tr>
	<td><?php echo $tl["general"]["g16"];?></td>
	<td><input type="text" name="ls_title" class="form-control" value="<?php echo LS_TITLE;?>" placeholder="<?php echo $tl["general"]["g16"];?>" /></td>
</tr>
<tr>
	<td><?php echo $tl["login"]["l5"];?> <a href="javascript:void(0)" class="rhino-help" data-content="<?php echo $tl["help"]["h"];?>" data-original-title="<?php echo $tl["help"]["t"];?>"><span class="glyphicon glyphicon-question-sign"></span></a></td>
	<td>
	<div class="form-group<?php if ($errors["e1"]) echo " has-error";?>">
		<input type="text" name="ls_email" class="form-control" value="<?php echo LS_EMAIL;?>" placeholder="<?php echo $tl["login"]["l5"];?>" />
	</div>
	</td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g201"];?> <a href="javascript:void(0)" class="rhino-help" data-content="<?php echo $tl["help"]["h16"];?>" data-original-title="<?php echo $tl["help"]["t"];?>"><span class="glyphicon glyphicon-question-sign"></span></a></td>
	<td>
	<input type="text" name="ls_emailcc" class="form-control" value="<?php echo LS_EMAILCC;?>" placeholder="<?php echo $tl["login"]["l5"];?>" />
	</td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g158"];?></td>
	<td><div class="radio"><label><input type="radio" name="ls_chat_direct" value="1"<?php if (LS_CHAT_DIRECT == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g19"];?></label></div>
	<div class="radio"><label><input type="radio" name="ls_chat_direct" value="0"<?php if (LS_CHAT_DIRECT == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g18"];?></label></div></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g100"];?></td>
	<td><div class="radio"><label><input type="radio" name="ls_cemail" value="1"<?php if (LS_CLIENT_EMAIL == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g19"];?></label></div>
	<div class="radio"><label><input type="radio" name="ls_cemail" value="0"<?php if (LS_CLIENT_EMAIL == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g18"];?></label></div></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g233"];?></td>
	<td><div class="radio"><label><input type="radio" name="ls_scemail" value="1"<?php if (LS_CLIENT_SEMAIL == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g19"];?></label></div>
	<div class="radio"><label><input type="radio" name="ls_scemail" value="0"<?php if (LS_CLIENT_SEMAIL == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g18"];?></label></div></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g144"];?></td>
	<td><div class="radio"><label><input type="radio" name="ls_cphone" value="1"<?php if (LS_CLIENT_PHONE == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g19"];?></label></div>
	<div class="radio"><label><input type="radio" name="ls_cphone" value="0"<?php if (LS_CLIENT_PHONE == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g18"];?></label></div></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g199"];?></td>
	<td><div class="radio"><label><input type="radio" name="ls_scphone" value="1"<?php if (LS_CLIENT_SPHONE == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g19"];?></label></div>
	<div class="radio"><label><input type="radio" name="ls_scphone" value="0"<?php if (LS_CLIENT_SPHONE == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g18"];?></label></div></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g231"];?></td>
	<td><div class="radio"><label><input type="radio" name="ls_question" value="1"<?php if (LS_CLIENT_QUESTION == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g19"];?></label></div>
	<div class="radio"><label><input type="radio" name="ls_question" value="0"<?php if (LS_CLIENT_QUESTION == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g18"];?></label></div></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g232"];?></td>
	<td><div class="radio"><label><input type="radio" name="ls_squestion" value="1"<?php if (LS_CLIENT_SQUESTION == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g19"];?></label></div>
	<div class="radio"><label><input type="radio" name="ls_squestion" value="0"<?php if (LS_CLIENT_SQUESTION == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g18"];?></label></div></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g92"];?></td>
	<td><div class="radio"><label><input type="radio" name="ls_rating" value="1"<?php if (LS_CRATING == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g19"];?></label></div>
	<div class="radio"><label><input type="radio" name="ls_rating" value="0"<?php if (LS_CRATING == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g18"];?></label></div></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g119"];?></td>
	<td><div class="radio"><label><input type="radio" name="ls_captcha" value="1"<?php if (LS_CAPTCHA == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g19"];?></label></div>
	<div class="radio"><label><input type="radio" name="ls_captcha" value="0"<?php if (LS_CAPTCHA == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g18"];?></label></div></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g125"];?></td>
	<td><div class="radio"><label><input type="radio" name="ls_smilies" value="1"<?php if (LS_SMILIES == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g19"];?></label></div>
	<div class="radio"><label><input type="radio" name="ls_smilies" value="0"<?php if (LS_SMILIES == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g18"];?></label></div></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g20"].'/'.$tl["general"]["g21"];?></td>
	<td><div class="radio"><label><input type="radio" name="ls_shttp" value="0"<?php if (LS_SITEHTTPS == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g20"];?></label></div>
	<div class="radio"><label><input type="radio" name="ls_shttp" value="1"<?php if (LS_SITEHTTPS == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g21"];?></label></div></td>
</tr>
<tr>
	<td><?php echo $tl["stat"]["s12"];?></td>
	<td><div class="radio"><label><input type="radio" name="ls_feedback" value="1"<?php if (LS_FEEDBACK == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g19"];?></label></div>
	<div class="radio"><label><input type="radio" name="ls_feedback" value="0"<?php if (LS_FEEDBACK == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g18"];?></label></div></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g190"];?></td>
	<td><div class="radio"><label><input type="radio" name="wait_message3" value="1"<?php if (LS_WAIT_MESSAGE3 == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g19"];?></label></div>
	<div class="radio"><label><input type="radio" name="wait_message3" value="0"<?php if (LS_WAIT_MESSAGE3 == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g18"];?></label></div></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g22"];?></td>
	<td><select name="ls_lang" class="form-control">
	<?php if (isset($lang_files) && is_array($lang_files)) foreach($lang_files as $lf) { ?><option value="<?php echo $lf;?>"<?php if (LS_LANG == $lf) { ?> selected="selected"<?php } ?>><?php echo ucwords($lf);?></option><?php } ?>
	</select></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g147"];?></td>
	<td><div class="radio"><label><input type="radio" name="ls_langd" value="1"<?php if (LS_LANGDIRECTION == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g148"];?>
	</label></div>
	<div class="radio"><label>
	<input type="radio" name="ls_langd" value="0"<?php if (LS_LANGDIRECTION == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g149"];?>
	</label>
	</td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g23"];?></td>
	<td>
	<div class="form-group<?php if ($errors["e2"]) echo " has-error";?>">
		<input type="text" name="ls_date" class="form-control" value="<?php echo LS_DATEFORMAT;?>" />
	</div>
	</td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g24"];?></td>
	<td>
	<div class="form-group<?php if ($errors["e3"]) echo " has-error";?>">
		<input type="text" name="ls_time" class="form-control" value="<?php echo LS_TIMEFORMAT?>" />
	</div>
	</td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g25"];?></td>
	<td><select name="ls_timezone_server" class="form-control">
	<?php include_once "timezoneserver.php";?>
	</select></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g191"];?></td>
	<td><div class="radio"><label><input type="radio" name="showalert" value="1"<?php if (LS_PRO_ALERT == 1) { ?> checked<?php } ?>> <?php echo $tl["general"]["g19"];?></label></div>
	<div class="radio"><label>
	  <input type="radio" name="showalert" value="0"<?php if (LS_PRO_ALERT == 0) { ?> checked<?php } ?>> <?php echo $tl["general"]["g18"];?>
	</label></div>
	</td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g192"];?></td>
	<td>
	<div class="row">
	<div class="col-md-6">
	<select name="alertfadein" class="form-control">
		<option value="swing"<?php if (LS_PRO_WAYIN == 'swing') { ?> selected="selected"<?php } ?>>swing</option>
		<option value="bounce"<?php if (LS_PRO_WAYIN == 'bounce') { ?> selected="selected"<?php } ?>>bounce</option>
		<option value="rollIn"<?php if (LS_PRO_WAYIN == 'rollIn') { ?> selected="selected"<?php } ?>>rollIn</option>
		<option value="lightSpeedIn"<?php if (LS_PRO_WAYIN == 'lightSpeedIn') { ?> selected="selected"<?php } ?>>lightSpeedIn</option>
	</select>
	</div>
	<div class="col-md-6">
	<select name="alertfadeout" class="form-control">
		<option value="hinge"<?php if (LS_PRO_WAYOUT == 'hinge') { ?> selected="selected"<?php } ?>>hinge</option>
		<option value="rollOut"<?php if (LS_PRO_WAYOUT == 'rollOut') { ?> selected="selected"<?php } ?>>rollOut</option>
		<option value="lightSpeedOut"<?php if (LS_PRO_WAYOUT == 'lightSpeedOut') { ?> selected="selected"<?php } ?>>lightSpeedOut</option>
	</select>
	</div>
	</div>
	</td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g109"];?></td>
	<td><input type="text" name="allowed_files" class="form-control" value="<?php echo LS_ALLOWED_FILES;?>" placeholder=".zip,.rar,.jpg,.jpeg,.png,.gif" /></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g128"];?></td>
	<td><input type="text" name="allowedo_files" class="form-control" value="<?php echo LS_ALLOWEDO_FILES;?>" placeholder=".zip,.rar,.jpg,.jpeg,.png,.gif" /></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g44"];?></td>
	<td>
	<div class="row">
	<div class="col-md-6">
	<input type="text" name="ls_avatwidth" class="form-control" value="<?php echo LS_USERAVATWIDTH;?>" placeholder="<?php echo $tl["general"]["g42"];?>" />
	</div>
	<div class="col-md-6">
	<input type="text" name="ls_avatheight" class="form-control" value="<?php echo LS_USERAVATHEIGHT;?>" placeholder="<?php echo $tl["general"]["g43"];?>" />
	</div>
	</div>
	</td>
</tr>
</table>

<table class="table table-striped">
<thead>
<tr>
<th colspan="2"><?php echo $tl["general"]["g97"];?></th>
</tr>
</thead>
<tr>
	<td><?php echo $tl["general"]["g95"];?> <a href="javascript:void(0)" class="rhino-help" data-content="<?php echo $tl["help"]["h3"];?>" data-original-title="<?php echo $tl["help"]["t"];?>"><span class="glyphicon glyphicon-question-sign"></span></a></td>
	<td><textarea name="ip_block" rows="5" class="form-control"><?php echo LS_IP_BLOCK;?></textarea></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g96"];?> <a href="javascript:void(0)" class="rhino-help" data-content="<?php echo $tl["help"]["h4"];?>" data-original-title="<?php echo $tl["help"]["t"];?>"><span class="glyphicon glyphicon-question-sign"></span></a></td>
	<td><textarea name="email_block" rows="5" class="form-control"><?php echo LS_EMAIL_BLOCK;?></textarea></td>
</tr>
</table>

<table class="table table-striped">
<thead>
<tr>
<th colspan="2"><?php echo $tl["general"]["g155"];?></th>
</tr>
</thead>
<tr>
	<td><a href="http://www.twilio.com">Twillio</a> <?php echo $tl["general"]["g157"];?> <a href="http://www.nexmo.com">Nexmo</a></td>
	<td><div class="radio"><label><input type="radio" name="ls_twilio_nexmo" value="1"<?php if (LS_TWILIO_NEXMO == 1) { ?> checked="checked"<?php } ?> /> Twilio</label></div>
	<div class="radio"><label><input type="radio" name="ls_twilio_nexmo" value="0"<?php if (LS_TWILIO_NEXMO == 0) { ?> checked="checked"<?php } ?> /> Nexmo</label></div></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g151"];?></td>
	<td><input  type="text" name="ls_tw_msg" class="form-control" value="<?php echo LS_TW_MSG;?>" maxlength="160" /></td>
</tr>

<tr>
	<td><?php echo $tl["general"]["g152"];?></td>
	<td><input  type="text" name="ls_tw_phone" class="form-control" value="<?php echo LS_TW_PHONE;?>" /></td>
</tr>

<tr>
	<td><?php echo $tl["general"]["g153"];?></td>
	<td><input  type="text" name="ls_tw_sid" class="form-control" value="<?php echo base64_decode(LS_TW_SID);?>" /></td>
</tr>

<tr>
	<td><?php echo $tl["general"]["g154"];?></td>
	<td><input  type="text" name="ls_tw_token" class="form-control" value="<?php echo base64_decode(LS_TW_TOKEN);?>" /></td>
</tr>

</table>

<table class="table table-striped">
<thead>
<tr>
<th colspan="2"><?php echo $tl["general"]["g212"];?></th>
</tr>
</thead>
<tr>
	<td><?php echo $tl["general"]["g212"];?></td>
	<td><div class="radio"><label><input type="radio" name="ls_smpt" value="0"<?php if (LS_SMTP_MAIL == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g204"];?></label></div>
	<div class="radio"><label><input type="radio" name="ls_smpt" value="1"<?php if (LS_SMTP_MAIL == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g205"];?></label></div></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g206"];?></td>
	<td><input type="text" class="form-control" name="ls_host" value="<?php echo LS_SMTPHOST;?>" /></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g207"];?></td>
	<td>
	<div class="form-group<?php if ($errors["e3"]) echo " has-error";?>">
		<input type="text" name="ls_port" class="form-control" value="<?php echo LS_SMTPPORT?>" placeholder="25" />
	</div></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g208"];?></td>
	<td><div class="radio"><label><input type="radio" name="ls_alive" value="1"<?php if (LS_SMTP_ALIVE == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g19"];?></label></div>
	<div class="radio"><label><input type="radio" name="ls_alive" value="0"<?php if (LS_SMTP_ALIVE == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g18"];?></label></div></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g209"];?></td>
	<td><div class="radio"><label><input type="radio" name="ls_auth" value="1"<?php if (LS_SMTP_AUTH == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g19"];?></label></div>
	<div class="radio"><label><input type="radio" name="ls_auth" value="0"<?php if (LS_SMTP_AUTH == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g18"];?></label></div></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g219"];?></td>
	<td>
	<input type="text" name="ls_prefix" class="form-control" value="<?php echo LS_SMTP_PREFIX;?>" placeholder="ssl/tls/true/false" />
	</td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g210"];?></td>
	<td><input type="text" name="ls_smtpusername" class="form-control" value="<?php if (LS_SMTPUSERNAME) echo base64_decode(LS_SMTPUSERNAME);?>" /></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g211"];?></td>
	<td><input type="password" name="ls_smtppassword" class="form-control" value="<?php if (LS_SMTPPASSWORD) echo base64_decode(LS_SMTPPASSWORD);?>" /></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g218"];?></td>
	<td><input type="submit" name="testMail" class="btn btn-success" id="sendTM" value="<?php echo $tl["general"]["g216"];?>" /> <span id="loader" style="display: none;"><img src="../../img/loader.gif" alt="loader" width="16" height="11" /></span></td>
</tr>
</table>

<button type="submit" name="save" class="btn btn-primary btn-block"><?php echo $tl["general"]["g38"];?></button>

</form>

<script type="text/javascript" src="js/page.ajax.js"></script>

<script type="text/javascript">
$(document).ready(function(){
	setChecker(<?php echo $lsuser->getVar("id");?>);
    setInterval("setChecker(<?php echo $lsuser->getVar("id");?>);", 10000);
	setTimer(<?php echo $lsuser->getVar("id");?>);
    setInterval("setTimer(<?php echo $lsuser->getVar("id");?>);", 120000);
    
    <!-- JavaScript to disable send button and show loading.gif image -->
    $("#sendTM").click(function() {
    	$("#loader").show();
    	$('#sendTM').val("<?php echo $tl["general"]["g67"];?>");
    	$('#sendTM').attr("disabled", "disabled");
    	$('.jak_form').submit();
    });
                
});

		ls.main_url = "<?php echo BASE_URL_ADMIN;?>";
		ls.main_lang = "<?php echo LS_LANG;?>";
		ls.ls_submit = "<?php echo $tl['general']['g69'];?>";
		ls.ls_submitwait = "<?php echo $tl['general']['g70'];?>";
</script>

<?php include_once APP_PATH.'operator/template/footer.php';?>