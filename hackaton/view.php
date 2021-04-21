<?php
/**
 * Created by PhpStorm.
 * User: KÁrpi
 * Date: 2018.01.09.
 * Time: 16:59
 */

define("secret","mikroci");
include "db_config.php";
$title="View";

if(empty($_GET["watch"])) header("Location:index.php");

$var=mysqli_escape_string($conn,$_GET["watch"]);
$sql="SELECT run.*,users.username FROM run LEFT JOIN users on run.user_id = users.user_id WHERE run_id='$var'";
$result=mysqli_query($conn,$sql) or die(mysqli_error($conn));
if(mysqli_num_rows($result)>0) {
    $item = mysqli_fetch_assoc($result);
    $coords = json_decode($item["coords"]);
    $user=$item["username"];
    $speeds=json_decode($item["speed_altitude"]);

    $speed_s="[['Date','Speed','Altitude'],";
    foreach ($speeds as $speed)
    {
        $speed_s.=json_encode(array(substr($speed[0],11,5),$speed[1],$speed[2])).",";
    }
    $speed=end($speeds);
    $speed_s.=json_encode(array(substr($speed[0],11,5),$speed[1],$speed[2]))."]".PHP_EOL;
}
else header("Location:index.php");

include ("head.php");

?>
<div>
    <h2><?php echo $user;?> runs</h2>
    <h3>Runned distance: <?php echo $item["distance"];?> m</h3>
</div>
<div class="col-lg-auto" id="map"></div>
<div class="col-lg-auto" id="chart"></div>

<div class="row">
    <div class="col-sm-6">
    <a class="btn btn-secondary" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo "https://".$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];?>" target="_blank">Share on Facebook</a></div>
    <div class="col fb-comments"></div></div>



<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    $.getScript("https://maps.googleapis.com/maps/api/js?key=AIzaSyBgnjCUdrCEbkidyaQRu_1KvBndKSKxnEE&callback=offset&v=3&libraries=geometry");

     google.charts.load('current', {'packages':['line', 'corechart']});
     google.charts.setOnLoadCallback(drawChart);
	
	var Options = {
        chart: {
          title: 'Futás folyamán',
            curveType: 'function'
        },
        width: 900,
        height: 300,
        series: {
          // Gives each series an axis name that matches the Y-axis below.
          0: {axis: 'Speed'},
          1: {axis: 'Altitude'}
        },
        axes: {
          // Adds labels to each axis; they don't have to match the axis names.
          y: {
            speed: {label: 'Speed (km/h)'},
            altitude: {label: 'Altitude m'}
          }
        }
      };

	
	
    function drawChart() {
       var chart = new google.charts.Line(document.getElementById("chart"));
       var data= new google.visualization.arrayToDataTable(<?php echo $speed_s;?>);
	   
       chart.draw(data,Options);
    }
	

    var map,LatLng=<?php echo json_encode($coords[0]); ?>;
    function offset(){
        map = new google.maps.Map(document.getElementById("map"),{
            center: LatLng,
            zoom: 16});

    var arrCoords= [<?php
        foreach ($coords as $coord)
        {
            echo"new google.maps.LatLng($coord->lat,$coord->lng),";
        }
        $coord=end($coords);  echo"new google.maps.LatLng($coord->lat,$coord->lng)";

        ?>];
    var route = new google.maps.Polyline({
        path: arrCoords,
        strokeColor: "#FF0000",
        strokeOpacity: 1.0,
        strokeWeight: 4,
        map: map
    });
        var start_pos=new google.maps.Marker({
            position:<?php echo json_encode($coords[0]); ?>,
            map:map,
            title:"Kezdeti pozició",
            label:'A'
        });

        var end_pos=new google.maps.Marker({
            position:<?php echo json_encode($coord); ?>,
            map:map,
            title:"Vég pozició",
            label:'B',
           });


    }
</script>

<div id="fb-root"></div>
<script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = 'https://connect.facebook.net/hu_HU/sdk.js#xfbml=1&version=v2.11';
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
<?php
include ("footer.php");



