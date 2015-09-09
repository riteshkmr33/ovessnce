<?php include_once APP_PATH.'operator/template/header.php';?>

<div class="hero-unit">
<h1><?php echo $tl["menu"]["m2"];?></h1>
</div>

<div class="row">
<div class="span9">

			<h4><?php echo $tl["general"]["g51"];?></h4>
					
			<table class="table table-striped">
			<thead>
			<tr>
			<th>#</th>
			<th><?php echo $tl["general"]["g53"];?></th>
			<th><?php echo $tl["general"]["g52"];?></th>
			<th><?php echo $tl["general"]["g47"];?></th>
			<th><?php echo $tl["general"]["g48"];?></th>
			</tr>
			</thead>
			<?php if (isset($FILES_ALL) && is_array($FILES_ALL)) foreach($FILES_ALL as $v) { ?>
			<tr>
			<td><?php if (getimagesize('../'.$v['path'])) { ?>
				<img src="../<?php echo $v['path'];?>" alt="<?php echo $v['name'];?>" width="40px" />
			<?php } else { ?>
				<?php echo $v['name'];?>
			<?php } ?></td>
			<td class="title"><a href="index.php?p=files&amp;sp=edit&amp;ssp=<?php echo $v["id"];?>"><?php echo $v["name"];?></a></td>
			<td class="desc"><?php echo $v["description"];?></td>
			<td><a href="index.php?p=files&amp;sp=edit&amp;ssp=<?php echo $v["id"];?>" class="edit_user btn btn-mini"><i class="icon-edit"></i></a></td>
			<td><a href="index.php?p=files&amp;sp=delete&amp;ssp=<?php echo $v["id"];?>" class="btn btn-mini btn-danger" onclick="if(!confirm('<?php echo $tl["error"]["e32"];?>'))return false;"><i class="icon-remove"></i></a></td>
			</tr>
			<?php } ?>
			</table>
			
		</div>
	
		<div class="span3">
		
				<h4><?php echo $tl["general"]["g50"];?></h4>
				
				<?php if ($errors) { ?>
				<div class="alert alert-error"><?php echo $errors["e"].$errors["e1"].$errors["e2"];?></div>
				<?php } ?>
				
				<form method="post" class="form-inline" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data">
				
				<input name="uploadedfile" type="file" class="input-large" />
					
				<div class="control-group<?php if ($errors["e1"]) echo " error";?>">
				    <label class="control-label" for="name"><?php echo $tl["general"]["g53"];?></label>
				    <div class="controls">
						<input type="text" name="name" class="input-large" value="<?php echo $_REQUEST["name"];?>" />
					</div>
				</div>
				<div class="control-group">
				    <label class="control-label" for="description"><?php echo $tl["general"]["g52"];?></label>
				    <div class="controls">
						<textarea name="description" class="input-large" rows="7"><?php echo $_REQUEST["description"];?></textarea>
					</div>
				</div>
					
					<div class="form-actions">
						<button type="submit" name="insert_response" class="btn btn-primary pull-right"><?php echo $tl["general"]["g38"];?></button>
					</div>
					
				<input type="hidden" name="max_file_size" value="10000000" />

				</form>
		</div>
			
</div>

<script type="text/javascript" src="<?php echo BASE_URL_ADMIN;?>js/page.ajax.js"></script>

<script type="text/javascript">
$(document).ready(function(){
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