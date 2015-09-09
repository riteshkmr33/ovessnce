<?php include_once APP_PATH.'operator/template/header.php';?>

<div class="hero-unit">
<h1><?php echo $tl["menu"]["m10"];?></h1>
</div>

<?php if ($errors) { ?>
<div class="alert alert-error"><?php echo $errors["e"].$errors["e1"];?></div>
<?php } ?>

<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data">
<table class="table table-striped">
<thead>
<tr>
<th colspan="2"><?php echo $tl["general"]["g47"];?></th>
</tr>
</thead>
<tr>
<td colspan="2"><img src="../<?php echo $LS_FORM_DATA['path'];?>" alt="img" /></td>
<tr>
	<td><?php echo $tl["general"]["g53"];?></td>
	<td>
	<div class="control-group<?php if ($errors["e"]) echo " error";?>">
		<input type="text" name="name" class="input-xlarge" value="<?php echo $LS_FORM_DATA["name"];?>" />
	</div>
	</td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g52"];?></td>
	<td><textarea name="description" rows="5" class="input-xlarge"><?php echo $LS_FORM_DATA["description"];?></textarea></td>
</tr>
</table>

<div class="form-actions">
<a href="index.php?p=files" class="btn"><?php echo $tl["general"]["g99"];?></a>
<button type="submit" name="save" class="btn btn-primary pull-right"><?php echo $tl["general"]["g38"];?></button>
</div>

</form>
		
<?php include_once APP_PATH.'operator/template/footer.php';?>