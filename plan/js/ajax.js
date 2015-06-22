var AJAX = {
    limit : 0,
    running : false,

    DataAvail : {
        left: false, 
        right: false, 
        footer: false
    },
    
    Data : null,
    
    GetObject : function() {
        if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
            return new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            return new ActiveXObject("Microsoft.XMLHTTP");
        }
    },
    
    Update : function(limit, IsTeacher) {
        if(this.running) { 
            return; } 
        
        this.running = true;
        Update.updateRequired = false;

        var mThis = this;

        this.fetchData(
            "ajax.php?limit="+limit+"&t="+Date.now()+ (IsTeacher ? '&teacher=true' : ''), function(httpCode, response) {
                mThis.insertData(httpCode, response);
            }
        );
    },
    
    insertData : function(httpCode, response) {  
        if(httpCode==200) {
            this.Data = JSON.parse(response);
            this.DataAvail = {
                left: true, 
                right: true, 
                footer: true
            };
            Cache.cacheWarning(false);
        } else {
            Cache.cacheWarning(true);
        }
        
        this.running = false;
    },
    
    fetchData : function(url, callback) {
        var xmlhttp = this.GetObject();
        
        xmlhttp.onreadystatechange=function()
        {
            if (xmlhttp.readyState==4) {
                callback(xmlhttp.status, xmlhttp.responseText);
            }
        }
        xmlhttp.open("GET",url,true);
        xmlhttp.send();
    },
    
    getData : function(site) {
        if(!this.DataAvail[site]) {
            return null; }
    
        this.DataAvail[site] = false;
    
        switch(site) {
        case 'left':
                return this.Data.left;
        case 'right':
                return this.Data.right;
        case 'footer':
                return this.Data.footer;
        }
    }
}
