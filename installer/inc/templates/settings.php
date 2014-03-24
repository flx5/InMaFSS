<?php
if (isset($_POST['schoolname']) && isset($_POST['system']) && isset($_POST['admin_name']) && isset($_POST['admin_pass'])) {
    $db = Core::DBfromSession();
    $db->Connect();

    $schoolname = $db->real_escape_string($_POST['schoolname']);
    $system = $db->real_escape_string($_POST['system']);
    $admin_name = $db->real_escape_string($_POST['admin_name']);
    $admin_pass = $_POST['admin_pass'];
    $salt = $_SESSION['salt'];
    $admin_pass = sha1($salt . md5($admin_pass . $salt . $admin_name));
    $admin_pass = $db->real_escape_string($admin_pass);

    $db->DoQuery("DELETE FROM settings");
    $db->DoQuery("DELETE FROM users");

    $db->DoQuery("INSERT INTO settings (schoolname, system, lang, auto_addition, time_for_next_page, teacher_time_for_next_page, updateStyle) VALUES ('" . $schoolname . "','" . $system . "', 'de', 0, 15, 15, 'ajax')");
    $db->DoQuery("INSERT INTO users (username, password) VALUES ('" . $admin_name . "', '" . $admin_pass . "')");
    echo "OK, Settings saved.";
    $button['back']['target'] = $nextStep;
    return;
}
$button['continue']['target'] = $nextStep;
?>
<table>
    <tr>
        <td>Schoolname</td>
        <td><input type="text" name="schoolname" value=""></td>
    </tr>
    <tr>
        <td>System</td>
        <td>
            <select name="system">
                <option value="willi2">Willi2</option>
            </select>
        </td>
    </tr>
    <tr>
        <th colspan="2"><center>Administrator Account</center></th>
</tr>
<tr>
    <td>Name</td>
    <td><input type="text" name="admin_name" value=""></td>
</tr>
<tr>
    <td>Password</td>
    <td><input type="password" name="admin_pass" value=""></td>
</tr>
</table>