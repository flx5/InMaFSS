<?php
if (!isset($_GET['key']) && !isset($_GET['new'])) {
    header("Location: oauth.php");
    exit;
}
?>
<div class="content">
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

                    dbquery("INSERT INTO oauth_clients (title, client_id, client_secret, redirect_uri, grant_types, scope, user_id) VALUES ('" . filter($_POST['title']) . "', '" . filter($key) . "', '" . filter($secret) . "', '" . filter($_POST['callback_uri']) . "', 'authorization_code, refresh_token, client_credentials','', " . USER_ID . ")");

                    header("Location: oauth_edit.php?key=" . $key);
                    exit;
                } else {
                    dbquery("UPDATE oauth_clients SET title = '".filter($name)."', redirect_uri = '".filter($_POST['callback_uri'])."'");
                }
            } else {
                foreach ($error as $err) {
                    echo $err . '<br>';
                }
            }
        }

        if (isset($_GET['new'])) {
            $data = Array('client_id' => -1, 'title' => '', 'redirect_uri' => '');
            if (isset($_POST['title']))
                $data = array_merge($data, $_POST);
        } else {
            $sql = dbquery("SELECT * FROM oauth_clients WHERE client_id = '" . $key . "'");
            $data = $sql->fetchAssoc();
        }
        ?>
        <form method="post" action="">
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
        </form>
    </div>
    <?php
    if (!isset($_GET['new'])) {
        ?>
        <div class="round" style="width:90%; margin:5px auto; margin-top:20px;">
            <h2>Consumer Key</h2>
            <div class="inner">
                <table style="margin:auto;">
                    <tr>
                        <td>
                            <label>Consumer Key</label>
                        </td>
                        <td><?php echo $data['client_id']; ?></td>
                    </tr>
                    <tr>
                        <td>
                            <label>Consumer Secret</label>
                        </td>
                        <td><?php echo $data['client_secret']; ?></td>
                    </tr>            
                </table>
            </div>
        </div>    
<?php } ?>
</div>