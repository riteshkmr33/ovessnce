<?php include_once APP_PATH.'operator/template/header.php';?>

<div class="form-signin">

<div class="loginF">

<h3><?php echo $tl['general']['g94'];?></h3>

<?php if ($errorsf) { ?>
<div class="alert alert-danger"><?php echo $errorsf["e"].$errorsf["e1"];?></div>
<?php } ?>

	<form role="form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		
		<div class="form-group<?php if ($errorsf["e"]) { ?> error<?php } ?>">
		    <label class="control-label" for="email"><?php echo $tl["login"]["l5"];?></label>
		     <input type="text" name="f_email" id="email" class="form-control" placeholder="<?php echo $tl["login"]["l5"];?>">
		  </div>
		  <div class="form-group<?php if ($errorsf["e1"]) { ?> error<?php } ?>">
		    <label class="control-label" for="password"><?php echo $tl["login"]["l2"];?></label>
		    <input type="password" name="f_pass" id="password" class="form-control" placeholder="<?php echo $tl["login"]["l2"];?>">
		  </div>
		  <div class="form-group<?php if ($errorsf["e1"]) { ?> error<?php } ?>">
		    <label class="control-label" for="password_r"><?php echo $tl["login"]["l9"];?></label>
		    <input type="password" name="f_newpass" id="password_r" class="form-control" placeholder="<?php echo $tl["login"]["l9"];?>">
		  </div>
		  
		  <button type="submit" name="newP" class="btn btn-primary btn-block"><?php echo $tl["login"]["l8"];?></button>
		
	</form>

</div>

</div>

<?php include_once APP_PATH.'operator/template/footer.php';?>