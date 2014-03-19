<div id="content">
    <form method="post">

        <?php
        if (isset($_GET['scope'])) {
            ?>

            Die Anwendung "<?php echo $data['title']; ?>" fordert folgende Berechtigungen: 
            <div id="scopes">
                <?php
                $scopes = explode(" ", $data['scope']);
                foreach ($scopes as $scope) {
                    echo '<div class="scope">';
                    lang()->loc('scope_'.$scope, true, true);
                    echo '</div>';
                }
                ?>
            </div>
        <?php } ?>

        <button name="allow" >Erlauben</button><button name="forbid">Ablehnen</button>
    </form>
</div>