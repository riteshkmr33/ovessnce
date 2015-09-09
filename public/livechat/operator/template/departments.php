<?php include_once APP_PATH.'operator/template/header.php';?>

<h3><?php echo $tl["menu"]["m9"];?></h3>

<div class="row">
<div class="col-md-9">

			<h4><?php echo $tl["general"]["g98"];?></h4>
			
			<?php if (isset($DEPARTMENTS_ALL) && is_array($DEPARTMENTS_ALL)) { ?>
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<?php foreach($DEPARTMENTS_ALL as $v) { ?>
			<table class="table table-striped">
			<thead>
			<tr>
			<th>#</th>
			<th><?php echo $tl["general"]["g16"];?></th>
			<th><?php echo $tl["general"]["g52"];?></th>
			<th><?php echo $tl["general"]["g47"];?></th>
			<th><?php echo $tl["general"]["g101"];?></th>
			<th><?php echo $tl["general"]["g48"];?></th>
			<th><?php echo $tl["general"]["g102"];?></th>
			</tr>
			</thead>
			<tr>
			<td><?php echo $v["id"];?><input type="hidden" name="real_dep_id[]" value="<?php echo $v["id"];?>" /></td>
			<td class="title"><a href="index.php?p=departments&amp;sp=edit&amp;ssp=<?php echo $v["id"];?>"><?php echo $v["title"];?></a></td>
			<td class="desc"><?php echo $v["description"];?></td>
			<td><a class="btn btn-default btn-xs" href="index.php?p=departments&amp;sp=edit&amp;ssp=<?php echo $v["id"];?>"><span class="glyphicon glyphicon-pencil"></span></a></td>
			<td><?php if ($v["id"] != 1) { ?><a class="btn btn-default btn-xs" href="index.php?p=departments&amp;sp=lock&amp;ssp=<?php echo $v["id"];?>" title="<?php if ($v["active"] == 1) { echo $tl["cmdesc"]["d35"]; } else { echo $tl["cmdesc"]["d36"];}?>"><span class="glyphicon glyphicon-<?php if ($v["active"] == '1') { ?>ok<?php } else { ?>lock<?php } ?>"></span></a><?php } ?></td>
			<td><?php if ($v["id"] != 1) { ?><a class="btn btn-danger btn-xs" href="index.php?p=departments&amp;sp=delete&amp;ssp=<?php echo $v["id"];?>" onclick="if(!confirm('<?php echo $tl["error"]["e30"];?>'))return false;"><span class="glyphicon glyphicon-trash"></span></a><?php } ?></td>
			<td><input type="text" name="corder[]" class="corder form-control" value="<?php echo $v["dorder"];?>" /></td>
			</tr>
			
			</table>
			<?php } ?>
			
			<div class="form-actions">
			<button type="submit" name="save" class="btn btn-primary btn-block"><?php echo $tl["general"]["g38"];?></button>
			</div>
			
			<?php } ?>
			</form>
			
		</div>
	
		<div class="col-md-3">
		
				<h4><?php echo $tl["general"]["g99"];?></h4>
				
				<?php if ($errors) { ?>
				<div class="alert alert-danger"><?php echo $errors["e"].$errors["e1"];?></div>
				<?php } ?>
				
				<form role="form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
				
					<div class="form-group<?php if ($errors["e"]) echo " has-error";?>">
					    <label class="control-label" for="title"><?php echo $tl["general"]["g16"];?></label>
						<input type="text" name="title" class="form-control" value="<?php echo $_REQUEST["title"];?>" />
					</div>
					<div class="form-group<?php if ($errors["e1"]) echo " has-error";?>">
					    <label class="control-label" for="email"><?php echo $tl["general"]["g68"];?></label>
						<input type="text" name="email" class="form-control" value="<?php echo $_REQUEST["email"];?>" />
					</div>
					<div class="form-group">
					    <label class="control-label" for="description"><?php echo $tl["general"]["g52"];?></label>
						<textarea name="description" class="form-control" rows="5"><?php echo $_REQUEST["description"];?></textarea>
					</div>
					
					<div class="form-actions">
					<button type="submit" name="insert_department" class="btn btn-primary btn-block"><?php echo $tl["general"]["g38"];?></button>
					</div>

				</form>
		</div>
			
</div>

<script type="text/javascript" src="js/page.ajax.js"></script>

<script type="text/javascript">
$(document).ready(function(){
		setChecker(<?php echo $lsuser->getVar("id");?>);
		        setInterval("setChecker(<?php echo $lsuser->getVar("id");?>);", 10000);
		setTimer(<?php echo $lsuser->getVar("id");?>);
		        setInterval("setTimer(<?php echo $lsuser->getVar("id");?>);", 120000);
});

		ls.main_url = "<?php echo BASE_URL_ADMIN;?>";
		ls.main_lang = "<?php echo LS_LANG;?>";
		ls.ls_submit = "<?php echo $tl['general']['g69'];?>";
		ls.ls_submitwait = "<?php echo $tl['general']['g70'];?>";
</script>

<?php include_once APP_PATH.'operator/template/footer.php';?>