<?php include_once APP_PATH.'operator/template/header.php';?>

<h3><?php echo $tl["menu"]["m18"];?></h3>

<div class="row">
<div class="col-md-3">
		
				<h4><?php echo $tl["general"]["g175"];?></h4>
				
				<?php if ($errors) { ?>
				<div class="alert alert-danger"><?php echo $errors["e"].$errors["e1"];?></div>
				<?php } ?>
				
				<form role="form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
					
					<div class="form-group<?php if ($errors["e"]) echo " has-error";?>">
					    <label class="control-label" for="path"><?php echo $tl["general"]["g167"];?></label>
						<input type="text" name="path" class="form-control" value="<?php echo $_REQUEST["path"];?>" />
					</div>
					
					<div class="form-group">
					    <label class="control-label" for="response"><?php echo $tl["general"]["g191"];?></label>
						<input type="radio" name="showalert" value="1"> <?php echo $tl["general"]["g19"];?>
						<input type="radio" name="showalert" value="0" checked> <?php echo $tl["general"]["g18"];?>
					</div>
					
					<div class="form-group">
					    <label class="control-label" for="response"><?php echo $tl["general"]["g192"];?></label>
					    <div class="row">
					    <div class="col-xs-6">
						<select name="alertfadein" class="form-control">
							<option value="swing">swing</option>
							<option value="bounce">bounce</option>
							<option value="rollIn">rollIn</option>
							<option value="lightSpeedIn">lightSpeedIn</option>
						</select>
						</div>
						<div class="col-xs-6">
						<select name="alertfadeout" class="form-control">
							<option value="hinge">hinge</option>
							<option value="rollOut">rollOut</option>
							<option value="lightSpeedOut">lightSpeedOut</option>
						</select>
						</div>
						</div>
					</div>
					
					<div class="form-group">
					    <label class="control-label" for="response"><?php echo $tl["general"]["g194"];?></label>
					    <select name="onsite" class="form-control">
					    <option value="2">2 <?php echo $tl["general"]["g196"];?></option>
					    <option value="5">5 <?php echo $tl["general"]["g196"];?></option>
					    <option value="15">15 <?php echo $tl["general"]["g196"];?></option>
					    <option value="30">30 <?php echo $tl["general"]["g196"];?></option>
					    <option value="60">1 <?php echo $tl["general"]["g197"];?></option>
					    <option value="120">2 <?php echo $tl["general"]["g197"];?></option>
					    <option value="180">3 <?php echo $tl["general"]["g197"];?></option>
					    <option value="240">4 <?php echo $tl["general"]["g197"];?></option>
					    <option value="300">5 <?php echo $tl["general"]["g197"];?></option>
					    </select>
					</div>
					
					<div class="form-group">
					    <label class="control-label" for="response"><?php echo $tl["general"]["g195"];?></label>
						<select name="visited" class="form-control">
						<?php for ($i = 1; $i <= 20; $i++) { ?>
						<option value="<?php echo $i ?>"><?php echo $i; ?> <?php echo $tl["general"]["g198"];?></option>
						<?php } ?>
						</select>
					</div>
					
					<div class="form-group<?php if ($errors["e1"]) echo " has-error";?>">
					    <label class="control-label" for="response"><?php echo $tl["general"]["g146"];?></label>
						<textarea name="message" class="form-control" rows="5"><?php echo $_REQUEST["message"];?></textarea>
					</div>
					
					<div class="form-actions">
						<button type="submit" name="insert_proactive" class="btn btn-primary btn-block"><?php echo $tl["general"]["g38"];?></button>
					</div>

				</form>
		</div>
<div class="col-md-9">
	
			<h4><?php echo $tl["general"]["g176"];?></h4>
			
			<table class="table table-striped">
			<thead>
			<tr>
			<th>#</th>
			<th><?php echo $tl["general"]["g167"];?></th>
			<th><?php echo $tl["general"]["g146"];?></th>
			<th><?php echo $tl["general"]["g47"];?></th>
			<th><?php echo $tl["general"]["g48"];?></th>
			</tr>
			</thead>
			<?php if (isset($RESPONSES_ALL) && is_array($RESPONSES_ALL)) foreach($RESPONSES_ALL as $v) { ?>
			<tr>
			<td><?php echo $v["id"];?></td>
			<td class="title"><a href="index.php?p=proactive&amp;sp=edit&amp;ssp=<?php echo $v["id"];?>"><?php echo $v["path"];?></a></td>
			<td class="desc"><?php echo $v["message"];?></td>
			<td><a class="btn btn-default btn-xs" href="index.php?p=proactive&amp;sp=edit&amp;ssp=<?php echo $v["id"];?>"><span class="glyphicon glyphicon-pencil"></span></a></td>
			<td><a class="btn btn-danger btn-xs" href="index.php?p=proactive&amp;sp=delete&amp;ssp=<?php echo $v["id"];?>" onclick="if(!confirm('<?php echo $tl["error"]["e31"];?>'))return false;"><span class="glyphicon glyphicon-trash"></span></a></td>
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