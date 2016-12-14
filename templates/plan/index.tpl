{* Smarty *}

{extends file='html.tpl'}

{block name=title}
    {t}Vertretungsplan{/t}
{/block}

{block name=head}
    <link href="{siteUrl url='/static/css/plan.css'}" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="{siteUrl url='/static/components/moment/min/moment-with-locales.js'}"></script>
    <script type="text/javascript" src="{siteUrl url='/static/js/clock.js'}" /></script>
{/block}

{block name=body}
    <div id="header" class='bar'>   
        <span style="float:left; width:20%; text-align:left; padding-left:20px; height:100%; overflow: visible; word-break: keep-all; white-space: nowrap; ">{$schoolname}</span>
        <span style="float:left; width:60%" id="header_cached">{t}ACHTUNG: DIES IST EINE ZWISCHENGESPEICHERTE ANSICHT{/t}</span>
        <span style="float:left; width:60%" id="header_normal">InMaFSS</span>
        <span id='clock' style="text-align:right; float:right; position: absolute; top:0px; right:20px;"></span>
    </div>

    <div class="main" id="plan_left" style="border-right:0px solid black;" >

    </div>

    <div class="main tomorrow" id="plan_right" style="right:0px; border-left:0px solid black;" >

    </div>

    <div class="bar" style="position:absolute; bottom:0px; overflow:hidden;width:100%; ">         
        <span id="ticker" class="marquee">+++&nbsp;{'&nbsp;+++&nbsp;'|implode:$ticker}&nbsp;+++</span>
    </div>

    <script type="text/javascript">
        window.addEventListener("load",
                function () {
                    createClock('clock');

                    var ticker = document.getElementById('ticker');
                    ticker.addEventListener("animationiteration", function() {
                      // TODO Update here!
                    });
                }
        );
    </script>
{/block}