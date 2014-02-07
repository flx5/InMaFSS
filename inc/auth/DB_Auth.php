<?php

class DB_Auth extends Authorization {

    private $username = '';
    
    public function Login($username, $password) {
        $username = filter($username);
        $password = getVar("core")->generatePW($username, $password);
        $password = filter($password);

        $sql = dbquery("SELECT id FROM users WHERE username = '" . $username . "' AND password = '" . $password . "' LIMIT 1");

        if ($sql->count() == 0)
            return false;

        $this->SetSession($username, $sql->result());
        $this->username = $username;
        
        return true;
    }

    public function Logout() {
        $this->DestroySession();
    }

    public function HasFuse($fuse) {
        switch($fuse) {
            default:
                return true;
        }
    }

    public function GetClasses() {
        return Array();
    }

    public function GetDisplayName() {
        return $this->username;
    }
}

?>
