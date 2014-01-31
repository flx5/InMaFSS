<form method="post">
    <?php
    if(isset($_GET['scope'])) {
        ?>
    
    Die Anwendung fordert folgende Berechtigungen: <?php echo $_GET['scope']; ?><br>
    <input type="hidden" name="unique" value="<?php echo $unique; ?>">
    <?php } ?>
    <button name="allow" >Erlauben</button>
</form>