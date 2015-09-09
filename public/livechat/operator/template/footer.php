<?php if ($LS_PROVED) { ?>

<hr>

<footer>Copyright <?=date('Y');?> by <a href="//ovessence.com">Ovessence Chat</a><?php if (LS_SUPERADMINACCESS) echo ' ('.$tl['general']['g118'].LS_VERSION.')';?><?php if ($LS_PROVED) { ?> | <a href="index.php?p=logout" onclick="if(!confirm('<?php echo $tl["logout"]["l2"];?>'))return false;"><?php echo $tl["logout"]["l"];?></a><?php } ?></footer>

<span id="audio_alert"></span>

</section><!-- Main -->
</section><!-- in between -->
</section><!-- wrapper -->
<?php } ?>

<script type="text/javascript" src="js/app.js?=<?php echo LS_UPDATED;?>"></script>

<script type="text/javascript">
$(document).ready(function(){
	ls.ls_online = "<?php echo $tl['general']['g'];?>";
	ls.ls_offline = "<?php echo $tl['general']['g1'];?>";
	ls.ls_busy = "<?php echo $tl['general']['g202'];?>";
	ls.ls_alert = "<?php echo $tl['general']['g2'];?>";
	<?php if ($LS_PROVED) { ?>
	ls.usrAvailable = <?php echo $lsuser->getVar("available");?>;
	ls.ls_dnotify = "<?php echo $lsuser->getVar("dnotify");?>";
	ls.ls_ringing = "<?php echo $lsuser->getVar("ringing");?>";
	// sound
	ls.muted = <?php echo $lsuser->getVar("sound");?>;
	<?php } ?>
	$('#generalModal').on('hidden.bs.modal', function () {
	  $(this).removeData();
	});
});
</script>

<?php if ($LS_PROVED) { ?>

<!-- New Pro Active Invitation -->
<div id="proActiveModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="proActiveLabel" aria-hidden="true">
	<div class="modal-dialog">
	  <div class="modal-content">
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	      <h4 class="modal-title"><?php echo $tl["user"]["u12"];?></h4>
	    </div>
	    <div class="modal-body">
			<input type="text" name="proactivemsg" id="proactivemsg" class="form-control" value="<?php echo $lsuser->getVar("invitationmsg"); ?>" />
			<input type="hidden" name="proactiveuid" id="proactiveuid" value="" />
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $tl["general"]["g103"];?></button>
			<button class="btn btn-primary" onclick="sendInvitation();"><?php echo $tl["general"]["g4"];?></button>
		</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- GeneralModal -->
<div id="generalModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="generalModal" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"></div></div></div><!-- /.modal -->

<?php if ($lic_nr) { ?>

<!-- Modal -->
<div id="licModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="licModalReminder" aria-hidden="true">
	<div class="modal-dialog">
	  <div class="modal-content">
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	      <h4 class="modal-title">Order Number</h4>
	    </div>
	    <div class="modal-body">
			<form role="form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			
				<div class="form-group">
				    <label for="license">Order Number</label>
					<input type="text" name="license" id="license" class="form-control" placeholder="Order Number" />
				</div>
				
				<button type="submit" name="insert_lic" class="btn btn-primary"><?php echo $tl["general"]["g38"];?></button>
				
			</form>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $tl["general"]["g180"];?></button>
		</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">$(document).ready(function(){ $('#licModal').modal('show'); });</script>

<?php } } ?>
</body>
</html>