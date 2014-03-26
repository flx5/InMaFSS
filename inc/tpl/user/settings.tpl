<div class="settings">
    <h1>Einstellungen</h1>
    <div class="settings_region">
        <h2>OAuth</h2>
        <?php
        $apps = OAuthHelper::GetConsumers(Authorization::GetUserID('LDAP'));

        if(count($apps) == 0)   
            echo '<h4>'.lang()->loc('no.consumer', false).'</h4>';
        
        foreach ($apps as $id => $scopes) {
            $name = OAuthHelper::GetConsumerName($id);

            // Consumer does not exist
            if ($name === null)
                continue;

            echo '<div class="consumer">';
            echo '<h3>' . OAuthHelper::GetConsumerName($id) . '</h3>';
            echo '<b>Rechte</b>';

            foreach ($scopes as $scope) {
                echo '<div class="scope">';
                $result = lang()->loc('scope_' . $scope, true, true);

                if ($result == 'scope_' . $scope)
                    echo $scope;

                echo '</div>';
            }
            
            echo '<form method="post">';
            echo '<input type="hidden" name="delOauthID" value="'.$id.'">';
            echo '<input type="submit" value="'.lang()->loc('delete', false).'">';
            echo '</form>';

            echo '</div>';
        }
        ?>
    </div>
</div>