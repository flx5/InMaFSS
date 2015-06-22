<?php
$sql = file_get_contents('config/data.sql');
$sql = explode(";", $sql);

$db = Core::DBfromSession();
$db->Connect();

$success = true;

foreach($sql as $query) {
    if(trim($query) == "") {
        continue; 
    }
    if($db->DoQuery($query) === false) {
        $success = false; 
    }
}

if($success) {
    echo 'Database import complete'; 
}
else 
{
    echo 'An error occured. Please clean your database and retry. If the problem persists contact the programmer!';
    $button['continue'] = Array('target'=>$nextStep, 'title'=>'Retry');
}
?>

