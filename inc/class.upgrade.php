<?php

class Upgrade {

    public static function Exec($oldVersion) {
        switch ($oldVersion) {
            case 'v2.0.0-beta.1':
                dbquery("ALTER TABLE `events` ADD `end` INT( 11 ) NOT NULL AFTER `startdate`");
                break;
        }
    }

}

?>
