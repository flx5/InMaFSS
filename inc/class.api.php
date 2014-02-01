<?php

require_once(dirname(__FILE__) . '/../global.php');
require_once(INC . "class.view.php");

class API {

    private $permissions = Array();
    private $data = Array();
    public $tfrom = 0;
    public $UseExceptions = false;

    public function __construct($key, $useAuth = true, $useEx = false) {

        $this->UseExceptions = $useEx;

        header('Content-type: application/json; charset=utf-8');

        if ($useAuth && (is_null($key) || !$this->CheckAuth($key))) {
            $this->Error("You have not been authenticated!");
        }

        if (isset($_GET))
            $this->data = array_merge($this->data, $_GET);
        if (isset($_POST))
            $this->data = array_merge($this->data, $_POST);
    }

    public function AddData($data) {
        $this->data = array_merge($this->data, $data);
    }

    public function Exec($return = false) {

        if (!isset($this->data['action'])) {
            $this->Error("No action specified");
        }

        if (!$this->HasPerm($this->data['action'])) {
            $this->Error("This action does not exist or you don't have sufficient rights for this action");
        }

        if (isset($this->data['day'])) {
            $tfrom = $this->data['day'];

            if (!is_numeric($tfrom)) {
                switch ($this->data['day']) {
                    case 'today':
                        $tfrom = gmmktime(0, 0, 0);
                        break;
                    case 'tomorrow':
                        $tfrom = View::GetNextDay();
                        break;
                    default:
                        $this->Error("Param day is neither today|tomorrow|numeric");
                        break;
                }
            }

            $this->tfrom = $tfrom;
        } else {
            $this->tfrom = gmmktime(0, 0, 0);
        }

        switch ($this->data['action']) {
            case 'plan_update':
                return $this->plan_update($return);

            case 'replacements':
                return $this->replacements($return);

            case 'other':
                return $this->other($return);

            case 'teacher_sub':
                return $this->teacherSub($return);

            case 'ticker':
                return $this->ticker($return);

            default:
                $this->Error("Unknown action!");
                break;
        }
    }

    function Error($msg) {
        $this->Output(Array('STATUS' => "ERROR", 'message' => $msg));

        if ($this->UseExceptions) {
            throw new Exception($msg);
        } else {
            exit;
        }
    }

    function EntityDecodeArray($output) { 
        $ret = Array();
        
        foreach($output as $k=>$o) {
            $k = html_entity_decode($k, ENT_COMPAT, "UTF-8");
            
            if(is_array($o))
            {
                $ret[$k] = $this->EntityDecodeArray($o);
                continue;
            }
            if(!is_string($o))
            {
                $ret[$k] = $o;
                continue; // Don't decode integers and booleans :D
            }
            
            $ret[$k] = html_entity_decode($o, ENT_COMPAT, "UTF-8");
        }
        
        return $ret;
    }
    
    function Output($output) {        
        if ($this->tfrom != 0)
            $output['day'] = $this->tfrom;   
        
        $output = $this->EntityDecodeArray($output);
        
        $output = getVar('core')->FormatJson(json_encode($output));
        
        echo $output;
    }

    function CheckAuth($api) {
        $api = filter($api);
        $sql = dbquery("SELECT permissions FROM api WHERE apikey = '" . $api . "'");

        if ($sql->count() != 1) {
            return false;
        }

        $data = $sql->result();
        $data = explode("|", $data);
        $this->permissions = $data;
        return true;
    }

    function HasPerm($perm) {
        return in_array($perm, $this->permissions);
    }

    function GetView() {
        $view = new View(null, 99e99);
        $view->tfrom = $this->tfrom;
        return $view;
    }

    /*
     * API functions
     */

    public function plan_update($return = false) {
        if (!isset($this->data['data'])) {
            $this->Error("No file content found!");
        }

        $data = urldecode($this->data['data']);

        $files = explode(chr(1), $data);
        $p = new parse();

        foreach ($files as $file) {
            
            if($file == "")
                continue;
            
            $file = utf8_decode($file);
            $file = substr($file, strpos($file, "<html>"));
            $p->parseHTML($file);
        }

        $p->UpdateDatabase();

        $output = Array('STATUS' => "OK", 'message' => 'Import completed');

        if ($return)
            return $output;

        $this->Output($output);
    }

