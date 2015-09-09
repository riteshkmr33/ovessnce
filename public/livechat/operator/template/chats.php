<?php include_once APP_PATH.'operator/template/header.php';?>

<h3><?php echo $tl["menu"]["m14"];?></h3>

<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<table class="table table-striped">
<thead>
<tr>
<th>#</th>
<th><input type="checkbox" id="ls_delete_all" /></th>
<th><?php echo $tl["general"]["g12"];?> <a class="btn btn-success btn-xs" href="index.php?p=chats&amp;sp=sort&amp;ssp=fromid&amp;sssp=DESC"><span class="glyphicon glyphicon-arrow-up"></span></a> <a class="btn btn-warning btn-xs" href="index.php?p=chats&amp;sp=sort&amp;ssp=fromid&amp;sssp=ASC"><span class="glyphicon glyphicon-arrow-down"></a></th>
<th><?php echo $tl["general"]["g145"];?> <a class="btn btn-success btn-xs" href="index.php?p=chats&amp;sp=sort&amp;ssp=toid&amp;sssp=DESC"><span class="glyphicon glyphicon-arrow-up"></a> <a class="btn btn-warning btn-xs" href="index.php?p=chats&amp;sp=sort&amp;ssp=toid&amp;sssp=ASC"><span class="glyphicon glyphicon-arrow-down"></a></th>
<th><?php echo $tl["general"]["g146"];?> <a class="btn btn-success btn-xs" href="index.php?p=chats&amp;sp=sort&amp;ssp=message&amp;sssp=DESC"><span class="glyphicon glyphicon-arrow-up"></a> <a class="btn btn-warning btn-xs" href="index.php?p=chats&amp;sp=sort&amp;ssp=message&amp;sssp=ASC"><span class="glyphicon glyphicon-arrow-down"></a></th>
<th><?php echo $tl["general"]["g13"];?></th>
<?php if ($LS_SPECIALACCESS) { ?><th class="content-go"><a class="btn btn-warning btn-xs" href="index.php?p=chats&amp;sp=truncate" onclick="if(!confirm('<?php echo $tl["error"]["e39"];?>'))return false;"><span class="glyphicon glyphicon-warning-sign"></span></a></th>
<th><button type="submit" name="delete" id="button_delete" class="btn btn-danger btn-xs" onclick="if(!confirm('<?php echo $tl["error"]["e30"];?>'))return false;"><span class="glyphicon glyphicon-trash"></span></button></th><?php } ?>

</tr>
</thead>
<tbody id="jak_result"></tbody>
</table>
</form>

<button class="btn btn-info btn-block load-more"><i class="fa fa-spinner fa-spin"></i> <?php echo $tl["general"]["g228"];?></button>

<script type="text/javascript" src="js/page.ajax.js"></script>

<!-- JavaScript for select all -->
<script type="text/javascript">
		$(document).ready(function() {
		
		loadContent("chats_pages",<?php echo $total_pages;?>,"<?php echo $page2;?>","<?php echo $page3;?>");
		
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