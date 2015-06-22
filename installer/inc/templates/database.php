<?php 
if (isset($_POST['db_type']) && isset($_POST['db_name']) && isset($_POST['db_user']) && isset($_POST['db_pass']) && isset($_POST['db_host'])) {
    $db = null;

    $errors = Array();

    switch ($_POST['db_type']) {
    case 'mysql':
        $db = SQL::GenerateInstance('MySQL', $_POST['db_host'], $_POST['db_user'], $_POST['db_pass'], $_POST['db_name']);
        $_POST['db_type'] = 'MySQL';
        break;
    case 'mysqli':
        $db = SQL::GenerateInstance('MySQLi', $_POST['db_host'], $_POST['db_user'], $_POST['db_pass'], $_POST['db_name']);
        $_POST['db_type'] = 'MySQLi';
        break;
    }

    if ($db == null) {
        $errors[] = "Invalid database type!"; 
    }
    else {
        try {
            $db->Connect();
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }

    if (count($errors) == 0) {
        echo "Database settings OK";
        $signs = Array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '?', '(', ')', ',', '!');
        $salt = "";

        for ($i = 0; $i < 20; $i++) {
            $salt .= $signs[rand(0, count($signs) - 1)];
        }

        $search = Array(
            '%db_type%',
            '%db_host%',
            '%db_user%',
            '%db_pass%',
            '%db_name%',
            '%salt%',
        );

        $replacements = Array(
            $_POST['db_type'],
            $_POST['db_host'],
            $_POST['db_user'],
            $_POST['db_pass'],
            $_POST['db_name'],
            $salt
        );

        $_SESSION['db_type'] = $_POST['db_type'];
        $_SESSION['db_host'] = $_POST['db_host'];
        $_SESSION['db_user'] = $_POST['db_user'];
        $_SESSION['db_pass'] = $_POST['db_pass'];
        $_SESSION['db_name'] = $_POST['db_name'];
        $_SESSION['salt'] = $salt;
        
        $configContent = str_replace($search, $replacements, $configContent);
        file_put_contents('../' . $configFile, $configContent);
        $button['back']['target'] = $nextStep;
        return;
    } else {
        echo '<ul>';
        foreach ($errors as $err) {
            echo '<li>' . $err . '</li>';
        }
        echo '</ul>';
    }
}

$button['continue']['target'] = $nextStep;
?>
<table>
    <tr>
        <td><b>Type</b></td>
        <td>
            <select name="db_type">
                <option value="mysql">MySQL</option>
                <option  value="mysqli">MySQLi</option>
            </select>
        </td>
    </tr>
    <tr>
        <td>Database host</td>
        <td><input type="text" name="db_host" value="localhost"></td>
    </tr>
    <tr>
        <td>Database name</td>
        <td><input type="text" name="db_name" value=""></td>
    </tr>
    <tr>
        <td>Database user</td>
        <td><input type="text" name="db_user" value=""></td>
    </tr>
    <tr>
        <td>Database password</td>
        <td><input type="password" name="db_pass" value=""></td>
    </tr>
</table>