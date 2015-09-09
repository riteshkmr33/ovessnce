<div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title"><?php echo $tl["menu"]["m1"];?></h4>
    </div>
    <div class="modal-body">
    	
    	<div class="ls-thankyou"></div>
    	
		<form role="form" class="ls-ajaxform" method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']);?>">
		
			<div class="form-group">
			    <label class="control-label" for="name"><?php echo $tl["general"]["g54"];?></label>
				<input type="text" name="name" id="name" class="form-control" value="<?php echo $rowi['name'];?>" />
			</div>
			
			<div class="form-group">
			    <label class="control-label" for="email"><?php echo $tl["general"]["g220"];?></label>
				<input type="text" name="email" id="email" class="form-control" value="<?php echo $rowi['email'];?>" />
			</div>
			<div class="form-group">
			    <label class="control-label" for="subject"><?php echo $tl["general"]["g221"];?></label>
				<input type="text" name="subject" id="subject" class="form-control" placeholder="<?php echo $tl["general"]["g221"];?>" />
			</div>
			<div class="form-group">
			    <label class="control-label" for="message"><?php echo $tl["general"]["g146"];?></label>
			    <textarea name="message" id="message" rows="5" class="form-control"></textarea>
			</div>
				
			<button type="submit" class="btn btn-primary btn-block ls-submit"><?php echo $tl["general"]["g4"];?></button>
			
			<input type="hidden" name="send_email" value="1" />
			
		</form>
		
		<?php if (isset($MESSAGES_ALL) && is_array($MESSAGES_ALL)) { ?>
		<hr>
		<h3><?php echo $tl["general"]["g222"];?></h3>
		<div class="panel-group" id="accordion">
		<?php foreach($MESSAGES_ALL as $v) { ?>
		  <div class="panel panel-default">
		    <div class="panel-heading">
		      <h4 class="panel-title">
		        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $v["id"];?>">
		          <?php echo $v["operator"];?> / <?php echo LS_base::lsTimesince($v['sent'], LS_DATEFORMAT, LS_TIMEFORMAT);?>
		        </a>
		      </h4>
		    </div>
		    <div id="collapse<?php echo $v["id"];?>" class="panel-collapse collapse">
		    <div class="panel-body">
		    	<h3><?php echo $v["subject"];?></h3>
		      	<p><?php echo $v["message"];?></p>
		    </div>
		   </div>
		  </div>
		  <?php } ?>
		  
		</div>
		
		<?php } ?>
		
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $tl["general"]["g180"];?></button>
	</div>

<script type="text/javascript" src="../js/contact.js"></script>

<script type="text/javascript">
	ls.main_url = "<?php echo BASE_URL;?>";
	ls.lsrequest_uri = "<?php echo LS_PARSE_REQUEST;?>";
	ls.ls_submit = "<?php echo $tl['general']['g4'];?>";
	ls.ls_submitwait = "<?php echo $tl['general']['g67'];?>";
</script>
