{* Smarty *}
{extends file='plan/layout.tpl'}

{block name=plan_body}
    <div class="main" id="plan_left" style="border-right:0px solid black;" >
        <div class="no_content">
            <h1>{t}Derzeit steht kein Inhalt zur Verfügung!{/t}</h1>
        </div>
        <div class="menu" align="center"></div>
    </div>

    <div class="main tomorrow" id="plan_right" style="right:0px; border-left:0px solid black;" >
        <div class="no_content">
            <h1>{t}Derzeit steht kein Inhalt zur Verfügung!{/t}</h1>
        </div>
        <div class="menu" align="center"></div>
    </div>
{/block}