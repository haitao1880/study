$(document).ready(function(){

	// === Sidebar navigation === //
	
	$('.submenu > a').click(function(e)
	{
		e.preventDefault();
		var submenu = $(this).siblings('ul');
		var li = $(this).parents('li');
		var submenus = $('#sidebar li.submenu ul');
		var submenus_parents = $('#sidebar li.submenu');
		if(li.hasClass('open'))
		{
			if(($(window).width() > 768) || ($(window).width() < 479)) {
				submenu.slideUp();
			} else {
				submenu.fadeOut(250);
			}
			li.removeClass('open');
		} else 
		{
			if(($(window).width() > 768) || ($(window).width() < 479)) {
				submenus.slideUp();			
				submenu.slideDown();
			} else {
				submenus.fadeOut(250);			
				submenu.fadeIn(250);
			}
			submenus_parents.removeClass('open');		
			li.addClass('open');	
		}
	});
    
    $('.locks').click(function(){
        
        $('.submenu > ul > li').removeClass('active');
        
        $(this).parent('li').addClass('active');
        
    })
    
	var ul = $('#sidebar > ul');
	
	$('#sidebar > a').click(function(e)
	{
		e.preventDefault();
		var sidebar = $('#sidebar');
		if(sidebar.hasClass('open'))
		{
			sidebar.removeClass('open');
			ul.slideUp(250);
		} else 
		{
			sidebar.addClass('open');
			ul.slideDown(250);
		}
	});
	
	// === Resize window related === //
	$(window).resize(function()
	{
		if($(window).width() > 479)
		{
			ul.css({'display':'block'});	
			$('#content-header .btn-group').css({width:'auto'});		
		}
		if($(window).width() < 479)
		{
			ul.css({'display':'none'});
			fix_position();
		}
		if($(window).width() > 768)
		{
			$('#user-nav > ul').css({width:'auto',margin:'0'});
            $('#content-header .btn-group').css({width:'auto'});
		}
	});
	
	if($(window).width() < 468)
	{
		ul.css({'display':'none'});
		fix_position();
	}
	
	if($(window).width() > 479)
	{
	   $('#content-header .btn-group').css({width:'auto'});
		ul.css({'display':'block'});
	}
	
	// === Tooltips === //
	$('.tip').tooltip();	
	$('.tip-left').tooltip({ placement: 'left' });	
	$('.tip-right').tooltip({ placement: 'right' });	
	$('.tip-top').tooltip({ placement: 'top' });	
	$('.tip-bottom').tooltip({ placement: 'bottom' });	
	
	// === Search input typeahead === //
	$('#search input[type=text]').typeahead({
		source: ['Dashboard','Form elements','Common Elements','Validation','Wizard','Buttons','Icons','Interface elements','Support','Calendar','Gallery','Reports','Charts','Graphs','Widgets'],
		items: 4
	});
	
	// === Fixes the position of buttons group in content header and top user navigation === //
	function fix_position()
	{
		var uwidth = $('#user-nav > ul').width();
		$('#user-nav > ul').css({width:uwidth,'margin-left':'-' + uwidth / 2 + 'px'});
        
        var cwidth = $('#content-header .btn-group').width();
        $('#content-header .btn-group').css({width:cwidth,'margin-left':'-' + uwidth / 2 + 'px'});
	}
	
	// === Style switcher === //
	$('#style-switcher i').click(function()
	{
		if($(this).hasClass('open'))
		{
			$(this).parent().animate({marginRight:'-=190'});
			$(this).removeClass('open');
		} else 
		{
			$(this).parent().animate({marginRight:'+=190'});
			$(this).addClass('open');
		}
		$(this).toggleClass('icon-arrow-left');
		$(this).toggleClass('icon-arrow-right');
	});
	
	$('#style-switcher a').click(function()
	{
		var style = $(this).attr('href').replace('#','');
		$('.skin-color').attr('href','css/maruti.'+style+'.css');
		$(this).siblings('a').css({'border-color':'transparent'});
		$(this).css({'border-color':'#aaaaaa'});
	});
	
	$('.lightbox_trigger').click(function(e) {
		
		e.preventDefault();
		
		var image_href = $(this).attr("href");
		
		if ($('#lightbox').length > 0) {
			
			$('#imgbox').html('<img src="' + image_href + '" /><p><i class="icon-remove icon-white"></i></p>');
		   	
			$('#lightbox').slideDown(500);
		}
		
		else { 
			var lightbox = 
			'<div id="lightbox" style="display:none;">' +
				'<div id="imgbox"><img src="' + image_href +'" />' + 
					'<p><i class="icon-remove icon-white"></i></p>' +
				'</div>' +	
			'</div>';
				
			$('body').append(lightbox);
			$('#lightbox').slideDown(500);
		}
		
	});
	

	$('#lightbox').live('click', function() { 
		$('#lightbox').hide(200);
	});
    
    //删除菜单btn-delete-menu
    $(".btn-delete-menu").click(function(){
        
        var id = $(this).attr("menuid");

        $(".confirm-delete-menu").attr("href","/menu/delete?id="+id);
        
    })

    //删除用户btn-delete-user
    $(".btn-delete-user").click(function(){
        
        var id = $(this).attr("userid");

        $(".confirm-delete-user").attr("href","/user/delete?id="+id);
        
    })
    
    //删除用户btn-delete-category
    $(".btn-delete-category").click(function(){
        
        var id = $(this).attr("categoryid");
        var dbc_id = $(this).attr("dbc_id");

        $(".confirm-delete-category").attr("href","/category/delete?id="+id+"&dbc_id="+dbc_id);
        
    })
    
    //删除字段btn-delete-field
    $(".btn-delete-field").click(function(){
        
        var id = $(this).attr("fieldid");

        $(".confirm-delete-field").attr("href","/dbcomponent/deleteDBField?id="+id);
        
    })
    
    //删除字段btn-delete-cm
    $(".btn-delete-cm").click(function(){
        
        var cid = $(this).attr("cid");
        var id  = $("#vid").val();
        var tid  = $("#vtid").val();
        var time  = $("#vtime").val();
        var v  = $("#vv").val();
        
        $(".confirm-delete-cm").attr("href","/category/operating?id="+id+"&tid="+tid+"&time="+time+"&o=delete&v="+v+"&cid="+cid);
        
    })
    
    //解锁set file
    $(".confirm-unlock-setting").click(function(){
        
        $("#deblocking error").html("");
        
        var dbcname = $("#deblocking input[name='dbcname']").val();
        var pwd = $("#deblocking input[name='pwd']").val();
      
        if(!pwd){
            $("#deblocking .error").html("<i class='icon-edit'></i> Please enter the password.");
            return false;
        }
        
        $.post("/dbcomponent/setfile",{unlock:1,dbcname:dbcname,pwd:pwd},function(data){
            
            if(data == 'PWDERROR'){
                
                $("#deblocking .error").html("<i class='icon-edit'></i> The password is error.");
                return false;
                
            }else if(data == 'DBCNAMEERROR'){
                
                $("#deblocking .error").html("<i class='icon-edit'></i> The DB lock file is error.");
                return false;
                
            }else if(data == 'NOFILEERROR'){
                
                $("#deblocking .error").html("<i class='icon-edit'></i> The file not found.");
                return false;
                
            }else{
                
                
                
            }
            
        });
      
        
    })
    
    //dbc input - 1
    $(".input-type").change(function(){
        
        var id = $(this).val();
        
        $("div[class^='input-']").hide(); 
        $(".input-"+id).show();
        
    })
    
    $("select[name='fieldbox']").change(function(){
        
        var id = Number($(this).val());
        var html = '<option value="0">-Field Class-</option>';
        
        switch (id){
        case 0:
            html = '';
            break;
        case 1:
            html += '<option value="int">int</option>';
            html += '<option value="tinyint">tinyint</option>';
            html += '<option value="smallint">smallint</option>';
            html += '<option value="mediumint">mediumint</option>';
            break;
        case 2:
            html += '<option value="char">char</option>';
            html += '<option value="varchar">varchar</option>';
            break;
//            case 3:
//            html = '';
//            html += '<option value="tinytext">tinytext</option>';
//            html += '<option value="text">text</option>';
//            html += '<option value="mediumtext">mediumtext</option>';
//            html += '<option value="longtext">longtext</option>';
//            break;
        }
        
        $("select[name='fieldclass']").html(html);
        
    })
    
    $("select[name='fieldclass']").change(function(){
        
        var fieldbox = Number($("select[name='fieldbox']").val());
        var id = $(this).val();
        
        var val = 0;
        
        switch (id){
        case 0:
            val = '';
            break;
        case 'int':
            val = 10;
            break;
        case 'tinyint':
            val = 3;
            break;
        case 'smallint':
            val = 5;
            break;
        case 'mediumint':
            val = 8;
            break;
        case 'char':
            val = 100;
            break;
        case 'varchar':
            val = 255;
            break;
        }
        
        $("input[name='length']").val(val);
        
    })
    
});

