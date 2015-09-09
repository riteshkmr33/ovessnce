<?php include_once APP_PATH.'operator/template/header.php';?>

<div class="hero-unit">
<h1><?php echo $tl["menu"]["m8"];?></h1>
<p><?php echo $tl["general"]["g84"];?></p>
</div>

<?php if (isset($CEMAILS_ALL) && is_array($CEMAILS_ALL)) { ?>

<table class="table table-striped">
<thead>
<tr>
<th>#</th>
<th><?php echo $tl["general"]["g54"];?></th>
<th><?php echo $tl["login"]["l5"];?></th>
</tr>
</thead>
<?php foreach($CEMAILS_ALL as $v) { ?>
<tr>
<td><?php echo $v["id"];?></td>
<td><?php echo $v["name"];?></td>
<td><?php echo $v["email"];?></td>
</tr>
<?php } ?>
</table>

<div class="alert alert-info">
<a href="index.php?p=emails&amp;sp=export" class="btn"><?php echo $tl["general"]["g83"];?></a>
</div>

<?php } else { ?>

<div class="alert alert-info">
<?php echo $tl["errorpage"]["data"];?>
</div>

<?php } ?>

<script type="text/javascript" src="js/page.ajax.js"></script>

<!-- JavaScript for select all -->
<script type="text/javascript">
		$(document).ready(function()
		{
		
		jrc_setChecker(<?php echo $lsuser->getVar("id");?>);
		        setInterval("jrc_setChecker(<?php echo $lsuser->getVar("id");?>);", 10000);
		jrc_setTimer(<?php echo $lsuser->getVar("id");?>);
		        setInterval("jrc_setTimer(<?php echo $lsuser->getVar("id");?>);", 120000);
							
		});
		
		ls.main_url = "<?php echo BASE_URL_ADMIN;?>";
		ls.main_lang = "<?php echo LS_LANG;?>";
		ls.ls_submit = "<?php echo $tl['general']['g69'];?>";
		ls.ls_submitwait = "<?php echo $tl['general']['g70'];?>";
</script>
		
<?php include_once APP_PATH.'operator/template/footer.php';?>