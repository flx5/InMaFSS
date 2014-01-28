<?php
if (!isset($_GET['key']) && !isset($_GET['new'])) {
    header("Location: oauth.php");
    exit;
}

$oauth = new OAuthHelper();
?>
<div class="content">
    <div class="round" style="width:90%; margin:5px auto; margin-top:20px;">
        <h2><?php lang()->loc('title'); ?></h2>
        <?php
        $key = -1;

        if (!isset($_GET['new'])) {
            $key = filter($_GET['key']);
        }

        if (isset($_POST['application_title']) && isset($_POST['application_descr']) && isset($_POST['application_uri']) && isset($_POST['application_uri'])) {
            $name = trim($_POST['application_title']);

            $error = Array();

            if (strlen($name) < 3) {
                $error[] = lang()->loc('name.too.short', false);
            }

            $_POST['requester_name'] = USERNAME;
            $_POST['requester_email'] = "test@example.com";

            if (count($error) == 0) {
                $store = $oauth->GetStore();
                
                $data = $oauth->GetStore()->getConsumer($key, USER_ID);
                
                foreach($data as $k=>$v) {
                    if(isset($_POST[$k]))
                        $data[$k] = $_POST[$k];
                }
                
                $key = $store->updateConsumer($data, USER_ID);
            } else {
                foreach ($error as $err) {
                    echo $err . '<br>';
                }
            }
        }

        if (isset($_GET['new'])) {
            $data = Array('id' => -1, 'application_title' => '', 'application_descr' => '', 'callback_uri' => '', 'application_uri' => '');
            if (isset($_POST['application_title']))
                $data = array_merge($data, $_POST);
        } else {
            $data = $oauth->GetStore()->getConsumer($key, USER_ID);
        }
        ?>
        <form method="post" action="">
            <table style="margin:auto;">
                <tr>
                    <td>
                        <label for="application_title"><?php lang()->loc('application_title'); ?></label>
                    </td>
                    <td>
                        <input size="100" type="text" id="application_title" name="application_title" value="<?php echo $data['application_title']; ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="application_descr"><?php lang()->loc('application_desc'); ?></label>
                    </td>
                    <td>
                        <textarea style="width:100%" id="application_desc" name="application_descr"><?php echo $data['application_descr']; ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="application_uri"><?php lang()->loc('application_uri'); ?></label>
                    </td>
                    <td>
                        <input size="100" type="text" id="application_uri" name="application_uri" value="<?php echo $data['application_uri']; ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="callback_uri"><?php lang()->loc('callback_uri'); ?></label>
                    </td>
                    <td>
                        <input size="100" type="text" id="callback_uri" name="callback_uri" value="<?php echo $data['callback_uri']; ?>">
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
                        <td><?php echo $data['consumer_key']; ?></td>
                    </tr>
                    <tr>
                        <td>
                            <label>Consumer Secret</label>
                        </td>
                        <td><?php echo $data['consumer_secret']; ?></td>
                    </tr>            
                </table>
            </div>
        </div>    
    <?php } ?>
</div>