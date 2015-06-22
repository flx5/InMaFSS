<?php
if (!defined('IN_INSTALLER')) {
    exit; 
}

$mayContinue = true;

if (isset($files['files']) && count($files['files']) > 0) {
    ?>
    <h3>Files</h3>
    <table>
        <tr><th>Name</th><th>Real Path</th><th>Status</th></tr>
        <?php
        foreach ($files['files'] as $file) {
            $name = $file;
            // Use the directory above the installer!
            $file = '../' . $file;
            $ok = "OK";
            if (file_exists($file) && !is_writeable($file)) {
                $ok = "Could not write to file";
                $mayContinue = false;
            } else {
                $fH = @fopen($file, "c");
                if ($fH) {
                    fclose($fH);
                } else {
                    $ok = "Could not create file";
                    $mayContinue = false;
                }
            }

            echo '<tr><td>' . $name . '</td><td>' . Core::truepath($file) . '</td><td>' . $ok . '</td></tr>';
        }
        ?>
    </table>
    <?php
}

if (isset($files['dirs']) && count($files['dirs']) > 0) {
    ?>
    <h3>Directories</h3>
    <table>
        <tr><th>Name</th><th>Real Path</th><th>Status</th></tr>
        <?php
        foreach ($files['dirs'] as $dir) {
            $name = $dir;
            $dir = '../' . $dir;

            $ok = "OK";

            if (is_dir($dir)) {
                if (!is_writeable($dir)) {
                    $mayContinue = false;
                    $ok = 'Could not write to directory';
                }
            } else {
                if (!@mkdir($dir, 0644, true)) {
                    $mayContinue = false;
                    $ok = "Could not create directory";
                } 
            }

            echo '<tr><td>' . $name . '</td><td>' . Core::truepath($dir) . '</td><td>' . $ok . '</td></tr>';
        }
        ?>
    </table>
    <?php
}

if (!$mayContinue) {
    $button['continue'] = Array('title' => 'Retry', 'target' => $nextStep);
}
?>