<?php include_once APP_PATH.'operator/template/header.php';?>

<div class="hero-unit">
<h1><?php echo $tl["menu"]["m1"];?></h1>
</div>

<?php if (isset($LEADS_ALL) && is_array($LEADS_ALL)) { ?>

<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<table class="table table-striped">
<thead>
<tr>
<th>#</th>
<th><input type="checkbox" id="ls_delete_all" /></th>
<th><?php echo $tl["general"]["g54"];?></th>
<th><?php echo $tl["login"]["l5"];?></th>
<th><?php echo $tl["general"]["g55"];?></th>
<th><?php echo $tl["general"]["g13"];?> </th>
<th><?php if ($LS_SPECIALACCESS) { ?><button type="submit" name="delete" id="button_delete" class="btn btn-mini" onclick="if(!confirm('<?php echo $tl["error"]["e30"];?>'))return false;"><i class="icon-remove"></i></button><?php } ?></th>
</tr>
</thead>
<?php foreach($LEADS_ALL as $v) { ?>
<tr>
<td><?php echo $v["id"];?></td>
<td><input type="checkbox" name="ls_delete_leads[]" class="highlight" value="<?php echo $v["id"];?>" /></td>
<td><?php echo $v["name"];?></td>
<td><?php echo $v["email"];?></td>
<td><a data-toggle="modal" href="index.php?p=leads&amp;sp=readleads&amp;ssp=<?php echo $v["id"];?>" data-target="#leadModal"><?php echo $tl["general"]["g65"];?></a</td>
<td><?php echo LS_base::lsTimesince($v['initiated'], LS_DATEFORMAT, LS_TIMEFORMAT);?></td>
<td><?php if (LS_SUPEROPERATORACCESS) { ?><a href="index.php?p=leads&amp;sp=delete&amp;ssp=<?php echo $v["id"];?>" class="btn btn-mini btn-danger" onclick="if(!confirm('<?php echo $tl["error"]["e33"];?>'))return false;"><i class="icon-remove"</a><?php } ?></td>
</tr>
<?php } ?>
</table>
</form>

<?php } else { ?>

<div class="alert alert-info">
<?php echo $tl["errorpage"]["data"];?>
</div>

<?php } ?>

<!-- Modal -->
<div id="leadModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="leadModal" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo $tl["menu"]["m1"];?></h3>
  </div>
  <div class="modal-body">
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo $tl["general"]["g99"];?></button>
  </div>
</div>

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
			
			$('#leadModal').on('hidden', function () {
			  $(this).removeData();
			});
							
		});
		
		ls.main_url = "<?php echo BASE_URL_ADMIN;?>";
		ls.main_lang = "<?php echo LS_LANG;?>";
		ls.ls_submit = "<?php echo $tl['general']['g69'];?>";
		ls.ls_submitwait = "<?php echo $tl['general']['g70'];?>";
</script>

		
<?php include_once APP_PATH.'operator/template/footer.php';?>