<div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title"><?php echo $tl["stat"]["s12"];?></h4>
    </div>
    <div class="modal-body">
<div class="padded-box">
<h4><?php echo $tl["general"]["g81"].' '.$page3;?></h4>

<ul class="list-group">
<?php if (isset($USER_FEEDBACK) && is_array($USER_FEEDBACK)) foreach($USER_FEEDBACK as $v) {

	if ($LS_SPECIALACCESS) { 
			
		echo '<li class="list-group-item" id="stat'.$v['id'].'"><span class="btn btn-xs btn-danger glyphicon glyphicon-remove delete-stat pull-right" id="'.$v['id'].'" onclick="if(!confirm(\''.$tl["error"]["e30"].'\'))return false;"></span><h4>'.$v['time'].' - '.$tl['general']['g86'].':</h4><span class="usr_rate">'.$tl['general']['g85'].': </span>'.$v['vote'].'/5<br />'.$tl['general']['g54'].': '.$v['name'].'<br />'.$tl['login']['l5'].': '.$v['email'].'<br />'.$tl['stat']['s12'].': '.$v['comment'].'<br />'.$tl['general']['g87'].': '.gmdate('H:i:s', $v['support_time']).'</li>';
		
	} else {
	
		echo '<li class="list-group-item"><h4>'.$v['time'].' - '.$tl['general']['g86'].':</h4><span class="usr_rate">'.$tl['general']['g85'].': </span>'.$v['vote'].'/5<br />'.$tl['general']['g54'].': '.$v['name'].'<br />'.$tl['login']['l5'].': '.$v['email'].'<br />'.$tl['stat']['s12'].': '.$v['comment'].'<br />'.$tl['general']['g87'].': '.gmdate('H:i:s', $v['support_time']).'</li>';
		
	}
	
	$count++;
    
} else {  

	echo '<li class="list-group-item">'.$tl["errorpage"]["data"].'</li>';

} ?>
</ul>

<?php if (isset($USER_FEEDBACK) && is_array($USER_FEEDBACK)) { ?>

<h4><?php echo $tl["general"]["g89"];?></h4>
<p><?php echo '<strong>'.$tl["general"]["g90"].':</strong> '.gmdate('H:i:s', $USER_SUPPORTT).'<br /><strong>'.$tl["general"]["g91"].':</strong> '.round(($USER_VOTES / $count), 2);?>/5</p>



<div class="ls-thankyou"></div>

<form role="form" class="ls-ajaxform" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

	<div class="form-group">
	    <label class="control-label" for="email"><?php echo $tl["general"]["g93"];?></label>
		<input type="text" name="email" id="email" class="form-control" placeholder="<?php echo $tl["general"]["g68"];?>" />
	</div>
	
	<div class="form-actions">
		<button type="submit" id="formsubmit" class="btn btn-primary ls-submit"><?php echo $tl["general"]["g4"];?></button>
	</div>
	
	<input type="hidden" name="email_feedback" value="1" />
	<input type="hidden" name="convid" value="<?php echo $page2;?>">
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
	
	
	<?php if ($LS_SPECIALACCESS) { ?>
	
		$('.delete-stat').click(function() {
		
			var sid = $(this).attr('id');
			
			var request = $.ajax({
			  url:  'ajax/delstat.php',
			  type: "POST",
			  data: "sid="+sid,
			  dataType: "json",
			  cache: false
			});
			
			request.done(function(msg) {
				
				if (msg.status) {
					$("#stat"+sid).fadeOut();
				} else {
					alert("<?php echo $tl["errorpage"]["not"];?>");
				}
			});
			
		});
	
	<?php } ?>
	
</script>