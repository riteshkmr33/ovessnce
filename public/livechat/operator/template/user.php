<?php include_once APP_PATH.'operator/template/header.php';?>

<h3><?php echo $tl["menu"]["m4"];?></h3>

<?php if ($LS_SPECIALACCESS) { ?>

<div class="btn-group pull-right">
  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
    <?php echo $tl["menu"]["m12"];?> <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" role="menu">
    <li><a href="index.php?p=users&amp;sp=new"><?php echo $tl["menu"]["m7"];?></a></li>
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
<th><?php if ($LS_SPECIALACCESS) { ?><button type="submit" name="lock" id="button_lock" class="btn btn-default btn-xs" onclick="if(!confirm('<?php echo $tl["user"]["all"];?>'))return false;"><span class="glyphicon glyphicon-lock"></span></button><?php } ?></th>
<th></th>
<th><?php if ($LS_SPECIALACCESS) { ?><button type="submit" name="delete" id="button_delete" class="btn btn-danger btn-xs" onclick="if(!confirm('<?php echo $tl["user"]["al"];?>'))return false;"><span class="glyphicon glyphicon-trash"></span></button><?php } ?></th>
</tr>
</thead>
<?php if (isset($LS_USER_ALL) && is_array($LS_USER_ALL)) foreach($LS_USER_ALL as $v) { ?>
<tr>
<td><?php echo $v["id"];?></td>
<td><input type="checkbox" name="ls_delete_user[]" class="highlight" value="<?php echo $v["id"];?>" /></td>
<td><a href="index.php?p=users&amp;sp=edit&amp;ssp=<?php echo $v["id"];?>"><?php echo $v["name"];?></a></td>
<td><?php echo $v["email"];?></td>
<td><a href="index.php?p=users&amp;sp=edit&amp;ssp=<?php echo $v["id"];?>"><?php echo $v["username"];?></a></td>
<td><a class="btn btn-default btn-xs" data-toggle="modal" href="index.php?p=users&amp;sp=stats&amp;ssp=<?php echo $v["id"];?>&amp;sssp=<?php echo $v["username"];?>" data-target="#generalModal"><span class="glyphicon glyphicon-signal"></span></a></td>
<td><?php if ($LS_SPECIALACCESS) { ?><a class="btn btn-default btn-xs" href="index.php?p=users&amp;sp=lock&amp;ssp=<?php echo $v["id"];?>"><span class="glyphicon glyphicon-<?php if ($v["access"] == '1') { ?>ok<?php } else { ?>lock<?php } ?>"></span></a><?php } ?></td>
<td><a class="btn btn-default btn-xs" href="index.php?p=users&amp;sp=edit&amp;ssp=<?php echo $v["id"];?>"><span class="glyphicon glyphicon-pencil"></span></a></td>
<td><?php if ($LS_SPECIALACCESS) { ?><a class="btn btn-default btn-xs" href="index.php?p=users&amp;sp=delete&amp;ssp=<?php echo $v["id"];?>" onclick="if(!confirm('<?php echo $tl["user"]["al"];?>'))return false;"><span class="glyphicon glyphicon-trash"></span></a><?php } ?></td>
</tr>
<?php } ?>
</table>
</form>

<script type="text/javascript" src="js/page.ajax.js"></script>

<!-- JavaScript for select all -->
<script type="text/javascript">
		$(document).ready(function() {
		
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