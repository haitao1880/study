
/**
 * 统计图
 * @param  string thm_title 主标题
 * @param  number title_x   标题相对位置
 * @param  string sub_title 副标题
 * @param  number  sub_x     副标题相对位置
 * @param  array x_cat      x轴栏目名称
 * @param  string y_cat     y轴栏说明
 * @param  array res      生成统计图所需的数据
 * @param  string id        放置图片的容器id
 * @param  string drowtype   图的类型
 */
function PaiTing(thm_title,title_x,sub_title,sub_x,x_cat,y_cat,res,id,drowtype){
	var chart;
    chart = new Highcharts.Chart({ 
        chart: {  //整体控制
            renderTo: id,  //图表容器的DIVbar:横向条形图
            defaultSeriesType: drowtype, //可选，默认为line【line:折线;spline:平滑的线;area:区域图;bar:曲线图;pie:饼图;scatter:点状图;column:柱状图等等;
            marginRight: 130, //外边距控制 (上下左右空隙)
            backgroundColor:"#EEEEEE",
            marginBottom: 25  //外边距控制
        }, 

        title: { 
            text: thm_title, //主标题
            x: title_x           //标题相对位置  默认居中
        },

        plotOptions: {//设置线上各点显示数据
            line: {
                dataLabels: {
                    enabled: true
                },
            },
            column:{
                dataLabels:{
                    enabled:true //是否显示数据标签
                }
            },
            spline: {
                dataLabels: {
                    enabled: true
                }
            },
            area:{
                dataLabels:{
                    enabled:true //是否显示数据标签
                }
            }
        },
        credits: {
             text: 'RockHippo.cn',
             href: 'http://www.rockhippo.cn'
        },
        subtitle: {
        text: sub_title,//副标题
        x: sub_x           //标题相对位置 60
        },
        xAxis: {          //x轴数据
            categories: x_cat
        }, 
        yAxis: {          //y轴数据
            title: { 
                text: y_cat 
            }, 
            plotLines: [{  //标线
                value: 0, 
                width: 1, 
                color: '#808080' 
            }] 
        },
        tooltip: {
            crosshairs: true, 
            shared: true
        }, 
        // tooltip: {        //数据点的提示框
        //     valueSuffix: '%',
        //     formatter: function () { 
        //         return '<b>' + this.series.name + '</b><br/>' + this.x + ': ' + this.y; 
        //     }  //formatter需要一个回调函数，可以通过this关键字打点得到当前一些图表信息
        // }, 
        legend: { 
            layout: 'vertical', 
            align: 'right', 
            verticalAlign: 'top', 
            x: -10, 
            y: 100, 
            borderWidth: 0 
        }, 
       
        series:res 
    }); 
}

/**
 * 统计图
 * @param  string thm_title 主标题
 * @param  number title_x   标题相对位置
 * @param  string sub_title 副标题
 * @param  number  sub_x     副标题相对位置
 * @param  array x_cat      x轴栏目名称
 * @param  string y_cat     y轴栏说明
 * @param  array res      生成统计图所需的数据
 * @param  string id        放置图片的容器id
 * @param  string drowtype   图的类型
 */
function PaiTingShow(thm_title,title_x,sub_title,sub_x,x_cat,y_cat,res,id,drowtype){
    var chart;
    chart = new Highcharts.Chart({ 
        chart: {  //整体控制
            renderTo: id,  //图表容器的DIVbar:横向条形图
            defaultSeriesType: drowtype, //可选，默认为line【line:折线;spline:平滑的线;area:区域图;bar:曲线图;pie:饼图;scatter:点状图;column:柱状图等等;
            marginRight: 130, //外边距控制 (上下左右空隙)
            marginBottom: 25, //外边距控制
            width:arguments[9]
        }, 
        title: { 
            text: thm_title, //主标题
            x: title_x           //标题相对位置  默认居中
        },

        plotOptions: {//设置线上各点显示数据
            line: {
                dataLabels: {
                    enabled: true
                },
            },
            column:{
                dataLabels:{
                    enabled:true //是否显示数据标签
                }
            },
            spline: {
                dataLabels: {
                    enabled: true
                },
            },
            area:{
                dataLabels:{
                    enabled:true //是否显示数据标签
                }
            }
        },
        credits: {
             text: 'RockHippo.cn',
             href: 'http://www.rockhippo.cn'
        },
        subtitle: {
        text: sub_title,//副标题
        x: sub_x           //标题相对位置 60
        },
        xAxis: {          //x轴数据
            categories: x_cat
        }, 
        yAxis: {          //y轴数据
            title: { 
                text: y_cat 
            }, 
            plotLines: [{  //标线
                value: 0, 
                width: 1, 
                color: '#808080' 
            }] 
        },
      
        tooltip: {        //数据点的提示框
            formatter: function () { 
            // console.info(this.series.userOptions.infos);                
                return ShowMsg(this.x,this.series.userOptions.infos,this.series.name,this.y); 
            }  //formatter需要一个回调函数，可以通过this关键字打点得到当前一些图表信息
        }, 
        legend: { 
            layout: 'vertical', 
            align: 'right', 
            verticalAlign: 'top', 
            x: -10, 
            y: 100, 
            borderWidth: 0 
        }, 
       
        series:res 
    }); 
}

