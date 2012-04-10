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

class pluginManager {
       private $ready = false;
       private $plugins = Array();
       private $handler;

       public function Init() {
              $this->handler = new handler();
              $this->reloadPlugins();
       }

       public function reloadPlugins() {
            $this->ready = false;

            foreach (glob(CWD."plugins/*.php") as $file) {
                  $file = substr($file,strlen(CWD."plugins/"),-4);
                  $this->plugins[$file] = new plugin($file,$this->handler);
            }
       }

       public function ExecuteEvent($event, $params = null) {
               return $this->handler->ExecuteEvent($event, &$params);
       }
}

class plugin {
      var $handler;
      var $plugin;
      var $running = false;

      public function plugin($file, $handler) {
             $this->handler = $handler;
             ob_start();
             include(CWD."plugins/".$file.".php");
             ob_clean();

             $this->plugin = new $file();
             setVar("PLUGIN",true);
             if(!$this->plugin->Init($this->handler)) {
                  setVar("PLUGIN",false);
                  return;
             }
             setVar("PLUGIN",false);
             $this->running  = true;
      }
}

class handler {
     var $executers = Array();

     public function RegisterEvent($plugin, $event, $action) {
           if(!isset($this->executers[$event])) {
                $this->executers[$event] = Array();
           }
           $this->executers[$event][] = Array("plugin"=>$plugin, "action"=>$action);
     }

     public function ExecuteEvent($event, $params = null) {
             if(!isset($this->executers[$event])) {
                   return false;
             }
             setPlugin(true, $this);
             foreach($this->executers[$event] as $action) {
                    ob_start();
                    $action["plugin"]->$action["action"](&$params);
                    ob_end_clean();
             }
             setPlugin(false, $this);
     }


}
?>