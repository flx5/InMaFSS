<div id="header">
    <div id="menu">
        <span class="selected"><a href="index.php"><?php lang()->loc('home'); ?></a></span>
        <?php if (Authorization::IsLoggedIn('LDAP')) { ?>
            <span><a href="settings.php"><?php lang()->loc('settings'); ?></a></span>
            <span><a href="logout.php"><?php lang()->loc('logout'); ?></a></span>
        <?php } ?>
    </div>
</div>
<div id="content">