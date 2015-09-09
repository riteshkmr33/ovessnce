<?php include_once APP_PATH.'operator/template/header.php';?>

<div class="span3 well">

<div class="loginF">

<h3><?php echo $tl['general']['g94'];?></h3>

<?php if ($errorsf) { ?>
<div class="alert alert-error"><?php echo $errorsf["e"].$errorsf["e1"];?></div>
<?php } ?>

	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		
		<div class="control-group<?php if ($errorsf["e"]) { ?> error<?php } ?>">
		    <label class="control-label" for="email"><?php echo $tl["login"]["l5"];?></label>
		    <div class="controls">
		      <input type="text" name="f_email" id="email" placeholder="<?php echo $tl["login"]["l5"];?>">
		    </div>
		  </div>
		  <div class="control-group<?php if ($errorsf["e1"]) { ?> error<?php } ?>">
		    <label class="control-label" for="password"><?php echo $tl["login"]["l2"];?></label>
		    <div class="controls">
		      <input type="password" name="f_pass" id="password" placeholder="<?php echo $tl["login"]["l2"];?>">
		    </div>
		  </div>
		  <div class="control-group<?php if ($errorsf["e1"]) { ?> error<?php } ?>">
		    <label class="control-label" for="password_r"><?php echo $tl["login"]["l9"];?></label>
		    <div class="controls">
		      <input type="password" name="f_newpass" id="password_r" placeholder="<?php echo $tl["login"]["l9"];?>">
		    </div>
		  </div>
		  
		  <button type="submit" name="newP" class="btn"><?php echo $tl["login"]["l8"];?></button>
		
	</form>

</div>

</div>

<?php include_once APP_PATH.'operator/template/footer.php';?>