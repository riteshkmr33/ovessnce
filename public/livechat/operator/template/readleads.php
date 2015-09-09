

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title"><?php echo $tl["menu"]["m1"];?></h4>
    </div>
    <div class="modal-body">
		<div class="padded-box">
		<p><?php echo $tl["general"]["g57"];?> <strong><?php echo $CONV_AGENT;?></strong> <?php echo $tl["general"]["g58"];?> <strong><?php echo $rowi['name'];?></strong></p>
		<p><i class="glyphicon glyphicon-user"></i> <?php echo $rowi['name'];?> <i class="glyphicon glyphicon-envelope"></i> <?php echo $rowi['email'];?> <i class="glyphicon glyphicon-headphones"></i> <?php echo $rowi['phone'];?> <i class="glyphicon glyphicon-globe"></i> <?php echo $rowi['ip'];?></p>
		<ul class="list-group">
		<?php if (isset($CONVERSATION_LS) && is_array($CONVERSATION_LS)) foreach($CONVERSATION_LS as $v) {
		
			if ($v['class'] == "notice") {
		    	echo '<li class="list-group-item '. $v['class'] .'"><span class="response_sum">'.$v['name'] .' '.$tl['general']['g66'].':</span><div class="chat-text">'.$v['message'].'</div></li>';
		    } else {
		        echo '<li class="list-group-item '. $v['class'] .'"><span class="badge">'.$v['time'].'</span><span class="response_sum">'.$v['name'].' '.$tl['general']['g66'].':</span><div class="chat-text">'.$v['message'].'</div></li>';
		    }
		    
		} ?>
		</ul>
		
		<?php if ($page3 == 1) { ?>
		
		<div class="ls-thankyou"></div>
		
		<form class="ls-ajaxform" role="form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			
			 <div class="form-group">
			    <label for="email"><?php echo $tl["general"]["g59"];?></label>
				<input type="text" name="email" id="email" class="form-control" placeholder="<?php echo $tl["general"]["g68"];?>" />
			</div>
			
			<button type="submit" class="btn btn-primary ls-submit"><?php echo $tl["general"]["g4"];?></button>
			
			<input type="hidden" name="email_conv" value="1" />
			<input type="hidden" name="convid" value="<?php echo $page2;?>">
			<input type="hidden" name="cagent" value="<?php echo $CONV_AGENT;?>">
			<input type="hidden" name="cuser" value="<?php echo $rowi['name'];?>">
		</form>
		
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
