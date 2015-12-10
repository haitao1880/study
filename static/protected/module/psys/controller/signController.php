<?php
/**
* Copyright(c) 2015
* 日    期:2015年07月07日
* 文 件 名:{sign}Controller.php
* 创建时间:16:04
* 字符编码:UTF-8
* 版本信息:v1.0
* 修改日期:2015年07月14日
* 最后版本:v1.0
* 创 建 者:jerry (jerry@rockhippo.cn)
* 修 改 者:Jerry (Jerry@rockhippo.cn)
* 版本地址:none
* 摘    要: 访问统计控制器
*/
class signController extends PSys_AbstractController{
    
	public function __construct() {
		parent::__construct();
        $this->smarty->assign("gameActive","active");
        $this->smarty->assign("trainActive","");
        $this->smarty->assign("gameHidden","");
        $this->smarty->assign("trainHidden","hidden");
        $this->smarty->assign("busHidden","hidden");
        $this->smarty->assign("busActive","");
        $this->smarty->assign("marketHidden","hidden");
        $this->smarty->assign("marketActive","");
	}
    
	/**
     *
     * @do 展示浏览数据
     *
     * @sign public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function indexAction(){
        $this->smarty->assign("active","sign/index");
        $this->smarty->assign("active_menu","sign");
        $this->smarty->assign("sdate",date('Y-m-d',strtotime("-6 days")));
        $this->smarty->assign("edate",date('Y-m-d'));
		$this->forward = "index";
        
	}
    
    /**
    *
    * @do ajax 获取日浏览数据
    *
    * @sign public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return json
    *
    */
    public function ajaxSignJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $PSys_signModel = new PSys_PageviewModel();
        $where["day_>="] = $start;
        $where["day_<="] = $end;
        $order = "day ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_sign_daily");
        
        $xAxis = $series = $start_times = $sign_times = "";
        foreach($result["allrow"] as $k=>$v){
            $xAxis .= '"'.$v["day"].'",';
            $start_times .= $v["start_times"].',';
            $sign_times .= $v["sign_times"].',';
        }
        $xAxis = trim($xAxis,",");
        $start_times = "{name:'启动次数',data:[".trim($start_times,",")."]},";
        $sign_times = "{name:'签到人数',data:[".trim($sign_times,",")."]},";
        $series = $start_times.$sign_times;
        $data['xAxis'] = $xAxis;
        $data['series'] = $series;
        die(json_encode($data));
    }
    
    /**
    *
    * @do ajax 获取浏览数据
    *
    * @sign public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return html
    *
    */
    public function ajaxSignHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $PSys_signModel = new PSys_PageviewModel();
        $where["day_>="] = $start;
        $where["day_<="] = $end;
        $order = "day ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_sign_daily");
        $total_sign_times = $total_start_times = 0;
        foreach($result["allrow"] as $k=>$v){
            $total_start_times += intval($v["start_times"]);
            $total_sign_times += intval($v["sign_times"]);
            $sign_rate = round(intval($v["sign_times"])/intval($v["start_times"])*100,2);
            $result["allrow"][$k]['sign_rate'] = $sign_rate."%";
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("total_start_times",$total_start_times);
        $this->smarty->assign("total_sign_times",$total_sign_times);
		$this->forward = "ajaxSign";
    }
    
    /**
     *
     * @do 展示周浏览数据
     *
     * @sign public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function weeklyAction(){
        $this->smarty->assign("active","sign/weekly");
        $this->smarty->assign("active_menu","sign");
        $this->smarty->assign("sdate",date('Y-m-d',strtotime("-14 days")));
        $this->smarty->assign("edate",date('Y-m-d'));
		$this->forward = "weekly";
        
	}
    
    /**
    *
    * @do ajax 获取周浏览数据
    *
    * @sign public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return json
    *
    */
    public function ajaxWeeklysignJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $tempdate=date('Y-m-d',strtotime("$start Monday")); 
        $start=date('Y-m-d',strtotime("$tempdate -7 days"));
        $end=date('Y-m-d',strtotime("$end Sunday"));
        
        $PSys_signModel = new PSys_PageviewModel();               
        $where["startday_>="] = $start;
        $where["endday_<="] = $end;
        $order = "startday ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_sign_weekly");
        $xAxis = $series = $start_times = $sign_times = "";
        foreach($result["allrow"] as $k=>$v){
            $xtemp = $v["startday"];
            $mictime = strtotime($v["startday"]);
            $endtemp = date('/m/d',strtotime("$xtemp Sunday"));
            $xAxis .= '"'.date('Y/m/d',$mictime).' - '.$endtemp.'",';
            $start_times .= $v["start_times"].',';
            $sign_times .= $v["sign_times"].',';
        }
        $xAxis = trim($xAxis,",");
        $start_times = "{name:'启动次数',data:[".trim($start_times,",")."]},";
        $sign_times = "{name:'签到人数',data:[".trim($sign_times,",")."]},";
        $series = $start_times.$sign_times;
        $data['xAxis'] = $xAxis;
        $data['series'] = $series;
        
        die(json_encode($data));
    }
    
    /**
    *
    * @do ajax 获取周浏览数据
    *
    * @sign public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return html
    *
    */
    public function ajaxWeeklysignHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $tempdate=date('Y-m-d',strtotime("$start Monday")); 
        $start=date('Y-m-d',strtotime("$tempdate -7 days"));
        $end=date('Y-m-d',strtotime("$end Sunday"));
        
        $PSys_signModel = new PSys_PageviewModel();
        $where["startday_>="] = $start;
        $where["endday_<="] = $end;
        $order = "startday ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_sign_weekly");
        $total_sign_times = $total_start_times = 0;
        foreach($result["allrow"] as $k=>$v){
            $total_start_times += intval($v["start_times"]);
            $total_sign_times += intval($v["sign_times"]);
            $sign_rate = round(intval($v["sign_times"])/intval($v["start_times"])*100,2);
            $result["allrow"][$k]['sign_rate'] = $sign_rate."%";
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("total_start_times",$total_start_times);
        $this->smarty->assign("total_sign_times",$total_sign_times);
		$this->forward = "ajaxWeeklySign";
    }
    
    /**
     *
     * @do 展示月浏览数据
     *
     * @sign public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function monthlyAction(){
        $this->smarty->assign("active","sign/monthly");
        $this->smarty->assign("active_menu","sign");
        $this->smarty->assign("sdate",date("Y-m",mktime(0,0,0,date("m")-2,1,date("Y"))));
        $this->smarty->assign("edate",date("Y-m",mktime(0,0,0,date("m"),1,date("Y"))));
		$this->forward = "monthly";
        
	}
    
    /**
    *
    * @do ajax 获取月浏览数据
    *
    * @sign public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return json
    *
    */
    public function ajaxMonthlysignJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        
        $PSys_signModel = new PSys_PageviewModel();               
        $where["month_>="] = $start;
        $where["month_<="] = $end;
        $order = "month ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_sign_monthly");
        $xAxis = $series = $start_times = $sign_times = "";
        foreach($result["allrow"] as $k=>$v){
            $xAxis .= '"'.$v["month"].'",';
            $start_times .= $v["start_times"].',';
            $sign_times .= $v["sign_times"].',';   
        }
        $xAxis = trim($xAxis,",");
        $start_times = "{name:'启动次数',data:[".trim($start_times,",")."]},";
        $sign_times = "{name:'签到人数',data:[".trim($sign_times,",")."]},";
        $series = $start_times.$sign_times;
        $data['xAxis'] = $xAxis;
        $data['series'] = $series;
        die(json_encode($data));
    }
    
    /**
    *
    * @do ajax 获取月浏览数据
    *
    * @sign public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return html
    *
    */
    public function ajaxMonthlysignHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        
        $PSys_signModel = new PSys_PageviewModel();
        $where["month_>="] = $start;
        $where["month_<="] = $end;
        $order = "month ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_sign_monthly");
     
        $total_start_times = $total_sign_times = 0;
        foreach($result["allrow"] as $k=>$v){
            $total_start_times += intval($v["start_times"]);
            $total_sign_times += intval($v["sign_times"]);
            $sign_rate = round(intval($v["sign_times"])/intval($v["start_times"])*100,2);
            $result["allrow"][$k]['sign_rate'] = $sign_rate."%";
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("total_start_times",$total_start_times);
        $this->smarty->assign("total_sign_times",$total_sign_times);
		$this->forward = "ajaxMonthlySign";
    }
    
}