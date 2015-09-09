<?php include_once APP_PATH.'operator/template/header.php';?>

<h3><?php echo $tl["menu"]["m6"];?></h3>

<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<div class="table-responsive">
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
<th><a class="btn btn-warning btn-xs" href="index.php?p=logs&amp;sp=truncate" onclick="if(!confirm('<?php echo $tl["error"]["e34"];?>'))return false;"><span class="glyphicon glyphicon-warning-sign"></span></a></th>
<th><button type="submit" name="delete" id="button_delete" class="btn btn-danger btn-xs" onclick="if(!confirm('<?php echo $tl["error"]["e33"];?>'))return false;"><span class="glyphicon glyphicon-trash"></span></button></th>
</tr>
</thead>
<tbody id="jak_result"></tbody>
</table>
</div>
</form>

<button class="btn btn-info btn-block load-more"><i class="fa fa-spinner fa-spin"></i> <?php echo $tl["general"]["g228"];?></button>

<script type="text/javascript" src="js/page.ajax.js"></script>
<!-- JavaScript for select all -->
<script type="text/javascript">
		$(document).ready(function() {
		
		loadContent("logs_pages",<?php echo $total_pages;?>,"","");
				
		setChecker(<?php echo $lsuser->getVar("id");?>);
		        setInterval("setChecker(<?php echo $lsuser->getVar("id");?>);", 10000);
		setTimer(<?php echo $lsuser->getVar("id");?>);
		        setInterval("setTimer(<?php echo $lsuser->getVar("id");?>);", 120000);
		
			$("#ls_delete_all").click(function() {
				var checked_status = this.checked;
				$(".highlight").each(function()
				{
					this.checked = checked_status;
				});
			});			
		});
		
		ls.main_url = "<?php echo BASE_URL_ADMIN;?>";
		ls.main_lang = "<?php echo LS_LANG;?>";
		ls.ls_submit = "<?php echo $tl['general']['g69'];?>";
		ls.ls_submitwait = "<?php echo $tl['general']['g70'];?>";
</script>
		
<?php include_once APP_PATH.'operator/template/footer.php';?>