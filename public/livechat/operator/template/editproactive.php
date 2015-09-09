<?php include_once APP_PATH.'operator/template/header.php';?>

<h3><?php echo $tl["menu"]["m18"];?></h3>

<?php if ($errors) { ?>
<div class="alert alert-danger"><?php echo $errors["e"].$errors["e1"];?></div>
<?php } ?>
<form role="form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data">
<table class="table table-striped">
<thead>
<tr>
<th colspan="2"><?php echo $tl["general"]["g47"];?></th>
</tr>
</thead>
<tr>
	<td><?php echo $tl["general"]["g167"];?></td>
	<td>
	<div class="form-group<?php if ($errors["e"]) echo " has-error";?>">
		<input type="text" name="path" class="form-control" value="<?php echo $LS_FORM_DATA["path"];?>" />
	</div>
	</td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g191"];?></td>
	<td><div class="radio"><label><input type="radio" name="showalert" value="1"<?php if ($LS_FORM_DATA["showalert"] == 1) { ?> checked<?php } ?>> <?php echo $tl["general"]["g19"];?></label></div>
	<div class="radio"><label><input type="radio" name="showalert" value="0"<?php if ($LS_FORM_DATA["showalert"] == 0) { ?> checked<?php } ?>> <?php echo $tl["general"]["g18"];?></label></div>
	</td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g192"];?></td>
	<td>
	<div class="row">
	<div class="col-xs-6">
	<select name="alertfadein" class="form-control">
		<option value="swing"<?php if ($LS_FORM_DATA["wayin"] == 'swing') { ?> selected="selected"<?php } ?>>swing</option>
		<option value="bounce"<?php if ($LS_FORM_DATA["wayin"] == 'bounce') { ?> selected="selected"<?php } ?>>bounce</option>
		<option value="rollIn"<?php if ($LS_FORM_DATA["wayin"] == 'rollIn') { ?> selected="selected"<?php } ?>>rollIn</option>
		<option value="lightSpeedIn"<?php if ($LS_FORM_DATA["wayin"] == 'lightSpeedIn') { ?> selected="selected"<?php } ?>>lightSpeedIn</option>
	</select>
	</div>
	<div class="col-xs-6">
	<select name="alertfadeout" class="form-control">
		<option value="hinge"<?php if ($LS_FORM_DATA["wayout"] == 'hinge') { ?> selected="selected"<?php } ?>>hinge</option>
		<option value="rollOut"<?php if ($LS_FORM_DATA["wayout"] == 'rollOut') { ?> selected="selected"<?php } ?>>rollOut</option>
		<option value="lightSpeedOut"<?php if ($LS_FORM_DATA["wayout"] == 'lightSpeedOut') { ?> selected="selected"<?php } ?>>lightSpeedOut</option>
	</select>
	</div>
	</div>
	</td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g194"];?></td>
	<td>
	<select name="onsite" class="form-control">
	<option value="2"<?php if ($LS_FORM_DATA["timeonsite"] == 2) { ?> selected="selected"<?php } ?>>2 <?php echo $tl["general"]["g196"];?></option>
	<option value="5"<?php if ($LS_FORM_DATA["timeonsite"] == 5) { ?> selected="selected"<?php } ?>>5 <?php echo $tl["general"]["g196"];?></option>
	<option value="15"<?php if ($LS_FORM_DATA["timeonsite"] == 15) { ?> selected="selected"<?php } ?>>15 <?php echo $tl["general"]["g196"];?></option>
	<option value="30"<?php if ($LS_FORM_DATA["timeonsite"] == 30) { ?> selected="selected"<?php } ?>>30 <?php echo $tl["general"]["g196"];?></option>
	<option value="60"<?php if ($LS_FORM_DATA["timeonsite"] == 60) { ?> selected="selected"<?php } ?>>1 <?php echo $tl["general"]["g197"];?></option>
	<option value="120"<?php if ($LS_FORM_DATA["timeonsite"] == 120) { ?> selected="selected"<?php } ?>>2 <?php echo $tl["general"]["g197"];?></option>
	<option value="180"<?php if ($LS_FORM_DATA["timeonsite"] == 180) { ?> selected="selected"<?php } ?>>3 <?php echo $tl["general"]["g197"];?></option>
	<option value="240"<?php if ($LS_FORM_DATA["timeonsite"] == 240) { ?> selected="selected"<?php } ?>>4 <?php echo $tl["general"]["g197"];?></option>
	<option value="300"<?php if ($LS_FORM_DATA["timeonsite"] == 300) { ?> selected="selected"<?php } ?>>5 <?php echo $tl["general"]["g197"];?></option>
	</select>
	</td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g195"];?></td>
	<td>
	<select name="visited" class="form-control">
	<?php for ($i = 1; $i <= 20; $i++) { ?>
	<option value="<?php echo $i ?>"<?php if ($LS_FORM_DATA["visitedsites"] == $i) { ?> selected="selected"<?php } ?>><?php echo $i; ?> <?php echo $tl["general"]["g198"];?></option>
	<?php } ?>
	</select>
	</td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g146"];?></td>
	<td>
	<div class="form-group<?php if ($errors["e1"]) echo " has-error";?>">
		<textarea name="message" rows="5" class="form-control"><?php echo $LS_FORM_DATA["message"];?></textarea>
	</div>
	</td>
</tr>
</table>

<a href="index.php?p=proactive" class="btn btn-default"><?php echo $tl["general"]["g103"];?></a>
<button type="submit" name="save" class="btn btn-primary pull-right"><?php echo $tl["general"]["g38"];?></button>

</form>
		
<?php include_once APP_PATH.'operator/template/footer.php';?>