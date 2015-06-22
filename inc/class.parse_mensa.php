<?php

class parseMensa implements ParseInterface
{

    private $data = Array();
    
    public function parse($content) 
    {
        if ($content == "") {
            return false; 
        }
        
        include_once CWD . DS . "inc" . DS . "parse" . DS . "mensa" . DS . "gymdon.php";
        $parser = "PARSE_MENSA_GYMDON";
        $parser = new $parser();
        $data = $parser->parse($content);
        
        if($data === false) {
            return false; 
        }
        
        $this->data[] = $data;
        return true;
    }

    public function CleanDatabase() 
    {
        dbquery("DELETE FROM mensa WHERE day < ".gmmktime(0, 0, 0));
    }

    public function UpdateDatabase() 
    {
        $this->CleanDatabase();
        
        foreach($this->data as $data) {
            foreach($data as $entry) { 
                dbquery("INSERT INTO mensa (day,menu1,menu2,desert,salad,additives) VALUES (".filter($entry['date']).", '".filter($entry['menu1'])."', '".filter($entry['menu2'])."', '".filter($entry['desert'])."', '".filter($entry['salad'])."', '".filter($entry['additives'])."')");
            }
        }
    }

}
?>