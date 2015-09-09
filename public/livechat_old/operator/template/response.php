<?php include_once APP_PATH.'operator/template/header.php';?>

<div class="hero-unit">
<h1><?php echo $tl["menu"]["m3"];?></h1>
</div>

<div class="row">
<div class="span3">
		
				<h4><?php echo $tl["general"]["g45"];?></h4>
				
				<?php if ($errors) { ?>
				<div class="alert alert-error"><?php echo $errors["e"].$errors["e1"];?></div>
				<?php } ?>
				
				<form method="post" class="form-inline" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
					
					<div class="control-group<?php if ($errors["e"]) echo " error";?>">
					    <label class="control-label" for="title"><?php echo $tl["general"]["g16"];?></label>
					    <div class="controls">
							<input type="text" name="title" class="input-large" value="<?php echo $_REQUEST["title"];?>" />
						</div>
					</div>
					
					<div class="control-group<?php if ($errors["e1"]) echo " error";?>">
					    <label class="control-label" for="response"><?php echo $tl["general"]["g49"];?></label>
					    <div class="controls">
							<textarea name="response" class="input-large" rows="5"><?php echo $_REQUEST["response"];?></textarea>
						</div>
					</div>
					
					<div class="form-actions">
						<button type="submit" name="insert_response" class="btn btn-primary pull-right"><?php echo $tl["general"]["g38"];?></button>
					</div>

				</form>
		</div>
<div class="span9">
	
			<h4><?php echo $tl["general"]["g46"];?></h4>
			
			<table class="table table-striped">
			<thead>
			<tr>
			<th>#</th>
			<th><?php echo $tl["general"]["g16"];?></th>
			<th><?php echo $tl["general"]["g49"];?></th>
			<th><?php echo $tl["general"]["g47"];?></th>
			<th><?php echo $tl["general"]["g48"];?></th>
			</tr>
			</thead>
			<?php if (isset($RESPONSES_ALL) && is_array($RESPONSES_ALL)) foreach($RESPONSES_ALL as $v) { ?>
			<tr>
			<td><?php echo $v["id"];?></td>
			<td class="title"><a href="index.php?p=response&amp;sp=edit&amp;ssp=<?php echo $v["id"];?>"><?php echo $v["title"];?></a></td>
			<td class="desc"><?php echo $v["message"];?></td>
			<td><a href="index.php?p=response&amp;sp=edit&amp;ssp=<?php echo $v["id"];?>" class="edit_user btn btn-mini"><i class="icon-edit"></i></a></td>
			<td><a href="index.php?p=response&amp;sp=delete&amp;ssp=<?php echo $v["id"];?>" class="btn btn-mini btn-danger" onclick="if(!confirm('<?php echo $tl["error"]["e31"];?>'))return false;"><i class="icon-remove"></i></a></td>
			</tr>
			<?php } ?>
			</table>
			
		</div>
			
</div>

<script type="text/javascript" src="js/page.ajax.js"></script>

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