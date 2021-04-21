<?php
/**
 * Created by PhpStorm.
 * User: KÁrpi
 * Date: 2017.12.23.
 * Time: 23:41
 */
$title="Main page";
include "head.php";

if(!$loggedin) include "caro.php";
else header("Location: mypage.php");
include "footer.php";

