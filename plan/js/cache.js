var Cache = {
    cacheWarning : function(enabled) {
        var el = document.getElementsByClassName('cachedWarning')
    
        if(enabled) {
            document.getElementById('header').style.backgroundColor = "#ff0000";
            document.getElementById('header_cached').style.display = 'inline';
            document.getElementById('header_normal').style.display = 'none';
        } else {
            document.getElementById('header').style.backgroundColor = "";
            document.getElementById('header_cached').style.display = 'none';
            document.getElementById('header_normal').style.display = 'inline';
        
            for(var i = 0; i<el.length;i++) {
                el[i].style.visibility = "hidden"; }
        }
    }
}