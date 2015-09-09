<!--- Container -->
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<?php if (isset($_SESSION['chatbox_redirected'])) {
				echo $tl["general"]["g64"];
			} else {
				echo $tl["general"]["g61"];
			}?>
		</div>
	</div>
	<hr>
	<div class="jrc_chat_form_slide">
		
		<?php if ($errors) { ?>
		<div class="alert alert-danger"><?php echo $errors["name"].$errors["email"].$errors["message"].$USR_IP_BLOCKED;?></div>
		<?php } ?>
		
				
		<?php if ($_SESSION['ls_msg_sent'] == 1) { ?>
			<div class="alert alert-success"><?php echo $tl["general"]["g65"];?></div>
			<div class="pull-center">
				<a href="<?php echo $parseurl;?>" class="btn btn-primary"<?php echo $parseid;?>><?php echo $tl["general"]["g3"];?></a>
			</div>
		
		<?php } ?>
		
		<div class="ls-thankyou"></div>
			
			<form role="form" class="ls-ajaxform" method="post" action="<?php echo htmlentities($_SERVER['REQUEST_URI']);?>">
			
				<div class="form-group">
				    <label class="sr-only" for="name"><?php echo $tl["general"]["g4"];?></label>
				    	<div class="input-group">
				    		<span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
							<input type="text" name="name" id="name" class="form-control input-sm" placeholder="<?php echo $tl["general"]["g4"];?>" />
						</div>
				</div>
				
				<div class="form-group">
				    <label class="sr-only" for="email"><?php echo $tl["general"]["g5"];?></label>
				    	<div class="input-group">
				    		<span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
							<input type="text" name="email" id="email" class="form-control input-sm" placeholder="<?php echo $tl["general"]["g5"];?>" />
						</div>
				</div>
				<?php if (LS_CLIENT_SPHONE) { ?>
				<div class="form-group">
				    <label class="sr-only" for="phone"><?php echo $tl["general"]["g49"];?></label>
				    	<div class="input-group">
				    		<span class="input-group-addon"><span class="glyphicon glyphicon-bell"></span></span>
							<input type="text" name="phone" id="phone" class="form-control input-sm" placeholder="<?php echo $tl["general"]["g49"];?>" />
						</div>
				</div>
				<?php } ?>
				<div class="form-group">
				    <label class="sr-only" for="message"><?php echo $tl["general"]["g6"];?></label>
				    <textarea name="message" id="message" rows="3" class="form-control" placeholder="<?php echo $tl["general"]["g6"];?>"></textarea>
				</div>
					
				<button type="submit" class="btn btn-block btn-primary ls-submit"><?php echo $tl["general"]["g7"];?></button>
				
				<input type="hidden" name="send_email" value="1" />
				<input type="hidden" name="department" value="<?php echo $_GET['dep'];?>" />
				
			</form>
			
		</div>
		
		<hr>
			
	<footer><a href="http://www.livesupportrhino.com" target="_blank">Live Chat Rhino</a></footer>
			
</div>