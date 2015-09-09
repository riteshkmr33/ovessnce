<?php include_once APP_PATH.'operator/template/header.php';?>

<h3><?php echo $tl["stat"]["s3"];?></h3>

<h4><em class="fa fa-map-marker"></em> <?php echo $tl["stat"]["s"];?></h4>
<div id="map_canvas" style="width: 100%;height: 400px;"></div>

<hr>

<form role="form" id="jak_statform" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
	<div class="row">
		<div class="col-md-6">
			<?php if (isset($LS_DEPARTMENTS) && is_array($LS_DEPARTMENTS)) { ?>
			<select name="jak_depid" id="jak_depid" class="form-control">
			
			<option value="0"<?php if ($_REQUEST["jak_depid"] == '0') { ?> selected="selected"<?php } ?>><?php echo $tl["general"]["g105"];?></option>
			
			<?php foreach($LS_DEPARTMENTS as $v) { ?>
			
			<option value="<?php echo $v["id"];?>"><?php echo $v["title"];?></option>
			
			<?php } ?>
			</select>
			<?php } ?>
			<input type="hidden" name="start_date" id="start_date" value="<?php echo $_SESSION["stat_start_date"];?>" />
			<input type="hidden" name="end_date" id="end_date" value="<?php echo $_SESSION["stat_end_date"];?>" />
		</div>
		<div class="col-md-6">
		<div id="reportrange" class="pull-right">
		    <i class="fa fa-calendar fa-lg"></i>
		    <span><?php echo date("F j, Y", strtotime($_SESSION["stat_start_date"]));?> - <?php echo date("F j, Y", strtotime($_SESSION["stat_end_date"]));?></span> <b class="caret"></b>
		</div>
		</div>
	</div>
</form>
	
<hr>

<?php if (isset($arrayoperator) && is_array($arrayoperator)) foreach($arrayoperator as $v) { ?>
<h4><em class="fa fa-user"></em> <?php echo $v["operator"];?></h4>
<!-- START summary widgets-->
<div class="row">
   <div class="col-md-6">
<!-- START widget-->
      <div data-toggle="play-animation" data-play="fadeInDown" data-offset="0" data-delay="100" class="panel widget">
         <div class="panel-body bg-primary">
            <div class="row row-table row-flush">
               <div class="col-xs-11">
                  <p class="mb0"><?php echo $v["operator"];?></p>
                  <h3 class="m0"><?php echo secondsToTime($v['total_support'], $tl['general']['g230']);?></h3>
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
               <?php echo secondsToTime($v['total_support'], $tl['general']['g230']);?>
            </div>
         </div>
      </div>
   </div>
   <div class="col-md-6">
   	<?php if ($v['vote']) { ?>
      <!-- START widget-->
      <div data-toggle="play-animation" data-play="fadeInDown" data-offset="0" data-delay="500" class="panel widget">
         <div class="panel-body bg-warning">
            <div class="row row-table row-flush">
               <div class="col-xs-11">
                  <p class="mb0"><?php echo $v["operator"];?></p>
                  <h3 class="m0"><?php echo $v['vote'];?>/5</h3>
               </div>
               <div class="col-xs-1 text-center">
                  <em class="fa fa-star fa-2x"></em>
               </div>
            </div>
         </div>
         <div class="panel-body">
            <!-- Bar chart-->
            <div class="text-center">
               <?php echo $v['vote'];?>/5
            </div>
         </div>
      </div>
      <?php } ?>
   </div>
</div>
<!-- END summary widgets-->
<hr>
<?php } ?>

<h4><em class="fa fa-pie-chart"></em> <?php echo $tl["menu"]["m10"];?></h4>
<div class="row">
	<div class="col-md-6">
		
		<div id="chart" style="width: 100%; height: 300px; margin-right: 20px;"></div>
		
	</div>
	
	<div class="col-md-6">
		
		<div id="chart2" style=";width: 100%; height: 300px;"></div>
		
	</div>
</div>

<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">   
//<![CDATA[

    function loadmap() {
      var map = new google.maps.Map(document.getElementById("map_canvas"), {
        center: new google.maps.LatLng(4, 10),
        zoom: 2,
        scrollwheel: false,
        mapTypeId: 'roadmap'
      });
      var infoWindow = new google.maps.InfoWindow;

      // Change this depending on the name of your PHP file
      downloadUrl("include/loadmap.php?ajax=1", function(data) {
        var xml = data.responseXML;
        if (xml) {
        var markers = xml.documentElement.getElementsByTagName("marker");
        for (var i = 0; i < markers.length; i++) {
          var country = markers[i].getAttribute("country");
          var city = markers[i].getAttribute("city");
          var countryflag = markers[i].getAttribute("countryflag");
          var username = markers[i].getAttribute("username");
          var point = new google.maps.LatLng(
              parseFloat(markers[i].getAttribute("lat")),
              parseFloat(markers[i].getAttribute("lng")));
          var html = '<figure style="background: #f4f4f4;border: 2px solid #cecdcd;border-radius: 5px;float: left;padding: 10px;margin: 0 10px 20px 0;"><img src="img/country/' + countryflag + '.gif" width="16" height="11" /></figure><b>' + country + '</b>, ' + city + ' <br/> <?php echo addslashes($tl['user']['u2']);?>: ' + username;
          var marker = new google.maps.Marker({
            map: map,
            position: point,
            icon: 'img/flag_green.png',
            shadow: 'img/flag_shadow.png'
          });
          bindInfoWindow(marker, map, infoWindow, html);
        }
        	
        }
      });
    }

    function bindInfoWindow(marker, map, infoWindow, html) {
      google.maps.event.addListener(marker, 'click', function() {
        infoWindow.setContent(html);
        infoWindow.open(map, marker);
      });
    }

    function downloadUrl(url, callback) {
      var request = window.ActiveXObject ?
          new ActiveXObject('Microsoft.XMLHTTP') :
          new XMLHttpRequest;

      request.onreadystatechange = function() {
        if (request.readyState == 4) {
          request.onreadystatechange = doNothing;
          callback(request, request.status);
        }
      };

      request.open('GET', url, true);
      request.send(null);
    }

    function doNothing() {}

    //]]>
    
    $(document).ready(function() {
    	loadmap();
    });
  
