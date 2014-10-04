<?php

class OAuthHelper {

    public static function GetConsumers($user) {
        $sql = dbquery("SELECT client_id, scope FROM oauth_access_tokens WHERE user_id = " . $user);

        $apps = Array();

        while ($app = $sql->fetchAssoc()) {
            if (!isset($apps[$app['client_id']])) {
                $apps[$app['client_id']] = Array();
            }

            $scopes = explode(" ", $app['scope']);
            $apps[$app['client_id']] = array_merge($apps[$app['client_id']], array_diff($scopes, $apps[$app['client_id']]));
        }

        $sql = dbquery("SELECT refresh_token, client_id, scope FROM oauth_refresh_tokens WHERE user_id = " . $user);

        while ($app = $sql->fetchAssoc()) {
            if (!isset($apps[$app['client_id']])) {
                $apps[$app['client_id']] = Array();
            }

            $scopes = explode(" ", $app['scope']);
            $apps[$app['client_id']] = array_merge($apps[$app['client_id']], array_diff($scopes, $apps[$app['client_id']]));
        }

        return $apps;
    }

    public static function GetConsumerName($id) {
        $sql = dbquery("SELECT title FROM oauth_clients WHERE client_id = '" . filter($id) . "' LIMIT 1");
        if ($sql->count() == 1)
            return $sql->result();

        return null;
    }

    public static function RemoveConsumerAccess($consumer, $user) {
        $consumer = filter($consumer);
        $user = filter($user);
        
        dbquery("DELETE FROM oauth_access_tokens WHERE client_id = '" . $consumer . "' AND user_id = " . $user);
        dbquery("DELETE FROM oauth_refresh_tokens WHERE client_id = '" . $consumer . "' AND user_id = " . $user);
    }

}

?>
