// JavaScript Document
$(function(){
	
	var mainHeight = parseInt($("body").css("height")) - 76;
	$("#main").css("height",mainHeight + "px");
	var imgLink,
	    history = $('#lifirst');
	/*$("#top_nav").find("li").hover(function(){
		imgLink = $(this).find("img").eq(0).attr('src');
		imgLink = imgLink.slice(0,-4) + "_h.jpg";
		$(this).find("img").eq(0).attr('src', imgLink);
		$(this).find("h4").eq(0).css('color', '#5cccff');
		$(this).find(".point").eq(0).css('visibility', 'visible');
	},function(){
		imgLink = $(this).find("img").eq(0).attr('src');
		imgLink = imgLink.slice(0,-6) + ".jpg";
		$(this).find("img").eq(0).attr('src', imgLink);
		$(this).find("h4").eq(0).css('color', '#fff');
		$(this).find(".point").eq(0).css('visibility', 'hidden');
	});	*/
	$("#top_nav").find("li").click(function(){		
		if(history != this){
			imgLink = $(this).find("img").eq(0).attr('src');
			imgLink = imgLink.slice(0,-4) + "_h.jpg";
			$(this).find("img").eq(0).attr('src', imgLink);
			$(this).find("h4").eq(0).css('color', '#5cccff');
			$(this).find(".point").eq(0).css('visibility', 'visible');
			if(history != ""){	
				imgLink = $(history).find("img").eq(0).attr('src');
				imgLink = imgLink.slice(0,-6) + ".jpg";
				$(history).find("img").eq(0).attr('src', imgLink);
				$(history).find("h4").eq(0).css('color', '#fff');
				$(history).find(".point").eq(0).css('visibility', 'hidden');
			}
			history = this;
			
			var cur = $(this).attr('data-value');
			if(cur=='market'){				
				$('.aside').hide();
				$('.aside').each(function(){
					if($(this).attr('data-value') == cur)
					{
						$(this).show();
						return;
					}
				});
			}
		}
		
	});
	
	var iframeWidth = parseInt($("body").css("width")) - 187 -5;
	$("iframe").css("width", iframeWidth + "px");
	window.onresize = function(){
		iframeWidth = parseInt($("body").css("width")) - 187 -5;
		$("iframe").css("width", iframeWidth + "px");
	}
});