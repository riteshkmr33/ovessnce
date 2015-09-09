<?php include_once APP_PATH.'operator/template/header.php';?>

<div class="hero-unit">
<h1><?php echo $tl["menu"]["m6"];?></h1>
</div>

<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<table class="table table-striped">
<thead>
<tr>
<th>#</th>
<th><input type="checkbox" id="ls_delete_all" /></th>
<th><?php echo $tl["login"]["l1"];?></th>
<th><?php echo $tl["general"]["g12"];?></th>
<th><?php echo $tl["general"]["g11"];?></th>
<th><?php echo $tl["general"]["g10"];?></th>
<th><?php echo $tl["general"]["g13"];?></th>
<th><?php echo $tl["general"]["g14"];?></th>
<th><a class="btn btn-mini btn-warning" href="index.php?p=logs&amp;sp=truncate" onclick="if(!confirm('<?php echo $tl["error"]["e34"];?>'))return false;"><i class="icon-warning-sign"></i></a></th>
<th><button type="submit" name="delete" id="button_delete" class="btn btn-mini" onclick="if(!confirm('<?php echo $tl["error"]["e33"];?>'))return false;"><i class="icon-remove"></i></button></th>
</tr>
</thead>
<?php if (isset($LS_LOGINLOG_ALL) && is_array($LS_LOGINLOG_ALL)) foreach($LS_LOGINLOG_ALL as $v) { ?>
<tr>
<td><?php echo $v["id"];?></td>
<td><input type="checkbox" name="ls_delete_log[]" class="highlight" value="<?php echo $v["id"];?>" /></td>
<td><?php echo $v["name"];?></td>
<td><?php echo $v["fromwhere"];?></td>
<td><?php echo $v["ip"];?></td>
<td><?php echo $v["usragent"];?></td>
<td><?php echo $v["time"]; ?></td>
<td><?php if ($v["access"] == '1') { ?><i class="icon-ok"></i><?php } else { ?><i class="icon-exclamation-sign"></i><?php } ?></td>
<td></td>
<td><a href="index.php?p=logs&amp;sp=delete&amp;ssp=<?php echo $v["id"];?>" class="btn btn-mini btn-danger" onclick="if(!confirm('<?php echo $tl["error"]["e33"];?>'))return false;"><i class="icon-remove"></i></a></td>
</tr>
<?php } ?>
</table>
</form>

<script type="text/javascript" src="<?php echo BASE_URL_ADMIN;?>js/page.ajax.js"></script>

<!-- JavaScript for select all -->
<script type="text/javascript">
		$(document).ready(function()
		{
		
		jrc_setChecker(<?php echo $lsuser->getVar("id");?>);
		        setInterval("jrc_setChecker(<?php echo $lsuser->getVar("id");?>);", 10000);
		jrc_setTimer(<?php echo $lsuser->getVar("id");?>);
		        setInterval("jrc_setTimer(<?php echo $lsuser->getVar("id");?>);", 120000);
		
			$("#ls_delete_all").click(function() {
			$("#button_delete").toggleClass("highlight-delete");
				var checked_status = this.checked;
				$(".highlight").each(function()
				{
					this.checked = checked_status;
				});
			});
			$(".highlight").click(function() {
			$("#button_delete").addClass("highlight-delete");
			});					
		});
		
		ls.main_url = "<?php echo BASE_URL_ADMIN;?>";
		ls.main_lang = "<?php echo LS_LANG;?>";
		ls.ls_submit = "<?php echo $tl['general']['g69'];?>";
		ls.ls_submitwait = "<?php echo $tl['general']['g70'];?>";
</script>

		
<?php include_once APP_PATH.'operator/template/footer.php';?>