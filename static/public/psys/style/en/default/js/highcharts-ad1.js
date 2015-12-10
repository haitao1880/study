/**
*
* @统计图
*
* @access public 
* @author Nick
* @copyright rockhippo
* @param -
* @return -
*
*/

function makeChart(type,render,b_title,xv,y_title,data,labels){
    
    labels = labels == true ? true : false;
  
    var option = new Object();  
    option.chart = new Object();  
    option.chart.type=type;  //"spline"图类型
    option.chart.renderTo=render; //"container"容器  
    option.chart.plotBackgroundColor="#EEEEEE";
    option.chart.backgroundColor="#EEEEEE";
    //option.chart.events={click :function(e){console.log(e)}};
    
    option.title={text:b_title};//"b_title"大标题  
      
    option.xAxis={categories:xv};//"xv" x横轴 数据 
    option.yAxis={title:{text:y_title}};//"y_title" y竖轴 标题  
    
    //this.userOptions.name2 名称
    //e.point.index 第几个点
    //e.currentTarget._i 第几条线
    //扩展 ,events: {click :function(e){alert(this.userOptions.id[e.point.index])}} 
    
    option.credits={text:'RockHippo.cn',href:'http://www.rockhippo.cn'};
      
    option.tooltip={crosshairs: true,shared: true};  
    option.plotOptions={bar:{dataLabels:{enabled:true,color:"#606060"}},spline: {marker: {radius: 4},dataLabels:{enabled:labels,color:"#606060"},events: {click :function(e){console.log(this);chainDiagram(this.userOptions.id[e.point.index])}}},column: {marker: {radius: 4},dataLabels:{enabled:labels,color:"#606060"},events: {click :function(e){console.log(this);chainDiagram(this.userOptions.id[e.point.index])}}}};
      
    option.series = new Array();
    for(var i=0;i<data.station.length;i++){
        option.series[i] = new Object();
        if(data[data.station[i]] != undefined){ 
            option.series[i].name=data[data.station[i]].name;  
            option.series[i].id=data[data.station[i]].id;  
            option.series[i].data=data[data.station[i]].data;  
            option.series[i].marker={symbol: data[data.station[i]].marker}; 
            
        }
        
        
    }
    
    
    
    var chart = new Highcharts.Chart(option); 
}

/**
*
* @合并 table列表
*
* @access public 
* @author Nick
* @copyright rockhippo
* @param -
* @return -
*
*/
function makeTable(data,num){
    
    var html = '';
    var spans = '';
    
    switch(num){
        
        case 1:
            spans = 'span12';
        break;
    
        case 2:
            spans = 'span6';
        break;
        
        case 3:
            spans = 'span4';
        break;
    
        default:
            spans = 'span3';
    }
    
    var i;
    
    var s = data.data.station;
    
    for(i in s){
        
        i = Number(i);
        
        if((i)%4 == 0 || i == 0){
            html += '<div class="row-fluid">';
        }
        
        if(data.data[s[i]] != undefined){
            html += '<div class="'+spans+'">';
            html += '<div class="widget-box">';
            html += '<div class="widget-title">';
            html += '<span class="icon"><i class="icon-list"></i></span>';
            html += '<h5>'+data.data[s[i]].name+'</h5>';
            html += '</div>';
            html += '<div class="widget-content nopadding">';
            html += '<table class="table form-horizontal">';
            html += '<thead>';
            html += '<tr>';
            for(var j=0;j<data.data.key.length;j++){
                html += '<th>'+data.data.key[j]+'</th>';
            }
            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';
            if(data.data.table[s[i]] != undefined){
                for(var k=0;k<data.data.table[s[i]].length;k++){
                    html += '<tr>';
                    html += '<td style="text-align:center;">'+data.data.table[s[i]][k].date+'</td>';
                    html += '<td style="text-align:center;">'+data.data.table[s[i]][k].total+'</td>';
                    html += '</tr>';
                }
            }
            html += '<tr>';
            html += '<td colspan="2"></td>';
            html += '</tr>';
            html += '</tbody>';
            html += '</table>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            if((i+1)%4 == 0 || (i+1) == num){
                html += '</div>';
            }
        }
        
    }
    
    $("#containerTable").html(html);
    
    
}

/**
*
* @比较图弹出
*
* @access public 
* @author Nick
* @copyright rockhippo
* @param -
* @return -
*
*/
function chainDiagram(id){
    
    var Hides = 1;
    
    $("#dataError").hide();
    
    var s = new Array();
    $("input[name='stationCheckboxS[]']:checked").each(function(){
        
        s.push($(this).val());
    })
    
    if(s.length == 0){
        $("input[name='stationCheck[]']").each(function(){  
            s.push($(this).val());
        }); 
    }
    
    $.ajax({

         type: "POST",
         url: "/ad/ajaxADChainDiagram",
         async: false,   
        
         data: {
                    id:id,
                    fastSearch:$("#fastHidden").val(),
                    dateSearch:$("#dataSearchHidden").val(),
                    oneday:$("input[name='oneday']").val(),
                    bmanyday:$("input[name='bmanyday']").val(),
                    emanyday:$("input[name='emanyday']").val(),
                    oneweek:$("input[name='oneweek']").val(),
                    bmanyweek:$("input[name='bmanyweek']").val(),
                    emanyweek:$("input[name='emanyweek']").val(),
                    onemonth:$("input[name='onemonth']").val(),
                    bmanymonth:$("input[name='bmanymonth']").val(),
                    emanymonth:$("input[name='emanymonth']").val(),
                    datastatus:$("input[name='datastatus']:checked").val(),
                    stationC:s,
                    distinguish:1
                },

         dataType: "json",

         success: function(data){
            
            if(data.data.station != undefined){

                makeChart("bar","container2","深度比较："+data.title[0],data.xv,"人数/个",data.data);
                Hides = 0;
                
            }else{
                
                //数据异常
                $("#dataError").show();
                return false;
                
            }    
                
         }

    });
    //点击事件触发环比层
    if(Hides == 0){
        $("#loading2").click();
    }
}

/**
*
* @output excel
*
* @access public 
* @author Nick
* @copyright rockhippo
* @param -
* @return -
*
*/
function output(){
    
    var s = new Array();
    $("input[name='stationCheckboxS[]']:checked").each(function(){
        
        s.push($(this).val());
    })
    
    if(s.length == 0){
        $("input[name='stationCheck[]']").each(function(){  
            s.push($(this).val());
        }); 
    }
    window.location.href="/ad/ajaxOutputExcel?fastSearch="+$("#fastHidden").val()+"&datastatusFS="+$("input[name='datastatusFS']:checked").val()+"&dateSearch="+$("#dataSearchHidden").val()+"&oneday="+$("input[name='oneday']").val()+"&bmanyday="+$("input[name='bmanyday']").val()+"&emanyday="+$("input[name='emanyday']").val()+"&oneweek="+$("input[name='oneweek']").val()+"&bmanyweek="+$("input[name='bmanyweek']").val()+"&emanyweek="+$("input[name='emanyweek']").val()+"&onemonth="+$("input[name='onemonth']").val()+"&bmanymonth="+$("input[name='bmanymonth']").val()+"&emanymonth="+$("input[name='emanymonth']").val()+"&datastatus="+$("input[name='datastatus']:checked").val()+"&stationC="+s+"&distinguish=1";
    //
//    $.post(,{
//            
//            fastSearch:,
//                    ,
//                    oneday:,
//                    :,
//                    :,
//                    :,
//                    :,
//                    :,
//                    :,
//                    :,
//                    :,
//                    :,
//                    stationC:s
//        
//    },function(data){
//        
//    });
    
    
}