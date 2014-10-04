var Height = {
    SetHeight : function() {
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
}
