<?php

class DB_Auth extends Authorization {

    public function Login($username, $password) {
        $username = filter($username);
        $password = getVar("core")->generatePW($username, $password);
        $password = filter($password);

        $user = $this->GetUserData("username = '" . $username . "' AND password = '" . $password . "'");
        
        if($user === null)
            return false;

        $this->SetSession($username, $user['id'], 'DB', $user['groups'], ReplacementsTypes::PUPIL, $user['display_name']);

        return true;
    }

    private function GetUserData($where) {
        $sql = dbquery("SELECT id, username FROM users WHERE " . $where . " LIMIT 1");
        if ($sql->count() == 0)
            return null;

        $user = $sql->fetchAssoc();
        
        $user['display_name'] = $user['username'];
        $user['first_name'] = '';
        $user['last_name'] = '';
        $user['groups'] = Array();
        $user['type'] = -1;
        return $user;
    }

    public function Logout() {
        $this->DestroySession();
    }

    public function HasFuse($fuse) {
        switch ($fuse) {
            default:
                return true;
        }
    }

    public function getUserDataByID($id) {
        $id = filter($id);
        return $this->GetUserData('id = '.$id);
    }

    public function getUserDataByName($username) {
        $username = filter($username);
        return $this->GetUserData("username = '".$username."'");
    }

}

?>
