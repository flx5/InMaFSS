<?php

class parseAppointments implements ParseInterface {

    private $data = Array();
    
    public function parse($file) {
        if ($file == "")
            return false;
        
        require_once(CWD . DS . "inc" . DS . "parse" . DS . "appointments" . DS . "ics.php");
        $parser = "PARSE_APPOINTMENTS_ICS";
        $parser = new $parser();
        $data = $parser->parse($file);
        
        if($data === false)
            return false;
        
        $this->data[] = $data;
        return true;
    }

    public function CleanDatabase() {
        dbquery("TRUNCATE TABLE events");
    }

    public function UpdateDatabase() {
        $this->CleanDatabase();
        
        foreach($this->data as $data) {
            foreach($data as $entry) { 
                dbquery("INSERT INTO events (startdate, title, content) VALUES (".filter($entry['start']).", '".filter($entry['title'])."', '".filter($entry['desc'])."')");
            }
        }
    }

}
?>