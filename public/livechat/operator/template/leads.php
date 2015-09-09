<?php include_once APP_PATH.'operator/template/header.php';?>

<h3><?php echo $tl["menu"]["m1"];?></h3>

<!-- START summary widgets-->
<div class="row">
   <div class="col-md-6">
<!-- START widget-->
      <div data-toggle="play-animation" data-play="fadeInDown" data-offset="0" data-delay="100" class="panel widget">
         <div class="panel-body bg-primary">
            <div class="row row-table row-flush">
               <div class="col-xs-11">
                  <p class="mb0"><?php echo $tl["stat"]["s6"];?></p>
                  <h3 class="m0"><?php echo secondsToTime($rowts['total_support'], $tl['general']['g230']);?></h3>
               </div>
               <div class="col-xs-1 text-center">
                  <em class="fa fa-clock-o fa-2x">
                  </em>
               </div>
            </div>
         </div>
         <div class="panel-body">
            <!-- Bar chart-->
            <div class="text-center">
               <?php echo secondsToTime($rowts['total_support'], $tl['general']['g230']);?>
            </div>
         </div>
      </div>
   </div>
   <div class="col-md-6">
      <!-- START widget-->
      <div data-toggle="play-animation" data-play="fadeInDown" data-offset="0" data-delay="500" class="panel widget">
         <div class="panel-body bg-warning">
            <div class="row row-table row-flush">
               <div class="col-xs-11">
                  <p class="mb0"><?php echo $tl["stat"]["s19"];?></p>
                  <h3 class="m0"><?php echo $bounce_percentage;?>%</h3>
               </div>
               <div class="col-xs-1 text-center">
                  <em class="fa fa-users fa-2x"></em>
               </div>
            </div>
         </div>
         <div class="panel-body">
            <!-- Bar chart-->
            <div class="text-center">
               <?php echo str_replace("%s", $rowtc['totalAll'], $tl["stat"]["s20"]).str_replace("%s", $rowt['totalAll'], $tl["stat"]["s21"]);?>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- END summary widgets-->

<?php if (!$searchstatus) { if ($errors) { ?>
<div class="alert alert-danger fade in">
	<?php echo $errors["e"].$errors["e1"].$errors["e2"];?>
</div>
<?php } } if ($page1 == 'search' && $searchstatus) { ?>
<div class="alert alert-info fade in">
	<button type="button" class="close" data-dismiss="alert">×</button>
	<?php echo str_replace("%s", $searchword, $tl["search"]["s1"]);?>
</div>

<?php } ?>

<p>
<span class="label label-default"><?php echo $tl['general']['g225'];?></span>
<span class="label label-danger"><?php echo $tl['general']['g226'];?></span>
</p>

