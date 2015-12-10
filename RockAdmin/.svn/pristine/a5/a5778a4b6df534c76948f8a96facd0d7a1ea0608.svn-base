var bw; (function(a) {
    this.version = "@1.1";
    this.layer = {
        width: 200,
        height: 100
    };
    this.title = "信息提示";
    this.time = 4000;
    this.anims = {
        type: "slide",
        speed: 600
    };
    this.inits = function(g, f) {
        if ($("#message").is("div")) {
            return;
        }
        var c = $('<div id="message" class="box_msg" style="z-index:800;width:' + this.layer.width + "px;height:" + this.layer.height + 'px;position:fixed; display:none;bottom:0; right:0; overflow:hidden;"></div>');
        var b = $('<div class="box_msg_topcenter"><div class="box_msg_topleft"><div class="box_msg_topright"><div class="box_msg_title">' + g + '</div><div class="box_msg_close" id="message_close"></div></div></div></div>');
        var e = $('<div class="box_msg_middlecenter"><div class="box_msg_middleleft"><div class="box_msg_middleright"><div class="boxContent" style="height:' + (this.layer.height - 35) + 'px;">' + f + "</div></div></div></div>");
        var d = $('<div class="box_msg_bottomcenter"><div class="box_msg_bottomleft"><div class="box_msg_bottomright"></div></div></div>');
        c.append(b).append(e).append(d);
        $(document.body).append(c);
    };
    this.show = function(h, g, f, d) {
        if ($("#message").is("div")) {
            return;
        }
        if (h == 0 || !h) {
            h = this.title;
        }
        this.inits(h, g);
        if (f) {
            this.time = f;
        }
        switch (this.anims.type) {
        case "slide":
            $("#message").slideDown(this.anims.speed);
            break;
        case "fade":
            $("#message").fadeIn(this.anims.speed);
            break;
        case "show":
            $("#message").show(this.anims.speed);
            break;
        default:
            $("#message").slideDown(this.anims.speed);
            break;
        }
        $("#message_close").click(function() {
            close();
            $('#tinfomessageID').remove();
        });
        if (d != null) {
            try {
                var b = $("<embed src=/Sys/imgaes/" + d + "  AutoStart='true' type='application/x-mplayer2' id='tinfomessageID' style='width:0px; height:1px'></embed>");
				//var b = $("<object src=music/" + d + "  ></object>");
                $("body").append(b);
            } catch(c) {}
        }
        this.rmmessage(this.time);
    };
    this.lays = function(c, b) {
        if ($("#message").is("div")) {
            return;
        }
        if (c != 0 && c) {
            this.layer.width = c;
        }
        if (b != 0 && b) {
            this.layer.height = b;
        }
    };
    this.anim = function(b, c) {
        if ($("#message").is("div")) {
            return;
        }
        if (b != 0 && b) {
            this.anims.type = b
        }
        if (c != 0 && c) {
            switch (c) {
            case "slow":
                break;
            case "fast":
                this.anims.speed = 200;
                break;
            case "normal":
                this.anims.speed = 400;
                break;
            default:
                this.anims.speed = c
            }
        }
    };
    this.rmmessage = function(b) {
        if (b != "stay") {
            bw = setTimeout("this.close()", b)
        }
    };
    this.close = function() {
        clearTimeout(bw);
        switch (this.anims.type) {
        case "slide":
            $("#message").slideUp(this.anims.speed);
            break;
        case "fade":
            $("#message").fadeOut(this.anims.speed);
            break;
        case "show":
            $("#message").hide(this.anims.speed);
            break;
        default:
            $("#message").slideUp(this.anims.speed);
            break;
        }
        $('#tinfomessageID').remove();
        var b = setTimeout('$("#message").remove();', this.anims.speed);
        setTimeout('$("#messageSound").remove();', this.anims.speed);
        this.original();
    };
    this.original = function() {
        this.layer = {
            width: 200,
            height: 100
        };
        this.title = "信息提示";
        this.time = 4000;
        this.anims = {
            type: "slide",
            speed: 600
        }
    };
    a.messager = this;
    return a
})(jQuery);