var Pages = {
    mPages : new Array(),
    
    Init : function(time) {
        if(time == null) {
            throw "No time given!";
        }

        this.mPages.push(new Page('left'));
        this.mPages.push(new Page('right'));
       
        window.setInterval(function(self) {
            return function() {
                self.NextPage();
            }
        }(this), time*1000);
    },
    
    NextPage : function() { 
        for(var i=0; i<this.mPages.length; i++) {
            this.mPages[i].NextPage();
        }
    }
}

var Page = function(site) {
    this.currentPage = 0;
    this.site = site;
       
    this.getPage = function(id) {
        return document.getElementById('plan_'+this.site+'_' + id);
    };
    
    this.getInfo = function(id) {
        return document.getElementById('info_'+this.site+'_' + id);
    },
    
    this.endReached = function() {
        this.currentPage = 0;
        Update.doUpdate(this.site);
    },
       
    this.NextPage = function() {
        var current_plan = this.getPage(this.currentPage);
        var current_info = this.getInfo(this.currentPage);
        
        this.currentPage++;
        
        var next = this.getPage(this.currentPage);
        var next_info = this.getInfo(this.currentPage);

        if(current_plan == null)
        {
            this.endReached();
            return;
        }
        
        current_plan.style.display = 'none';
        current_info.className = '';

        if(next == null) {
            this.endReached();
            next = this.getPage(0);
            next_info = this.getInfo(0);
        }

        next.style.display = '';
        next_info.className = 'active';
    }
}