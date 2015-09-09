<div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title"><?php echo $tl["menu"]["m1"];?></h4>
    </div>
    <div class="modal-body">
		<div class="padded-box">

			<?php if (isset($LEADS_ALL) && is_array($LEADS_ALL)) { ?>
			
			<div class="panel-group" id="accordion">
			<?php foreach($LEADS_ALL as $v) { ?>
			  <div class="panel panel-default">
			    <div class="panel-heading">
			      <h4 class="panel-title">
			        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $v["id"];?>">
			          <?php echo $v["name"];?>
			        </a>
			        <span class="label label-default pull-right"><?php echo LS_base::lsTimesince($v['initiated'], LS_DATEFORMAT, LS_TIMEFORMAT);?></span>
			      </h4>
			    </div>
			    <div id="collapse<?php echo $v["id"];?>" class="panel-collapse collapse">
			      <div class="panel-body">
			        <ul class="list-group">
			        <?php if (isset($v['chat']) && is_array($v['chat'])) foreach($v['chat'] as $z) {
			        
			        	if ($z['class'] == "notice") {
			            	echo '<li class="list-group-item '. $z['class'] .'"><span class="response_sum">'.$z['name'] .' '.$tl['general']['g66'].':</span><div class="chat-text">'.$z['message'].'</div></li>';
			            } else {
			                echo '<li class="list-group-item '. $z['class'] .'"><span class="badge">'.$z['time'].'</span><span class="response_sum">'.$z['name'].' '.$tl['general']['g66'].':</span><div class="chat-text">'.$z['message'].'</div></li>';
			            }
			            
			        } ?>
			        </ul>
			      </div>
			    </div>
			  </div>
			  <?php } ?>
			  
			</div>
			
			<?php } else { ?>
			
			<div class="alert alert-info">
			<?php echo $tl["errorpage"]["data"];?>
			</div>
			
			<?php } ?>
</div>

</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $tl["general"]["g180"];?></button>
	</div>