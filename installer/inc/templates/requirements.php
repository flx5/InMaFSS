<?php
if (!defined('IN_INSTALLER'))
    exit;

$mayContinue = true;

if (isset($requirements['php']['version'])) {
    ?>
    <h3>PHP</h3>
    <table>
        <tr>
            <th>Required</th><th>Installed</th><th>Status</th>
        </tr>
        <?php
        $ok = "Below requirement! Please update your PHP installation.";
        if (version_compare(phpversion(), $requirements['php']['version']) >= 0)
            $ok = "OK";
        else
            $mayContinue = false;

        echo '<tr><td>PHP ' . $requirements['php']['version'] . '</td><td>PHP ' . phpversion() . '</td><td>' . $ok . '</td></tr>';
        ?>
    </table>
    <?php
}
if (isset($requirements['php']['extensions']) && count($requirements['php']['extensions']) > 0) {
    ?>

    <h3>PHP required extensions</h3>
    <table>
        <tr>
            <th>Extension</th><th>Required</th><th>Installed</th><th>Status</th>
        </tr>
        <?php
        foreach ($requirements['php']['extensions'] as $extension) {
            $versionInstalled = "";

            if (!extension_loaded($extension['name'])) {
                $ok = "Extension not installed";
                $mayContinue = false;
            } else {
                $versionInstalled = phpversion($extension['name']);

                $ok = "OK";

                if (isset($extension['version'])) {
                    if ($versionInstalled === false)
                        $versionInstalled = "Unknown";
                    else if (version_compare($versionInstalled, $extension['version']) < 0) {
                        $ok = "Version too low";
                        $mayContinue = false;
                    }
                }
            }

            if (!isset($extension['version']))
                $extension['version'] = "";

            echo '<tr><td>' . $extension['name'] . '</td><td>' . $extension['version'] . '</td><td>' . $versionInstalled . '</td><td>' . $ok . '</td></tr>';
        }
        ?>
    </table>
    <?php
}

if (isset($requirements['php']['optionalExt']) && count($requirements['php']['optionalExt']) > 0) {
    ?>

    <h3>PHP optional extensions</h3>
    <table>
        <tr>
            <th>Extension</th><th>Required</th><th>Installed</th><th>Status</th>
        </tr>
        <?php
        foreach ($requirements['php']['optionalExt'] as $extension) {
            $versionInstalled = "";

            if (!extension_loaded($extension['name'])) {
                $ok = "Extension not installed";
            } else {
                $versionInstalled = phpversion($extension['name']);

                $ok = "OK";

                if (isset($extension['version'])) {
                    if ($versionInstalled === false)
                        $versionInstalled = "Unknown";
                    else if (version_compare($versionInstalled, $extension['version']) < 0) {
                        $ok = "Version too low";
                    }
                }
            }

            if (!isset($extension['version']))
                $extension['version'] = "";

            echo '<tr><td>' . $extension['name'] . '</td><td>' . $extension['version'] . '</td><td>' . $versionInstalled . '</td><td>' . $ok . '</td></tr>';
        }
        ?>
    </table>
    <?php
}

if (isset($requirements['php']['exchangeableExt']) && count($requirements['php']['exchangeableExt']) > 0) {
    foreach ($requirements['php']['exchangeableExt'] as $group) {
        ?>

        <h3>PHP - <?php echo $group['name']; ?></h3>
        <table>
            <tr>
                <th>Extension</th><th>Required</th><th>Installed</th><th>Status</th>
            </tr>
            <?php
            $foundOne = false;

            foreach ($group['ext'] as $extension) {
                $versionInstalled = "";

                if (!extension_loaded($extension['name'])) {
                    $ok = "Extension not installed";
                } else {
                    $versionInstalled = phpversion($extension['name']);

                    if (isset($extension['version'])) {
                        if ($versionInstalled === false) {
                            $versionInstalled = "Unknown";
                            $ok = "OK";
                            $foundOne = true;
                        } else if (version_compare($versionInstalled, $extension['version']) < 0) {
                            $ok = "Version too low";
                        } else {
                            $ok = "OK";
                            $foundOne = true;
                        }
                    }
                }

                if (!isset($extension['version']))
                    $extension['version'] = "";

                echo '<tr><td>' . $extension['name'] . '</td><td>' . $extension['version'] . '</td><td>' . $versionInstalled . '</td><td>' . $ok . '</td></tr>';
            }

            if (!$foundOne)
                $mayContinue = false;
            ?>
        </table>
        <?php
    }
}

if (isset($requirements['server']['modules']) && count($requirements['server']['modules']) > 0) {
    ?>
    <h3>Apache modules</h3>
    <?php
    $canCheck = true;
    if (!function_exists('apache_get_modules') || count(apache_get_modules()) == 0) {
        echo '<div class="box">You are not using Apache. Please check the following modules manually!</div>';
        $canCheck = false;
    }
    ?>
    <table>
        <tr>
            <th>Module</th><th>Status</th>
        </tr>
        <?php
        $modules = apache_get_modules();
        foreach ($requirements['server']['modules'] as $module) {
            if ($canCheck) {
                if (in_array($module, $modules))
                    $ok = "OK";
                else {
                    $ok = "Module not installed!";
                    $mayContinue = false;
                }
            } else {
                $ok = "UNKNOWN";
            }
            echo '<tr><td>' . $module . '</td><td>' . $ok . '</td></tr>';
        }
        ?>
    </table>
    <?php
}
?>
<?php
if (!$mayContinue)
    $button['continue'] = Array('title' => 'Retry', 'target' => $nextStep);
?>