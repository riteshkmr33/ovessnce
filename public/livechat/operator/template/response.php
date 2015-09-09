<?php include_once APP_PATH.'operator/template/header.php';?>

<h3><?php echo $tl["menu"]["m3"];?></h3>

<div class="row">
<div class="col-md-3">
		
				<h4><?php echo $tl["general"]["g45"];?></h4>
				
				<?php if ($errors) { ?>
				<div class="alert alert-danger"><?php echo $errors["e"].$errors["e1"];?></div>
				<?php } ?>
				
				<form role="form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
					
					<div class="form-group<?php if ($errors["e"]) echo " has-error";?>">
					    <label class="control-label" for="title"><?php echo $tl["general"]["g16"];?></label>
						<input type="text" name="title" class="form-control" value="<?php echo $_REQUEST["title"];?>" />
					</div>
					
					<div class="form-group<?php if ($errors["e1"]) echo " has-error";?>">
					    <label class="control-label" for="response"><?php echo $tl["general"]["g49"];?> <a href="javascript:void(0)" class="rhino-help" data-content="<?php echo $tl["help"]["h13"];?>" data-original-title="<?php echo $tl["help"]["t"];?>"><span class="glyphicon glyphicon-question-sign"></span></a></label>
						<textarea name="response" class="form-control" rows="5"><?php echo $_REQUEST["response"];?></textarea>
					</div>
					
					<div class="form-actions">
						<button type="submit" name="insert_response" class="btn btn-primary btn-block"><?php echo $tl["general"]["g38"];?></button>
					</div>

				</form>
		</div>
<div class="col-md-9">
	
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
			<td><a class="btn btn-default btn-xs" href="index.php?p=response&amp;sp=edit&amp;ssp=<?php echo $v["id"];?>"><span class="glyphicon glyphicon-pencil"></span></a></td>
			<td><a class="btn btn-danger btn-xs" href="index.php?p=response&amp;sp=delete&amp;ssp=<?php echo $v["id"];?>" onclick="if(!confirm('<?php echo $tl["error"]["e31"];?>'))return false;"><span class="glyphicon glyphicon-trash"></span></a></td>
			</tr>
			<?php } ?>
			</table>
			
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