<?php include "header.php";?>

<!-- START wrapper-->
<div style="height: 100%; padding: 20px 0; background-color: #2c3037" class="row row-table">
   <div class="col-lg-3 col-md-6 col-sm-8 col-xs-12 align-middle">
      <div data-toggle="play-animation" data-play="zoomIn" data-offset="0" data-duration="300" class="panel b0">
         <p class="text-center mb-lg">
            <br>
            <a href="#">
               <!--<img src="img/logo.png" alt="logo" class="block-center img-rounded">-->
               <img src="https://s3-us-west-2.amazonaws.com/ovessence/img/logo.png" alt="logo" class="block-center img-rounded">
            </a>
         </p>
         <div id="accordion" data-toggle="collapse-autoactive" class="panel-group">
            <!-- START panel-->
            <div class="panel radius-clear b0 shadow-clear">
               <div class="panel-heading radius-clear panel-heading-active"><a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" class="text-muted btn-block text-center"><?php echo $tl["login"]["l3"];?></a>
               </div>
               <div id="collapseOne" class="panel-collapse collapse in">
                  <div class="panel-body">
                  	<form role="form" id="login_form" class="mb-lg" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
                        <div class="form-group<?php if ($ErrLogin) echo " has-error";?>">
                        	<label for="username" class="sr-only"><?php echo $tl["login"]["l1"];?>/<?php echo $tl["login"]["l5"];?></label>
                            <div class="input-group">
                              <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                              <input type="text" name="username" id="username" class="form-control" placeholder="<?php echo $tl["login"]["l1"];?>/<?php echo $tl["login"]["l5"];?>">
                            </div>
                        </div>
                        <div class="form-group<?php if ($ErrLogin) echo " has-error";?>">
                            <label for="password" class="sr-only"><?php echo $tl["login"]["l2"];?></label>
                            <div class="input-group">
                              <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
                              <input type="password" name="password" id="password" class="form-control" placeholder="<?php echo $tl["login"]["l2"];?>">
                            </div>
                        </div>
                        <div class="clearfix">
                           <div class="checkbox c-checkbox pull-left mt0">
                              <label>
                                 <input type="checkbox" name="lcookies" value="">
                                 <span class="fa fa-check"></span><?php echo $tl["login"]["l4"];?></label>
                           </div>
                        </div>
                        <input type="hidden" name="action" value="login">
                        <button type="submit" name="logID" class="btn btn-primary btn-block"><?php echo $tl["login"]["l3"];?></button>
                     </form>
                  </div>
               </div>
            </div>
            <!-- END panel-->
            <!-- START panel-->
            <div class="panel radius-clear b0 shadow-clear">
               <div class="panel-heading radius-clear"><a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" class="text-muted btn-block text-center"><?php echo $tl['login']['l13'];?></a>
               </div>
               <div id="collapseTwo" class="panel-collapse collapse">
                  <div class="panel-body">
                     <form role="form" action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
                        <div class="form-group has-feedback<?php if ($errorfp) echo " has-error";?>">
                        	<label for="email" class="sr-only"><?php echo $tl["login"]["l5"];?></label>
                        	<div class="input-group">
                           		<span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
                           		<input type="text" name="lsE" id="email" class="form-control" placeholder="<?php echo $tl["login"]["l5"];?>" />
                           	</div>
                        </div>
                        <button type="submit" name="forgotP" class="btn btn-danger btn-block"><?php echo $tl["general"]["g229"];?></button>
                     </form>
                  </div>
               </div>
            </div>
            <!-- END panel-->
         </div>
      </div>
      <hr>
      
      <footer>Copyright <?=date('Y');?> by <a href="//ovessence.com">Live Chat Ovessence</a></footer>
   </div>
</div>
<!-- END wrapper-->

<script type="text/javascript">

$(document).ready(function() {
	
	<?php if ($errorfp) { ?>
	$("#collapseOne, #collapseTwo").collapse('toggle');
	<?php } if (isset($_SESSION['password_recover'])) { ?>
	
	$.notify('<em class="fa fa-info"></em> <?php echo $tl['login']['l7'];?>', {status:'success',pos:'top-right'});
	
	<?php } ?>
		
});

</script>

<?php include "footer.php";?>