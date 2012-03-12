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

var aktiv = null;

function Init() {
  SetHeight();
  aktiv = window.setInterval("Continue()", 5000);
}
function Continue() {
      NextPage('left');
      NextPage('right');
}

function NextPage(site) {
       var current_page = 0;

       if(site == 'left') {
             current_page = current_page_left;
       }  else {
             current_page = current_page_right;
       }

       var next = document.getElementById('plan_'+site+'_' + (current_page+1));
       var next_info = document.getElementById('info_'+site+'_' + (current_page+1));

       current_plan = document.getElementById('plan_'+site+'_' + current_page);

       if(current_plan == null) {
           if(site == 'left') {
             runs_left++;
           }  else {
             runs_right++;
           }
           return;
       }
       current_plan.style.display = 'none'
       document.getElementById('info_'+site+'_' + current_page).style.color = '#C0C0C0';

       if(next == null) {
               next = document.getElementById('plan_'+site+'_0');
               next_info = document.getElementById('info_'+site+'_0');
               current_page = 0;
               if(runs_right > 5 && runs_left > 5) {  
                     location.reload(true);
               }

               if(site == 'left') {
                  runs_left++;
                }  else {
                  runs_right++;
                }
       } else {
          current_page = current_page+1;
       }
       next.style.display = '';
       next_info.style.color = '#004488';

       if(site == 'left') {
             current_page_left = current_page;
       }  else {
             current_page_right = current_page;
       }
}