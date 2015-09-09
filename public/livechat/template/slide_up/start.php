<!--- Container -->
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<?php echo $tl["general"]["g62"];?>
		</div>
	</div>
	<hr>
	<div class="jrc_chat_form_slide">
		
		<?php if ($errors) { ?>
		<div class="alert alert-danger"><?php echo $errors["name"].$errors["email"];?></div>
		<?php } ?>
		
		<form role="form" class="ls-ajaxform" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
		
			<div class="form-group">
			    <label class="sr-only" for="name"><?php echo $tl["general"]["g4"];?></label>
			    	<div class="input-group">
			    		<span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
						<input type="text" name="name" id="name" class="form-control input-sm" placeholder="<?php echo $tl["general"]["g4"];?>">
					</div>
			</div>
			<?php if (LS_CLIENT_SEMAIL) { ?>
			<div class="form-group">
			    <label class="sr-only" for="email"><?php echo $tl["general"]["g5"];?></label>
			    	<div class="input-group">
			    		<span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
						<input type="text" name="email" id="email" class="form-control input-sm" value="<?php echo $_REQUEST["email"];?>" placeholder="<?php echo $tl["general"]["g5"];?>">
					</div>
			</div>
			<?php } if (LS_CLIENT_SPHONE) { ?>
			<div class="form-group">
			    <label class="sr-only" for="phone"><?php echo $tl["general"]["g49"];?></label>
			    	<div class="input-group">
			    		<span class="input-group-addon"><span class="glyphicon glyphicon-bell"></span></span>
						<input type="text" name="phone" id="phone" class="form-control input-sm" value="<?php echo $_REQUEST["email"];?>" placeholder="<?php echo $tl["general"]["g49"];?>">
					</div>
			</div>
			
			<?php } if (LS_CLIENT_SQUESTION) { ?>
			<div class="form-group">
			    <label class="sr-only" for="question"><?php echo $tl["general"]["g71"];?></label>
			    	<div class="input-group">
			    		<span class="input-group-addon"><span class="glyphicon glyphicon-question-sign"></span></span>
						<input type="text" name="question" id="question" class="form-control input-sm" value="<?php echo $_REQUEST["question"];?>" placeholder="<?php echo $tl["general"]["g71"];?>">
					</div>
			</div>
			
			<?php } if ($op_direct == 0 && $dep_direct != 0 && is_numeric($dep_direct)) { ?>
				<input type="hidden" name="department" value="<?php echo $dep_direct;?>" />
			<?php } elseif ($op_direct == 0 && count($lv_departments) > 1) { ?>
				<div class="form-group">
				    <label class="sr-only" for="department"><?php echo $tl["general"]["g30"];?></label>
					<select name="department" id="department" class="form-control input-sm" size="1">
						<?php foreach($lv_departments as $v) { ?><option value="<?php echo $v["id"];?>"<?php if ($_REQUEST["department"] == $v["id"]) { ?> selected="selected"<?php } ?>><?php echo $v["title"];?></option><?php } ?>
						</select>
				</div>
			<?php } else { ?>
				<input type="hidden" name="department" value="<?php echo $lv_departments[0]["id"];?>" />
				<input type="hidden" name="opdirect" value="<?php echo $op_direct;?>" />
			<?php } ?>
		
			<button type="submit" class="btn btn-small btn-block btn-primary ls-submit"><?php echo $tl["general"]["g10"];?></button>
			
			<input type="hidden" name="start_chat" value="1" />
			<input type="hidden" name="slide_chat" value="<?php echo $_GET['slide'];?>" />
			<input type="hidden" name="lang" value="<?php echo $_GET['lang'];?>" />
			
		</form>
		
	</div>
	
	<hr>
	
	<footer><a href="//ovessence.com" target="_blank">Live Chat Ovessence</a></footer>
</div>