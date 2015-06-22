<?php
    require_once "../global.php";

if(!isset($_REQUEST['url']) || !isset($_REQUEST['file']) || !isset($_REQUEST['action'])) {
    exit;
}

    $file = $_REQUEST['file'];

if(preg_replace("#https://github.com/flx5/InMaFSS/raw/([a-zA-Z0-9])*/".$file."#", "", $_REQUEST['url']) != "") {
    exit;
}


    $action = $_REQUEST['action'];
    $url = $_REQUEST['url'];

if(getVar("update")->PerformUpdate($file, $action, $url)) {
    echo "|OK";
} else {
   echo "|ERROR";
}
?>