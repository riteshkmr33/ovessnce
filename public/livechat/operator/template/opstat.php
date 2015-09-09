<div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title"><?php echo $tl["general"]["g137"];?></h4>
</div>
<div class="modal-body">

	<h4><?php echo $tl["general"]["g137"];?></h4>
	<strong><?php echo $tl["user"]["u"].':</strong> '.$row["name"];?><br />
	<strong><?php echo $tl["user"]["u2"].':</strong> '.$row["username"];?>
	
	<hr>
	
	<h4><?php echo $tl["general"]["g5"];?></h4>
	<div class="alert alert-success">
	<?php echo $row1["totalAll"];?>
	</div>
	
	<hr>

	<h4><?php echo $tl["general"]["g89"];?></h4>
	<?php if ($row2['total_support']) { ?>
	<p><?php echo '<strong>'.$tl["general"]["g90"].':</strong> '.gmdate('H:i:s', $row2['total_support']).'<br /><strong>'.$tl["general"]["g91"].':</strong> '.round(($row2['total_vote'] / $row2["totalAll"]), 2);?>/5</p>
	<?php } else { ?>
	<div class="alert alert-info">
	<?php echo $tl["errorpage"]["data"];?>
	</div>
	<?php } ?>
	    
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $tl["general"]["g180"];?></button>
</div>