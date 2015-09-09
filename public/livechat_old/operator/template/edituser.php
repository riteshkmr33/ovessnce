<?php include_once APP_PATH.'operator/template/header.php';?>

<div class="hero-unit">
<h1><?php echo $tl["menu"]["m11"];?></h1>
</div>

<?php if ($errors) { ?>
<div class="alert alert-error"><?php echo $errors["e"].$errors["e1"].$errors["e2"].$errors["e3"].$errors["e4"].$errors["e5"].$errors["e6"];?></div>
<?php } ?>
<form class="ls_form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data">

<table class="table table-striped">
<thead>
<tr>
<th colspan="2"><?php echo $tl["general"]["g40"];?></th>
</tr>
</thead>
<tr>
	<td><?php echo $tl["user"]["u"];?></td>
	<td>
	<div class="control-group<?php if ($errors["e1"]) echo " error";?>">
		<input type="text" name="ls_name" class="input-xlarge" value="<?php echo $LS_FORM_DATA["name"];?>" />
	</div>
	</td>
</tr>
<tr>
	<td><?php echo $tl["user"]["u1"];?></td>
	<td>
	<div class="control-group<?php if ($errors["e2"]) echo " error";?>">
		<input type="text" name="ls_email" class="input-xlarge" value="<?php echo $LS_FORM_DATA["email"];?>" />
	</div>
	</td>
</tr>
<tr>
	<td><?php echo $tl["user"]["u2"];?></td>
	<td>
	<div class="control-group<?php if ($errors["e3"] || $errors["e4"]) echo " error";?>">
		<input  type="text" name="ls_username" value="<?php echo $LS_FORM_DATA["username"];?>" /><input type="hidden" name="ls_username_old" value="<?php echo $LS_FORM_DATA["username"];?>" />
	</div>
	</td>
</tr>
<?php if ($LS_SPECIALACCESS) { ?>
<tr>
	<td><?php echo $tl["user"]["u3"];?></td>
	<td><label class="radio"><input type="radio" name="ls_access" value="1"<?php if ($LS_FORM_DATA["access"] == 1) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g19"];?></label>
	<label class="radio"><input type="radio" name="ls_access" value="0"<?php if ($LS_FORM_DATA["access"] == 0) { ?> checked="checked"<?php } ?> /> <?php echo $tl["general"]["g18"];?></label></td>
</tr>
<?php } ?>
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
<th colspan="2"><?php echo $tl["general"]["g39"];?></th>
</tr>
</thead>
<tr>
	<td><?php echo $tl["user"]["u4"];?></td>
	<td>
	<div class="control-group<?php if ($errors["e5"] || $errors["e6"]) echo " error";?>">
		<input type="text" name="ls_password" value="" />
	</div>
	</td>
</tr>
<tr>
	<td><?php echo $tl["user"]["u5"];?></td>
	<td>
	<div class="control-group<?php if ($errors["e5"] || $errors["e6"]) echo " error";?>">
		<input type="text" name="ls_confirm_password" value="" />
	</div>
	</td>
</tr>
</table>

<div class="form-actions">
<button type="submit" name="save" class="btn btn-primary pull-right"><?php echo $tl["general"]["g38"];?></button>
</div>

</form>
		
<?php include_once APP_PATH.'operator/template/footer.php';?>