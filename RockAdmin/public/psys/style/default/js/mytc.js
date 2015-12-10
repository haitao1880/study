function popWin(a){
	function n(){var a=k?k:document.body,b=a.scrollHeight>a.clientHeight?a.scrollHeight:a.clientHeight,c=a.scrollWidth>a.clientWidth?a.scrollWidth:a.clientWidth;$("#maskLayer").css({height:b,width:c})}
	var d,e,b=9e3,c=!1,f=$("#"+a),g=f.width(),h=f.height(),i=f.find(".tit"),j=f.find(".close"),k=document.documentElement,l=($(document).width()-f.width())/2,m=(k.clientHeight-f.height())/2;
	f.css({left:l,top:m,display:"block","z-index":b- -1});
	j.bind("click",function(){$(this).parent().parent().hide().siblings("#maskLayer").remove()});
	$('<div id="maskLayer"></div>').appendTo("body").css({background:"#000",opacity:".4",top:0,left:0,position:"absolute",zIndex:"8000"}),n(),$(window).bind("resize",function(){n()});
}