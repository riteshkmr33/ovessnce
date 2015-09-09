<?php include_once APP_PATH.'operator/template/header.php';?>

<h3><?php echo $tl["menu"]["m11"];?></h3>

<?php if ($errors) { ?>
<div class="alert alert-danger"><?php echo $errors["e"].$errors["e1"].$errors["e2"].$errors["e3"].$errors["e4"].$errors["e5"].$errors["e6"];?></div>
<?php } ?>
<form class="ls_form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data">
<?php if ($LS_SPECIALACCESS) { ?>
<table class="table table-striped">
<thead>
<tr>
<th><?php echo $tl["menu"]["m9"];?> <a href="javascript:void(0)" class="rhino-help" data-content="<?php echo $tl["help"]["h6"];?>" data-original-title="<?php echo $tl["help"]["t"];?>"><span class="glyphicon glyphicon-question-sign"></span></a></th>
</tr>
</thead>
<tr>
	<td>
	
	<select name="jak_depid[]" multiple="multiple" class="form-control">
	
	<option value="0"<?php if ($LS_FORM_DATA["departments"] == 0) { ?> selected="selected"<?php } ?>><?php echo $tl["general"]["g105"];?></option>
	<?php if (isset($LS_DEPARTMENTS) && is_array($LS_DEPARTMENTS)) foreach($LS_DEPARTMENTS as $z) { ?>
	
	<option value="<?php echo $z["id"];?>"<?php if (in_array($z["id"], explode(',', $LS_FORM_DATA["departments"]))) { ?> selected="selected"<?php } ?>><?php echo $z["title"];?></option>
	
	<?php } ?>
	
	</select>
	
	</td>
</tr>
</table>
<?php } ?>
<table class="table table-striped">
<thead>
<tr>
<th><?php echo $tl["user"]["u12"];?></th>
</tr>
</thead>
<tr>
	<td><input type="text" name="ls_inv" class="form-control" value="<?php echo $LS_FORM_DATA["invitationmsg"]; ?>" class="form-control" /></td>
</tr>
</table>
<table class="table table-striped">
<thead>
<tr>
<th colspan="2"><?php echo $tl["general"]["g40"];?></th>
</tr>
</thead>
<tr>
	<td><?php echo $tl["user"]["u"];?></td>
	<td>
	<div class="form-group<?php if ($errors["e1"]) echo " has-error";?>">
		<input type="text" name="ls_name" class="form-control" value="<?php echo $LS_FORM_DATA["name"];?>" />
	</div>
	</td>
</tr>
<tr>
	<td><?php echo $tl["user"]["u1"];?></td>
	<td>
	<div class="form-group<?php if ($errors["e2"]) echo " has-error";?>">
		<input type="text" name="ls_email" class="form-control" value="<?php echo $LS_FORM_DATA["email"];?>" />
	</div>
	</td>
</tr>
<tr>
	<td><?php echo $tl["user"]["u2"];?></td>
	<td>
	<div class="form-group<?php if ($errors["e3"] || $errors["e4"]) echo " has-error";?>">
		<input  type="text" name="ls_username" class="form-control" value="<?php echo $LS_FORM_DATA["username"];?>" /><input type="hidden" name="ls_username_old" value="<?php echo $LS_FORM_DATA["username"];?>" />
	</div>
	</td>
