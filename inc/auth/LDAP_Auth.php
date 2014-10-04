<?php

class LDAP_Auth extends Authorization {

    var $ldaphost = "ldaps://10.16.1.1/";  // your ldap servers
    var $ldapport = 636;                 // your ldap server's port number
    var $base_dn = 'dc=paedgymdon,dc=local';
    private $link;
    var $error;
    private $displayName = '';
    private $groups = Array();
    private $force_debug_login = true;

    function __construct() {
        if (!function_exists('ldap_connect'))
            throw new Exception("PHP is missing the LDAP extension");

        $this->Connect();
    }

    public function Logout() {
        $this->DestroySession();
    }

    public function HasFuse($fuse) {
        return false;
    }

    public function Login($username, $password) {
        $password = $this->filter($password);

        $user = $this->getUserDataByName($username);

        if ($user != null) {
            if (@ldap_bind($this->link, $user['dn'], $password) || $this->force_debug_login) {
                $this->displayName = $user['display_name'];
                $this->groups = $user['groups'];

                $this->SetSession($username, $user['id'], "LDAP", $user['groups'], $user['type'], $user['display_name']);

                return true;
            }
        }
        return false;
    }

    public function getUserDataByID($id) {
        $id = $this->filter($id);
        return $this->getUserData('uidNumber=' . $id);
    }

    public function getUserDataByName($username) {
        $username = $this->filter($username);
        return $this->getUserData('uid=' . $username);
    }

    private function getUserData($filter) {

        // limit attributes we want to look for
        $attributes_ad = array("givenName", "sn", "uid", "uidNumber", "displayName", "gidnumber");

        $r = @ldap_search($this->link, $this->base_dn, $filter, $attributes_ad);

        if ($r) {
            $result = @ldap_get_entries($this->link, $r);
            if ($result['count'] == 1) {
                $result = $result[0];
                $user = Array();
                $user['display_name'] = $result['displayname'][0];
                $user['first_name'] = $result['givenname'][0];
                $user['last_name'] = $result['sn'][0];
                $user['id'] = $result['uidnumber'][0];
                $user['dn'] = $result['dn'];

                $user['groups'] = Array();

                $groups = $result['gidnumber'];
                unset($groups['count']);

                foreach ($groups as $groupID) {
                    $group = $this->getGroupData($groupID);
                    $names = $group['displayname'];
                    unset($names['count']);
                    $user['groups'] = array_merge($user['groups'], $names);
                }

                $user['type'] = ReplacementsTypes::PUPIL;

                foreach ($user['groups'] as $k => $grade) {
                    if ($grade == "teachers") {
                        $user['type'] = ReplacementsTypes::TEACHER;
                    }

                    if ($grade == "12q" || $grade == "11q") {
                        $gN = substr($grade, 0, 2);
                        $user['groups'][$k] = 'Q' . $gN;
                    }
                }

                return $user;
            }
        }
        return null;
    }

    private function getGroupData($gID) {
        $r = @ldap_search($this->link, "ou=groups," . $this->base_dn, 'gidNumber=' . $gID);

        if (!$r)
            return null;

        $result = ldap_get_entries($this->link, $r);
        return $result[0];
    }

    private function Connect() {
        $this->link = ldap_connect($this->ldaphost, $this->ldapport);

        // Only works with OPENLDAP 1.X.X else this must be caught later
        if ($this->link === false)
            throw new Exception("Could not connect to $this->ldaphost");

        ldap_set_option($this->link, LDAP_OPT_PROTOCOL_VERSION, 3);

        // Checking the connection
        if (!@ldap_bind($this->link)) {
            throw new Exception("Could not connect to $this->ldaphost");
            return;
        }
    }

    public function __destruct() {
        if ($this->link != null)
            ldap_close($this->link);
    }

    private function filter($s) {
        return preg_replace(Array("#=#", "#&#", "#,#"), "", $s);
    }

}

?>