//提示信息
function ShowMsg(key,data,name,y,x){
    keys = 'date'+key;
    var msg = '';
    if (data[keys]['title'] || data[keys]['descript']) {
        msg = '<b>' + data[keys]['title'] + '</b><br/>' + data[keys]['descript'];
    }else{
        msg = '<b>' + key + '</b><br/>' + name + ': ' + y;

    }
    return msg;
}


//带水平滚动条
function PaiTingScroll(thm_title,title_x,sub_title,sub_x,x_cat,y_cat,res,id,drowtype){
    var chart;
    chart = new Highcharts.Chart({ 
        chart: {  //整体控制
            renderTo: id,  //图表容器的DIVbar:横向条形图
            defaultSeriesType: drowtype, //可选，默认为line【line:折线;spline:平滑的线;area:区域图;bar:曲线图;pie:饼图;scatter:点状图;column:柱状图等等;
            marginRight: 130, //外边距控制 (上下左右空隙)
            marginBottom: 25,  //外边距控制
            width:arguments[9]
        }, 
        title: { 
            text: thm_title, //主标题
            x: title_x           //标题相对位置  默认居中
        },

        plotOptions: {//设置线上各点显示数据
            line: {
                dataLabels: {
                    enabled: true
                },
            },
            column:{
                dataLabels:{
                    enabled:true //是否显示数据标签
                }
            },
            spline: {
                dataLabels: {
                    enabled: true
                },
            },
            area:{
                dataLabels:{
                    enabled:true //是否显示数据标签
                }
            }
        },
        credits: {
             text: 'RockHippo.cn',
             href: 'http://www.rockhiipo.cn'
        },
        subtitle: {
        text: sub_title,//副标题
        x: sub_x           //标题相对位置 60
        },
        xAxis: {          //x轴数据
            categories: x_cat,
            min: 0,
            max:7
        }, 
        scrollbar: {
            enabled: true
        },
        yAxis: {          //y轴数据
            title: { 
                text: y_cat 
            }, 
            plotLines: [{  //标线
                value: 0, 
                width: 1, 
                color: '#808080' 
            }] 
        },
        tooltip: {
            valueSuffix: ''
        }, 
        // tooltip: {        //数据点的提示框
        //     valueSuffix: '%',
        //     formatter: function () { 
        //         return '<b>' + this.series.name + '</b><br/>' + this.x + ': ' + this.y; 
        //     }  //formatter需要一个回调函数，可以通过this关键字打点得到当前一些图表信息
        // }, 
        legend: { 
            layout: 'vertical', 
            align: 'right', 
            verticalAlign: 'top', 
            x: -10, 
            y: 100, 
            borderWidth: 0 
        }, 
       
        series:res 
    }); 
}


function graphpie(id,title,data){
    $("#"+id).highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            width:arguments[3]
        },
        title: {
            text: title
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: data
    });
}

//初始化弹窗
function reset() {
    alertify.set({
        labels : {
            ok     : "确认",
            cancel : "取消"
        },
        delay : 5000,
        buttonReverse : false,
        buttonFocus   : "ok"
    });
};

//提示框
function alertmsg(msg){
    reset();
    alertify.alert(msg);
    return false;
}

function createTable(title,data,idselector){
	$("#"+idselector).html("");	
	var $table = '';
	var $title = '';
	var $data = '';
	$.each(title,function(k,v1){
		$title += '<th>'+v1+'</th>';
	});

	$.each(data,function(k,v2){
		$data += '<td style="text-align:center;">'+v2+'</td>';
	});

	$table+='<div class="row-fluid">'+
			    '<div class="span12">'+
			        '<div class="widget-box">'+
			            '<div class="widget-content nopadding">'+
			                '<table class="table form-horizontal">'+
			                    '<thead>'+
			                        '<tr>'+
				                       $title +
			                        '</tr>'+
			                    '</thead>'+
			                    '<tbody>'+
		                            '<tr>'+
			                            $data+
		                            '</tr>'+                      
			                    '</tbody>'+
			                '</table>'+
			            '</div>'+
			        '</div>'+
			    '</div>'+
			  
			'</div>';
	$("#"+idselector).html($table);	
}

$('#screening').delegate('.checi', 'click', function(){
	$('#screening').find('span').removeClass('checked');
	$(this).find('span').addClass('checked');

	$('#screening').find('span input').prop("checked",false);
	$(this).find('span input').prop("checked",true);
})




