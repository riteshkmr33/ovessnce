<div class="navbar navbar-default">
	<div class="container">
    	<div class="navbar-header">
        	<a class="navbar-brand" href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']);?>">
                    <img src="https://s3-us-west-2.amazonaws.com/ovessence/img/logo.png" alt="logo" style="width:28%" /> <?php echo $tl["general"]["g"];?> - <?php echo LS_TITLE;?>
                </a>
    	</div>
	</div>
</div>

<!--- Container -->
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<?php if (isset($_SESSION['chatbox_redirected'])) {
				echo '<p>'.$tl["general"]["g64"].'</p>';
			} else {
				echo '<p>'.$tl["general"]["g61"].'</p>';
			}?>
		</div>
	</div>
	<hr>
	<div class="jrc_chat_form">
		
		<?php if ($errors) { ?>
		<div class="alert alert-danger"><?php echo $errors["name"].$errors["email"].$errors["message"].$USR_IP_BLOCKED;?></div>
		<?php } ?>
		
				
		<?php if ($_SESSION['ls_msg_sent'] == 1) { ?>
			<div class="alert alert-success"><?php echo $tl["general"]["g65"];?></div>
			<div class="pull-center">
				<a href="javascript:window.close();" class="btn btn-primary"><?php echo $tl["general"]["g3"];?></a>
			</div>
		
		<?php } ?>
		
		<div class="ls-thankyou"></div>
			
			<form role="form" class="ls-ajaxform" method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']);?>">
			
				<div class="form-group">
				    <label class="control-label" for="name"><?php echo $tl["general"]["g4"];?></label>
					<input type="text" name="name" id="name" class="form-control" placeholder="<?php echo $tl["general"]["g4"];?>" />
				</div>
				
				<div class="form-group">
				    <label class="control-label" for="email"><?php echo $tl["general"]["g5"];?></label>
					<input type="text" name="email" id="email" class="form-control" placeholder="<?php echo $tl["general"]["g5"];?>" />
				</div>
				<?php if (LS_CLIENT_SPHONE) { ?>
				<div class="form-group">
				    <label class="control-label" for="phone"><?php echo $tl["general"]["g49"];?></label>
					<input type="text" name="phone" id="phone" class="form-control" placeholder="<?php echo $tl["general"]["g49"];?>" />
				</div>
				<?php } ?>
				<div class="form-group">
				    <label class="control-label" for="message"><?php echo $tl["general"]["g6"];?></label>
				    <textarea name="message" id="message" rows="5" class="form-control"></textarea>
				</div>
				
				<button type="submit" class="btn btn-primary btn-block ls-submit"><?php echo $tl["general"]["g7"];?></button>
				
				<input type="hidden" name="send_email" value="1" />
				<input type="hidden" name="department" value="<?php echo $_GET['dep'];?>" />
				
			</form>
		</div>

		<hr>
			
	<footer><a href="//ovessence.com" target="_blank">Live Chat Ovessence</a></footer>
			
</div>