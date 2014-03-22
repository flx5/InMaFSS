<?php

/* =================================================================================*\
  |* This file is part of InMaFSS                                                    *|
  |* InMaFSS - INformation MAnagement for School Systems - Keep yourself up to date! *|
  |* ############################################################################### *|
  |* Copyright (C) flx5                                                              *|
  |* E-Mail: me@flx5.com                                                             *|
  |* ############################################################################### *|
  |* InMaFSS is free software; you can redistribute it and/or modify                 *|
  |* it under the terms of the GNU Affero General Public License as published by     *|
  |* the Free Software Foundation; either version 3 of the License,                  *|
  |* or (at your option) any later version.                                          *|
  |* ############################################################################### *|
  |* InMaFSS is distributed in the hope that it will be useful,                      *|
  |* but WITHOUT ANY WARRANTY; without even the implied warranty of                  *|
  |* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                            *|
  |* See the GNU Affero General Public License for more details.                     *|
  |* ############################################################################### *|
  |* You should have received a copy of the GNU Affero General Public License        *|
  |* along with InMaFSS; if not, see http://www.gnu.org/licenses/.                   *|
  \*================================================================================= */

class tpl {

    var $content;
    var $headers;
    var $params;
    private $standards = Array();
    private $outputSent = false;

    public function Init($title) {
        $this->content = '';
        $this->headers = Array(
            '<title>InMaFSS // ' . $title . '</title>',
            '<meta http-equiv="content-type" content="text/html; charset=UTF-8">'
        );

        $this->addCSS(WWW . '/css/main.css');

        $this->params = Array('username' => USERNAME, "www" => WWW);
    }

    public function registerStandard($page, $callback) {
        $this->standards[$page] = $callback;
    }

    public function addStandards($page) {

        if (isset($this->standards[$page]))
            $this->standards[$page]($this);
    }

    public function addTemplate($name) {
        $tpl = new Template($name);
        $this->content .= $tpl->GetHtml();
    }

    public function addTemplateClass($tpl) {
        $this->content .= $tpl->GetHtml();
    }

    public function getTemplate($name) {
        return new Template($name);
    }

    public function addHeader($text) {
        $this->headers[] = $text;
    }

    public function addJS($url) {
        $this->addHeader('<script type="text/javascript" src="' . $url . '"></script>');
    }

    public function addCSS($url) {
        $this->addHeader('<link rel="stylesheet" type="text/css" href="' . $url . '">');
    }

    public function write($text) {
        $this->content .= $text;
    }

    public function setParam($title, $value) {
        $this->params[$title] = $value;
    }

    private function GetHeader() {
        $output = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">' . "\n";
        $header = "";
        foreach ($this->headers as $head) {
            $header .= $head . "\n";
        }

        $output .= "<!-- InMaFSS Version: " . core::getVersion() . " -->\n";

        $output .= "<html>\n<head>\n" . $header . "\n</head>\n<body>\n";
        return $output;
    }

    private function GetFooter() {
        return "\n</body>\n</html>";
    }

    public function Flush() {
        $header = "";
        if (!$this->outputSent) {
            $this->disable_gzip();
            ob_start();
            $header = $this->GetHeader();
        }
        $output = $header . $this->content;
        foreach ($this->params as $key => $value) {
            $output = preg_replace('/%' . $key . '%/', $value, $output);
        }
        echo $output;
        echo str_pad('', 4096) . "\n";
        ob_flush();
        flush();
        // Clean buffer
        $this->content = "";
        $this->outputSent = true;
    }

    function disable_gzip() {
        for ($i = 0; $i < ob_get_level(); $i++) {
            ob_end_flush();
        }
        ini_set('zlib.output_compression', 0);
        ini_set('output_buffering', 0);
        ini_set('output_handler', '');
        apache_setenv('no-gzip', 1);
        ob_implicit_flush(1);
    }

    function __destruct() {
        echo $this->GetFooter();
        // ob_end_flush();
    }

    public function Output() {
        $this->Flush();
    }

}

class Template {

    var $tplName;
    var $params = Array();
    var $vars = Array();

    public function Template($tplName) {
        $this->tplName = $tplName;
    }

    public function setParam($title, $value) {
        $this->params[$title] = $value;
    }

    public function setVar($name, $value) {
        $this->vars[$name] = $value;
    }

    public function GetHtml() {
        $file = CWD . 'inc/tpl/' . $this->tplName . '.tpl';

        if (!file_exists($file)) {
            core::SystemError('Template system error', 'Could not load template: ' . $this->tplName);
        }

        foreach ($this->vars as $key => $var) {
            $$key = $var;
        }

        ob_start();
        include($file);
        $data = ob_get_contents();
        ob_end_clean();

        foreach ($this->params as $key => $value) {
            $data = preg_replace('/%' . $key . '%/', $value, $data);
        }

        return $data;
    }

}

?>