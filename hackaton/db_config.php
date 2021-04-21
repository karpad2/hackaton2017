<?php
/**
 * Created by PhpStorm.
 * User: Piszi
 * Date: 2018.01.03.
 * Time: 13:58
 */
date_default_timezone_set('Europe/Belgrade');
if(defined("secret") AND secret=="mikroci")
{
    define("SALT","asdfghjkl");
    $host="localhost";
    $user="root";
    $pass="";
    $db="hackaton";
    $conn=mysqli_connect($host,$user,$pass,$db) or die("Connect was failed");
}
else sleep(10);