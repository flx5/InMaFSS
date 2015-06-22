var Update = {
    updateRequired : false,
    Interval : 60,
    style : null,
    limit : null,
    isTeacher : false,
    
    init : function(style, limit, isTeacher) {
        this.style = style;
        this.limit = limit;
        this.isTeacher = isTeacher;
        
        this.reset();
    },
    
    requestUpdate : function() {
        this.updateRequired = true;
        
        switch(this.style) {
        case 'ajax':
            AJAX.Update(this.limit, this.isTeacher);
                break;
        } 
    },
    
    doUpdate : function(site) {
        switch(this.style) {
        case 'reload':
        default:
            this.reload();
                break;
        case 'ajax':
            this.updateDOM(site);
                break;
        }  
        this.reset();
    },
    
    reload : function() {
        location.reload(true);
        this.updateRequired = false;
    },
    
    reset : function() {       
        window.setTimeout(
            function(self) {
                return function() {
                    self.requestUpdate();
                }
            }(this), this.Interval*1000
        );
    },
    
    updateDOM : function(site) { 
        var data = null;
        switch(this.style) {
        case 'ajax':
            data = AJAX.getData(site);
                break;
        }  
        
        if(data == null) {
            return; }
        
        var el = null;
        
        switch(site) {
        case 'footer':
            el = document.getElementById('footer');
                break;
        default:
            el = document.getElementById('plan_'+site);
                break;
        }
        
        if(el != null) {
            el.innerHTML = data
        } }
}