<?php
/**
 * Created by PhpStorm.
 * User: Piszi
 * Date: 2018.01.05.
 * Time: 13:49
 */
$title="Bejelentkezes";
if(isset($_GET["edit"])) $title="Felhasználó szerkesztése";

include "head.php";
define("secret","mikroci");
include "db_config.php";

if(isset($_GET["edit"])){

    if(((isset($_SESSION["user_id"]) AND $_SESSION["user_id"]==$_GET["edit"]) or (isset($_SESSION["admin"]) and $_SESSION["admin"]==true)))
{
    $user_id=mysqli_escape_string($conn,$_GET["edit"]);

    if(!empty($_GET["upload"])AND isset($_FILES['file']))
    {
        $file=$_FILES['file'];
        $uploadOk= move_uploaded_file($_FILES['file']["tmp_name"],'img/profile/'.$_GET["edit"].".png");
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">File feltöltés sikerült!</div>';
    }
    if(!empty($_GET["delete"]))
    {
        $sql="DELETE FROM users where user_id=$user_id";
        $result=mysqli_query($conn,$sql);
        if($result==true)
        {
            header("Location:admin/index.php");
        }
    }



    $user_id=mysqli_escape_string($conn,$user_id);
    $sql="SELECT * FROM users where user_id=$user_id;";
    $result=mysqli_query($conn,$sql) or die(mysqli_error($conn));
    $user=mysqli_fetch_assoc($result);

    if(isset($_POST["pass1"]) and isset($_POST["pass2"]))
    {

        if($_POST["pass1"]==$_POST["pass2"])
        {

            $pass1=$_POST["pass1"];

            $pass=md5(SALT.mysqli_escape_string($conn,$pass1).SALT);
            $sql="UPDATE users SET password='$pass' WHERE user_id={$_GET["edit"]}";
            if(mysqli_query($conn,$sql) or die(mysqli_error($conn)))
            {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Jelszó módosítva!</div>';
            }
        }
        else header("Location:login.php?edit={$_GET["edit"]}&e=\"differentpasswords\"");
    }

    echo"
         <div class=\"col-md-6\"><form action=\"login.php?edit={$_GET["edit"]}\" method=\"post\">
         Felhasználónév:    <input class=\"form-control\" type=\"text\" name=\"username\" disabled=\"disabled\" value=\"{$user["username"]}\">
         Jelszó módosítás:  <input class=\"form-control\" type=\"password\" name=\"pass1\"><br>
                            <input class=\"form-control\" type=\"password\" name=\"pass2\">
                            <br>
         <input type=\"submit\" class=\"btn btn-primary\" value=\"Módositás\">
        </form><br>
        <form action=\"login.php?edit=$user_id&upload=1\" method=\"post\" enctype=\"multipart/form-data\">
        Kép feltöltés: <input type=\"file\"  name=\"file\" id=\"file\" accept=\"image/png\">
        <input type=\"submit\" class=\"btn btn-secondary\" value=\"Feltöltés\">
        </form>
        </div> 
    ";
    if($_SESSION["admin"]==true){ echo"<a class=\"btn bg-danger btn-secondary\" href=\"login.php?edit={$_GET["edit"]}&delete={$_GET["edit"]}\">Felhasználó törlése</a>";}
        }
}
else {


    if (isset($_GET["mod"])) {
        switch ($_GET["mod"]) {
            case 1: {
                if (isset($_POST["user"]) && isset($_POST["passwd"])) {
                    $user = mysqli_escape_string($conn, $_POST["user"]);
                    $pass = md5(SALT . mysqli_escape_string($conn, $_POST["passwd"]) . SALT);

                    $sql = "SELECT user_id,username FROM users WHERE (username = '$user' AND password = '$pass');";
                    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    if (mysqli_num_rows($result) > 0) {
                        $item = mysqli_fetch_assoc($result);
                        //session_start();
                        $_SESSION["logged_in"] = true;
                        $_SESSION["user_id"] = $item["user_id"];
                        $_SESSION["username"] = $item["username"];

                        header("Location: mypage.php");
                    } else header("Location: login.php?mod=1&err=1");
                }
            }
                break;
            case 2: {
                if (isset($_POST["user"]) && !empty($_POST["passwd1"]) && !empty($_POST["passwd2"])) {
                    if ($_POST["passwd1"] != $_POST["passwd2"]) header("Location: login.php?mod=2&err=2");
                    $user = mysqli_escape_string($conn, $_POST["user"]);
                    $sql = "SELECT user_id FROM users WHERE username='$user';";
                    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

                    if (mysqli_num_rows($result) > 0) header("Location: login.php?mod=2&err=1");
                    $pass = md5(SALT . mysqli_escape_string($conn, $_POST["passwd1"]) . SALT);
                    $sql = "INSERT INTO users (username,password) VALUES('$user','$pass');";
                    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    if ($result) {
                        $sql = "SELECT user_id FROM users WHERE username='$user';";
                        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                        if (mysqli_num_rows($result) > 0) {
                            
                            header("Location: mypage.php");
                        }
                        header("Location: index.php");

                    }

                }

            }
                break;
            case 3: {
                $sql = "DELETE FROM tmp_run WHERE user_id='" . $_SESSION["user_id"] . "';";
                mysqli_query($conn, $sql);
                session_destroy();
                header("Location: index.php");
            }

        }

        switch ($_GET["mod"]) {
            case 1: {
                echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
    </button>
    <strong>Belépés sikertelen!</strong>';
                if (isset($_GET["err"]) && $_GET["err"] == 1) echo "Hibás felhasználó név, vagy jelszó!";
                echo '</div>';
            }
                break;
            case 2: {
                echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
    </button>
    <strong>Regisztárció sikertelen!</strong>';
                if (isset($_GET["err"]) && $_GET["err"] == 1) echo "Felhasználó név foglalt!";
                echo '</div>';
            }
                break;
        }

    } else echo "<form action=\"login.php?mod=1\" method=\"post\">
                username: <input type=\"text\" name=\"username\" placeholder=\"Kérem a felhasználó nevet\" class=\"form-control\"><br>
                password: <input type=\"password\" name=\"pass\" class=\"form-control\"><br>
                <input type=\"submit\" class=\"btn btn-primary btn-block btn-large\" value=\"Login\"></form>";
}
include "footer.php";