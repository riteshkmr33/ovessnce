<?php include_once APP_PATH.'operator/template/header.php';?>

<h3><?php echo $tl["general"]["g177"];?></h3>

<div id="userOnline"></div>

<?php if (isset($rowt['totalAll'])) { ?>

<h3><?php echo $tl["general"]["g105"].' ('.$rowt['totalAll'].')';?></h3>

<table class="table table-striped">
<tr>
<th>#</th>
<th><?php echo $tl["general"]["g169"];?></th>
<th><?php echo $tl["general"]["g170"];?></th>
<th><?php echo $tl["general"]["g171"];?></th>
<th><?php echo $tl["general"]["g172"];?></th>
<th><?php echo $tl["general"]["g11"];?></th>
<th><?php echo $tl["general"]["g173"];?></th>
<th><?php echo $tl["general"]["g174"];?></th>
<th><?php if (LS_SUPERADMINACCESS) { ?><a class="btn btn-warning btn-xs" href="index.php?p=uonline&amp;sp=truncate" onclick="if(!confirm('<?php echo $tl["error"]["e41"];?>'))return false;"><span class="glyphicon glyphicon-warning-sign"></span></a><?php } ?></th>
</tr>
</thead>
<tbody id="jak_result"></tbody>
</table>
</form>

<button class="btn btn-info btn-block load-more"><i class="fa fa-spinner fa-spin"></i> <?php echo $tl["general"]["g228"];?></button>

<?php } else { ?>

<div class="alert alert-info">
<?php echo $tl["errorpage"]["data"];?>
</div>

<?php } ?>

<script type="text/javascript" src="js/page.ajax.js"></script>

<!-- JavaScript for select all -->
<script type="text/javascript">
		$(document).ready(function() {
		
		loadContent("uonline_pages",<?php echo $total_pages;?>,"","");
		
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