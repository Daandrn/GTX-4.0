<?php 

$url = $_SERVER['REQUEST_URI'];

if($url == "/inicio.php" || $url == "/index.php" || $url == "/inicio" || $url == "/"){
    header("location: /control/control.inicio.php");
} else {
    echo "error 404";
}

?>