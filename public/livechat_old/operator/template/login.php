<?php include "header.php";?>

<div class="form-signin">

<?php if ($ErrLogin) { ?>
<div class="alert alert-error">
<button type="button" class="close" data-dismiss="alert">×</button>
<a class="lost-pwd" href="#"><?php echo $tl["error"]["f"];?></a>
</div>
<?php } ?>

<?php if (isset($_SESSION['password_recover'])) {

	echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">×</button>'.$tl['login']['l7'].'</div>';

} ?>

<div class="loginF">
<h2><?php echo $tl["login"]["l3"];?></h2>
<form id="login_form" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
<div class="control-group">
    <label class="control-label" for="username"><?php echo $tl["login"]["l1"];?></label>
    <div class="controls">
      <input type="text" name="username" id="username" placeholder="<?php echo $tl["login"]["l1"];?>">
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="password"><?php echo $tl["login"]["l2"];?></label>
    <div class="controls">
      <input type="password" name="password" id="password" placeholder="<?php echo $tl["login"]["l2"];?>">
    </div>
  </div>
  <div class="control-group">
    <div class="controls">
      <label class="checkbox">
        <input type="checkbox" name="lcookies"> <?php echo $tl["login"]["l4"];?>
      </label>
      <input type="hidden" name="action" value="login" />
      <button type="submit" name="logID" class="btn"><?php echo $tl["login"]["l3"];?></button>
    </div>
  </div>
</form>

</div>

<div class="forgotP hide">
<h3><?php echo $tl["login"]["l13"];?></h3>
<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
	<input type="text" name="lsE" id="email" value="" placeholder="<?php echo $tl["login"]["l5"];?>" />
<p><button type="submit" name="forgotP" class="btn block"><?php echo $tl["general"]["g39"];?></button></p>
</form>
<?php if ($errorfp) { ?><div class="alert alert-error"><?php echo $errorfp["e"];?></div><?php } ?>
<a class="lost-pwd" href="#"><?php echo $tl["general"]["g3"];?></a>
</div>
  
</div>

<script type="text/javascript">

$(document).ready(function() {
	
	// Switch buttons from "Log In | Register" to "Close Panel" on click
	$(".lost-pwd").click(function(e) {
		e.preventDefault();
		$(".loginF").slideToggle();
		$(".forgotP").slideToggle();
	});
	
	<?php if ($errorfp) { ?>
		$(".loginF").hide();
		$(".forgotP").show();
		$(".forgotP").addClass("shake");
	<?php } if ($ErrLogin) { ?>
		$(".loginF").addClass("shake");
	<?php } ?>
		
});

</script>

<?php include "footer.php";?>