</tr>
<?php if ($LS_SPECIALACCESS) { ?>
<tr>
	<td><?php echo $tl["user"]["u3"];?></td>
	<td><div class="radio"><label><input type="radio" name="ls_access" value="1"<?php if ($LS_FORM_DATA["access"] == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g19"];?></label></div>
	<div class="radio"><label><input type="radio" name="ls_access" value="0"<?php if ($LS_FORM_DATA["access"] == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g18"];?></label></div></td>
</tr>
<tr>
	<td><?php echo $tl["user"]["u6"];?></td>
	<td><div class="radio"><label><input type="radio" name="ls_responses" value="1"<?php if ($LS_FORM_DATA["responses"] == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g19"];?></label></div>
	<div class="radio"><label><input type="radio" name="ls_responses" value="0"<?php if ($LS_FORM_DATA["responses"] == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g18"];?></label></div></td>
</tr>
<tr>
	<td><?php echo $tl["user"]["u7"];?></td>
	<td><div class="radio"><label><input type="radio" name="ls_files" value="1"<?php if ($LS_FORM_DATA["files"] == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g19"];?></label></div>
	<div class="radio"><label><input type="radio" name="ls_files" value="0"<?php if ($LS_FORM_DATA["files"] == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g18"];?></label></div></td>
</tr>
<tr>
	<td><?php echo $tl["user"]["u13"];?></td>
	<td><div class="radio"><label><input type="radio" name="ls_chat" value="1"<?php if ($LS_FORM_DATA["operatorchat"] == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g19"];?></label></div>
	<div class="radio"><label><input type="radio" name="ls_chat" value="0"<?php if ($LS_FORM_DATA["operatorchat"] == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g18"];?></label></div></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g137"];?></td>
	<td><div class="radio"><label><input type="radio" name="ls_chatlist" value="1"<?php if ($LS_FORM_DATA["operatorlist"] == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g19"];?></label></div>
	<div class="radio"><label><input type="radio" name="ls_chatlist" value="0"<?php if ($LS_FORM_DATA["operatorlist"] == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g18"];?></label></div></td>
</tr>
<?php } ?>
<tr>
	<td><?php echo $tl["general"]["g2"];?></td>
	<td><div class="radio"><label><input type="radio" name="ls_sound" value="0"<?php if ($LS_FORM_DATA["sound"] == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g19"];?></label></div>
	<div class="radio"><label><input type="radio" name="ls_sound" value="1"<?php if ($LS_FORM_DATA["sound"] == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g18"];?></label></div></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g200"];?></td>
	<td>
	<select name="ls_ringing" class="form-control">
		<?php for ($i = 3; $i < 100; $i++) {
			if ($i == $LS_FORM_DATA["ringing"]) {
				echo '<option value="'.$i.'" selected>'.$i.'</option>';
			} else {
				echo '<option value="'.$i.'">'.$i.'</option>';
			}
		} ?>
	</select>
	</td>
</tr>
<tr>
	<td><?php echo $tl["user"]["u25"];?> <a href="javascript:void(0)" class="rhino-help" data-content="<?php echo $tl["help"]["h12"];?>" data-original-title="<?php echo $tl["help"]["t"];?>"><span class="glyphicon glyphicon-question-sign"></span></a> <a class="btn btn-mini btn-success" href="javascript:void(0)" onclick="dNotifyPermission()"><?php echo $tl["user"]["u26"];?></a></td>
	<td><div class="radio"><label><input type="radio" name="ls_dnotify" value="1"<?php if ($LS_FORM_DATA["dnotify"] == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g19"];?></label></div>
	<div class="radio"><label><input type="radio" name="ls_dnotify" value="0"<?php if ($LS_FORM_DATA["dnotify"] == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g18"];?></label></div></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g22"];?></td>
	<td><select name="ls_lang" class="form-control">
	<option value=""><?php echo $tl["user"]["u11"];?></option>
	<?php if (isset($lang_files) && is_array($lang_files)) foreach($lang_files as $lf) { ?><option value="<?php echo $lf;?>"<?php if ($LS_FORM_DATA["language"] == $lf) { ?> selected="selected"<?php } ?>><?php echo ucwords($lf);?></option><?php } ?>
	</select></td>
</tr>
<tr>
	<td><?php echo $tl["user"]["u10"];?></td>
	<td>
	<input type="file" name="uploadpp" accept="image/*" />
	<p><img src="../<?php echo LS_FILES_DIRECTORY.$LS_FORM_DATA["picture"];?>" alt="avatar" /></p>
	</td>
</tr>
<tr>
	<td><?php echo $tl["user"]["u46"];?></td>
	<td><input type="checkbox" name="ls_delete_avatar" /></td>
</tr>
</table>

<table class="table table-striped">
<thead>
<tr>
<th colspan="2"><?php echo $tl["general"]["g155"];?></th>
</tr>
</thead>

<tr>
	<td><?php echo $tl["general"]["g214"];?></td>
	<td><div class="radio"><label><input type="radio" name="ls_emailnot" value="1" <?php if ($LS_FORM_DATA["emailnot"] == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g19"];?></label></div>
	<div class="radio"><label><input type="radio" name="ls_emailnot" value="0" <?php if ($LS_FORM_DATA["emailnot"] == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g18"];?></label></div></td>
</tr>

<tr>
	<td><?php echo $tl["user"]["u14"];?></td>
	<td><input  type="text" name="ls_phone" class="form-control" value="<?php echo $LS_FORM_DATA["phonenumber"];?>" /></td>
</tr>

<tr>
	<td><?php echo $tl["user"]["u15"];?></td>
	<td>
	
	<label class="checkbox-inline"><input type="checkbox" name="ls_days[]" value="Mon"<?php if (in_array("Mon", explode(',', $LS_FORM_DATA["tw_days"]))) { ?> checked="checked"<?php } ?> /> <?php echo $tl["user"]["u18"];?></label>
	<label class="checkbox-inline"><input type="checkbox" name="ls_days[]" value="Tue"<?php if (in_array("Tue", explode(',', $LS_FORM_DATA["tw_days"]))) { ?> checked="checked"<?php } ?> /> <?php echo $tl["user"]["u19"];?></label>
	<label class="checkbox-inline"><input type="checkbox" name="ls_days[]" value="Wed"<?php if (in_array("Wed", explode(',', $LS_FORM_DATA["tw_days"]))) { ?> checked="checked"<?php } ?> /> <?php echo $tl["user"]["u20"];?></label>
	<label class="checkbox-inline"><input type="checkbox" name="ls_days[]" value="Thu"<?php if (in_array("Thu", explode(',', $LS_FORM_DATA["tw_days"]))) { ?> checked="checked"<?php } ?> /> <?php echo $tl["user"]["u21"];?></label>
	<label class="checkbox-inline"><input type="checkbox" name="ls_days[]" value="Fri"<?php if (in_array("Fri", explode(',', $LS_FORM_DATA["tw_days"]))) { ?> checked="checked"<?php } ?> /> <?php echo $tl["user"]["u22"];?></label>
	<label class="checkbox-inline"><input type="checkbox" name="ls_days[]" value="Sat"<?php if (in_array("Sat", explode(',', $LS_FORM_DATA["tw_days"]))) { ?> checked="checked"<?php } ?> /> <?php echo $tl["user"]["u23"];?></label>
	<label class="checkbox-inline"><input type="checkbox" name="ls_days[]" value="Sun"<?php if (in_array("Sun", explode(',', $LS_FORM_DATA["tw_days"]))) { ?> checked="checked"<?php } ?> /> <?php echo $tl["user"]["u24"];?></label>
	
	</td>
</tr>

<tr>
	<td><?php echo $tl["user"]["u16"];?> <a href="javascript:void(0)" class="rhino-help" data-content="<?php echo $tl["help"]["h11"];?>" data-original-title="<?php echo $tl["help"]["t"];?>"><span class="glyphicon glyphicon-question-sign"></span></a></td>
	<td>
	<div class="row">
	<div class="col-md-6">
	<select name="jak_timefrom" class="form-control">
	<?php for ($i = 0; $i <= 23; $i++) { ?>
	<option value="<?php echo $i ?>"<?php if ($LS_FORM_DATA["tw_time_from"] && date('H', strtotime($LS_FORM_DATA["tw_time_from"])) == $i) { ?> selected="selected"<?php } ?>><?php echo str_pad($i, 2, "0", STR_PAD_LEFT); ?></option>
	<?php } ?>
	</select>
	</div>
	<div class="col-md-6">
	<select name="jak_timefromm" class="form-control">
	<?php for ($i = 0; $i <= 59; $i++) { ?>
	<option value="<?php echo $i ?>"<?php if ($LS_FORM_DATA["tw_time_from"] && date('i', strtotime($LS_FORM_DATA["tw_time_from"])) == $i) { ?> selected="selected"<?php } ?>><?php echo str_pad($i, 2, "0", STR_PAD_LEFT); ?></option>
	<?php } ?>
	</select>
	</div>
	</div>
	</td>
</tr>
<tr>
	<td><?php echo $tl["user"]["u17"];?> <a href="javascript:void(0)" class="rhino-help" data-content="<?php echo $tl["help"]["h11"];?>" data-original-title="<?php echo $tl["help"]["t"];?>"><span class="glyphicon glyphicon-question-sign"></span></a></td>
	<td>
	<div class="row">
	<div class="col-md-6">
	<select name="jak_timeto" class="form-control">
	<?php for ($i = 0; $i <= 23; $i++) { ?>
	<option value="<?php echo $i ?>"<?php if ($LS_FORM_DATA["tw_time_to"] && date('H', strtotime($LS_FORM_DATA["tw_time_to"])) == $i) { ?> selected="selected"<?php } ?>><?php echo str_pad($i, 2, "0", STR_PAD_LEFT); ?></option>
	<?php } ?>
	</select>
	</div>
	<div class="col-md-6">
	<select name="jak_timetom" class="form-control">
	<?php for ($i = 0; $i <= 59; $i++) { ?>
	<option value="<?php echo $i ?>"<?php if ($LS_FORM_DATA["tw_time_to"] && date('i', strtotime($LS_FORM_DATA["tw_time_to"])) == $i) { ?> selected="selected"<?php } ?>><?php echo str_pad($i, 2, "0", STR_PAD_LEFT); ?></option>
	<?php } ?>
	</select>
	</div>
	</div>
	</td>
</tr>
</table>

<?php if ($LS_SPECIALACCESS) { ?>
<table class="table table-striped">
<thead>
<tr>
<th colspan="2"><?php echo $tl["user"]["u28"];?></th>
</tr>
</thead>
<tr>
	<td><?php echo $tl["user"]["u29"];?></td>
	<td>
	
	<label class="checkbox-inline"><input type="checkbox" name="ls_roles[]" value="leads"<?php if (in_array("leads", explode(',', $LS_FORM_DATA["permissions"]))) { ?> checked="checked"<?php } ?> /> <?php echo $tl["user"]["u30"];?></label>
	<label class="checkbox-inline"><input type="checkbox" name="ls_roles[]" value="leads_all"<?php if (in_array("leads_all", explode(',', $LS_FORM_DATA["permissions"]))) { ?> checked="checked"<?php } ?> /> <?php echo $tl["user"]["u30"].' ('.$tl["general"]["g105"].')';?></label>
	<label class="checkbox-inline"><input type="checkbox" name="ls_roles[]" value="ochat"<?php if (in_array("ochat", explode(',', $LS_FORM_DATA["permissions"]))) { ?> checked="checked"<?php } ?> /> <?php echo $tl["user"]["u31"];?></label>
	<label class="checkbox-inline"><input type="checkbox" name="ls_roles[]" value="ochat_all"<?php if (in_array("ochat_all", explode(',', $LS_FORM_DATA["permissions"]))) { ?> checked="checked"<?php } ?> /> <?php echo $tl["user"]["u31"].' ('.$tl["general"]["g105"].')';?></label>
	<label class="checkbox-inline"><input type="checkbox" name="ls_roles[]" value="statistic"<?php if (in_array("statistic", explode(',', $LS_FORM_DATA["permissions"]))) { ?> checked="checked"<?php } ?> /> <?php echo $tl["user"]["u32"];?></label>
	<label class="checkbox-inline"><input type="checkbox" name="ls_roles[]" value="statistic_all"<?php if (in_array("statistic_all", explode(',', $LS_FORM_DATA["permissions"]))) { ?> checked="checked"<?php } ?> /> <?php echo $tl["user"]["u32"].' ('.$tl["general"]["g105"].')';?></label><br />
	<label class="checkbox-inline"><input type="checkbox" name="ls_roles[]" value="files"<?php if (in_array("files", explode(',', $LS_FORM_DATA["permissions"]))) { ?> checked="checked"<?php } ?> /> <?php echo $tl["user"]["u33"];?></label>
	<label class="checkbox-inline"><input type="checkbox" name="ls_roles[]" value="proactive"<?php if (in_array("proactive", explode(',', $LS_FORM_DATA["permissions"]))) { ?> checked="checked"<?php } ?> /> <?php echo $tl["user"]["u34"];?></label>
	<label class="checkbox-inline"><input type="checkbox" name="ls_roles[]" value="responses"<?php if (in_array("responses", explode(',', $LS_FORM_DATA["permissions"]))) { ?> checked="checked"<?php } ?> /> <?php echo $tl["user"]["u35"];?></label>
	<label class="checkbox-inline"><input type="checkbox" name="ls_roles[]" value="departments"<?php if (in_array("departments", explode(',', $LS_FORM_DATA["permissions"]))) { ?> checked="checked"<?php } ?> /> <?php echo $tl["user"]["u36"];?></label>
	<label class="checkbox-inline"><input type="checkbox" name="ls_roles[]" value="settings"<?php if (in_array("settings", explode(',', $LS_FORM_DATA["permissions"]))) { ?> checked="checked"<?php } ?> /> <?php echo $tl["user"]["u37"];?></label>
	<label class="checkbox-inline"><input type="checkbox" name="ls_roles[]" value="maintenance"<?php if (in_array("maintenance", explode(',', $LS_FORM_DATA["permissions"]))) { ?> checked="checked"<?php } ?> /> <?php echo $tl["user"]["u38"];?></label>
	<label class="checkbox-inline"><input type="checkbox" name="ls_roles[]" value="logs"<?php if (in_array("logs", explode(',', $LS_FORM_DATA["permissions"]))) { ?> checked="checked"<?php } ?> /> <?php echo $tl["user"]["u39"];?></label>
	
	</td>
</tr>
</table>
<?php } ?>

<table class="table table-striped">
<thead>
<tr>
<th colspan="2"><?php echo $tl["general"]["g39"];?></th>
</tr>
</thead>
<tr>
	<td><?php echo $tl["user"]["u4"];?></td>
	<td>
	<div class="form-group<?php if ($errors["e5"] || $errors["e6"]) echo " has-error";?>">
		<input type="text" name="ls_password" class="form-control" id="pass" value="" />
	</div>
	</td>
</tr>
<tr>
	<td><?php echo $tl["user"]["u5"];?></td>
	<td>
	<div class="form-group<?php if ($errors["e5"] || $errors["e6"]) echo " has-error";?>">
		<input type="text" name="ls_confirm_password" class="form-control" value="" />
	</div>
	</td>
</tr>
<tr>
<td colspan="2">
	<div class="progress progress-striped active">
		<div id="jak_pstrength" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
	</div>
</td>
</tr>
</table>

<button type="submit" name="save" class="btn btn-primary btn-block"><?php echo $tl["general"]["g38"];?></button>

</form>

<script type="text/javascript" src="js/page.ajax.js"></script>

<!-- JavaScript for select all -->
<script type="text/javascript">
		$(document).ready(function() {
		
			setChecker(<?php echo $lsuser->getVar("id");?>);
		        setInterval("setChecker(<?php echo $lsuser->getVar("id");?>);", 10000);
			setTimer(<?php echo $lsuser->getVar("id");?>);
		        setInterval("setTimer(<?php echo $lsuser->getVar("id");?>);", 120000);
		        
			jQuery(document).ready(function(){
				jQuery("#pass").keyup(function() {
				  passwordStrength(jQuery(this).val());
				});
			});
						
		});
		
		ls.main_url = "<?php echo BASE_URL_ADMIN;?>";
		ls.main_lang = "<?php echo LS_LANG;?>";
		ls.ls_submit = "<?php echo $tl['general']['g69'];?>";
		ls.ls_submitwait = "<?php echo $tl['general']['g70'];?>";
</script>
		
<?php include_once APP_PATH.'operator/template/footer.php';?>