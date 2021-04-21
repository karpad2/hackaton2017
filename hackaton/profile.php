<?php
/**
 * Created by PhpStorm.
 * User: KÁrpi
 * Date: 2018.01.11.
 * Time: 2:01
 */

define("secret","mikroci");
include "db_config.php";
if(empty($_GET["profile_id"])) header("Location:index.php");
$user_id=mysqli_escape_string($conn,$_GET["profile_id"]);
$sql="SELECT u.user_id,username,run_id,distance FROM users u join run r on r.user_id=u.user_id WHERE u.user_id=$user_id";
$result=mysqli_query($conn,$sql) or die(mysqli_errno($conn));
if(!(mysqli_num_rows($result)>0)) header("Location:index.php");
$user=mysqli_fetch_assoc($result);



$title=$user["username"];
include "head.php";
//if($loggedin==false) header("Location:index.php");
?>

<div class="col-lg-auto profile">
    <img align="left" class="p-image-lg" src="img/profile/banner_<?php echo $user_id;?>.png" alt="Profile image"/>
    <img align="left" class="p-image-profile" src="img/profile/<?php echo $user_id;?>.png" alt="Profile image"/>
    <div class="profile-text">
        <h1><?php echo $user["username"] ?></h1>
    </div>
</div>
    <p>
      <?php if(isset($_SESSION["user_id"]) and $_SESSION["user_id"]==$user["user_id"])
        echo"<div class=\"col-md-6\">
<a class=\"btn btn-secondary\" href=\"login.php?edit={$user["user_id"]}\">Profil szerkesztése</a></div>";?>
      <?php $sql="SELECT SUM(distance) as 'SUM' FROM run where user_id=$user_id";
            $result=mysqli_query($conn,$sql) or die(mysqli_error($conn));
            $sum=mysqli_fetch_assoc($result)["SUM"];

            ?>
    </p>
    <p>&nbsp; </p>
    <h1>Achievments:</h1>

    <div class="col-lg-auto">
        <h4>Eddigi lefutott km száma:<?php echo (double)$sum/1000; ?> km</h4>
        <h6>A holdig még :<?php echo (384400-$sum); ?> km van hátra.</h6>
    </div>

<?php
include "footer.php";
?>



