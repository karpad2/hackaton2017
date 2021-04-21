<?php
session_start();

define("secret","mikroci");
include "../db_config.php";
$_SESSION["admin"]=true;
if(isset($_GET["delete"])) {
    if ($_GET["delete"] == "tmp_all")
        $sql = "TRUNCATE  TABLE tmp_run";
    if ($_GET["delete"] == "run_all")
        $sql = "TRUNCATE  TABLE run";
mysqli_query($conn,$sql) or die(mysqli_error($sql));
}


?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Admin user interface">
    <meta name="author" content="NaN">
    <meta name="robot" content="no index,no follow";
    <link rel="icon" href="../favicon.ico">
    <title>Admin UI</title>
    <!-- Bootstrap core CSS -->
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../favicon.ico" rel="icon">
</head>

<body>
<nav class="navbar navbar-toggleable-md navbar-light bg-faded">
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand" href="../index.php">Run Because Fun</a>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">

            <li class="nav-item">
                <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="settings.php">DB actions</a>
            </li>

        </ul>

    </div>
</nav>
<div class="container">

    <div >
            <h3>Datas in temporary run table delete:</h3>
            <a href="settings.php?delete=tmp_all" class="btn btn-secondary">Adatok törlése</a><br>

            <h3>Datas in run table delete:</h3>
            <a href="settings.php?delete=run_all" class="btn btn-secondary">Adatok törlése</a><br>

     </div>

    </div>


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="../js/jquery-3.2.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="../bootstrap/js/bootstrap.min.js"></script>
</body>
</html>