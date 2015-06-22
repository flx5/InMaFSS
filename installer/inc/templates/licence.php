<?php
if(!defined('IN_INSTALLER')) {
    exit; 
}

$button['continue']['title'] = "Accept";
$button['back'] = Array('title'=>'Abort', 'target'=>0);
?>
<div class="box">
    You must accept the Licence to continue!
</div>
<textarea readonly="readonly"><?php echo $licence; ?></textarea>