    public function replacements($return = false) {
        if (!$this->HasPerm('replacements_all') && !isset($this->data['g'])) {
            $this->Error("You must provide a Grade!");
        }

        $view = $this->GetView();
        $view->AddRepacements();

        $output = Array();

        foreach ($view->replacements as $page) {
            foreach ($page as $grade => $val) {

                $pos = $this->findFirstLetter($grade);

                $gN = substr($grade, 0, $pos);
                $gS = substr($grade, $pos);

                if (strlen($gS) > 1 && $pos > 0) {
                    $gS = substr($gS, 0, 1);
                }

                $_grade = $gN . $gS;

                if (!isset($this->data['g']) || $_grade == $this->data['g'] ||
                        ($this->data['g'] == "11q" && ($_grade == "Q11" || $gN == "11")) ||
                        ($this->data['g'] == "12q" && ($_grade == "Q12" || $gN == "12")) ||
                        (strlen($gN) == 0 && $_grade != "Q11" && $_grade != "Q12")
                ) {

                    foreach ($val as $i => $entry) {
                        foreach ($entry as $f => $x) { 
                            $val[$i][$f] = trim(preg_replace("/&nbsp;/", "", $x));                          

                            switch ($f) {
                                case 'id':
                                case 'timestamp':
                                case 'timestamp_update':
                                    $val[$i][$f] = intval($val[$i][$f]);
                                    break;
                                case 'addition':
                                    $val[$i][$f] = ($val[$i][$f] == 1);
                                    break;
                            }
                        }
                    }

                    $output[$grade] = $val;
                }
            }
        }
        
        $output['lastUpdate'] = $view->GetLastUpdate();
        
        if ($return)
            return $output;

        $this->Output($output);
    }

    public function other($return = false) {

        $view = $this->GetView();
        $view->type = 1;
        $view->AddRepacements();

        $output = Array();

        if (isset($this->data['type']))
            $this->data['type'] = explode(",", $this->data['type']);

        if (isset($view->replacements[1])) {
            foreach ($view->replacements[1] as $k => $val) {
                if (isset($this->data['type']) && !in_array($k, $this->data['type']))
                    continue;

                switch ($k) {
                    case 't':
                    case 'g':
                    case 'a':
                    case 's':
                    case 'r':
                    case 'n':
                        foreach($val as $x=>$y) {
                            foreach($y as $z=>$w) 
                            {                                                             
                                switch($z) {
                                    case "id":
                                    case 'timestamp':
                                    case 'timestamp_update':    
                                        $val[$x][$z] = intval($w);
                                        break;   
                                    case 'addition':
                                         $val[$x][$z] = ($w == 1);
                                    break;
                                }                               
                            }
                        }
                        
                        $output[$k] = $val;
                        break;
                }
            }
        }

        if ($return)
            return $output;

        $this->Output($output);
    }

    public function teacherSub($return = false) {
        $view = $this->GetView();
        $view->type = 1;
        $view->AddRepacements();

        $output = Array();

        foreach ($view->replacements as $page) { // Can only be one page!
            foreach ($page as $k => $val) {
                switch ($k) {
                    case 't':
                    case 'n':
                    case 'g':
                    case 's':
                    case 'a':
                    case 'r':
                        continue;
                        break;

                    default:
                        if (!isset($this->data['short']) || $k == $this->data['short']) {
                            if (isset($this->data['teacher']) && isset($val[0])) {

                                $req = urldecode($this->data['teacher']);

                                $s = Array('/ae/', '/oe/', '/ue/', '/Ae/', '/Ue/', '/Oe/', '/ß/');
                                $r = Array('&auml;', '&ouml;', '&uuml;', '&Auml;', '&Uuml;', '&Ouml;', '&szlig;');

                                $req = preg_replace($s, $r, $req);

                                $name_options = Array();

                                $name_options[] = $req;
                                $name_options[] = preg_replace("/ss/", '&szlig;', $req);

                                $found = false;

                                $teacher = trim($val[0]['teacher']);

                                $split = strrpos($teacher, " ");

                                $lastname = substr($teacher, 0, $split);
                                $prename = substr($teacher, $split + 1);

                                foreach ($name_options as $name) {
                                    $req = Array();
                                    $req[0] = substr($name, strrpos($name, " ") + 1);
                                    $req[1] = substr($name, 0, strrpos($name, " "));

                                    if (count($req) != 2)
                                        continue;

                                    if ($req[0] != $lastname) {
                                        continue;
                                    }

                                    $abr = strpos($prename, ".");

                                    if ($abr !== false)
                                        $req[1] = substr($req[1], 0, $abr) . ".";

                                    if ($req[1] != $prename)
                                        continue;

                                    $found = true;
                                }

                                if (!$found)
                                    continue;
                            }

                            foreach ($val as $i => $entry) {
                                foreach ($entry as $f => $x)
                                    
                                $val[$i][$f] = trim(preg_replace("/&nbsp;/", "", $x));                              

                                switch ($f) {
                                    case 'id':
                                    case 'timestamp':
                                    case 'timestamp_update':
                                        $val[$i][$f] = intval($val[$i][$f]);
                                        break;
                                    case 'addition':
                                        $val[$i][$f] = ($val[$i][$f] == 1);
                                        break;
                                }
                            }

                            if (isset($this->data['teacher']) || isset($this->data['short']))
                                $output = $val;
                            else
                                $output[$k] = $val;
                        }
                        break;
                }
            }
        }

        if ($return)
            return $output;

        $this->Output($output);
    }

    public function ticker($return = false) {
        $view = $this->GetView();
        $output = $view->GetTickers();

        if ($return)
            return $output;

        $this->Output($output);
    }

    private function findFirstLetter($str) {
        $i = 0;
        while ($i < strlen($str)) {
            if (ctype_alpha($str[$i]))
                return $i;

            $i++;
        }

        return false;
    }

}

?>
