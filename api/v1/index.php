<?php
require_once("../../global.php");
require_once(INC."rest.php");

$rest = new Rest((isset($_REQUEST['RESTurl']) ? $_REQUEST['RESTurl'] : null), realpath(dirname(__FILE__))."/Controllers/");
?>
