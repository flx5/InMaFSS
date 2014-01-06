<?php

if (basename($_SERVER['PHP_SELF']) == "selfupdate.php") {
    $u = new selfupdate();
    $_GET['selfupdate'] = true;
    $u->Init(null);
}

class selfupdate {

    public function Init($handler) {
        if (!isset($_GET['selfupdate']))
            return false;

        $data = Array();

        $data[] = $this->GetData("https://intranet.gymnasium-donauwoerth.de/indiss/vertretungen/lehrer/lehrer_heute.html");
        $data[] = $this->GetData("https://intranet.gymnasium-donauwoerth.de/indiss/vertretungen/lehrer/lehrer_morgen.html");

        $data[] = $this->GetData("https://intranet.gymnasium-donauwoerth.de/indiss/vertretungen/heute/vertretungsplan_heute.html");
        $data[] = $this->GetData("https://intranet.gymnasium-donauwoerth.de/indiss/vertretungen/morgen/vertretungsplan_morgen.html");

        $p = new parse();

        foreach ($data as $html) {
            # $file = utf8_decode($html);
            $p->parseHTML($html);
        }

        $p->UpdateDatabase();

        return true;
    }

    private function GetData($url) {
        $context = stream_context_create(array(
            'http' =>
            array(
                'method' => "GET",
                'header' => "Authorization: Basic cHJhc3NlZmU6WGlsZWY0NTY=\r\n"
            )
                ));

        if (version_compare(PHP_VERSION, '5.3.0') == -1) {  // Thats an annoying bug ...
            $tmpUserAgent = ini_get('user_agent');
            ini_set('user_agent', 'PHP-SOAP/' . PHP_VERSION . "\r\n" . "Authorization: Basic cHJhc3NlZmU6WGlsZWY0NTY=");
        }

        $handle = fopen($url, 'r', false, $context);

        $data = stream_get_contents($handle);
        fclose($handle);
        
        if(isset($tmpUserAgent))
            ini_set('user_agent', $tmpUserAgent);
        
        return $data;
    }

}

?>
