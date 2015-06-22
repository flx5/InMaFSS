<?php

class Controller_Me extends RestController
{

    public function GetDescription() 
    {
        return "Information about current user";
    }

    public function GET() 
    {
        $auth = Authorization::GenerateInstance('LDAP');
        /* @var $auth LDAP_Auth */

        $user = $auth->getUserDataByID($this->user);

        $this->response = Array(
            'display_name' => $user['display_name'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'id' => $user['id'],
            'groups' => $user['groups'],
            'type' => $user['type'],
        );

        $this->meta = Array(
            'types' => (object)Array(
                ReplacementsTypes::PUPIL => 'pupil',
                ReplacementsTypes::TEACHER => 'teacher'
            )
        );
    }

}

?>
