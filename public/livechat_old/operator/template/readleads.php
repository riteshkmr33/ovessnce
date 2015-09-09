<div class="padded-box">
<p><?php echo $tl["general"]["g57"];?> <strong><?php echo $CONV_AGENT;?></strong> <?php echo $tl["general"]["g58"];?> <strong><?php echo $CONV_USER;?></strong></p>
<div id="archive">
<ul>
<?php if (isset($CONVERSATION_LS) && is_array($CONVERSATION_LS)) foreach($CONVERSATION_LS as $v) {

	if ($v['class'] == "notice") {
    	echo '<li class="'. $v['class'] .'"><span class="user_said">'.$v['name'] .' '.$tl['general']['g66'].':</span><br />'.$v['message'].'</li>';
    } else {
        echo '<li class="'. $v['class'] .'"><span class="user_said">'.$v['time'].' - '.$v['name'].' '.$tl['general']['g66'].':</span><br /> '.$v['message'].'</li>';
    }
    
} ?>
</ul>
</div>

<div id="thank-you"></div>
<div id="contact-container">
<form id="cSubmit" class="form-inline" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

	<div class="control-group">
	    <label class="control-label" for="email"><?php echo $tl["general"]["g59"];?></label>
	    <div class="controls">
			<input type="text" name="email" id="email" class="input-large" placeholder="<?php echo $tl["general"]["g68"];?>" />
		</div>
	</div>
	
	<div class="form-actions">
		<button type="submit" id="formsubmit" class="btn btn-primary pull-right"><?php echo $tl["general"]["g4"];?></button>
	</div>
	
	<input type="hidden" name="convid" value="<?php echo $page2;?>">
	<input type="hidden" name="cagent" value="<?php echo $CONV_AGENT;?>">
	<input type="hidden" name="cuser" value="<?php echo $CONV_USER;?>">
	<input type="hidden" name="email_conv" value="1" />
</form>
</div>
</div>


<script type="text/javascript" src="../js/contact.js"></script>

<script type="text/javascript">
	ls.main_url = "<?php echo BASE_URL;?>";
	ls.lsrequest_uri = "<?php echo LS_PARSE_REQUEST;?>";
	ls.ls_submit = "<?php echo $tl['general']['g4'];?>";
	ls.ls_submitwait = "<?php echo $tl['general']['g67'];?>";
</script>
