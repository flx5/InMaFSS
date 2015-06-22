var Ticker = function(elementID) {
    this.speed = 10;
    this.pos = -999999999999;
    this.elementID = null;
    
    this.tick = function()
    {         
        var element = document.getElementById(this.elementID);
        this.pos--;
                
        if(this.pos <= -element.offsetWidth) {
            this.pos = element.parentNode.offsetWidth;
            Update.doUpdate('footer');
        }
              
        element.style.left = this.pos+"px";
        element.style.position = 'absolute';
        element.style.whiteSpace = 'nowrap';
    };
                
    this.elementID = elementID;

    if(document.getElementById(this.elementID) == null) {
        return; }

    window.setInterval(
        (function(self) {       
            return function() { 
                self.tick(); 
            }
        })(this), this.speed
    );
};