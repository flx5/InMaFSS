<div id="header">
    <div id="menu">
        <span class="selected"><a href="index.php">Home</a></span>
        <?php if (Authorization::IsLoggedIn('LDAP')) { ?>
            <span><a href="plan.php">Vertretungsplan</a></span>
            <span><a href="logout.php">Logout</a></span>
        <?php } ?>
    </div>
</div>
<div id="content">