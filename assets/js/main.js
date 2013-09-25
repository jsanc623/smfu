$(document).ready(function(){    
    $("#copy-button").zclip({
        path: "/assets/js/ZeroClipboard.swf",
        copy: $(".responseLink").text()
    });
    
    $(".api-link").on("click", function(){
        var id = $(this).attr("id");
        window.location.href = "http://smfu.in/api/documentation#" + id;
        return false;
    });
    
    Documentation = {
        highlight : function(){
            var hash = window.location.hash;
            $(".api-link").parent().removeClass("highlight");
            $(hash).parent().addClass("highlight");
        }
    }
    
    $(window).on("hashchange", function(){ Documentation.highlight() });
    if(window.location.hash.length > 0){
        Documentation.highlight();
    }
    
    Director = {
        url : window.location.search + window.location.hash,
        getUrl : function(){ this.url = this.url.replace("?url=", ""); return this; },
        doRedirect : function(){ window.location.href = this.url.length > 0 ? this.url : "http://smfu.in"; },
        timeCountdown : function(){
            var self = this;
            this.timer(
                5000, // milliseconds
                function(timeleft) { // called every step to update the visible countdown
                    $(".countdown").html(timeleft);
                },
                function() {
                    self.doRedirect();
                }
            );
            return this;
        },
        timer : function(time, update, complete) {
            var start = new Date().getTime();
            var interval = setInterval(function() {
                var now = time-(new Date().getTime()-start);
                if( now <= 0) {
                    clearInterval(interval);
                    complete();
                }
                else update(Math.floor(now/1000));
            },100);
        }
    }
});