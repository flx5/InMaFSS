<?php

require_once("global.php");
require_once(INC . "class.view.php");

getVar('tpl')->Init('User');
getVar('tpl')->AddStandards("user");
getVar('tpl')->addTemplate('user/header');

getVar("tpl")->Write('<div id="plan_left">');

getVar('tpl')->addTemplate('user/plan');

getVar("tpl")->Write('</div>');

getVar('tpl')->addTemplate('user/footer');
getVar('tpl')->Output();
?>