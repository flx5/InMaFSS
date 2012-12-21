
var publicList;
var count = 0;
var initLength;

function DoUpdate(liste) {
       for(i = 0; i<liste.length; i++) {
            var item = liste[i];
            document.getElementById('liste').innerHTML += '<li id="'+item[0]+'">'+item[0]+' '+item[1]+'</li>';
       }

       document.getElementById('liste').innerHTML += '<div id="progress_bg"><div id="progress"></div><div id="progress_content">&nbsp;</div></div>';
       document.getElementById('liste').innerHTML += '<textarea id="log" cols="60" rows="20" ></textarea>';

       publicList = liste;
       initLength = liste.length;

       DoNext();
}

function DoNext() {

      var item = publicList[0];
      var prog = (count*(100/publicList.length))+'%';


      document.getElementById(item[0]).style.color = '#0080FF';
      BuildRequest(item[2], item[0], item[1]);


}

function BuildRequest(url,id, action) {
       LogLine("----------------------------------");
       LogLine("Progressing File: "+ id);
       DoRequest("ajax/do_update.php","url="+url+"&file="+id+"&action="+action, id);
}

function LogLine(line) {
      document.getElementById("log").value += line+'\n';
}


function DoRequest(url, post, id) {
  xmlhttp = getXML();

  xmlhttp.onreadystatechange=function()
   {
   if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
      var State = xmlhttp.responseText.substring(xmlhttp.responseText.lastIndexOf("|")+1);
      var Text =  xmlhttp.responseText.substring(0, xmlhttp.responseText.lastIndexOf("|"));

      LogLine("["+State+"]   Response: "+Text);

      document.getElementById(id).style.color = '#00ff00';

      count++;

      UpdateInfo();

      publicList.splice(0,1);

      if(publicList.length != 0 && State == "OK") {
             DoNext();
      }
    }
   }

   xmlhttp.open("POST",url,true);

   xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

   xmlhttp.send(post);
}

function UpdateInfo() {
      var prog = (count*(100/initLength))+'%';
      document.getElementById('progress_content').innerHTML = prog;
      document.getElementById('progress').style.width = prog;
}

function getXML() {
     if (window.XMLHttpRequest)
     {// code for IE7+, Firefox, Chrome, Opera, Safari
          return new XMLHttpRequest();
     }
     else
     {// code for IE6, IE5
          return new ActiveXObject("Microsoft.XMLHTTP");
     }
}