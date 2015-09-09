<?php include_once APP_PATH.'operator/template/header.php';?>

<h3><?php echo $tl["menu"]["m16"];?></h3>

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
	<td><?php echo $tl["general"]["g16"];?></td>
	<td>
	<div class="form-group<?php if ($errors["e"]) echo " has-error";?>">
		<input type="text" name="title" class="form-control" value="<?php echo $LS_FORM_DATA["title"];?>" />
	</div>
	</td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g49"];?> <a href="javascript:void(0)" class="rhino-help" data-content="<?php echo $tl["help"]["h13"];?>" data-original-title="<?php echo $tl["help"]["t"];?>"><span class="glyphicon glyphicon-question-sign"></span></a></td>
	<td>
	<div class="form-group<?php if ($errors["e1"]) echo " has-error";?>">
		<textarea name="response" rows="5" class="form-control"><?php echo $LS_FORM_DATA["message"];?></textarea>
	</div>
	</td>
</tr>
</table>

<a href="index.php?p=response" class="btn btn-default"><?php echo $tl["general"]["g103"];?></a>
<button type="submit" name="save" class="btn btn-primary pull-right"><?php echo $tl["general"]["g38"];?></button>

</form>
		
<?php include_once APP_PATH.'operator/template/footer.php';?>