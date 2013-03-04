<?php
require_once(dirname(__FILE__).'/../global.php');

class API {

    private $permissions = Array();
    private $data = Array();
    
    public $UseExceptions = false;

    public function __construct($key, $useAuth = true, $useEx = false) {
        
        $this->UseExceptions = $useEx;
        
        header('content-type: application/json; charset=utf-8');

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
        
        if($this->UseExceptions) {
            throw new Exception($msg);         
        }
        else {
            exit;
        }
    }
    
    function Output($output) {
        $output = getVar('core')->FormatJson(json_encode($output));
        echo $output;
    }

    function CheckAuth($api) {

        if (isset($_GET['licence'])) {
            if (strpos(file_get_contents("http://licence.flx5.com/inmafss.php?ver=" . getVersion() . "&licence=" . $_GET['licence']), "OK") !== false) {
                $this->permissions = Array("all");
                return true;
            }
        }

        $api = filter($api);
        $sql = dbquery("SELECT permissions FROM api WHERE apikey = '" . $api . "'");

        if (mysql_num_rows($sql) != 1) {
            return false;
        }

        $data = mysql_result($sql, 0);
        $data = explode("|", $data);
        $this->permissions = $data;
        return true;
    }

    function HasPerm($perm) {
        return true; // todo!
        return in_array($perm, $this->permissions);
    }

    function GetView() {
        if (!isset($_GET['day']) || !is_numeric($_GET['day']) || strlen($_GET['day']) != 10) {
            $this->Error("Day must be Unix Timestamp");
        }

        require_once("inc/view.php");

        $day = $_GET['day'];

        $tfrom = gmmktime(0, 0, 0, date('m', $day), date('d', $day), date('Y', $day));

        $view = new View(null, 99e99);
        $view->tfrom = $tfrom;
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
                if (!isset($this->data['g']) || $grade == $this->data['g']) {
                    foreach ($val as $k => $v) {
                        $val[$k]['comment'] = preg_replace("/&nbsp;/", "", htmlentities($v['comment']));
                        $val[$k]['replacement'] = preg_replace("/&nbsp;/", "", htmlentities($v['replacement']));
                    }
                    $output[$grade] = $val;
                }
            }
        }

        if ($return)
            return $output;

        $this->Output($output);
    }

    public function other($return = false) {
        if (!isset($this->data['type'])) {
            $this->Error("Specify a type");
        }

        $view = $this->GetView();
        $view->type = 1;
        $view->AddRepacements();

        $output = Array();


        foreach ($view->replacements[1] as $k => $val) {
            if ($k == $_GET['type']) {
                $output[$k] = $val;
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
                        if (!isset($_GET['short']) || $k == $_GET['short']) {
                            foreach ($val as $i => $entry) {
                                foreach ($entry as $f => $x)
                                    $val[$i][$f] = html_entity_decode($x, ENT_COMPAT, "UTF-8");
                            }

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

}

?>
