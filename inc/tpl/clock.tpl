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
?>


<script language="JavaScript">
<!--
var d = <?php echo date('j'); ?>, m = <?php echo date('n'); ?>, y = <?php echo date('Y'); ?>;
var h = <?php echo date('G'); ?>, n = <?php echo intval(date('i')); ?>, s = <?php echo intval(date('s')); ?>;
window.onload = setInterval("tick()", 1000);
function tick() {
if (++s == 60) {
if (++n == 60) {
if (++h == 24) {
++d;
h = 0;
}
n = 0;
}
s = 0;
}
var out;
if (d < 10)
out = "0" + d;
else
out = String(d);
out += ".";
if (m < 10)
out += "0" + m;
else
out += String(m);
out += "." + y + " ";
if (h < 10)
out += "0" + h;
else
out += String(h);
out += ":";
if (n < 10)
out += "0" + n;
else
out += String(n);
out += ":";
if (s < 10)
out += "0" + s;
else
out += String(s);
document.getElementById('clock').innerHTML = out;
}
//-->
</script>
<noscript>
<style type="text/css">
<!--
#clock {
display: none;
}
=-->
</style>
</noscript>
