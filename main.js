document.cookie = "TEST=YES";

if(!document.cookie && location.search.indexOf("cookies=no") == -1) {
    var new_location = ""+window.location;
    if(new_location.indexOf("?") > -1) {
        new_location = new_location+"&cookies=no";
    } else {
        new_location = new_location+"?cookies=no";
    }
    window.location = new_location;
}

if(document.cookie && location.search != "" && location.search.indexOf("cookies=no") > -1) {
    var new_location = ""+window.location;

    if(location.search == "?cookies=no") {
        new_location = new_location.replace("?cookies=no","");
    }

    new_location = new_location.replace("&cookies=no","");
    new_location = new_location.replace("?cookies=no&","?");

    window.location = new_location;
}

function SetHeight() {
    if(location.search.indexOf("size=") == -1) {
        var new_location = ""+window.location;
        if(new_location.indexOf("?") > -1) {
            new_location = new_location+"&";
        } else {
            new_location = new_location+"?";
        }
        new_location += "size=" +document.getElementById("plan_left").offsetHeight;
        window.location = new_location;
    }
}

var current_page_left = 0;
var current_page_right = 0;
var runs_left = 0;
var runs_right = 0;

var onUpdate = null;
var updateRequired = false;

var aktiv = null;
var updateStyle = null;

// Only required for AJAX
var limit = 0;
var ajaxRunning = false;

var ajaxDataAvail = false;
var ajaxData = null;

// 1 Second is enougth as we have to wait 2 rounds until the Data is visible => Time for an update = PageCount*time*2+updateTime
var updateTime = 1;

var Update = {
    reload : function() {
        location.reload(true);
        updateRequired = false;
    },
    
    ajax : function() {
        
        if(ajaxRunning)
            return;
        
        ajaxRunning = true;
        updateRequired = false;
        
        var xmlhttp;
        
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        
        xmlhttp.onreadystatechange=function()
        {
            if (xmlhttp.readyState==4)
            {
                if(xmlhttp.status==200) {
                    ajaxData = JSON.parse(xmlhttp.responseText);
                    ajaxDataAvail = true;
                    cacheWarning(false);
                } else {
                    cacheWarning(true);
                }
                
                ajaxRunning = false;
                window.setTimeout(RequestUpdate, updateTime*1000);
            }
        }
        xmlhttp.open("GET","ajax.php?limit="+limit+"&t="+Date.now(),true);
        xmlhttp.send();
    }
}

function cacheWarning(setOn) {
    var el = document.getElementsByClassName('cachedWarning')
    
    if(setOn) {
        document.getElementById('header').style.backgroundColor = "#ff0000";
              
        for(var i = 0; i<el.length;i++)
            el[i].style.visibility = "visible";
    } else {
        document.getElementById('header').style.backgroundColor = "";
        
        for(var i = 0; i<el.length;i++)
            el[i].style.visibility = "hidden";
    }
}

function UpdateDOM() {
    if(!ajaxDataAvail)
        return;
    
    ajaxDataAvail = false;
    
    document.getElementById('plan_left').innerHTML = ajaxData.left;
    document.getElementById('plan_right').innerHTML = ajaxData.right;
    document.getElementById('footer').innerHTML = ajaxData.footer;
}

function Init(time, mupdateStyle, mLimit) {
    if(time == null) {
        throw "No time given!";
    }
  
    switch(mupdateStyle) {
        case 'reload':
        default:
            onUpdate = Update.reload;
            break;
        case 'ajax':
            onUpdate = Update.ajax;
            break;
    }
  
    // We're not using Interval as we've either have to wait for ajax to respond or for the reload to happen
    window.setTimeout(RequestUpdate, updateTime*1000);
  
    SetHeight();
    aktiv = window.setInterval(Continue, time*1000);
    limit = mLimit;
}

function RequestUpdate() {
    updateRequired = true;
}

function Continue() {
    NextPage('left');
    NextPage('right');
}

function NextPage(site) { 
    var current_page = 0;

    if(site == 'left') {
        current_page = current_page_left;
    } else {
        current_page = current_page_right;
    }

    var next = document.getElementById('plan_'+site+'_' + (current_page+1));
    var next_info = document.getElementById('info_'+site+'_' + (current_page+1));

    current_plan = document.getElementById('plan_'+site+'_' + current_page);

    if(current_plan == null) {
        if(site == 'left') {
            runs_left++;
            current_page_left = 0;
        } else {
            runs_right++;
            current_page_right = 0;
        }
        
        if(ajaxDataAvail) {
            UpdateDOM();
            NextPage(site);
        }
        
        if(updateRequired)
            onUpdate();
        
        return;
    }
    current_plan.style.display = 'none'

    if(site == 'left') {
        document.getElementById('info_'+site+'_' + current_page).style.color = '#C0C0E0';
    } else {
        document.getElementById('info_'+site+'_' + current_page).style.color = '#A5CDCD';
    }

    if(next == null) {
        next = document.getElementById('plan_'+site+'_0');
        next_info = document.getElementById('info_'+site+'_0');
        current_page = 0;
        
        if(ajaxDataAvail) {
            UpdateDOM();
        }
        
        if(updateRequired && runs_right >= 1 && runs_left >= 1) {
            onUpdate();
        }

        if(site == 'left') {
            runs_left++;
        } else {
            runs_right++;
        }
    } else {
        current_page = current_page+1;
    }
    next.style.display = '';

    if(site == 'left') {
        next_info.style.color = '#004488';
        current_page_left = current_page;
    } else {
        next_info.style.color = '#43886F';
        current_page_right = current_page;
    }
}