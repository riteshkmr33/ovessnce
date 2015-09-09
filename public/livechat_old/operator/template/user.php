<?php include_once APP_PATH.'operator/template/header.php';?>

<div class="hero-unit">
<h1><?php echo $tl["menu"]["m4"];?></h1>
</div>

<?php if ($LS_SPECIALACCESS) { ?>

<div class="btn-group pull-right">
	<a class="btn dropdown-toggle" data-toggle="dropdown" href="#"><?php echo $tl["menu"]["m12"];?>
	    <span class="caret"></span>
	</a>
  <ul class="dropdown-menu">
    <li><a href="index.php?p=users&amp;sp=newuser"><?php echo $tl["menu"]["m7"];?></a></li>
  </ul>
</div>

<div class="clearfix"></div>

<hr>

<?php } ?>

<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<table class="table table-striped">
<thead>
<tr>
<th>#</th>
<th><input type="checkbox" id="ls_delete_all" /></th>
<th><?php echo $tl["user"]["u"];?></th>
<th><?php echo $tl["user"]["u1"];?></th>
<th><?php echo $tl["user"]["u2"];?></th>
<th></th>
<th><?php if ($LS_SPECIALACCESS) { ?><button type="submit" name="lock" id="button_lock" class="btn btn-mini" onclick="if(!confirm('<?php echo $tl["user"]["all"];?>'))return false;"><i class="icon-lock"></i></button><?php } ?></th>
<th></th>
<th><?php if ($LS_SPECIALACCESS) { ?><button type="submit" name="delete" id="button_delete" class="btn btn-mini" onclick="if(!confirm('<?php echo $tl["user"]["al"];?>'))return false;"><i class="icon-remove"></i></button><?php } ?></th>
</tr>
</thead>
<?php if (isset($LS_USER_ALL) && is_array($LS_USER_ALL)) foreach($LS_USER_ALL as $v) { ?>
<tr>
<td><?php echo $v["id"];?></td>
<td><input type="checkbox" name="ls_delete_user[]" class="highlight" value="<?php echo $v["id"];?>" /></td>
<td><a href="index.php?p=users&amp;sp=edit&amp;ssp=<?php echo $v["id"];?>"><?php echo $v["name"];?></a></td>
<td><?php echo $v["email"];?></td>
<td><a href="index.php?p=users&amp;sp=edit&amp;ssp=<?php echo $v["id"];?>"><?php echo $v["username"];?></a></td>
<td><a data-toggle="modal" href="index.php?p=users&amp;sp=stats&amp;ssp=<?php echo $v["id"];?>&amp;sssp=<?php echo $v["username"];?>" data-target="#userModal" class="btn btn-mini"><i class="icon-signal"></i></a></td>
<td><?php if ($LS_SPECIALACCESS) { ?><a href="index.php?p=users&amp;sp=lock&amp;ssp=<?php echo $v["id"];?>" class="btn btn-mini"><i class="icon-<?php if ($v["access"] == '1') { ?>ok<?php } else { ?>lock<?php } ?>"></i></a><?php } ?></td>
<td><a href="index.php?p=users&amp;sp=edit&amp;ssp=<?php echo $v["id"];?>" class="btn btn-mini"><i class="icon-edit"></i></a></td>
<td><?php if ($LS_SPECIALACCESS) { ?><a href="index.php?p=users&amp;sp=delete&amp;ssp=<?php echo $v["id"];?>" class="btn btn-mini btn-danger" onclick="if(!confirm('<?php echo $tl["user"]["al"];?>'))return false;"><i class="icon-remove"></i></a><?php } ?></td>
</tr>
<?php } ?>
</table>
</form>

<!-- Modal -->
<div id="userModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="userModal" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo $tl["general"]["g92"];?></h3>
  </div>
  <div class="modal-body">
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo $tl["general"]["g99"];?></button>
  </div>
</div>

<script type="text/javascript" src="js/page.ajax.js"></script>

<!-- JavaScript for select all -->
<script type="text/javascript">
		$(document).ready(function() {
		
		jrc_setChecker(<?php echo $lsuser->getVar("id");?>);
		        setInterval("jrc_setChecker(<?php echo $lsuser->getVar("id");?>);", 5000);
		jrc_setTimer(<?php echo $lsuser->getVar("id");?>);
		        setInterval("jrc_setTimer(<?php echo $lsuser->getVar("id");?>);", 120000);
		
			$("#ls_delete_all").click(function() {
			$("#button_lock").toggleClass("highlight-lock");
			$("#button_delete").toggleClass("highlight-delete");
				var checked_status = this.checked;
				$(".highlight").each(function()
				{
					this.checked = checked_status;
				});
			});
			$(".highlight").click(function() {
			$("#button_lock").addClass("highlight-lock");
			$("#button_delete").addClass("highlight-delete");
			});
			
			$('#userModal').on('hidden', function () {
			  $(this).removeData();
			});
						
		});
		
		ls.main_url = "<?php echo BASE_URL_ADMIN;?>";
		ls.main_lang = "<?php echo LS_LANG;?>";
		ls.ls_submit = "<?php echo $tl['general']['g69'];?>";
		ls.ls_submitwait = "<?php echo $tl['general']['g70'];?>";
</script>

		
<?php include_once APP_PATH.'operator/template/footer.php';?>