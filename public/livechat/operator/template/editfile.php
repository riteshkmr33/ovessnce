<?php include_once APP_PATH.'operator/template/header.php';?>

<h3><?php echo $tl["menu"]["m15"];?></h3>

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
<td colspan="2"><img src="../<?php echo $LS_FORM_DATA['path'];?>" alt="img" /></td>
<tr>
	<td><?php echo $tl["general"]["g53"];?></td>
	<td>
	<div class="form-group<?php if ($errors["e"]) echo " has-error";?>">
		<input type="text" name="name" class="form-control" value="<?php echo $LS_FORM_DATA["name"];?>" />
	</div>
	</td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g52"];?></td>
	<td><textarea name="description" rows="5" class="form-control"><?php echo $LS_FORM_DATA["description"];?></textarea></td>
</tr>
</table>

<a href="index.php?p=files" class="btn btn-default"><?php echo $tl["general"]["g103"];?></a>
<button type="submit" name="save" class="btn btn-primary pull-right"><?php echo $tl["general"]["g38"];?></button>

</form>
		
<?php include_once APP_PATH.'operator/template/footer.php';?>