<?php if (isset($rowt['totalAll']) || (isset($LEADS_ALL) && is_array($LEADS_ALL))) { ?>

<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<div class="table-responsive">
<table class="table table-striped">
<thead>
<tr>
<th>#</th>
<th><input type="checkbox" id="ls_delete_all" /></th>
<th><?php echo $tl["general"]["g54"];?> <a href="index.php?p=leads&amp;sp=sort&amp;ssp=name&amp;sssp=DESC" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-arrow-up"></span></a> <a href="index.php?p=leads&amp;sp=sort&amp;ssp=name&amp;sssp=ASC" class="btn btn-warning btn-xs"><span class="glyphicon glyphicon-arrow-down"></span></a></th>
<th><?php echo $tl["login"]["l5"];?> <a href="index.php?p=leads&amp;sp=sort&amp;ssp=email&amp;sssp=DESC" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-arrow-up"></span></a> <a href="index.php?p=leads&amp;sp=sort&amp;ssp=email&amp;sssp=ASC" class="btn btn-warning btn-xs"><span class="glyphicon glyphicon-arrow-down"></span></a></th>
<th><?php echo $tl["general"]["g130"];?></th>
<th><?php echo $tl["general"]["g131"];?></th>
<th><?php echo $tl["general"]["g65"];?></th>
<th><?php echo $tl['general']['g224'];?></th>
<th><?php echo $tl["general"]["g181"];?></th>
<th><?php echo $tl["general"]["g13"];?> <a href="index.php?p=leads&amp;sp=sort&amp;ssp=initiated&amp;sssp=DESC" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-arrow-up"></span></a> <a href="index.php?p=leads&amp;sp=sort&amp;ssp=initiated&amp;sssp=ASC" class="btn btn-warning btn-xs"><span class="glyphicon glyphicon-arrow-down"></span></a></th>
<th><?php echo $tl["general"]["g11"];?> <?php if ($LS_SPECIALACCESS) { ?><a class="btn btn-warning btn-xs" href="index.php?p=leads&amp;sp=truncate" onclick="if(!confirm('<?php echo $tl["error"]["e40"];?>'))return false;"><span class="glyphicon glyphicon-warning-sign"></span></a><?php } ?></th>
<th class="content-go"><button type="submit" name="delete" id="button_delete" class="btn btn-danger btn-xs" onclick="if(!confirm('<?php echo $tl["error"]["e30"];?>'))return false;"><span class="glyphicon glyphicon-trash"></span></button></th>
</tr>
</thead>
<tbody id="jak_result"></tbody>
<?php if (isset($LEADS_ALL) && is_array($LEADS_ALL)) foreach($LEADS_ALL as $v) { ?>
<tr<?php if ($v['fcontact'] == 1) echo ' class="danger"';?>>
<td><?php echo $v["id"];?></td>
<td><input type="checkbox" name="ls_delete_leads[]" class="highlight" value="<?php echo $v["id"];?>" /></td>
<td><?php echo $v["name"]; if ($v['countrycode'] != 'xx') echo ' <img src="img/country/'.$v['countrycode'].'.gif" alt="nocountry" title="'.$v['country'].'/'.$v['city'].'" />';?></td>
<td><?php echo $v["email"]; if (filter_var($v['email'], FILTER_VALIDATE_EMAIL)) { ?> <a class="btn btn-default btn-xs" data-toggle="modal" href="index.php?p=leads&amp;sp=clientcontact&amp;ssp=<?php echo $v["id"];?>&amp;sssp=1" data-target="#generalModal"><span class="glyphicon glyphicon-envelope"></span></a><?php } ?></td>
<td><a href="index.php?p=leads&amp;sp=operator&amp;ssp=<?php echo $v["operatorid"];?>"><?php echo $v["username"];?></a></td>
<td><a href="index.php?p=leads&amp;sp=departement&amp;ssp=<?php echo $v["department"];?>"><?php echo $v["title"];?></a></td>
<td><a class="btn btn-default btn-xs" data-toggle="modal" href="index.php?p=leads&amp;sp=readleads&amp;ssp=<?php echo $v["id"];?>&amp;sssp=1" data-target="#generalModal"><span class="glyphicon glyphicon-eye-open"></span></a></td>
<td><a class="btn btn-default btn-xs" data-toggle="modal" href="index.php?p=leads&amp;sp=location&amp;ssp=<?php echo $v['id'];?>" data-target="#generalModal"><span class="glyphicon glyphicon-globe"></span></a></td>
<td><a class="btn<?php if ($v['notes']) { echo ' btn-success'; } else { echo ' btn-default';}?> btn-xs" data-toggle="modal" href="index.php?p=notes&amp;sp=<?php echo $v["id"];?>" data-target="#generalModal"><span class="glyphicon glyphicon-comment"></span></a></td>
<td><?php echo LS_base::lsTimesince($v['initiated'], LS_DATEFORMAT, LS_TIMEFORMAT);?></td>
<td><?php echo $v["ip"];?></td>
<td><?php if (LS_SUPERADMINACCESS) { ?><a class="btn btn-default btn-xs" href="index.php?p=leads&amp;sp=delete&amp;ssp=<?php echo $v["id"];?>" onclick="if(!confirm('<?php echo $tl["error"]["e33"];?>'))return false;"><span class="glyphicon glyphicon-trash"></span></a><?php } ?></td>
<td></td>
</tr>
<?php } ?>
</table>
</div>
</form>

<?php if (!isset($LEADS_ALL)) { ?>

<button class="btn btn-info btn-block load-more"><i class="fa fa-spinner fa-spin"></i> <?php echo $tl["general"]["g228"];?></button>

<?php } } else { ?>

<div class="alert alert-info">
<?php echo $tl["errorpage"]["data"];?>
</div>

<?php } ?>

<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
<script type="text/javascript" src="js/page.ajax.js"></script>

<!-- JavaScript for select all -->
<script type="text/javascript">
		$(document).ready(function() {
		
		<?php if (!isset($LEADS_ALL)) { ?>
		
			loadContent("leads_pages",<?php echo $total_pages;?>,"<?php echo $page2;?>","<?php echo $page3;?>");
		
		<?php } ?>
		
		setChecker(<?php echo $lsuser->getVar("id");?>);
		        setInterval("setChecker(<?php echo $lsuser->getVar("id");?>);", 10000);
		setTimer(<?php echo $lsuser->getVar("id");?>);
		        setInterval("setTimer(<?php echo $lsuser->getVar("id");?>);", 120000);
		        
			$("#ls_delete_all").click(function() {
				var checked_status = this.checked;
				$(".highlight").each(function()
				{
					this.checked = checked_status;
				});
			});
							
		});
		
		ls.main_url = "<?php echo BASE_URL_ADMIN;?>";
		ls.main_lang = "<?php echo LS_LANG;?>";
		ls.ls_submit = "<?php echo $tl['general']['g69'];?>";
		ls.ls_submitwait = "<?php echo $tl['general']['g70'];?>";
</script>
		
<?php include_once APP_PATH.'operator/template/footer.php';?>