<?php include_once APP_PATH.'operator/template/header.php';?>

<h3><?php echo $tl["menu"]["m19"];?></h3>

<form role="form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data">
<table class="table table-striped">
<thead>
<tr>
<th colspan="2"><?php echo $tl["general"]["g183"];?></th>
</tr>
</thead>
<tr>
	<td><?php echo $tl["general"]["g182"];?></td>
	<td><button type="submit" name="delCache" class="btn btn-primary"><?php echo $tl["general"]["g48"];?></button></td>
</tr>
</table>
<table class="table table-striped">
<thead>
<tr>
<th colspan="2"><?php echo $tl["general"]["g184"];?></th>
</tr>
</thead>
<tr>
	<td><?php echo $tl["general"]["g185"];?></td>
	<td><button type="submit" name="optimize" class="btn btn-success"><?php echo $tl["general"]["g185"];?></button></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g83"];?></td>
	<td><button type="submit" name="export" class="btn btn-info"><?php echo $tl["general"]["g83"];?></button></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g186"];?></td>
	<td><button type="submit" name="download" class="btn btn-warning"><?php echo $tl["general"]["g188"];?></button></td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g187"];?></td>
	<td><input type="file" name="uploaddb" /> <button type="submit" name="import" class="btn btn-danger" onclick="if(!confirm('<?php echo $tl["error"]["e42"];?>'))return false;"><?php echo $tl["general"]["g189"];?></button></td>
</tr>
</table>

</form>

<?php include_once APP_PATH.'operator/template/footer.php';?>