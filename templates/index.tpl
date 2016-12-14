{* Smarty *}

{extends file='html.tpl'}

{block name=title}
    {t}Willkommen{/t}
{/block}

{block name=head}
    <link href="static/css/index.css" rel="stylesheet" type="text/css"/>
{/block}

{block name=body}
    <div class="index_content">
        <div class="entry">
            <a href="plan/">
                <img src="static/images/index/plan.png">
                <div>{t}Vertretungsplan{/t}</div>
            </a>
        </div>
        <div class="entry">
            <a href="user/">
                <img src="static/images/index/user.png">
                <div>{t}Nutzer{/t}</div>
            </a>
        </div>
        <div class="entry">
            <a href="manage/">
                <img src="static/images/index/manage.png">
                <div>{t}Administration{/t}</div>
            </a>
        </div>
    </div>
    <div id="footer">
        <a href="https://github.com/flx5/InMaFSS" target="_blank">&copy 2016 Felix Prasse</a>
    </div>
{/block}