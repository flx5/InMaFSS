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


class lang {
      private $language;
      private $local;
      private $file;
      private $info;

      public function lang($lang) {
            $this->language = $lang;
            $this->local = Array();
            $this->file = CWD.DS."inc".DS."lang".DS.$lang.".php";

            $key = "";
            require($this->file);
            $this->info = $info;

            if(!file_exists($this->file)) {
               core::SystemError('Language system error', 'Could not load language: ' . $this->language);
               return;
            }
      }

      public function add($keys){
                if(is_array($keys)){
                        foreach($keys as $key){
                                require($this->file);
                                $this->local = array_merge($this->local,$loc);
                        }
                }else{
                        $key = $keys;
                        require($this->file);
                        $this->local = array_merge($this->local,$loc);
                }
                return true;
        }

      public function loc($id, $output = true, $noError = false) {
          if(array_key_exists($id, $this->local)) {
             if($output) {
                echo $this->local[$id];
                return;
             }
             return $this->local[$id];
          }
          
          if($noError)
              return $id;
          
          core::SystemError('Language System Error', 'Could not find index '. $id);
      }

      public function info($id, $output = true) {
          if(array_key_exists($id, $this->info)) {
             if($output) {
                echo $this->info[$id];
                return;
             }
             return $this->info[$id];
          }
          core::SystemError('Language System Error', 'Could not find Info index '. $id);
      }
}