<?php
$current = substr($_SERVER["REQUEST_URI"], strrpos($_SERVER["REQUEST_URI"], "user/") + strlen("user/"));
if (strpos($current, '?') !== false) {
    $current = substr($current, 0, strpos($current, '?'));
}
?>
<div id="header">
    <div id="menu">
        <?php
        if (Authorization::IsLoggedIn('LDAP')) {
            echo '<span ' . (($current == 'index.php') ? 'class="selected"' : '') . '><a href="index.php" >' . lang()->loc('home', false) . '</a></span>';
            echo '<span ' . (($current == 'settings.php') ? 'class="selected"' : '') . '><a href="settings.php" >' . lang()->loc('settings', false) . '</a></span>';
            echo '<span ' . (($current == 'logout.php') ? 'class="selected"' : '') . '><a href="logout.php" >' . lang()->loc('logout', false) . '</a></span>';
        }
        ?>
    </div>
</div>
<div id="content">