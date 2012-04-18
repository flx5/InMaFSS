<?php
/*=================================================================================*\
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
\*=================================================================================*/


class tpl {
    var $content;
    var $headers;
    var $params;

    public function Init($title) {
         $this->content = '';
         $this->headers = Array('<title>InMaFSS // '.$title.'</title>','<link rel="stylesheet" type="text/css" href="'.WWW.'/main.css">');
         $this->params = Array('username'=>USERNAME);
         $this->addJS(WWW."/main.js");
    }

    public function addStandards($page) {
               switch($page) {
                   case 'admin':
                        $this->addTemplate('clock');
                        $this->addTemplate('admin_header');
                        $this->addTemplate('menu');
                   break;
               }
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
       $this->addHeader('<script type="text/javascript" src="'.$url.'"></script>');
    }

    public function write($text) {
         $this->content .= $text;
    }

    public function setParam($title,$value) {
         $this->params[$title] = $value;
    }

    public function Output() {
         $output = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'."\n";

         $header = "";
         foreach($this->headers as $head) {
              $header .= $head."\n";
         }

         $output .= "<html>\n<head>\n".$header."\n</head>\n<body>\n".$this->content."\n</body>\n</html>";

         foreach($this->params as $key=>$value) {
             $output = preg_replace('/%'.$key.'%/', $value, $output);
         }
         echo $output;
    }
}

class Template {
    var $tplName;
    var $params = Array();
    var $vars = Array();

    public function Template($tplName) {
       $this->tplName = $tplName;
    }

    public function setParam($title,$value) {
         $this->params[$title] = $value;
    }

    public function setVar($name, $value) {
         $this->vars[$name] = $value;
    }

    public function GetHtml()
    {
                $file = CWD . 'inc/tpl/' . $this->tplName . '.tpl';

                if (!file_exists($file))
                {
                        core::SystemError('Template system error', 'Could not load template: ' . $this->tplName);
                }

                foreach($this->vars as $key=>$var) {
                     $$key = $var;
                }

                ob_start();
                include($file);
                $data = ob_get_contents();
                ob_end_clean();

                foreach($this->params as $key=>$value) {
                   $data = preg_replace('/%'.$key.'%/', $value, $data);
                }

                return $data;
    }
}


?>