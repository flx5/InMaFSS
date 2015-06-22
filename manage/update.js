var Update = {
    Init : function() {
        window.setInterval(this.UpdateStatus, 100);
    },
    
    FindStatus : function() {
        var statusEL = document.getElementById('statusValue');
        if(statusEL == null) {
            return null; }
        
        var lastStatus = null;
        for(var i = statusEL.childNodes.length-1; i>=0;i--) {
            if(statusEL.childNodes[i].nodeName == 'DIV') {
                lastStatus = statusEL.childNodes[i];
                break;
            }
        }

        if(lastStatus == null) {
            return null; }
        
        return lastStatus.innerHTML;
    },
    
    UpdateStatus : function() {
        var el = document.getElementById('status_bar');
                
        var value =  Update.FindStatus();
        if(value != null) {
            el.style.width = value+"%";
            document.getElementById('status_bar_content').innerHTML = value+"%";
        }
    }
} 