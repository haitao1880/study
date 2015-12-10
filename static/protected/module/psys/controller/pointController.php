<?php
/**
* Copyright(c) 2015
* 日    期:2015年07月07日
* 文 件 名:{point}Controller.php
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
class pointController extends PSys_AbstractController{
    
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
     * @point public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function indexAction(){
        $this->smarty->assign("active","point/index");
        $this->smarty->assign("active_menu","point");
        $this->smarty->assign("sdate",date('Y-m-d',strtotime("-6 days")));
        $this->smarty->assign("edate",date('Y-m-d'));
		$this->forward = "index";
        
	}
    
    /**
    *
    * @do ajax 获取日浏览数据
    *
    * @point public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return json
    *
    */
    public function ajaxPointJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $PSys_pointModel = new PSys_PageviewModel();
        $where["day_>="] = $start;
        $where["day_<="] = $end;
        $order = "day ASC";
        $result = $PSys_pointModel->GetList($where, $order, 0, 0, "*","rhc_point_daily");
        
        $xAxis = $series = $total_send_point = $total_use_point = $sign_point = $download_point = $buy_point = $package_point = $huoban_point = $huafei_point = $other_point = "";
        foreach($result["allrow"] as $k=>$v){
            $xAxis .= '"'.$v["day"].'",';
            $total_send_point .= $v["total_send_point"].',';
            $total_use_point .= abs($v["total_use_point"]).',';
            $sign_point .= $v["sign_point"].',';
            $download_point .= $v["download_point"].',';
            $buy_point .= $v["buy_point"].',';
            $package_point .= $v["package_point"].',';
            $huoban_point .= $v["huoban_point"].',';
            $huafei_point .= abs($v["huafei_point"]).',';
            $other_point .= $v["other_point"].',';
        }
        $xAxis = trim($xAxis,",");
        $total_send_point = "{name:'积分发放总量',data:[".trim($total_send_point,",")."]},";
        $total_use_point = "{name:'积分消耗总量',data:[".trim($total_use_point,",")."]},";
        $sign_point = "{name:'签到获取积分',data:[".trim($sign_point,",")."]},";
        $download_point = "{name:'下载获取积分',data:[".trim($download_point,",")."]},";
        $buy_point = "{name:'购买获取积分',data:[".trim($buy_point,",")."]},";
        $package_point = "{name:'礼包获取积分',data:[".trim($package_point,",")."]},";
        $huafei_point = "{name:'话费消耗积分',data:[".trim($huafei_point,",")."]},";
        $huoban_point = "{name:'火伴获取积分',data:[".trim($huoban_point,",")."]},";
        $other_point = "{name:'其他获取积分',data:[".trim($other_point,",")."]},";
        $series = $total_send_point.$total_use_point.$sign_point.$download_point.$buy_point.$package_point.$huoban_point.$huafei_point.$other_point;
        $data['xAxis'] = $xAxis;
        $data['series'] = $series;
        die(json_encode($data));
    }
    
    /**
    *
    * @do ajax 获取浏览数据
    *
    * @point public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return html
    *
    */
    public function ajaxPointHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $PSys_pointModel = new PSys_PageviewModel();
        $where["day_>="] = $start;
        $where["day_<="] = $end;
        $order = "day ASC";
        $result = $PSys_pointModel->GetList($where, $order, 0, 0, "*","rhc_point_daily");
        $total_send_point = $total_use_point = $sign_point = $download_point = $buy_point = $package_point = $huoban_point = $huafei_point = $other_point = 0;
        foreach($result["allrow"] as $k=>$v){
            $total_send_point += intval($v["total_send_point"]);
            $total_use_point += intval($v["total_use_point"]);
            $sign_point += intval($v["sign_point"]);
            $download_point += intval($v["download_point"]);
            $buy_point += intval($v["buy_point"]);
            $package_point += intval($v["package_point"]);
            $huoban_point += intval($v["huoban_point"]);
            $huafei_point += intval($v["huafei_point"]);
            $other_point += intval($v["other_point"]);
			$result["allrow"][$k]['total_use_point'] = abs($v["total_use_point"]);
			$result["allrow"][$k]['huafei_point'] = abs($v["huafei_point"]);
        }
		$total_use_point = abs($total_use_point);
		$huafei_point = abs($huafei_point);
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("total_send_point",$total_send_point);
        $this->smarty->assign("total_use_point",$total_use_point);
        $this->smarty->assign("sign_point",$sign_point);
        $this->smarty->assign("download_point",$download_point);
        $this->smarty->assign("buy_point",$buy_point);
        $this->smarty->assign("package_point",$package_point);
        $this->smarty->assign("huoban_point",$huoban_point);
        $this->smarty->assign("huafei_point",$huafei_point);
        $this->smarty->assign("other_point",$other_point);
		$this->forward = "ajaxPoint";
    }
    
    /**
     *
     * @do 展示周浏览数据
     *
     * @point public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function weeklyAction(){
        $this->smarty->assign("active","point/weekly");
        $this->smarty->assign("active_menu","point");
        $this->smarty->assign("sdate",date('Y-m-d',strtotime("-14 days")));
        $this->smarty->assign("edate",date('Y-m-d'));
		$this->forward = "weekly";
        
	}
    
    /**
    *
    * @do ajax 获取周浏览数据
    *
    * @point public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return json
    *
    */
    public function ajaxWeeklypointJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $tempdate=date('Y-m-d',strtotime("$start Monday")); 
        $start=date('Y-m-d',strtotime("$tempdate -7 days"));
        $end=date('Y-m-d',strtotime("$end Sunday"));
        
        $PSys_pointModel = new PSys_PageviewModel();               
        $where["startday_>="] = $start;
        $where["endday_<="] = $end;
        $order = "startday ASC";
        $result = $PSys_pointModel->GetList($where, $order, 0, 0, "*","rhc_point_weekly");
        $xAxis = $series = $total_send_point = $total_use_point = $sign_point = $download_point = $buy_point = $package_point = $huoban_point = $huafei_point = $other_point = "";
        foreach($result["allrow"] as $k=>$v){
            $xtemp = $v["startday"];
            $mictime = strtotime($v["startday"]);
            $endtemp = date('/m/d',strtotime("$xtemp Sunday"));
            $xAxis .= '"'.date('Y/m/d',$mictime).' - '.$endtemp.'",';
            $total_send_point .= $v["total_send_point"].',';
            $total_use_point .= abs($v["total_use_point"]).',';
            $sign_point .= $v["sign_point"].',';
            $download_point .= $v["download_point"].',';
            $buy_point .= $v["buy_point"].',';
            $package_point .= $v["package_point"].',';
            $huoban_point .= $v["huoban_point"].',';
            $huafei_point .= abs($v["huafei_point"]).',';
            $other_point .= $v["other_point"].',';
        }
        $xAxis = trim($xAxis,",");
        $total_send_point = "{name:'积分发放总量',data:[".trim($total_send_point,",")."]},";
        $total_use_point = "{name:'积分消耗总量',data:[".trim($total_use_point,",")."]},";
        $sign_point = "{name:'签到获取积分',data:[".trim($sign_point,",")."]},";
        $download_point = "{name:'下载获取积分',data:[".trim($download_point,",")."]},";
        $buy_point = "{name:'购买获取积分',data:[".trim($buy_point,",")."]},";
        $package_point = "{name:'礼包获取积分',data:[".trim($package_point,",")."]},";
        $huafei_point = "{name:'话费消耗积分',data:[".trim($huafei_point,",")."]},";
        $huoban_point = "{name:'火伴获取积分',data:[".trim($huoban_point,",")."]},";
        $other_point = "{name:'其他获取积分',data:[".trim($other_point,",")."]},";
        $series = $total_send_point.$total_use_point.$sign_point.$download_point.$buy_point.$package_point.$huoban_point.$huafei_point.$other_point;
        $data['xAxis'] = $xAxis;
        $data['series'] = $series;
        die(json_encode($data));
    }
    
    /**
    *
    * @do ajax 获取周浏览数据
    *
    * @point public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return html
    *
    */
    public function ajaxWeeklypointHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $tempdate=date('Y-m-d',strtotime("$start Monday")); 
        $start=date('Y-m-d',strtotime("$tempdate -7 days"));
        $end=date('Y-m-d',strtotime("$end Sunday"));
        
        $PSys_pointModel = new PSys_PageviewModel();
        $where["startday_>="] = $start;
        $where["endday_<="] = $end;
        $order = "startday ASC";
        $result = $PSys_pointModel->GetList($where, $order, 0, 0, "*","rhc_point_weekly");
        $total_send_point = $total_use_point = $sign_point = $download_point = $buy_point = $package_point = $huoban_point = $huafei_point = $other_point = 0;
        foreach($result["allrow"] as $k=>$v){
            $total_send_point += intval($v["total_send_point"]);
            $total_use_point += intval($v["total_use_point"]);
            $sign_point += intval($v["sign_point"]);
            $download_point += intval($v["download_point"]);
            $buy_point += intval($v["buy_point"]);
            $package_point += intval($v["package_point"]);
            $huoban_point += intval($v["huoban_point"]);
            $huafei_point += intval($v["huafei_point"]);
            $other_point += intval($v["other_point"]);
			$result["allrow"][$k]['total_use_point'] = abs($v["total_use_point"]);
			$result["allrow"][$k]['huafei_point'] = abs($v["huafei_point"]);
        }
		$total_use_point = abs($total_use_point);
		$huafei_point = abs($huafei_point);
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("total_send_point",$total_send_point);
        $this->smarty->assign("total_use_point",$total_use_point);
        $this->smarty->assign("sign_point",$sign_point);
        $this->smarty->assign("download_point",$download_point);
        $this->smarty->assign("buy_point",$buy_point);
        $this->smarty->assign("package_point",$package_point);
        $this->smarty->assign("huoban_point",$huoban_point);
        $this->smarty->assign("huafei_point",$huafei_point);
        $this->smarty->assign("other_point",$other_point);
		$this->forward = "ajaxWeeklyPoint";
    }
    
    /**
     *
     * @do 展示月浏览数据
     *
     * @point public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function monthlyAction(){
        $this->smarty->assign("active","point/monthly");
        $this->smarty->assign("active_menu","point");
        $this->smarty->assign("sdate",date("Y-m",mktime(0,0,0,date("m")-2,1,date("Y"))));
        $this->smarty->assign("edate",date("Y-m",mktime(0,0,0,date("m"),1,date("Y"))));
		$this->forward = "monthly";
        
	}
    
    /**
    *
    * @do ajax 获取月浏览数据
    *
    * @point public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return json
    *
    */
    public function ajaxMonthlypointJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        
        $PSys_pointModel = new PSys_PageviewModel();               
        $where["month_>="] = $start;
        $where["month_<="] = $end;
        $order = "month ASC";
        $result = $PSys_pointModel->GetList($where, $order, 0, 0, "*","rhc_point_monthly");
        $xAxis = $series = $total_send_point = $total_use_point = $sign_point = $download_point = $buy_point = $package_point = $huoban_point = $huafei_point = $other_point = "";
        foreach($result["allrow"] as $k=>$v){
            $xAxis .= '"'.$v["month"].'",';
            $total_send_point .= $v["total_send_point"].',';
            $total_use_point .= abs($v["total_use_point"]).',';
            $sign_point .= $v["sign_point"].',';
            $download_point .= $v["download_point"].',';
            $buy_point .= $v["buy_point"].',';
            $package_point .= $v["package_point"].',';
            $huoban_point .= $v["huoban_point"].',';
            $huafei_point .= abs($v["huafei_point"]).',';
            $other_point .= $v["other_point"].',';
        }
        $xAxis = trim($xAxis,",");
        $total_send_point = "{name:'积分发放总量',data:[".trim($total_send_point,",")."]},";
        $total_use_point = "{name:'积分消耗总量',data:[".trim($total_use_point,",")."]},";
        $sign_point = "{name:'签到获取积分',data:[".trim($sign_point,",")."]},";
        $download_point = "{name:'下载获取积分',data:[".trim($download_point,",")."]},";
        $buy_point = "{name:'购买获取积分',data:[".trim($buy_point,",")."]},";
        $package_point = "{name:'礼包获取积分',data:[".trim($package_point,",")."]},";
        $huafei_point = "{name:'话费消耗积分',data:[".trim($huafei_point,",")."]},";
        $huoban_point = "{name:'火伴获取积分',data:[".trim($huoban_point,",")."]},";
        $other_point = "{name:'其他获取积分',data:[".trim($other_point,",")."]},";
        $series = $total_send_point.$total_use_point.$sign_point.$download_point.$buy_point.$package_point.$huoban_point.$huafei_point.$other_point;
        $data['xAxis'] = $xAxis;
        $data['series'] = $series;
        die(json_encode($data));
    }
    
    /**
    *
    * @do ajax 获取月浏览数据
    *
    * @point public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return html
    *
    */
    public function ajaxMonthlypointHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        
        $PSys_pointModel = new PSys_PageviewModel();
        $where["month_>="] = $start;
        $where["month_<="] = $end;
        $order = "month ASC";
        $result = $PSys_pointModel->GetList($where, $order, 0, 0, "*","rhc_point_monthly");
        $total_send_point = $total_use_point = $sign_point = $download_point = $buy_point = $package_point = $huoban_point = $huafei_point = $other_point = 0;
        foreach($result["allrow"] as $k=>$v){
            $total_send_point += intval($v["total_send_point"]);
            $total_use_point += intval($v["total_use_point"]);
            $sign_point += intval($v["sign_point"]);
            $download_point += intval($v["download_point"]);
            $buy_point += intval($v["buy_point"]);
            $package_point += intval($v["package_point"]);
            $huoban_point += intval($v["huoban_point"]);
            $huafei_point += intval($v["huafei_point"]);
            $other_point += intval($v["other_point"]);
			$result["allrow"][$k]['total_use_point'] = abs($v["total_use_point"]);
			$result["allrow"][$k]['huafei_point'] = abs($v["huafei_point"]);
        }
		$total_use_point = abs($total_use_point);
		$huafei_point = abs($huafei_point);
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("total_send_point",$total_send_point);
        $this->smarty->assign("total_use_point",$total_use_point);
        $this->smarty->assign("sign_point",$sign_point);
        $this->smarty->assign("download_point",$download_point);
        $this->smarty->assign("buy_point",$buy_point);
        $this->smarty->assign("package_point",$package_point);
        $this->smarty->assign("huoban_point",$huoban_point);
        $this->smarty->assign("huafei_point",$huafei_point);
        $this->smarty->assign("other_point",$other_point);
		$this->forward = "ajaxMonthlyPoint";
    }
    
}
