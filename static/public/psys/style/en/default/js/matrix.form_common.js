
$(document).ready(function(){
	
	$('input[type=checkbox],input[type=radio],input[type=file]').uniform();
	
	$('select').select2();
    $('.colorpicker').colorpicker();
});

$(document).ready(function() { 	

	//------------- Tags plugin  -------------//
	
	$("#tags").select2({
		tags:["red", "green", "blue", "orange"]
	});

	//------------- Elastic textarea -------------//
	if ($('textarea').hasClass('elastic')) {
		$('.elastic').elastic();
	}

	//------------- Input limiter -------------//
	if ($('textarea').hasClass('limit')) {
		$('.limit').inputlimiter({
			limit: 100
		});
	}

	//------------- Masked input fields -------------//
	$("#mask-phone").mask("(999) 999-9999", {completed:function(){alert("Callback action after complete");}});
	$("#mask-phoneExt").mask("(999) 999-9999? x99999");
	$("#mask-phoneInt").mask("+40 999 999 999");
	$("#mask-date").mask("99/99/9999");
	$("#mask-ssn").mask("999-99-9999");
	$("#mask-productKey").mask("a*-999-a999", { placeholder: "*" });
	$("#mask-eyeScript").mask("~9.99 ~9.99 999");
	$("#mask-percent").mask("99%");  

	//------------- Toggle button  -------------//

//	$('.normal-toggle-button').toggleButtons();
//	$('.text-toggle-button').toggleButtons({
//	    width: 140,
//	    label: {
//	        enabled: "ONLINE",
//	        disabled: "OFFLINE"
//	    }
//	});
//	$('.iToggle-button').toggleButtons({
//	    width: 70,
//	    label: {
//	        enabled: "<span class='icon16 icomoon-icon-checkmark-2 white'></span>",
//	        disabled: "<span class='icon16 icomoon-icon-cancel-3 white marginL5'></span>"
//	    }
//	});

	//------------- Spinners with steps  -------------//
//	$( "#spinner1" ).spinner();
//
//	/*Demacial*/
//	$( "#spinner2" ).spinner({
//      step: 0.01,
//      numberFormat: "n"
//    });
//
//	/*Custom step size*/
//    $( "#spinner3" ).spinner({
//      step: 5
//    });
//
//    /*Currency spinner*/
//    $( "#spinner4" ).spinner({ 
//    	numberFormat: "C" 
//    });

	//------------- Colorpicker -------------//
	if($('div').hasClass('picker')){
		$('.picker').farbtastic('#color');
	}	
	//------------- Datepicker -------------//
	if($('#datepicker').length) {
		$("#datepicker").datepicker({
			showOtherMonths:true
		});
	}
	if($('#datepicker-inline').length) {
		$('#datepicker-inline').datepicker({
	        inline: true,
			showOtherMonths:true
	    });
	}

	//------------- Combined picker -------------//
	if($('#combined-picker').length) {
		$('#combined-picker').datetimepicker();
	}
	
    //------------- Time entry (picker) -------------//
//	$('#timepicker').timeEntry({
//		show24Hours: true,
//		spinnerImage: ''
//	});
//	$('#timepicker').timeEntry('setTime', '22:15')

	//------------- Select plugin -------------//
	$("#select1").select2();
	$("#select2").select2();

	//--------------- Dual multi select ------------------//
//	$.configureBoxes();

	//Boostrap modal
	$('#myModal').modal({ show: false});
	
	//add event to modal after closed
	$('#myModal').on('hidden', function () {
	  	console.log('modal is closed');
	})
	
	
	//role by nick 2015-05-24
	$("#roles").on("change",function (){

		if($(this).val() == 1){
			$("#roles_list").show();
		}else{
			$("#roles_list").hide();
		}
	
	} )
	
	$("#rolesgroup .control-group .control-label input[type='checkbox']").on("click",function () {
		
		var parentid = $(this).attr("id");
		
		var checked = this.checked;

		$("#child-"+parentid+" input[type='checkbox']").each(function(){
			
			if(checked == true){
				$(this).parent().addClass("checked");
			}else{
				$(this).parent().removeClass("checked");
			}
			
			$(this).attr("checked",checked);
			
		})
	
	})
	
	$("#rolesgroup .control-group .control-label input[type='checkbox']").on("click",function () {
		
		var parentid = $(this).attr("id");
		
		var checked = this.checked;

		$("#child-"+parentid+" input[type='checkbox']").each(function(){
			
			if(checked == true){
				$(this).parent().addClass("checked");
			}else{
				$(this).parent().removeClass("checked");
			}
			
			$(this).attr("checked",checked);
			
		})
	
	})
	
	
	$("#rolesgroup .control-group .controls input[type='checkbox']").on("click",function () {
		
		var parentid = $(this).attr("parentid");
		
		if($("#"+parentid).attr("checked") == false || $("#"+parentid).attr("checked") == undefined){
			$("#"+parentid).attr("checked",true);
			$("#"+parentid).parent().addClass("checked");
		}
		
	})
    
    
    $("#tid").on("change",function () {
        
        var value = $(this).val();
        
        //link change & clear value
        if(value == 100){
            $(".seo").hide();
            $(".catname input[type='text']").val("");
            $(".seo textarea").val("");
            $(".catname").hide();
            $(".link").show();
        }else{
            $(".seo").show();
            $(".catname").show();
            $(".link").hide();
            $(".link input[type='text']").val("");
        }
        
    })
    
    //category catname check
    $(".catname_check").on("blur",function () {
    	
    	var value = $(this).val();
    	var check = /^[a-z]+$/;
    	
    	if(!check.test(value)){
    		$(".btn-answer-catname").click();
    		$(this).val("");
    	}
    	
    })
    
    //dbc field-ajax-create
    $(".field-ajax-create").on("click",function () {
        
        $(this).removeClass("btn-danger").addClass("btn-inverse");
        $(".control-group").removeClass("error");
        var ajaxurl = "/dbcomponent/ajaxCreateDBField";
        
        //input class
        var fieldtype = Number($("select[name='fieldtype']").val());
        
        if(!$("input[name='fieldname']").val()){
            
            var e   = "input[name='fieldname']";
            var str = "Please fill out <i>Field Name</i>.";
            
            form_error_fun(e,str);
            
        }else if(!fieldtype){
                        
            var e   = "select[name='fieldtype']";
            var str = "Please select <i>Field Type</i>.";
            
            form_error_fun(e,str);
                        
        }
        
        //fieldtype = 1
        switch (fieldtype){
        case 0:
            html = '';
            break;
        case 1:
            
            if(!$("select[name='fieldbox']").val() || $("select[name='fieldbox']").val() == 0){
                       
                var e   = "select[name='fieldbox']";
                var str = "Please select <i>type</i>.";
                
                form_error_fun(e,str);  
                       
            }else if(!$("select[name='fieldclass']").val() || $("select[name='fieldclass']").val() == 0){
                
                var e   = "select[name='fieldclass']";
                var str = "Please select <i>type</i>.";
                
                form_error_fun(e,str);  
                
            }else if(!$("input[name='length']").val()){
                
                var e   = "input[name='length']";
                var str = "Please fill out <i>Field Length</i>.";
                
                form_error_fun(e,str); 
            }
//            
//            //ajax add
//            var fname = $("input[name='fieldname']").val();
//            var fdesc = $("input[name='fielddesc']").val();
//            var ftype = $("select[name='fieldtype']").val();
//            var width = $("select[name='width']").val();
//            var regular = $("select[name='regular']").val();
//            var fieldclass = $("select[name='fieldclass']").val();
//            var length = $("input[name='length']").val();
            
            $("form[class='form-horizontal']").submit();
            
            break;
        case 2:

        	if(!$("textarea[name='radio-value']").val()){
                
                var e   = "textarea[name='radio-value']";
                var str = "Please set <i>radio value</i>.";
                
                form_error_fun(e,str); 
            }

        	
        	$("form[class='form-horizontal']").submit();
        	
            break;
        case 3:
        
            if(!$("textarea[name='checkbox-value']").val()){
                
                var e   = "textarea[name='checkbox-value']";
                var str = "Please set <i>checkbox value</i>.";
                
                form_error_fun(e,str); 
            }

        	
        	$("form[class='form-horizontal']").submit();    
            break;
        case 4:
        
            if(!$("textarea[name='select-value']").val()){
                
                var e   = "textarea[name='select-value']";
                var str = "Please set <i>select value</i>.";
                
                form_error_fun(e,str); 
            }

        	$("form[class='form-horizontal']").submit();    
            break;  
        case 5:
        
            if(!$("select[name='file-value[]']").val()){
                
                var e   = "select[name='file-value[]']";
                var str = "Please set <i>upload file type</i>.";
                
                form_error_fun(e,str); 
            }
        	
        	$("form[class='form-horizontal']").submit();    
            break; 
        case 6:
        
            if(!$("select[name='color-value']").val()){
                
                var e   = "select[name='color-value']";
                var str = "Please set <i>color type</i>.";
                
                form_error_fun(e,str); 
            }
        	
        	$("form[class='form-horizontal']").submit();    
            break; 
        case 7:
        
            if(!$("select[name='date-value']").val()){
                
                var e   = "select[name='date-value']";
                var str = "Please set <i>date type</i>.";
                
                form_error_fun(e,str); 
            }

        	
        	$("form[class='form-horizontal']").submit();    
            break;   
        case 8:
        	
        	$("form[class='form-horizontal']").submit();    
            break;   
        
        case 9:
        	
        	$("form[class='form-horizontal']").submit();    
            break;     
          
         

        }
        
        
        
    })
  

});//End document ready functions


//form warning
function form_error_fun(e,str){
    
    $("#error").html(str);
    $("#modal-error").click();
    $(e).parents("div[class*='control-group']").addClass("error");
    $(this).removeClass("btn-inverse").addClass("btn-danger");
    return false;
    
}

