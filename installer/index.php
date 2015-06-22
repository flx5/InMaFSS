<?php
session_start();
define('IN_INSTALLER', true);
date_default_timezone_set("UTC");
require_once 'inc/config.php';
require_once 'inc/core.php';
require_once 'inc/class.sql.php';

$nextStep = 0;
if (isset($_POST['step']) && is_numeric($_POST['step']) && isset($steps[$_POST['step']])) {
    $nextStep = $_POST['step']; 
}

$step = $steps[$nextStep];
$button = Array('continue' => Array('title' => 'Continue', 'target' => $nextStep + 1), 'back' => Array('title' => 'Back', 'target' => $nextStep - 1));
?>
<!doctype html>
<html>
    <head>
        <title><?php echo $productName; ?> Setup Wizard</title>
        <link rel="stylesheet" href="css/style.css" type="text/css">
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <div id="container">
            <div id="header"><?php echo $productName; ?> Setup Wizard</div>
            <div id="menu">
                <h3>Installation Steps</h3>
                <ul>
                    <?php
                    foreach ($steps as $key => $data) {
                        echo '<li class="' . (($key == $nextStep) ? 'active' : '') . '">' . $data['title'] . '</li>';
                    }
                    ?>
                </ul>
            </div>
            <div class="seperator">&nbsp;</div>
            <div id="content">
                <h3><?php echo $step['title']; ?></h3>
                <form method="post">
                    <?php
                    require_once 'inc/templates/' . $step['template'] . '.php';
                    ?>
                    <br><br>

                    <?php
                    if ($nextStep < count($steps) - 1 && !isset($button['continue']['disable'])) {
                        ?>
                        <button type="submit" name="step" value="<?php echo $button['continue']['target']; ?>" class="button" style="float:right;">
                            <?php echo $button['continue']['title'] ?>
                        </button>
                    <?php
                    }
                    if ($nextStep > 0 && !isset($button['back']['disable'])) {
                        ?>

                        <button type="submit" name="step" value="<?php echo $button['back']['target']; ?>" class="button">
                        <?php echo $button['back']['title']; ?>
                        </button>
                        <?php
                    }
                    ?>
                </form>
            </div>
            <hr>
        </div>
    </body>
</html>