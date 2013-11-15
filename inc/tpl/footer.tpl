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

lang()->add('ticker');
$tickers = $view_left->GetTickers();
$tickers = array_merge($tickers, $view_right->GetTickers());

ksort($tickers);

$output = Array();

if (count($tickers) == 0) {
    if(config("useMarquee")) 
        $output[] = lang()->loc('no.ticker', false);
    else
        $output[] = "'" . lang()->loc('no.ticker', false) . "'";
}

foreach ($tickers as $ticker) {
    
    $out = "";
    
    if ($ticker['automatic']) {
        $out .= lang()->loc(strtolower(substr(date("D", $ticker['day']), 0, 2)), false) . ":&nbsp";
    }

    $out .= $ticker['content'];
  
    if(!config("useMarquee"))
        $out = "'".preg_replace("/'/","\\'", $out)."'";

    if (!in_array($out, $output)) {
        $output[] = $out;
    }
}


?>
<div class="bar" style="position:absolute; bottom:0px;">
    <span class="copyright">http://flx5.com/inmafss</span>
    <div id="ticker_marquee">           
        <?php 
        // Marquee is NOT official HTML, thus we can decide wether we are going to use it
        if(config("useMarquee")) { 
        ?>
        <marquee>+++&nbsp;<?php echo implode("&nbsp;+++&nbsp;", $output); ?>&nbsp;+++</marquee>
        <?php } else { ?>
        <script language="JavaScript">
            var tickers = new Array(<?php echo implode(",", $output) ?>); 
            var tickerId = 0;    
            var tickerIntv = setInterval(rotateTicker, 3*1000);
            
            rotateTicker();
            
            function rotateTicker() {
                tickerId++;
                if(tickerId >= tickers.length)
                    tickerId = 0;
        
                var tickerDiv = document.getElementById("ticker_marquee");
                tickerDiv.innerHTML = '+++&nbsp;'+tickers[tickerId]+'&nbsp;+++'; 
            }    
        </script>
        <?php } ?>
    </div>
</div>