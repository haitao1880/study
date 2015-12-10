<?php
/**
* Copyright(c) 2015
* 日    期:2015年07月07日
* 文 件 名:{package}Controller.php
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
class packageController extends PSys_AbstractController{
    
	public function __construct() {
		parent::__construct();
        $PSys_GameModel = new PSys_GameModel();
        $order = "id ASC";
        $where['ispack'] = 1;
        $result = $PSys_GameModel->GetList($where, $order, 0, 0, "id,appname");
        $this->smarty->assign("gamelist",$result['allrow']);
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
     * @package public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function indexAction(){
        $this->smarty->assign("active","package/index");
        $this->smarty->assign("active_menu","package");
		$this->forward = "index";
        
	}
    
    /**
    *
    * @do ajax 获取日浏览数据
    *
    * @package public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return json
    *
    */
    public function ajaxPackageJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $game = reqstr("game","");
        $PSys_packageModel = new PSys_PageviewModel();
        $where["day_>="] = $start;
        $where["day_<="] = $end;
        $where["game"] = $game;
        $order = "day ASC";
        $result = $PSys_packageModel->GetList($where, $order, 0, 0, "*","rhc_package_daily");
       
        $xAxis = $series = $send_times = "";
        foreach($result["allrow"] as $k=>$v){
            $xAxis .= '"'.$v["day"].'",';
            $send_times .= $v["send_times"].','; 
        }
        
        $xAxis = trim($xAxis,",");
        $send_times = "{name:'发放次数',data:[".trim($send_times,",")."]},";
        $series = $send_times;
        $data['xAxis'] = $xAxis;
        $data['series'] = $series;
        die(json_encode($data));
    }
    
    /**
    *
    * @do ajax 获取浏览数据
    *
    * @package public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return html
    *
    */
    public function ajaxPackageHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $game = reqstr("game","");
        
        $PSys_packageModel = new PSys_PageviewModel();
        $where["day_>="] = $start;
        $where["day_<="] = $end;
        $where["game"] = $game;
        $order = "day ASC";
        $result = $PSys_packageModel->GetList($where, $order, 0, 0, "*","rhc_package_daily");
        $send_times = "";
        foreach($result["allrow"] as $k=>$v){
            $send_times += intval($v["send_times"]);
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("send_times",$send_times);
		$this->forward = "ajaxPackage";
    }
    
    /**
     *
     * @do 展示周浏览数据
     *
     * @package public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function weeklyAction(){
        $this->smarty->assign("active","package/weekly");
        $this->smarty->assign("active_menu","package");
		$this->forward = "weekly";
        
	}
    
    /**
    *
    * @do ajax 获取周浏览数据
    *
    * @package public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return json
    *
    */
    public function ajaxWeeklyPackageJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $game = reqstr("game","");
        $tempdate=date('Y-m-d',strtotime("$start Monday")); 
        $start=date('Y-m-d',strtotime("$tempdate -7 days"));
        $end=date('Y-m-d',strtotime("$end Sunday"));
        
        $PSys_packageModel = new PSys_PageviewModel();               
        $where["startday_>="] = $start;
        $where["endday_<="] = $end;
        $where["game"] = $game;
        $order = "startday ASC";
        $result = $PSys_packageModel->GetList($where, $order, 0, 0, "*","rhc_package_weekly");
        $xAxis = $series = $send_times = "";
        foreach($result["allrow"] as $k=>$v){
            $xtemp = $v["startday"];
            $mictime = strtotime($v["startday"]);
            $endtemp = date('/m/d',strtotime("$xtemp Sunday"));
            $xAxis .= '"'.date('Y/m/d',$mictime).' - '.$endtemp.'",';
            $send_times .= $v["send_times"].',';
        }
        $xAxis = trim($xAxis,",");
        $send_times = "{name:'发放次数',data:[".trim($send_times,",")."]},";
        $series = $send_times;
        $data['xAxis'] = $xAxis;
        $data['series'] = $series;
        
        die(json_encode($data));
    }
    
    /**
    *
    * @do ajax 获取周浏览数据
    *
    * @package public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return html
    *
    */
    public function ajaxWeeklyPackageHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $game = reqstr("game","");
        $tempdate=date('Y-m-d',strtotime("$start Monday")); 
        $start=date('Y-m-d',strtotime("$tempdate -7 days"));
        $end=date('Y-m-d',strtotime("$end Sunday"));
        
        $PSys_packageModel = new PSys_PageviewModel();
        $where["startday_>="] = $start;
        $where["endday_<="] = $end;
        $where["game"] = $game;
        $order = "startday ASC";
        $result = $PSys_packageModel->GetList($where, $order, 0, 0, "*","rhc_package_weekly");
        $send_times = "";
        foreach($result["allrow"] as $k=>$v){
            $send_times += intval($v["send_times"]);
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("send_times",$send_times);
		$this->forward = "ajaxWeeklyPackage";
    }
    
    /**
     *
     * @do 展示月浏览数据
     *
     * @package public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function monthlyAction(){
        $this->smarty->assign("active","package/monthly");
        $this->smarty->assign("active_menu","package");
		$this->forward = "monthly";
        
	}
    
    /**
    *
    * @do ajax 获取月浏览数据
    *
    * @package public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return json
    *
    */
    public function ajaxMonthlyPackageJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $game = reqstr("game","");
        $PSys_packageModel = new PSys_PageviewModel();               
        $where["month_>="] = $start;
        $where["month_<="] = $end;
        $where["game"] = $game;
        $order = "month ASC";
        $result = $PSys_packageModel->GetList($where, $order, 0, 0, "*","rhc_package_monthly");
        $xAxis = $series = $send_times = "";
        foreach($result["allrow"] as $k=>$v){
            $xAxis .= '"'.$v["month"].'",';
            $send_times .= $v["send_times"].',';
        }
        $xAxis = trim($xAxis,",");
        $send_times = "{name:'发放次数',data:[".trim($send_times,",")."]},";
        $series = $send_times;
        $data['xAxis'] = $xAxis;
        $data['series'] = $series;
        die(json_encode($data));
    }
    
    /**
    *
    * @do ajax 获取月浏览数据
    *
    * @package public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return html
    *
    */
    public function ajaxMonthlyPackageHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $game = reqstr("game","");
        $PSys_packageModel = new PSys_PageviewModel();
        $where["month_>="] = $start;
        $where["month_<="] = $end;
        $where["game"] = $game;
        $order = "month ASC";
        $result = $PSys_packageModel->GetList($where, $order, 0, 0, "*","rhc_package_monthly");
        $send_times = "";
        foreach($result["allrow"] as $k=>$v){
            $send_times += intval($v["send_times"]);
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("send_times",$send_times);
		$this->forward = "ajaxMonthlyPackage";
    }
    
}