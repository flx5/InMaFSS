<?php
if (!isset($_GET['key']) && !isset($_GET['new'])) {
    header("Location: oauth.php");
    exit;
}

require_once(INC.'class.scope_data.php');

$authTypes = Array(
    'authorization_code',
    'refresh_token',
    'client_credentials',
    'password',
    'implicit'
);
?>
<div class="content">
    <form method="post" action="">
        <div class="round" style="width:90%; margin:5px auto; margin-top:20px;">
            <h2><?php lang()->loc('title'); ?></h2>
            <?php
            $key = -1;

            if (!isset($_GET['new'])) {
                $key = filter($_GET['key']);
            }

            if (isset($_POST['title']) && isset($_POST['callback_uri'])) {
                $name = trim($_POST['title']);

                $error = Array();

                if (strlen($name) < 3) {
                    $error[] = lang()->loc('name.too.short', false);
                }

                $grants = "";
                foreach ($authTypes as $grant) {
                    if (isset($_POST[$grant]))
                        $grants .= " " . $grant;
                }
                $grants = trim($grants);
                
                $scopes = "";
                foreach (ScopeData::GetScopes() as $scope) {
                    if (isset($_POST[$scope]))
                        $scopes .= " " . $scope;
                }
                $scopes = trim($scopes);

                if (count($error) == 0) {

                    if (isset($_GET['new'])) {
                        do {
                            $entropy = openssl_random_pseudo_bytes(32);
                            $entropy .= uniqid(mt_rand(), true);
                            $hash = hash('sha512', $entropy);

                            $key = substr($hash, 0, 64);
                            $secret = substr($hash, 64, 128);

                            $sql = dbquery("SELECT * FROM oauth_clients WHERE client_id = '" . filter($key) . "'");
                        } while ($sql->count() != 0);

                        dbquery("INSERT INTO oauth_clients (title, client_id, client_secret, redirect_uri, grant_types, scope, user_id) VALUES ('" . filter($_POST['title']) . "', '" . filter($key) . "', '" . filter($secret) . "', '" . filter($_POST['callback_uri']) . "', '" . filter($grants) . "','" . filter($scopes) . "', " . USER_ID . ")");

                        header("Location: oauth_edit.php?key=" . $key);
                        exit;
                    } else {
                        dbquery("UPDATE oauth_clients SET title = '" . filter($name) . "', redirect_uri = '" . filter($_POST['callback_uri']) . "', grant_types = '" . filter($grants) . "', scope = '" . filter($scopes) . "' WHERE client_id = '".$key."'");
                    }
                } else {
                    foreach ($error as $err) {
                        echo $err . '<br>';
                    }
                }
            }

            if (isset($_GET['new'])) {
                $defaultScopes = Array(
                ScopeData::BASIC,
                ScopeData::OTHER,
                ScopeData::SUBSTITUTION_PLAN,
                ScopeData::TICKER
                );
                $data = Array('client_id' => -1, 'title' => '', 'redirect_uri' => '', 'grant_types' => 'authorization_code refresh_token client_credentials', 'scope'=>implode(" ", $defaultScopes));
                if (isset($_POST['title']))
                    $data = array_merge($data, $_POST);
            } else {
                $sql = dbquery("SELECT * FROM oauth_clients WHERE client_id = '" . $key . "'");
                $data = $sql->fetchAssoc();
            }
            $grants = explode(" ", $data['grant_types']);
            $scopes = explode(" ", $data['scope']);
            ?>

            <table style="margin:auto;">
                <tr>
                    <td>
                        <label for="title"><?php lang()->loc('application_title'); ?></label>
                    </td>
                    <td>
                        <input size="100" type="text" id="title" name="title" value="<?php echo $data['title']; ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="callback_uri"><?php lang()->loc('callback_uri'); ?></label>
                    </td>
                    <td>
                        <input size="100" type="text" id="callback_uri" name="callback_uri" value="<?php echo $data['redirect_uri']; ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                    </td>
                    <td>
                        <input type="submit" value="<?php lang()->loc('save'); ?>">
                    </td>
                </tr>                

            </table>

        </div>
        <?php
        if (!isset($_GET['new'])) {
            ?>
            <div class="round" style="width:90%; margin:5px auto; margin-top:20px;">
                <h2><?php lang()->loc('consumer_key'); ?></h2>
                <div class="inner">
                    <table style="margin:auto;">
                        <tr>
                            <td>
                                <label><?php lang()->loc('consumer_key'); ?></label>
                            </td>
                            <td><?php echo $data['client_id']; ?></td>
                        </tr>
                        <tr>
                            <td>
                                <label><?php lang()->loc('consumer_secret'); ?></label>
                            </td>
                            <td><?php echo $data['client_secret']; ?></td>
                        </tr>            
                    </table>
                </div>
            </div>    
        <?php } ?>
        <div class="round" style="width:90%; margin:5px auto; margin-top:20px;">
            <h2><?php lang()->loc('login.rights'); ?></h2>
            <div class="inner">
                <table style="margin:auto;">
                    <?php
                    foreach ($authTypes as $grant) {
                        echo '<tr><td>';
                        echo '<input type="checkbox" name="' . $grant . '" ' . ((in_array($grant, $grants)) ? 'checked' : '') . '>';
                        echo '</td><td>';
                        lang()->loc('grant_' . $grant, true, true);
                        echo '</td></tr>';
                    }
                    ?>    
                </table>
                <input type="submit" value="<?php lang()->loc('save'); ?>"><br><br>
                <a target="_blank" href="http://bshaffer.github.io/oauth2-server-php-docs/overview/grant-types/"><?php lang()->loc('further.information'); ?></a>
            </div>
        </div>
        <div class="round" style="width:90%; margin:5px auto; margin-top:20px;">
            <h2><?php lang()->loc('scopes'); ?></h2>
            <div class="inner">
                <table style="margin:auto;">
                    <?php
                    foreach (ScopeData::GetScopes() as $scope) {
                        echo '<tr><td>';
                        echo '<input type="checkbox" name="' . $scope . '" ' . ((in_array($scope, $scopes)) ? 'checked' : '') . '>';
                        echo '</td><td>';
                        lang()->loc('scope_' . $scope, true, true);
                        echo '</td></tr>';
                    }
                    ?>    
                </table>
                <input type="submit" value="<?php lang()->loc('save'); ?>"><br><br>
            </div>
        </div>
    </form>
</div>