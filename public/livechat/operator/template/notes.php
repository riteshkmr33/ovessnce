<div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title"><?php echo LS_TITLE;?></h4>
    </div>
    <div class="modal-body">
<div class="padded-box">

<div id="contact-container">
<form id="cNotes" role="form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

	 <div class="form-group">
	    <label for="note"><?php echo $tl["general"]["g181"];?></label>
		<textarea name="note" id="note" rows="5" class="form-control"><?php echo $LS_FORM_DATA['notes'];?></textarea>
	</div>
	
	<button type="submit" id="formsubmit" class="btn btn-primary btn-block"><?php echo $tl["general"]["g38"];?></button>
	
	<input type="hidden" name="convid" value="<?php if (is_numeric($page1)) { echo $page1; } else { echo $page2;}?>">
</form>
</div>

</div>

</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $tl["general"]["g180"];?></button>
	</div>

<script type="text/javascript" src="js/notes.js"></script>

<script type="text/javascript">
	ls.main_url = "<?php echo BASE_URL;?>";
	ls.lsrequest_uri = "<?php echo LS_PARSE_REQUEST;?>";
	ls.ls_submit = "<?php echo $tl['general']['g38'];?>";
	ls.ls_submitwait = "<?php echo $tl['general']['g67'];?>";
</script>

