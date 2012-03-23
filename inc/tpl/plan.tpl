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


   lang()->add('date');


    $view->AddRepacements();
    $view->CreateTables();
    $view->GetPages();

    $menu = $view->CreateMenu();
    $content = $view->GenerateContent();

if($content == "") {
    echo '<div style="height:80px; width:90%; background-color:#C0C0C0; padding:5px; margin:auto; margin-top:40%;"><h1>'.lang()->loc('empty', false).'</h1></div>';
}  else {

echo '<div style="height:30px; font-size:20px; width:95%; margin:auto;" align="center">';
echo $menu;
echo '</div>';

}

echo $content;

?>