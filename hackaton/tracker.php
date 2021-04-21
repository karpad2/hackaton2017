<?php
/**
 * Created by PhpStorm.
 * User: Piszi
 * Date: 2018.01.03.
 * Time: 23:09
 */
$title="Tracker";
define("secret","mikroci");
include "db_config.php";
session_start();
if(isset($_GET["record"])AND $_GET["record"]=="record") {
    if (isset($_POST["data"])) {
        $distance = $speed = 0;
        $vars_s = json_decode($_POST["data"],true);//true
        $vars=array();
        foreach ($vars_s as $var_s) $vars[]=json_decode($var_s,true);

        $first_item = true;

        $items = array();

        $speed_alti = array("speed" => 0, "altitude" => 0);
        $speed_alti_s = json_encode($speed_alti);

        $sql = "SELECT * FROM tmp_run where (user_id='{$_SESSION["user_id"]}') ORDER BY run_date  DESC";
        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

        if (mysqli_num_rows($result) > 0) {
            $first_item = false;
            $itemofquerys = mysqli_fetch_all($result, MYSQLI_ASSOC);
            $item = $itemofquerys[0];
            //var_dump($item);
            $items[] = array("coord"=>json_decode($item["coord"],true),
                            "run_date"=>$item["run_date"]);}
        else{
            $run_d=mysqli_escape_string($conn,$vars[0]["run_date"]);
            $coord=mysqli_escape_string($conn,json_encode($vars[0]["coord"]));
            $sql = "INSERT INTO tmp_run (user_id,run_date,coord,speed_distance,distance)
            VALUES('{$_SESSION["user_id"]}','$run_d','$coord','$speed_alti_s','0');";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));
            $items[]=$vars[0];
        }


        foreach ($vars as $var) $items[] =$var;
        //var_dump($items);

        for ($i = 0; $i < count($items)-1;) {
            $i++;
            $result = strtotime($items[$i]["run_date"]) - strtotime($items[$i - 1]["run_date"]);
            //if ($result < 5) die("");
            $coord_o = $items[$i - 1]["coord"];

            if($items[$i]["coord"]["lat"]==$coord_o["lat"] AND $items[$i]["coord"]["lat"]==$coord_o["lng"]) continue;


            $distance = getDistance($items[$i]["coord"]["lat"],
                                    $items[$i]["coord"]["lng"], $coord_o["lat"], $coord_o["lng"]);
            if($result==0) continue;
            $speed = ($distance / $result) * 3.6; //km/h


            $speed_alti["speed"] = round($speed, 1);
            $speed_alti_s = json_encode($speed_alti);

            $run_d=mysqli_escape_string($conn,$items[$i]["run_date"]);

            $coord=json_encode($items[$i]["coord"]);
            if($coord==null) continue;

            $sql = "INSERT INTO tmp_run (user_id,run_date,coord,speed_distance,distance)
            VALUES('{$_SESSION["user_id"]}','$run_d','$coord','$speed_alti_s','$distance');";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));
        }

        $sql = "SELECT SUM(distance) as 'SUM' FROM tmp_run where user_id='{$_SESSION["user_id"]}';";
        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        $result = mysqli_fetch_all($result, MYSQLI_ASSOC);

        $conout = array("speed" => $speed_alti["speed"], "sum" => (int)$result[0]["SUM"]);
        echo json_encode($conout).PHP_EOL;
    } else die("Nope");

}


if(!isset($_SESSION["user_id"])) header("Location: index.php");
if(isset($_GET["mod"]) AND $_GET["mod"]=="start")
{
    include "head.php";
    echo'<div id="map"></div>
    <div class="col-md-5">
    
    <h1>Data:</h1>
    <p>Average speed:<span id="speed">0</span> km/h</p>
    <p>SUM distance: <span id="distance">0</span> m</p>
    <button class="btn btn-primary bg-danger" id="end">Befejezés</button>
    </div>

';
    include "footer.php";
}
else if(isset($_GET["mod"])&&$_GET["mod"]=="end")
{
    $sql="SELECT * FROM tmp_run where user_id='{$_SESSION["user_id"]}' ORDER BY run_date;";
    $result=mysqli_query($conn,$sql) or die(mysqli_error($conn));

    if(mysqli_num_rows($result)>0){
        $items = mysqli_fetch_all($result, MYSQLI_BOTH);
        $coord1 = array();
        $speed = array();
        $i = 0;
        $begin = $items[0]["run_date"];

        foreach ($items as $item) {
            $coord1[$i] = json_decode($item["coord"]);
            $data=json_decode($item["speed_distance"]);
            $speed[($i++)] = array($item["run_date"],$data->speed,$data->altitude);}

        $sql = "SELECT SUM(distance) as 'SUM' FROM tmp_run where user_id='{$_SESSION["user_id"]}';";
        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        $result = mysqli_fetch_assoc($result);
        $coord1 = json_encode($coord1);
        $speed = json_encode($speed);
        $sql = "INSERT INTO run (user_id,run_date_begin,coords,speed_altitude,distance)
        VALUES ('{$_SESSION["user_id"]}','$begin','$coord1','$speed',{$result["SUM"]})";
        mysqli_query($conn, $sql) or die("Mentés nem sikerült:".mysqli_error($conn));

        $sql = "DELETE FROM tmp_run WHERE user_id='{$_SESSION["user_id"]}'";
        mysqli_query($conn, $sql) or die("Cleaning");

        $sql = "SELECT MAX(run_id)as 'current' FROM run WHERE user_id={$_SESSION["user_id"]}";
        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        $result = mysqli_fetch_assoc($result);
        header("Location: view.php?watch=\"{$result["current"]}\"");
    }
    header("Location: mypage.php");
}
else if(isset($_GET["mod"])&&$_GET["mod"]=="delete")
{
    $sql = "DELETE FROM tmp_run WHERE user_id='{$_SESSION["user_id"]}'";
    mysqli_query($conn, $sql) or die("Tisztitas");
    header("Location: index.php");
}
else {}
function getDistance($latitudeTo, $longitudeTo, $latitudeFrom, $longitudeFrom) {//Haversine formula
    $R = 6378137; // Earth’s mean radius in meter
    $dLat = deg2rad($latitudeTo - $latitudeFrom);
    $dLong = deg2rad($longitudeTo - $longitudeFrom);
    $a = sin($dLat / 2) * sin($dLat / 2) +
        cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) *
        sin($dLong / 2) * sin($dLong / 2);
    $c = 2 * atan2(pow($a,(1/2)), pow(1-$a,(1/2)));
    $d = $R * $c;
    return $d; // returns the distance in meter
};