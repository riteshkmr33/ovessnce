<div class="navbar navbar-default">
	<div class="container">
    	<div class="navbar-header">
        	<a class="navbar-brand" href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']);?>"><img src="img/logo.png" alt="logo" /> <?php echo $tl["general"]["g"];?> - <?php echo LS_TITLE;?></a>
    	</div>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<?php echo $tl["general"]["g67"];?>
		</div>
	</div>
	<hr>
	<div class="jrc_chat_form">
			
		<?php if ($errors) { ?>
			<div class="alert alert-danger"><?php echo $errors["name"].$errors["email"];?></div>
		<?php } ?>
		
		<div class="ls-thankyou"></div>
				
		<!--- Chat Rating -->
		<form role="form" class="ls-ajaxform" action="<?php echo htmlentities($_SERVER['REQUEST_URI']);?>" method="post">
		
					<div class="form-group">
					    <label class="control-label" for="vote5"><?php echo $tl["general"]["g23"];?></label>
							<div class="rate-result"><?php echo $tl["general"]["g29"];?>: <span id="stars-cap"></span></div>
							<div id="starify">
								<label for="vote1"><input type="radio" name="fbvote" id="vote1" value="1" title="Poor" /> Poor</label>
								<label for="vote2"><input type="radio" name="fbvote" id="vote2" value="2" title="Fair" /> Fair</label>
								<label for="vote3"><input type="radio" name="fbvote" id="vote3" value="3" title="Average" /> Average</label>
								<label for="vote4"><input type="radio" name="fbvote" id="vote4" value="4" title="Good" /> Good</label>
								<label for="vote5"><input type="radio" name="fbvote" id="vote5" value="5" title="Excellent" checked="checked" /> Excellent</label>
							</div>
					</div>
					
					<div class="clearfix"></div>
						
					<div class="form-group">
					    <label class="control-label" for="name"><?php echo $tl["general"]["g4"].$tl["general"]["g26"];?></label>
						<input type="text" name="name" id="name" class="form-control" value="<?php echo $_SESSION['jrc_name'];?>" />
					</div>
					
					<div class="form-group">
					    <label class="control-label" for="email"><?php echo $tl["general"]["g5"].$tl["general"]["g26"];?></label>
							<input type="text" name="email" id="email" class="form-control" value="<?php if ($_SESSION['jrc_email'] != $tl['general']['g12']) echo $_SESSION['jrc_email'];?>" />
					</div>
					
					<div class="form-group">
					    <label class="control-label" for="message"><?php echo $tl["general"]["g24"].$tl["general"]["g26"];?></label>
					    <textarea name="message" id="message" rows="5" class="form-control"></textarea>
					</div>
					
					<div class="checkbox">
						<label>
					      <input type="checkbox" name="send_email" id="send_email"> <?php echo $tl["general"]["g38"];?>
						</label>
					</div>
					
					<div class="form-actions">
						<button type="submit" class="btn btn-primary btn-block ls-submit"><?php echo $tl["general"]["g25"];?></button>
					</div>
					
					<input type="hidden" name="send_feedback" value="1" />
					<input type="hidden" name="convid" value="<?php echo $_SESSION['convid'];?>" />
				
			</form>
			
			</div>
			
	<hr>
			
	<footer><a href="http://www.livesupportrhino.com" target="_blank">Live Chat Rhino</a></footer>
			
</div>