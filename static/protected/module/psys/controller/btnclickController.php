<?php
/**
* Copyright(c) 2015
* 日    期:2015年07月07日
* 文 件 名:{btnclick}Controller.php
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
class btnclickController extends PSys_AbstractController{
    
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
     * @btnclick public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function indexAction(){
        $this->smarty->assign("active","btnclick/index");
        $this->smarty->assign("active_menu","btnclick");
        $this->smarty->assign("sdate",date('Y-m-d',strtotime("-6 days")));
        $this->smarty->assign("edate",date('Y-m-d'));
		$this->forward = "index";
        
	}
    
    /**
    *
    * @do ajax 获取日浏览数据
    *
    * @btnclick public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return json
    *
    */
    public function ajaxBtnclickJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $PSys_BtnclickModel = new PSys_PageviewModel();
        $where["day_>="] = $start;
        $where["day_<="] = $end;
        $order = "day ASC";
        $result = $PSys_BtnclickModel->GetList($where, $order, 0, 0, "*","rhc_click_daily");
        $xAxis = $series = $btn_201 = $btn_202 = $btn_203 = $btn_204 = $btn_205 = $btn_207 = $btn_208 = $btn_301 = $btn_302 = $btn_303 = $btn_401 = $btn_402 = $btn_501 = $btn_601 = $btn_602 = $btn_603  = $btn_604 = $btn_605 = $btn_701 = $btn_702 = $btn_801 = "";
        foreach($result["allrow"] as $k=>$v){
            $xAxis .= '"'.$v["day"].'",';
            $btn_201 .= $v["btn_201"].',';
            $btn_202 .= $v["btn_202"].',';
            $btn_203 .= $v["btn_203"].',';
            $btn_204 .= $v["btn_204"].',';
            $btn_205 .= $v["btn_205"].',';
            $btn_207 .= $v["btn_207"].',';
            $btn_208 .= $v["btn_208"].',';
            $btn_301 .= $v["btn_301"].',';
            $btn_302 .= $v["btn_302"].',';
            $btn_303 .= $v["btn_303"].',';
            $btn_401 .= $v["btn_401"].',';
            $btn_402 .= $v["btn_402"].',';
            $btn_501 .= $v["btn_501"].',';
            $btn_601 .= $v["btn_601"].',';
            $btn_602 .= $v["btn_602"].',';
            $btn_603 .= $v["btn_603"].',';
            $btn_604 .= $v["btn_604"].',';
            $btn_605 .= $v["btn_605"].',';
            $btn_701 .= $v["btn_701"].',';
            $btn_702 .= $v["btn_702"].',';
            $btn_801 .= $v["btn_801"].',';
        }
        $xAxis = trim($xAxis,",");
        $btn_201 = "{name:'[web]下载',data:[".trim($btn_201,",")."]},";
        $btn_202 = "{name:'[web]安装',data:[".trim($btn_202,",")."]},";
        $btn_203 = "{name:'[web]打开',data:[".trim($btn_203,",")."]},";
        $btn_204 = "{name:'确定下载',data:[".trim($btn_204,",")."]},";
        $btn_205 = "{name:'[安卓]更新',data:[".trim($btn_205,",")."]},";
        $btn_207 = "{name:'[安卓]安装',data:[".trim($btn_207,",")."]},";
        $btn_208 = "{name:'[安卓]打开',data:[".trim($btn_208,",")."]},";
        $btn_301 = "{name:'游戏礼包',data:[".trim($btn_301,",")."]},";
        $btn_302 = "{name:'领取礼包[详情]',data:[".trim($btn_302,",")."]},";
        $btn_303 = "{name:'领取礼包',data:[".trim($btn_303,",")."]},";
        $btn_401 = "{name:'签到有礼',data:[".trim($btn_401,",")."]},";
        $btn_402 = "{name:'今日签到',data:[".trim($btn_402,",")."]},";
        $btn_501 = "{name:'排行榜',data:[".trim($btn_501,",")."]},";
        $btn_601 = "{name:'动作',data:[".trim($btn_601,",")."]},";
        $btn_602 = "{name:'消除',data:[".trim($btn_602,",")."]},";
        $btn_603 = "{name:'跑酷',data:[".trim($btn_603,",")."]},";
        $btn_604 = "{name:'网游rpg',data:[".trim($btn_604,",")."]},";
        $btn_605 = "{name:'其他',data:[".trim($btn_605,",")."]},";
        $btn_701 = "{name:'登录',data:[".trim($btn_701,",")."]},";
        $btn_702 = "{name:'退出',data:[".trim($btn_702,",")."]},";
        $btn_801 = "{name:'Banner广告',data:[".trim($btn_801,",")."]}";
        $series = $btn_201.$btn_202.$btn_203.$btn_204.$btn_205.$btn_207.$btn_208.$btn_301.$btn_302.$btn_303.$btn_401.$btn_402.$btn_501.$btn_601.$btn_602.$btn_603.$btn_604.$btn_605.$btn_701.$btn_702.$btn_801;
        $data['xAxis'] = $xAxis;
        $data['series'] = $series;
        die(json_encode($data));
    }
    
    /**
    *
    * @do ajax 获取浏览数据
    *
    * @btnclick public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return html
    *
    */
    public function ajaxBtnclickHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $PSys_BtnclickModel = new PSys_PageviewModel();
        $where["day_>="] = $start;
        $where["day_<="] = $end;
        $order = "day ASC";
        $result = $PSys_BtnclickModel->GetList($where, $order, 0, 0, "*","rhc_click_daily");
        $btn_201 = $btn_202 = $btn_203 = $btn_204 = $btn_205 = $btn_207 = $btn_208 = $btn_301 = $btn_302 = $btn_303 = $btn_401 = $btn_402 = $btn_501 = $btn_601 = $btn_602 = $btn_603  = $btn_604 = $btn_605 = $btn_701 = $btn_702 = $btn_801 = 0;
        foreach($result["allrow"] as $k=>$v){
            $btn_201 += intval($v["btn_201"]);
            $btn_202 += intval($v["btn_202"]);
            $btn_203 += intval($v["btn_203"]);
            $btn_204 += intval($v["btn_204"]);
            $btn_205 += intval($v["btn_205"]);
            $btn_207 += intval($v["btn_207"]);
            $btn_208 += intval($v["btn_208"]);
            $btn_301 += intval($v["btn_301"]);
            $btn_302 += intval($v["btn_302"]);
            $btn_303 += intval($v["btn_303"]);
            $btn_401 += intval($v["btn_401"]);
            $btn_402 += intval($v["btn_402"]);
            $btn_501 += intval($v["btn_501"]);
            $btn_601 += intval($v["btn_601"]);
            $btn_602 += intval($v["btn_602"]);
            $btn_603 += intval($v["btn_603"]);
            $btn_604 += intval($v["btn_604"]);
            $btn_605 += intval($v["btn_605"]);
            $btn_701 += intval($v["btn_701"]);
            $btn_702 += intval($v["btn_702"]);
            $btn_801 += intval($v["btn_801"]);
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("btn_201",$btn_201);
        $this->smarty->assign("btn_202",$btn_202);
        $this->smarty->assign("btn_203",$btn_203);
        $this->smarty->assign("btn_204",$btn_204);
        $this->smarty->assign("btn_205",$btn_205);
        $this->smarty->assign("btn_207",$btn_207);
        $this->smarty->assign("btn_208",$btn_208);
        $this->smarty->assign("btn_301",$btn_301);
        $this->smarty->assign("btn_302",$btn_302);
        $this->smarty->assign("btn_303",$btn_303);
        $this->smarty->assign("btn_401",$btn_401);
        $this->smarty->assign("btn_402",$btn_402);
        $this->smarty->assign("btn_501",$btn_501);
        $this->smarty->assign("btn_601",$btn_601);
        $this->smarty->assign("btn_602",$btn_602);
        $this->smarty->assign("btn_603",$btn_603);
        $this->smarty->assign("btn_604",$btn_604);
        $this->smarty->assign("btn_605",$btn_605);
        $this->smarty->assign("btn_701",$btn_701);
        $this->smarty->assign("btn_702",$btn_702);
        $this->smarty->assign("btn_801",$btn_801);
		$this->forward = "ajaxBtnclick";
    }
    
    /**
     *
     * @do 展示周浏览数据
     *
     * @btnclick public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function weeklyAction(){
        $this->smarty->assign("active","btnclick/weekly");
        $this->smarty->assign("active_menu","btnclick");
        $this->smarty->assign("sdate",date('Y-m-d',strtotime("-14 days")));
        $this->smarty->assign("edate",date('Y-m-d'));
		$this->forward = "weekly";
        
	}
    
    /**
    *
    * @do ajax 获取周浏览数据
    *
    * @btnclick public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return json
    *
    */
    public function ajaxWeeklyBtnclickJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $tempdate=date('Y-m-d',strtotime("$start Monday")); 
        $start=date('Y-m-d',strtotime("$tempdate -7 days"));
        $end=date('Y-m-d',strtotime("$end Sunday"));
        
        $PSys_BtnclickModel = new PSys_PageviewModel();               
        $where["startday_>="] = $start;
        $where["endday_<="] = $end;
        $order = "startday ASC";
        $result = $PSys_BtnclickModel->GetList($where, $order, 0, 0, "*","rhc_click_weekly");
        $xAxis = $series = $btn_201 = $btn_202 = $btn_203 = $btn_204 = $btn_205 = $btn_207 = $btn_208 = $btn_301 = $btn_302 = $btn_303 = $btn_401 = $btn_402 = $btn_501 = $btn_601 = $btn_602 = $btn_603  = $btn_604 = $btn_605 = $btn_701 = $btn_702 = $btn_801 = "";
        foreach($result["allrow"] as $k=>$v){
            $xtemp = $v["startday"];
            $mictime = strtotime($v["startday"]);
            $endtemp = date('/m/d',strtotime("$xtemp Sunday"));
            $xAxis .= '"'.date('Y/m/d',$mictime).' - '.$endtemp.'",';
            $btn_201 .= $v["btn_201"].',';
            $btn_202 .= $v["btn_202"].',';
            $btn_203 .= $v["btn_203"].',';
            $btn_204 .= $v["btn_204"].',';
            $btn_205 .= $v["btn_205"].',';
            $btn_207 .= $v["btn_207"].',';
            $btn_208 .= $v["btn_208"].',';
            $btn_301 .= $v["btn_301"].',';
            $btn_302 .= $v["btn_302"].',';
            $btn_303 .= $v["btn_303"].',';
            $btn_401 .= $v["btn_401"].',';
            $btn_402 .= $v["btn_402"].',';
            $btn_501 .= $v["btn_501"].',';
            $btn_601 .= $v["btn_601"].',';
            $btn_602 .= $v["btn_602"].',';
            $btn_603 .= $v["btn_603"].',';
            $btn_604 .= $v["btn_604"].',';
            $btn_605 .= $v["btn_605"].',';
            $btn_701 .= $v["btn_701"].',';
            $btn_702 .= $v["btn_702"].',';
            $btn_801 .= $v["btn_801"].',';
        }
        $xAxis = trim($xAxis,",");
        $btn_201 = "{name:'[web]下载',data:[".trim($btn_201,",")."]},";
        $btn_202 = "{name:'[web]安装',data:[".trim($btn_202,",")."]},";
        $btn_203 = "{name:'[web]打开',data:[".trim($btn_203,",")."]},";
        $btn_204 = "{name:'确定下载',data:[".trim($btn_204,",")."]},";
        $btn_205 = "{name:'[安卓]更新',data:[".trim($btn_205,",")."]},";
        $btn_207 = "{name:'[安卓]安装',data:[".trim($btn_207,",")."]},";
        $btn_208 = "{name:'[安卓]打开',data:[".trim($btn_208,",")."]},";
        $btn_301 = "{name:'游戏礼包',data:[".trim($btn_301,",")."]},";
        $btn_302 = "{name:'领取礼包[详情]',data:[".trim($btn_302,",")."]},";
        $btn_303 = "{name:'领取礼包',data:[".trim($btn_303,",")."]},";
        $btn_401 = "{name:'签到有礼',data:[".trim($btn_401,",")."]},";
        $btn_402 = "{name:'今日签到',data:[".trim($btn_402,",")."]},";
        $btn_501 = "{name:'排行榜',data:[".trim($btn_501,",")."]},";
        $btn_601 = "{name:'动作',data:[".trim($btn_601,",")."]},";
        $btn_602 = "{name:'消除',data:[".trim($btn_602,",")."]},";
        $btn_603 = "{name:'跑酷',data:[".trim($btn_603,",")."]},";
        $btn_604 = "{name:'网游rpg',data:[".trim($btn_604,",")."]},";
        $btn_605 = "{name:'其他',data:[".trim($btn_605,",")."]},";
        $btn_701 = "{name:'登录',data:[".trim($btn_701,",")."]},";
        $btn_702 = "{name:'退出',data:[".trim($btn_702,",")."]},";
        $btn_801 = "{name:'Banner广告',data:[".trim($btn_801,",")."]}";
        $series = $btn_201.$btn_202.$btn_203.$btn_204.$btn_205.$btn_207.$btn_208.$btn_301.$btn_302.$btn_303.$btn_401.$btn_402.$btn_501.$btn_601.$btn_602.$btn_603.$btn_604.$btn_605.$btn_701.$btn_702.$btn_801;
        $data['xAxis'] = $xAxis;
        $data['series'] = $series;
        die(json_encode($data));
    }
    
    /**
    *
    * @do ajax 获取周浏览数据
    *
    * @btnclick public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return html
    *
    */
    public function ajaxWeeklyBtnclickHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $tempdate=date('Y-m-d',strtotime("$start Monday")); 
        $start=date('Y-m-d',strtotime("$tempdate -7 days"));
        $end=date('Y-m-d',strtotime("$end Sunday"));
        
        $PSys_BtnclickModel = new PSys_PageviewModel();
        $where["startday_>="] = $start;
        $where["endday_<="] = $end;
        $order = "startday ASC";
        $result = $PSys_BtnclickModel->GetList($where, $order, 0, 0, "*","rhc_click_weekly");
        $btn_201 = $btn_202 = $btn_203 = $btn_204 = $btn_205 = $btn_207 = $btn_208 = $btn_301 = $btn_302 = $btn_303 = $btn_401 = $btn_402 = $btn_501 = $btn_601 = $btn_602 = $btn_603  = $btn_604 = $btn_605 = $btn_701 = $btn_702 = $btn_801 = 0;
        foreach($result["allrow"] as $k=>$v){
            $btn_201 += intval($v["btn_201"]);
            $btn_202 += intval($v["btn_202"]);
            $btn_203 += intval($v["btn_203"]);
            $btn_204 += intval($v["btn_204"]);
            $btn_205 += intval($v["btn_205"]);
            $btn_207 += intval($v["btn_207"]);
            $btn_208 += intval($v["btn_208"]);
            $btn_301 += intval($v["btn_301"]);
            $btn_302 += intval($v["btn_302"]);
            $btn_303 += intval($v["btn_303"]);
            $btn_401 += intval($v["btn_401"]);
            $btn_402 += intval($v["btn_402"]);
            $btn_501 += intval($v["btn_501"]);
            $btn_601 += intval($v["btn_601"]);
            $btn_602 += intval($v["btn_602"]);
            $btn_603 += intval($v["btn_603"]);
            $btn_604 += intval($v["btn_604"]);
            $btn_605 += intval($v["btn_605"]);
            $btn_701 += intval($v["btn_701"]);
            $btn_702 += intval($v["btn_702"]);
            $btn_801 += intval($v["btn_801"]);
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("btn_201",$btn_201);
        $this->smarty->assign("btn_202",$btn_202);
        $this->smarty->assign("btn_203",$btn_203);
        $this->smarty->assign("btn_204",$btn_204);
        $this->smarty->assign("btn_205",$btn_205);
        $this->smarty->assign("btn_207",$btn_207);
        $this->smarty->assign("btn_208",$btn_208);
        $this->smarty->assign("btn_301",$btn_301);
        $this->smarty->assign("btn_302",$btn_302);
        $this->smarty->assign("btn_303",$btn_303);
        $this->smarty->assign("btn_401",$btn_401);
        $this->smarty->assign("btn_402",$btn_402);
        $this->smarty->assign("btn_501",$btn_501);
        $this->smarty->assign("btn_601",$btn_601);
        $this->smarty->assign("btn_602",$btn_602);
        $this->smarty->assign("btn_603",$btn_603);
        $this->smarty->assign("btn_604",$btn_604);
        $this->smarty->assign("btn_605",$btn_605);
        $this->smarty->assign("btn_701",$btn_701);
        $this->smarty->assign("btn_702",$btn_702);
        $this->smarty->assign("btn_801",$btn_801);
		$this->forward = "ajaxWeeklyBtnclick";
    }
    
    /**
     *
     * @do 展示月浏览数据
     *
     * @btnclick public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function monthlyAction(){
        $this->smarty->assign("active","btnclick/monthly");
        $this->smarty->assign("active_menu","btnclick");
        $this->smarty->assign("sdate",date("Y-m",mktime(0,0,0,date("m")-2,1,date("Y"))));
        $this->smarty->assign("edate",date("Y-m",mktime(0,0,0,date("m"),1,date("Y"))));
		$this->forward = "monthly";
        
	}
    
    /**
    *
    * @do ajax 获取月浏览数据
    *
    * @btnclick public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return json
    *
    */
    public function ajaxMonthlyBtnclickJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        
        $PSys_BtnclickModel = new PSys_PageviewModel();               
        $where["month_>="] = $start;
        $where["month_<="] = $end;
        $order = "month ASC";
        $result = $PSys_BtnclickModel->GetList($where, $order, 0, 0, "*","rhc_click_monthly");
        $xAxis = $series = $btn_201 = $btn_202 = $btn_203 = $btn_204 = $btn_205 = $btn_207 = $btn_208 = $btn_301 = $btn_302 = $btn_303 = $btn_401 = $btn_402 = $btn_501 = $btn_601 = $btn_602 = $btn_603  = $btn_604 = $btn_605 = $btn_701 = $btn_702 = $btn_801 = "";
        foreach($result["allrow"] as $k=>$v){
            $xAxis .= '"'.$v["month"].'",';
            $btn_201 .= $v["btn_201"].',';
            $btn_202 .= $v["btn_202"].',';
            $btn_203 .= $v["btn_203"].',';
            $btn_204 .= $v["btn_204"].',';
            $btn_205 .= $v["btn_205"].',';
            $btn_207 .= $v["btn_207"].',';
            $btn_208 .= $v["btn_208"].',';
            $btn_301 .= $v["btn_301"].',';
            $btn_302 .= $v["btn_302"].',';
            $btn_303 .= $v["btn_303"].',';
            $btn_401 .= $v["btn_401"].',';
            $btn_402 .= $v["btn_402"].',';
            $btn_501 .= $v["btn_501"].',';
            $btn_601 .= $v["btn_601"].',';
            $btn_602 .= $v["btn_602"].',';
            $btn_603 .= $v["btn_603"].',';
            $btn_604 .= $v["btn_604"].',';
            $btn_605 .= $v["btn_605"].',';
            $btn_701 .= $v["btn_701"].',';
            $btn_702 .= $v["btn_702"].',';
            $btn_801 .= $v["btn_801"].',';
        }
        $xAxis = trim($xAxis,",");
        $btn_201 = "{name:'[web]下载',data:[".trim($btn_201,",")."]},";
        $btn_202 = "{name:'[web]安装',data:[".trim($btn_202,",")."]},";
        $btn_203 = "{name:'[web]打开',data:[".trim($btn_203,",")."]},";
        $btn_204 = "{name:'确定下载',data:[".trim($btn_204,",")."]},";
        $btn_205 = "{name:'[安卓]更新',data:[".trim($btn_205,",")."]},";
        $btn_207 = "{name:'[安卓]安装',data:[".trim($btn_207,",")."]},";
        $btn_208 = "{name:'[安卓]打开',data:[".trim($btn_208,",")."]},";
        $btn_301 = "{name:'游戏礼包',data:[".trim($btn_301,",")."]},";
        $btn_302 = "{name:'领取礼包[详情]',data:[".trim($btn_302,",")."]},";
        $btn_303 = "{name:'领取礼包',data:[".trim($btn_303,",")."]},";
        $btn_401 = "{name:'签到有礼',data:[".trim($btn_401,",")."]},";
        $btn_402 = "{name:'今日签到',data:[".trim($btn_402,",")."]},";
        $btn_501 = "{name:'排行榜',data:[".trim($btn_501,",")."]},";
        $btn_601 = "{name:'动作',data:[".trim($btn_601,",")."]},";
        $btn_602 = "{name:'消除',data:[".trim($btn_602,",")."]},";
        $btn_603 = "{name:'跑酷',data:[".trim($btn_603,",")."]},";
        $btn_604 = "{name:'网游rpg',data:[".trim($btn_604,",")."]},";
        $btn_605 = "{name:'其他',data:[".trim($btn_605,",")."]},";
        $btn_701 = "{name:'登录',data:[".trim($btn_701,",")."]},";
        $btn_702 = "{name:'退出',data:[".trim($btn_702,",")."]},";
        $btn_801 = "{name:'Banner广告',data:[".trim($btn_801,",")."]}";
        $series = $btn_201.$btn_202.$btn_203.$btn_204.$btn_205.$btn_207.$btn_208.$btn_301.$btn_302.$btn_303.$btn_401.$btn_402.$btn_501.$btn_601.$btn_602.$btn_603.$btn_604.$btn_605.$btn_701.$btn_702.$btn_801;
        $data['xAxis'] = $xAxis;
        $data['series'] = $series;
        die(json_encode($data));
    }
    
    /**
    *
    * @do ajax 获取月浏览数据
    *
    * @btnclick public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return html
    *
    */
    public function ajaxMonthlyBtnclickHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        
        $PSys_BtnclickModel = new PSys_PageviewModel();
        $where["month_>="] = $start;
        $where["month_<="] = $end;
        $order = "month ASC";
        $result = $PSys_BtnclickModel->GetList($where, $order, 0, 0, "*","rhc_click_monthly");
        $btn_201 = $btn_202 = $btn_203 = $btn_204 = $btn_205 = $btn_207 = $btn_208 = $btn_301 = $btn_302 = $btn_303 = $btn_401 = $btn_402 = $btn_501 = $btn_601 = $btn_602 = $btn_603  = $btn_604 = $btn_605 = $btn_701 = $btn_702 = $btn_801 = 0;
        foreach($result["allrow"] as $k=>$v){
            $btn_201 += intval($v["btn_201"]);
            $btn_202 += intval($v["btn_202"]);
            $btn_203 += intval($v["btn_203"]);
            $btn_204 += intval($v["btn_204"]);
            $btn_205 += intval($v["btn_205"]);
            $btn_207 += intval($v["btn_207"]);
            $btn_208 += intval($v["btn_208"]);
            $btn_301 += intval($v["btn_301"]);
            $btn_302 += intval($v["btn_302"]);
            $btn_303 += intval($v["btn_303"]);
            $btn_401 += intval($v["btn_401"]);
            $btn_402 += intval($v["btn_402"]);
            $btn_501 += intval($v["btn_501"]);
            $btn_601 += intval($v["btn_601"]);
            $btn_602 += intval($v["btn_602"]);
            $btn_603 += intval($v["btn_603"]);
            $btn_604 += intval($v["btn_604"]);
            $btn_605 += intval($v["btn_605"]);
            $btn_701 += intval($v["btn_701"]);
            $btn_702 += intval($v["btn_702"]);
            $btn_801 += intval($v["btn_801"]);
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("btn_201",$btn_201);
        $this->smarty->assign("btn_202",$btn_202);
        $this->smarty->assign("btn_203",$btn_203);
        $this->smarty->assign("btn_204",$btn_204);
        $this->smarty->assign("btn_205",$btn_205);
        $this->smarty->assign("btn_207",$btn_207);
        $this->smarty->assign("btn_208",$btn_208);
        $this->smarty->assign("btn_301",$btn_301);
        $this->smarty->assign("btn_302",$btn_302);
        $this->smarty->assign("btn_303",$btn_303);
        $this->smarty->assign("btn_401",$btn_401);
        $this->smarty->assign("btn_402",$btn_402);
        $this->smarty->assign("btn_501",$btn_501);
        $this->smarty->assign("btn_601",$btn_601);
        $this->smarty->assign("btn_602",$btn_602);
        $this->smarty->assign("btn_603",$btn_603);
        $this->smarty->assign("btn_604",$btn_604);
        $this->smarty->assign("btn_605",$btn_605);
        $this->smarty->assign("btn_701",$btn_701);
        $this->smarty->assign("btn_702",$btn_702);
        $this->smarty->assign("btn_801",$btn_801);
		$this->forward = "ajaxMonthlyBtnclick";
    }
    
}