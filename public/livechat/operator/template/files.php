<?php include_once APP_PATH.'operator/template/header.php';?>

<h3><?php echo $tl["menu"]["m2"];?></h3>

<div class="row">
<div class="col-md-9">

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
			<td><?php echo $v["id"];?></td>
			<td class="title"><a href="index.php?p=files&amp;sp=edit&amp;ssp=<?php echo $v["id"];?>"><?php echo $v["name"];?></a></td>
			<td class="desc"><?php echo $v["description"];?></td>
			<td><a class="btn btn-default btn-xs" href="index.php?p=files&amp;sp=edit&amp;ssp=<?php echo $v["id"];?>"><span class="glyphicon glyphicon-pencil"></span></a></td>
			<td><a class="btn btn-danger btn-xs" href="index.php?p=files&amp;sp=delete&amp;ssp=<?php echo $v["id"];?>" onclick="if(!confirm('<?php echo $tl["error"]["e32"];?>'))return false;"><span class="glyphicon glyphicon-trash"></span></a></td>
			</tr>
			<?php } ?>
			</table>
			
			<?php if (isset($LS_OPERATOR_FILES) && is_array($LS_OPERATOR_FILES)) { ?>
			
			<div class="heading_solid">
			<h4><?php echo $tl["general"]["g132"];?></h4>
			</div>
			
			<table class="table table-striped">
			<?php foreach($LS_OPERATOR_FILES as $l) { ?>
				
				<tr><td>
				<?php if (getimagesize('../'.LS_FILES_DIRECTORY.'/operator/'.$l)) { ?>
					<a class="lightbox" href="../<?php echo LS_FILES_DIRECTORY;?>/operator/<?php echo $l;?>"><img src="../<?php echo LS_FILES_DIRECTORY;?>/operator/<?php echo $l;?>" alt="<?php echo $l;?>" width="40px" /></a> <a href="index.php?p=files&amp;sp=deletefo&amp;ssp=<?php echo $l;?>" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" onclick="if(!confirm('<?php echo $tl["error"]["e32"];?>'))return false;"></span></a>
				<?php } else { ?>
					<?php echo $l;?> <a href="../<?php echo LS_FILES_DIRECTORY;?>/operator/<?php echo $l;?>" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-floppy-save"></span></a> <a href="index.php?p=files&amp;sp=deletefo&amp;ssp=<?php echo $l;?>" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" onclick="if(!confirm('<?php echo $tl["error"]["e32"];?>'))return false;"></span></a>
				<?php } ?>
				</td></tr>
			<?php } ?>
			</table>
			
			<?php } ?>
			
			<?php if (isset($LS_USER_FILES) && is_array($LS_USER_FILES)) { ?>
			
			<h4><?php echo $tl["general"]["g133"];?></h4>
			
			<table class="table table-striped">
			<?php foreach($LS_USER_FILES as $k) { ?>
				
				<tr><td>
					<?php if (getimagesize('../'.LS_FILES_DIRECTORY.'/user/'.$k)) { ?>
						<a class="lightbox" href="../<?php echo LS_FILES_DIRECTORY;?>/user/<?php echo $k;?>"><img src="../<?php echo LS_FILES_DIRECTORY;?>/user/<?php echo $k;?>" alt="<?php echo $k;?>" width="40px" /></a> <a href="index.php?p=files&amp;sp=deletef&amp;ssp=<?php echo $k;?>" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" onclick="if(!confirm('<?php echo $tl["error"]["e32"];?>'))return false;"></span></a>
					<?php } else { ?>
						<?php echo $k;?> <a href="../<?php echo LS_FILES_DIRECTORY;?>/user/<?php echo $k;?>" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-floppy-save"></span></a> <a href="index.php?p=files&amp;sp=deletef&amp;ssp=<?php echo $k;?>" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash" onclick="if(!confirm('<?php echo $tl["error"]["e32"];?>'))return false;"></span></a>
					<?php } ?>
				</td></tr>
			<?php } ?>
			</table>
			
			<?php } ?>
			
		</div>
	
		<div class="col-md-3">
		
				<h4><?php echo $tl["general"]["g50"];?></h4>
				
				<?php if ($errors) { ?>
				<div class="alert alert-danger"><?php echo $errors["e"].$errors["e1"].$errors["e2"];?></div>
				<?php } ?>
				
				<form role="form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data">
				
				<div class="form-group">
					<label class="control-label" for="file"><?php echo $tl["general"]["g9"];?></label>
					<input name="uploadedfile" id="file" type="file" />
				</div>
					
				<div class="form-group<?php if ($errors["e1"]) echo " has-error";?>">
				    <label class="control-label" for="name"><?php echo $tl["general"]["g53"];?></label>
					<input type="text" name="name" class="form-control" value="<?php echo $_REQUEST["name"];?>" />
				</div>
				<div class="form-group">
				    <label class="control-label" for="description"><?php echo $tl["general"]["g52"];?></label>
					<textarea name="description" class="form-control" rows="7"><?php echo $_REQUEST["description"];?></textarea>
				</div>
					
					<div class="form-actions">
						<button type="submit" name="insert_response" class="btn btn-primary btn-block"><?php echo $tl["general"]["g38"];?></button>
					</div>
					
				<input type="hidden" name="max_file_size" value="10000000" />

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