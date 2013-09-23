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

class variables {
     private $core;
     private $lang;
     private $sql;
     private $tpl;
     private $update;
     private $pluginManager;
     private $PLUGIN;
     private $AllowOverride = Array("PLUGIN");
     private $PLUGIN_ACTOR = null;

     public function variables($core,$lang,$sql,$tpl,$update,$pluginManager, $PLUGIN) {
               $this->core          = $core;
               $this->lang          = $lang;
               $this->sql           = $sql;
               $this->tpl           = $tpl;
               $this->update        = $update;
               $this->pluginManager = $pluginManager;
               $this->PLUGIN = $PLUGIN;
     }

     public function Get($var) {
            if($this->PLUGIN && $var != "PLUGIN") {
              return;
            }
            return $this->$var;
     }

     public function Set($var,$val) {
            if($this->PLUGIN || $var == "PLUGIN") {
              return;
            }
            if($this->$var == null || in_array($var, $this->AllowOverride)) {
                   $this->$var = $val;
            }
     }

     public function SetPlugin($val,$actor) {
             if($this->PLUGIN_ACTOR == null && !$val) {
                       return;
             }

             if($val) {
                  $this->PLUGIN_ACTOR = $actor;
             }
             $this->PLUGIN = $val;
     }
}
?>