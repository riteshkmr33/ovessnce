<div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title"><?php echo $tl["general"]["g224"];?></h4>
    </div>
    <div class="modal-body">
    
    	<div class="table-responsive">
    	  <table class="table table-condensed">
    	  <thead>
    	  <tr>
    	  <th><?php echo $tl["general"]["g54"];?></th>
    	  <th><?php echo $tl["general"]["g12"];?></th>
    	  <th><?php echo $tl["general"]["g11"];?></th>
    	  </tr>
    	    <tr>
    	    	<td><?php echo $row['name'];?></td>
    	    	<td><?php echo $row['country'];?> / <?php echo $row['city'];?></td>
    	    	<td><?php echo $row['ip'];?></td>
    	    </tr>
    	  </table>
    	</div>
    
    	<h3><?php echo $tl["general"]["g224"];?></h3>
		
		<div id="clientmap-canvas" style="height: 300px;margin: 0px;padding: 0px;"></div>
		
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $tl["general"]["g180"];?></button>
	</div>
	
	<script type="text/javascript">
	function loadmap() {
	<?php if ($row['latitude']) { ?>
	  	var myLatlng = new google.maps.LatLng(<?php echo $row['latitude'];?>,<?php echo $row['longitude'];?>);
	  	var mapOptions = {
	  	    zoom: 8,
	  	    center: myLatlng
	  	  }
	  	  var map = new google.maps.Map(document.getElementById('clientmap-canvas'), mapOptions);
	  	
	  	  var marker = new google.maps.Marker({
	  	      position: myLatlng,
	  	      map: map,
	  	      title: '<?php echo $row['name'];?>',
	  	      icon: 'img/flag_green.png',
	  	      shadow: 'img/flag_shadow.png'
	  	  });
	<?php } else { ?>
		$('#clientmap-canvas').addClass("text-danger").html("<?php echo $tl['errorpage']['data'];?>");
	<?php } ?>
	  
	}
	
	$(document).ready(function() {
		loadmap();
	});
	
	</script>