</script>

<!-- First Stat -->
<script type="text/javascript">
var chart_operator;
var chart_feedback;
var chart;
var chart2;
$(document).ready(function() {

$('#reportrange').daterangepicker({
      ranges: {
         '<?php echo $tl["stat"]["s15"];?>': [moment(), moment()],
         '<?php echo $tl["stat"]["s16"];?>': [moment().subtract(2,'days'), moment()],
         '<?php echo $tl["stat"]["s17"];?>': [moment().subtract(6,'days'), moment()],
         '<?php echo $tl["stat"]["s18"];?>': [moment().subtract(29, 'days'), moment()],
         '<?php echo $tl["stat"]["s22"];?>': [moment().startOf('month'), moment().endOf('month')],
         '<?php echo $tl["stat"]["s23"];?>': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
      startDate: moment("<?php echo $_SESSION["stat_start_date"];?>", "YYYY-MM-DD"),
      endDate: moment("<?php echo $_SESSION["stat_end_date"];?>", "YYYY-MM-DD"),
      format: 'YYYY-MM-DD',
      locale: {
      	customRangeLabel: '<?php echo $tl["stat"]["s24"];?>'
      }
    },
    function() {
        $('#reportrange span').html(<?php echo $_SESSION["stat_start_date"];?> + ' - ' + <?php echo $_SESSION["stat_end_date"];?>);
    }
);

$('#reportrange').on('apply.daterangepicker', function(ev, picker) {
  //do something, like clearing an input
  $('#start_date').val(picker.startDate.format('YYYY-MM-DD'));
  $('#end_date').val(picker.endDate.format('YYYY-MM-DD'));
  $("#jak_statform").submit();
});

$(document).on("change", "#jak_depid", function() {
	$("#jak_statform").submit();
});

chart = new Highcharts.Chart({
	chart: {
		renderTo: 'chart',
		plotBackgroundColor: null,
		plotBorderWidth: null,
		plotShadow: false
	},
	title: {
		text: '<?php echo addslashes($tl["stat"]["s1"]);?>'
	},
	tooltip: {
		formatter: function() {
			return '<b>'+ this.point.name +'</b>: '+ Highcharts.numberFormat(this.percentage, 1) +' %';
		}
	},
	plotOptions: {
		pie: {
			allowPointSelect: true,
			cursor: 'pointer',
			dataLabels: {
				enabled: true,
				color: '#000000',
				connectorColor: '#000000',
				formatter: function() {
					return '<b>'+ this.point.name +'</b>: '+ Highcharts.numberFormat(this.percentage, 1) +' %';
				}
			}
		}
	},
	series: [{
		type: 'pie',
		name: '<?php echo addslashes($tl["stat"]["s1"]);?>',
		data: [<?php echo $stat1country;?>
		]
	}]
});

chart2 = new Highcharts.Chart({
	chart: {
		renderTo: 'chart2',
		plotBackgroundColor: null,
		plotBorderWidth: null,
		plotShadow: false
	},
	title: {
		text: '<?php echo addslashes($tl["stat"]["s4"]);?>'
	},
	tooltip: {
		formatter: function() {
			return '<b>'+ this.point.name +'</b>: '+ Highcharts.numberFormat(this.percentage, 1) +' %';
		}
	},
	plotOptions: {
		pie: {
			allowPointSelect: true,
			cursor: 'pointer',
			dataLabels: {
				enabled: true,
				color: '#000000',
				connectorColor: '#000000',
				formatter: function() {
					return '<b>'+ this.point.name +'</b>: '+ Highcharts.numberFormat(this.percentage, 1) +' %';
				}
			}
		}
	},
	series: [{
		type: 'pie',
		name: '<?php echo addslashes($tl["stat"]["s4"]);?>',
		data: [<?php echo $stat1ref;?>
		]
	}]
});	

});
</script>

<script type="text/javascript" src="charts/highcharts.js"></script>
<script type="text/javascript" src="charts/exporting.js"></script>

<?php include_once APP_PATH.'operator/template/footer.php';?>