//全选
function checkAll(e){
    
    $("#"+e+" :checkbox").attr("checked", true);
    $("#"+e+" :checkbox").parent("span").addClass("checked");
    
}


//全选
function checkAlls(e,v){
    
    var ischeck = $("#"+v).prop('checked');
     
    $("#"+e+" :checkbox").attr("checked", ischeck);
    if(ischeck == true){
        $("#"+e+" :checkbox").parent("span").addClass("checked");
    }else{
        $("#"+e+" :checkbox").parent("span").removeClass("checked");
    }
}


//反选
function checkReverse(e){
    
    $("#"+e+" :checkbox").each(function () {  
        
        if($(this).parent("span").attr("class") == "checked"){
            $(this).attr("checked", false);  
            $(this).parent("span").removeClass("checked");
        }else{
            $(this).attr("checked", true);  
            $(this).parent("span").addClass("checked");
        }
    });  
    
}

//时间转时间戳
function datetime_to_unix(datetime){
    var tmp_datetime = datetime.replace(/:/g,'-');
    tmp_datetime = tmp_datetime.replace(/ /g,'-');
    var arr = tmp_datetime.split("-");
    var now = new Date(Date.UTC(arr[0],arr[1]-1,arr[2],arr[3]-8,arr[4],arr[5]));
    alert( parseInt(now.getTime()/1000) ); 
}

