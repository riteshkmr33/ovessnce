<?php include_once APP_PATH.'operator/template/header.php';?>

<div class="hero-unit">
<h1><?php echo $tl["menu"]["m5"];?></h1>
</div>

<ul class="nav nav-tabs" id="ls-tabs">
  <li class="active"><a href="#settings"><?php echo $tl["menu"]["m5"];?></a></li>
  <li><a href="#buttons"><?php echo $tl["general"]["g71"];?></a></li>
</ul>
 
<div class="tab-content">
  <div class="tab-pane active" id="settings">


<?php if ($errors) { ?>
<div class="alert alert-error"><?php echo $errors["e"].$errors["e1"].$errors["e2"].$errors["e3"].$errors["e4"].$errors["e5"].$errors["e6"].$errors["e7"];?></div>
<?php } ?>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<table class="table table-striped">
<thead>
<tr>
<th colspan="2"><?php echo $tl["general"]["g15"];?></th>
</tr>
</thead>
<tr>
	<td><?php echo $tl["general"]["g16"];?></td>
	<td><input type="text" name="ls_title" class="input-xlarge" value="<?php echo LS_TITLE;?>" placeholder="<?php echo $tl["general"]["g16"];?>" /></td>
</tr>
<tr>
	<td><?php echo $tl["login"]["l5"];?> <a href="javascript:void(0)" class="rhino-help" data-content="<?php echo $tl["help"]["h"];?>" data-original-title="<?php echo $tl["help"]["t"];?>"><i class="icon-question-sign"></i></a></td>
	<td>
	<div class="control-group<?php if ($errors["e1"]) echo " error";?>">
		<input type="text" name="ls_email" class="input-xlarge" value="<?php echo LS_EMAIL;?>" placeholder="<?php echo $tl["login"]["l5"];?>" />
	</div>
	</td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g92"];?></td>
	<td><label class="radio"><input type="radio" name="ls_feedback" value="1"<?php if (LS_FEEDBACK == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g19"];?></label>
	<label class="radio"><input type="radio" name="ls_feedback" value="0"<?php if (LS_FEEDBACK == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g18"];?></label></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g96"];?></td>
	<td><label class="radio"><input type="radio" name="ls_captcha" value="1"<?php if (LS_CAPTCHA == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g19"];?></label>
	<label class="radio"><input type="radio" name="ls_captcha" value="0"<?php if (LS_CAPTCHA == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g18"];?></label></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g97"];?></td>
	<td><label class="radio"><input type="radio" name="ls_captchac" value="1"<?php if (LS_CAPTCHACHAT == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g19"];?></label>
	<label class="radio"><input type="radio" name="ls_captchac" value="0"<?php if (LS_CAPTCHACHAT == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g18"];?></label></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g98"];?></td>
	<td><label class="radio"><input type="radio" name="ls_smilies" value="1"<?php if (LS_SMILIES == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g19"];?></label>
	<label class="radio"><input type="radio" name="ls_smilies" value="0"<?php if (LS_SMILIES == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g18"];?></label></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g20"].'/'.$tl["general"]["g21"];?></td>
	<td><label class="radio"><input type="radio" name="ls_shttp" value="0"<?php if (LS_SITEHTTPS == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g20"];?></label>
	<label class="radio"><input type="radio" name="ls_shttp" value="1"<?php if (LS_SITEHTTPS == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g21"];?></label></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g22"];?></td>
	<td><select name="ls_lang" size="1">
	<?php if (isset($lang_files) && is_array($lang_files)) foreach($lang_files as $lf) { ?><option value="<?php echo $lf;?>"<?php if (LS_LANG == $lf) { ?> selected="selected"<?php } ?>><?php echo ucwords($lf);?></option><?php } ?>
	</select></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g23"];?></td>
	<td>
	<div class="control-group<?php if ($errors["e2"]) echo " error";?>">
		<input type="text" name="ls_date" value="<?php echo LS_DATEFORMAT;?>" />
	</div>
	</td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g24"];?></td>
	<td>
	<div class="control-group<?php if ($errors["e3"]) echo " error";?>">
		<input type="text" name="ls_time" value="<?php echo LS_TIMEFORMAT?>" />
	</div>
	</td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g25"];?></td>
	<td><select name="ls_timezone_server" size="1" class="input_field">
	<?php include_once "timezoneserver.php";?>
	</select></td>
</tr>

<tr>
	<td><?php echo $tl["general"]["g44"];?></td>
	<td><input type="text" name="ls_avatwidth" value="<?php echo LS_USERAVATWIDTH;?>" placeholder="<?php echo $tl["general"]["g42"];?>" /> <input type="text" name="ls_avatheight" value="<?php echo LS_USERAVATHEIGHT;?>" placeholder="<?php echo $tl["general"]["g43"];?>" /></td>
</tr>
</table>
<table class="table table-striped">
<thead>
<tr>
<th colspan="2"><?php echo $tl["general"]["g32"];?></th>
</tr>
</thead>
<tr>
	<td><?php echo $tl["general"]["g33"];?></td>
	<td><textarea name="offline_message" rows="5" class="input-xxlarge"><?php echo LS_OFFLINE_MESSAGE;?></textarea></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g34"];?></td>
	<td><textarea name="login_message" rows="5" class="input-xxlarge"><?php echo LS_LOGIN_MESSAGE;?></textarea></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g35"];?></td>
	<td><textarea name="welcome_message" rows="5" class="input-xxlarge"><?php echo LS_WELCOME_MESSAGE;?></textarea></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g36"];?></td>
	<td><textarea name="leave_message" rows="5" class="input-xxlarge"><?php echo LS_LEAVE_MESSAGE;?></textarea></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g37"];?></td>
	<td><textarea name="thankyou_message" rows="5" class="input-xxlarge"><?php echo LS_THANKYOU_MESSAGE;?></textarea></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g80"];?></td>
	<td><textarea name="feedback_message" rows="5" class="input-xxlarge"><?php echo LS_FEEDBACK_MESSAGE;?></textarea></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g82"];?></td>
	<td><textarea name="thankyou_feedback" rows="5" class="input-xxlarge"><?php echo LS_THANKYOU_FEEDBACK;?></textarea></td>
</tr>
</table>

<div class="form-actions">
<button type="submit" name="save" class="btn btn-primary pull-right"><?php echo $tl["general"]["g38"];?></button>
</div>

</form>

</div>

<div class="tab-pane" id="buttons">

<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

<table class="table table-striped">
<thead>
<tr>
<th colspan="2"><?php echo $tl["general"]["g71"];?> | <?php echo $tl["general"]["host"];?><input type="checkbox" name="hostname" id="hostname" value="off"<?php if($_SESSION['show_host'] == 'off') echo ' checked="checked"';?> /></th>
</tr>
</thead>
<?php if (isset($get_buttons) && is_array($get_buttons)) { 

	if($_SESSION['show_host'] == 'off') { 
		$b_host = parse_url(BASE_URL_ORIG, PHP_URL_PATH);
	} else {
		$b_host = BASE_URL_ORIG;
	}

	foreach($get_buttons as $v) {

$buttoncode = htmlentities('<!-- live support rhino button --><a href="'.$b_host.'index.php?p=start&amp;lang='.LS_LANG.'" target="_blank" onclick="if(navigator.userAgent.toLowerCase().indexOf(\'opera\') != -1 && window.event.preventDefault) window.event.preventDefault();this.newWindow = window.open(\''.$b_host.'index.php?p=start&amp;lang='.LS_LANG.'\', \'lsr\', \'toolbar=0,scrollbars=1,location=0,status=1,menubar=0,width=540,height=550,resizable=1\');this.newWindow.focus();this.newWindow.opener=window;return false;"><img src="'.$b_host.'index.php?p=b&amp;i='.$v['name'].'&amp;lang='.LS_LANG.'" width="'.$v['width'].'" height="'.$v['height'].'" alt="" /></a><!-- end live support rhino button -->');
?>

<tr>
	<td class="go"><img src="<?php echo BASE_URL_ORIG;?>img/buttons/<?php echo LS_LANG;?>/<?php echo $v['name'];?>_on.png" width="<?php echo $v['width'];?>" height="<?php echo $v['height'];?>" alt=""/></td>
	<td><textarea cols="100" rows="5" class="input-xxlarge" readonly="readonly"><?php echo $buttoncode;?></textarea></td>
</tr>
<?php } } ?>
</table>

<input type="hidden" name="button_c" value="1" />

</form>

</div>

</div>

<script type="text/javascript" src="js/page.ajax.js"></script>

<script type="text/javascript">
$(document).ready(function(){

	$('#ls-tabs a').click(function (e) {
	  e.preventDefault();
	  $(this).tab('show');
	})
	
	jrc_setChecker(<?php echo $lsuser->getVar("id");?>);
    setInterval("jrc_setChecker(<?php echo $lsuser->getVar("id");?>);", 10000);
	jrc_setTimer(<?php echo $lsuser->getVar("id");?>);
    setInterval("jrc_setTimer(<?php echo $lsuser->getVar("id");?>);", 120000);
                
	$("#hostname").change(function() {
	    $(this).closest("form").submit();
	});
	
});

		ls.main_url = "<?php echo BASE_URL_ADMIN;?>";
		ls.main_lang = "<?php echo LS_LANG;?>";
		ls.ls_submit = "<?php echo $tl['general']['g69'];?>";
		ls.ls_submitwait = "<?php echo $tl['general']['g70'];?>";
</script>

<?php include_once APP_PATH.'operator/template/footer.php';?>