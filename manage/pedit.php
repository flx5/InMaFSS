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


require_once "global.php";
lang()->add('pages');
lang()->add('date');

getVar("tpl")->Init(lang()->loc('page', false));
getVar("tpl")->addStandards('admin');

getVar("tpl")->addJS('./tinymce/tinymce.min.js');
getVar("tpl")->addHeader(
    '<script type="text/javascript" >
tinyMCE.init({
        selector : "textarea",
        theme : "modern",
        language  : "'.lang()->info('lang.short', false).'",
        plugins : "advlist autolink link image lists charmap emoticons hr insertdatetime preview",
        height:"400px",
      
});
</script>
<link rel="stylesheet" type="text/css" href="./calendar/tcal.css" />
<script type="text/javascript" src="./calendar/tcal.js"></script>
<script type="text/javascript" >
var A_TCALCONF = {
        "cssprefix"  : "tcal",
        "months"     : ["'.lang()->loc('january', false).'", "'.lang()->loc('february', false).'", "'.lang()->loc('march', false).'", "'.lang()->loc('april', false).'", "'.lang()->loc('may', false).'", "'.lang()->loc('june', false).'", "'.lang()->loc('july', false).'", "'.lang()->loc('august', false).'", "'.lang()->loc('september', false).'", "'.lang()->loc('october', false).'", "'.lang()->loc('november', false).'", "'.lang()->loc('december', false).'"],
        "weekdays"   : ["'.lang()->loc('su', false).'", "'.lang()->loc('mo', false).'", "'.lang()->loc('tu', false).'", "'.lang()->loc('we', false).'", "'.lang()->loc('th', false).'", "'.lang()->loc('fr', false).'", "'.lang()->loc('sa', false).'"],
        "yearscroll" : true, // show year scroller
        "weekstart"  : 1, // first day of week: 0-Su or 1-Mo
        "prevyear"   : "'.lang()->loc('prev.year', false).'",
        "nextyear"   : "'.lang()->loc('next.year', false).'",
        "prevmonth"  : "'.lang()->loc('prev.month', false).'",
        "nextmonth"  : "'.lang()->loc('next.month', false).'",
        "format"     : "d.m.Y" // "d-m-Y", Y-m-d", "l, F jS Y"
};
</script>
'
);

getVar("tpl")->addTemplate('manage/pages_edit');
getVar("tpl")->Output();
?>