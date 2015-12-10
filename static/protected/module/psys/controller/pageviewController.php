<?php
/**
* Copyright(c) 2015
* 日    期:2015年07月07日
* 文 件 名:{pageview}Controller.php
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
class pageviewController extends PSys_AbstractController{
    
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
     * @pageview public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function indexAction(){
        $this->smarty->assign("active","pageview/index");
        $this->smarty->assign("active_menu","pageview");
        $this->smarty->assign("sdate",date('Y-m-d',strtotime("-6 days")));
        $this->smarty->assign("edate",date('Y-m-d'));
		$this->forward = "index";
        
	}
    
    /**
     *
     * @do 展示其他浏览数据
     *
     * @pageview public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function otherDailyAction(){
        $this->smarty->assign("active","pageview/otherDaily");
        $this->smarty->assign("active_menu","pageview");
        $this->smarty->assign("sdate",date('Y-m-d',strtotime("-6 days")));
        $this->smarty->assign("edate",date('Y-m-d'));
		$this->forward = "otherDaily";
        
	}
    
    /**
    *
    * @do ajax 获取日浏览数据
    *
    * @pageview public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return json
    *
    */
    public function ajaxOtherJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $PSys_PageviewModel = new PSys_PageviewModel();
        $PSys_PageviewRule = new PSys_PageviewRule();
        $PSys_GameRule = new PSys_GameRule();
        $PSys_MemberRule = new PSys_MemberRule();
        $where["day_>="] = $start;
        $where["day_<="] = $end;
        $order = "day ASC";
        $result = $PSys_PageviewModel->GetList($where, $order, 0, 0, "*");
        
        $xAxis = $series = $start_times = $wifi = $nowifi = $register = $download = $page_views = "";
        foreach($result["allrow"] as $k=>$v){
            $sql = 'SELECT count(client) AS "wifi" FROM rhc_game_platform WHERE type = 1 AND station_id > 0 AND cday = '.date('Ymd',strtotime($v['day']));
            $rs = $PSys_PageviewRule->tempQuery($sql);
            $gameSql = 'SELECT count(id) as "dl_total" FROM rhc_game_platform WHERE type = 206 AND cday BETWEEN '.date('Ymd',strtotime($v['day'])).' AND '.date('Ymd',strtotime($v['day']));
            $gameRs = $PSys_GameRule->gameQuery($gameSql);
            $xAxis .= '"'.$v["day"].'",';
            $start_times .= $v["start_times"].',';
            $wifi .= $rs[0]['wifi'].',';
            $nowifi .= ($v['start_times']-$rs[0]['wifi']).',';
            $page_views .= $v["page_views"].',';
            $regSql = 'SELECT count(id) as "reg" FROM rhi_account WHERE regfrom = 2 AND regtime BETWEEN '.strtotime($v['day']).' AND '.(strtotime($v['day'])+86400-1);
            $regRs = $PSys_MemberRule->regQuery($regSql);
            $register .= $regRs[0]['reg'].',';
            $download .= $gameRs[0]['dl_total'].',';
           
        }
        $xAxis = trim($xAxis,",");
        $start_times = "{name:'启动平台次数',data:[".trim($start_times,",")."]},";
        $wifi = "{name:'WiFi启动次数',data:[".trim($wifi,",")."]},";
        $nowifi = "{name:'非WiFi启动次数',data:[".trim($nowifi,",")."]},";
        $page_views = "{name:'页面浏览次数',data:[".trim($page_views,",")."]},";
        $register = "{name:'注册用户数',data:[".trim($register,",")."]},";
        $download = "{name:'游戏下载总数',data:[".trim($download,",")."]},";
        $series = $start_times.$wifi.$nowifi.$page_views.$register.$download;
        $data['xAxis'] = $xAxis;
        $data['series'] = $series;
        die(json_encode($data));
    }
    
        /**
    *
    * @do ajax 获取浏览数据
    *
    * @pageview public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return html
    *
    */
    public function ajaxOtherHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $PSys_PageviewModel = new PSys_PageviewModel();
        $PSys_PageviewRule = new PSys_PageviewRule();
        $PSys_GameRule = new PSys_GameRule();
        $PSys_MemberRule = new PSys_MemberRule();
        $where["day_>="] = $start;
        $where["day_<="] = $end;
        $order = "day ASC";
        $result = $PSys_PageviewModel->GetList($where, $order, 0, 0, "*");
        $total_start_times = $total_wifi = $total_nowifi = $total_download = $total_reg = $total_page_views = 0;
        foreach($result["allrow"] as $k=>$v){
            $total_start_times += intval($v["start_times"]);
            $sql = 'SELECT count(client) AS "wifi" FROM rhc_game_platform WHERE type = 1 AND station_id > 0 AND cday = '.date('Ymd',strtotime($v['day']));
            $rs = $PSys_PageviewRule->tempQuery($sql);
            $result["allrow"][$k]['wifi'] = $rs[0]['wifi'];
            $result["allrow"][$k]['nowifi'] = $v['start_times']-$rs[0]['wifi'];
            $total_wifi += intval($result["allrow"][$k]['wifi']);
            $total_nowifi += intval($result["allrow"][$k]['nowifi']);
            $gameSql = 'SELECT count(id) as "dl_total" FROM rhc_game_platform WHERE type = 206 AND cday BETWEEN '.date('Ymd',strtotime($v['day'])).' AND '.date('Ymd',strtotime($v['day']));
            $gameRs = $PSys_GameRule->gameQuery($gameSql);
            $result["allrow"][$k]['download'] = $gameRs[0]['dl_total'];
            $total_download += intval($result["allrow"][$k]['download']);
            $regSql = 'SELECT count(id) as "reg" FROM rhi_account WHERE regfrom = 2 AND regtime BETWEEN '.strtotime($v['day']).' AND '.(strtotime($v['day'])+86400-1);
            $regRs = $PSys_MemberRule->regQuery($regSql);
            $result["allrow"][$k]['reg'] = $regRs[0]['reg'];
            $total_reg += intval($result["allrow"][$k]['reg']);
            $total_page_views += intval($v["page_views"]);
        }
        $this->smarty->assign("total_start_times",$total_start_times);
        $this->smarty->assign("total_page_views",$total_page_views);
        $this->smarty->assign("total_wifi",$total_wifi);
        $this->smarty->assign("total_nowifi",$total_nowifi);
        $this->smarty->assign("total_download",$total_download);
        $this->smarty->assign("total_reg",$total_reg);
        $this->smarty->assign("data",$result["allrow"]);
		$this->forward = "ajaxOther";
    }
    
    /**
    *
    * @do ajax 获取日浏览数据
    *
    * @pageview public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return json
    *
    */
    public function ajaxPageviewJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $PSys_PageviewModel = new PSys_PageviewModel();
        $where["day_>="] = $start;
        $where["day_<="] = $end;
        $order = "day ASC";
        $result = $PSys_PageviewModel->GetList($where, $order, 0, 0, "*");
        
        $xAxis = $series = $start_times = $page_views = $per_view = $online_times = $per_online = "";
        foreach($result["allrow"] as $k=>$v){
            $xAxis .= '"'.$v["day"].'",';
            $start_times .= $v["start_times"].',';
            $page_views .= $v["page_views"].',';
            $per_view .= $v["per_view"].',';
            $online_times .= $v["online_times"].',';
            $per_online .= $v["per_online"].',';
        }
        $xAxis = trim($xAxis,",");
        $start_times = "{name:'启动平台次数',data:[".trim($start_times,",")."]},";
        $page_views = "{name:'浏览页面次数',data:[".trim($page_views,",")."]},";
        $per_view = "{name:'人均浏览量',data:[".trim($per_view,",")."]},";
        $online_times = "{name:'浏览时长',data:[".trim($online_times,",")."]},";
        $per_online = "{name:'人均浏览时长',data:[".trim($per_online,",")."]},";
        $series = $start_times.$page_views.$per_view.$online_times.$per_online;
        $data['xAxis'] = $xAxis;
        $data['series'] = $series;
        die(json_encode($data));
    }
    
    /**
    *
    * @do ajax 获取浏览数据
    *
    * @pageview public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return html
    *
    */
    public function ajaxPageviewHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $PSys_PageviewModel = new PSys_PageviewModel();
        $PSys_PageviewRule = new PSys_PageviewRule();
        $where["day_>="] = $start;
        $where["day_<="] = $end;
        $order = "day ASC";
        $result = $PSys_PageviewModel->GetList($where, $order, 0, 0, "*");
        $total_start_times = $total_page_views = $total_online_times = 0;
        foreach($result["allrow"] as $k=>$v){
            $total_start_times += intval($v["start_times"]);
            $total_page_views += intval($v["page_views"]);
            $total_online_times += $v["online_times"];
            $sql = 'SELECT count(client) AS "wifi" FROM rhc_game_platform WHERE type = 1 AND station_id > 0 AND cday = '.date('Ymd',strtotime($v['day']));
            $rs = $PSys_PageviewRule->tempQuery($sql);
            $result["allrow"][$k]['wifi'] = $rs[0]['wifi'];
            $result["allrow"][$k]['nowifi'] = $v['start_times']-$rs[0]['wifi'];
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("total_start_times",$total_start_times);
        $this->smarty->assign("total_page_views",$total_page_views);
        $this->smarty->assign("total_online_times",$total_online_times);
		$this->forward = "ajaxPageview";
    }
    
    /**
     *
     * @do 展示周浏览数据
     *
     * @pageview public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function weeklyAction(){
        $this->smarty->assign("active","pageview/weekly");
        $this->smarty->assign("active_menu","pageview");
        $this->smarty->assign("sdate",date('Y-m-d',strtotime("-14 days")));
        $this->smarty->assign("edate",date('Y-m-d'));
		$this->forward = "weekly";
        
	}
    
    /**
    *
    * @do ajax 获取周浏览数据
    *
    * @pageview public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return json
    *
    */
    public function ajaxWeeklyPageviewJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $tempdate=date('Y-m-d',strtotime("$start Monday")); 
        $start=date('Y-m-d',strtotime("$tempdate -7 days"));
        $end=date('Y-m-d',strtotime("$end Sunday"));
        
        $PSys_PageviewModel = new PSys_PageviewModel();               
        $where["startday_>="] = $start;
        $where["endday_<="] = $end;
        $order = "startday ASC";
        $result = $PSys_PageviewModel->GetList($where, $order, 0, 0, "*","rhc_view_weekly");
        $xAxis = $series = $start_times = $page_views = $per_view = $online_times = $per_online = "";
        foreach($result["allrow"] as $k=>$v){
            $xtemp = $v["startday"];
            $mictime = strtotime($v["startday"]);
            $endtemp = date('/m/d',strtotime("$xtemp Sunday"));
            $xAxis .= '"'.date('Y/m/d',$mictime).' - '.$endtemp.'",';
            $start_times .= $v["start_times"].',';
            $page_views .= $v["page_views"].',';
            $per_view .= $v["per_view"].',';
            $online_times .= $v["online_times"].',';
            $per_online .= $v["per_online"].',';
        }
        $xAxis = trim($xAxis,",");
        $start_times = "{name:'启动平台次数',data:[".trim($start_times,",")."]},";
        $page_views = "{name:'浏览页面次数',data:[".trim($page_views,",")."]},";
        $per_view = "{name:'人均浏览量',data:[".trim($per_view,",")."]},";
        $online_times = "{name:'浏览时长',data:[".trim($online_times,",")."]},";
        $per_online = "{name:'人均浏览时长',data:[".trim($per_online,",")."]},";
        $series = $start_times.$page_views.$per_view.$online_times.$per_online;
        $data['xAxis'] = $xAxis;
        $data['series'] = $series;
        die(json_encode($data));
    }
    
    /**
    *
    * @do ajax 获取周浏览数据
    *
    * @pageview public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return html
    *
    */
    public function ajaxWeeklyPageviewHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $tempdate=date('Y-m-d',strtotime("$start Monday")); 
        $start=date('Y-m-d',strtotime("$tempdate -7 days"));
        $end=date('Y-m-d',strtotime("$end Sunday"));
        
        $PSys_PageviewModel = new PSys_PageviewModel();
        $PSys_PageviewRule = new PSys_PageviewRule();
        $where["startday_>="] = $start;
        $where["endday_<="] = $end;
        $order = "startday ASC";
        $result = $PSys_PageviewModel->GetList($where, $order, 0, 0, "*","rhc_view_weekly");
        $total_start_times = $total_page_views = $total_online_times = 0;
        foreach($result["allrow"] as $k=>$v){
            $total_start_times += intval($v["start_times"]);
            $total_page_views += intval($v["page_views"]);
            $total_online_times += $v["online_times"];
            $sql = 'SELECT count(client) AS "wifi" FROM rhc_game_platform WHERE type = 1 AND station_id > 0 AND cday BETWEEN '.date('Ymd',strtotime($v['startday'])).' AND '. date('Ymd',strtotime($v['endday']));
            $rs = $PSys_PageviewRule->tempQuery($sql);
            $result["allrow"][$k]['wifi'] = $rs[0]['wifi'];
            $result["allrow"][$k]['nowifi'] = $v['start_times']-$rs[0]['wifi'];
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("total_start_times",$total_start_times);
        $this->smarty->assign("total_page_views",$total_page_views);
        $this->smarty->assign("total_online_times",$total_online_times);
		$this->forward = "ajaxWeeklyPageview";
    }
    
    /**
     *
     * @do 展示月浏览数据
     *
     * @pageview public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function monthlyAction(){
        $this->smarty->assign("active","pageview/monthly");
        $this->smarty->assign("active_menu","pageview");
        $this->smarty->assign("sdate",date("Y-m",mktime(0,0,0,date("m")-2,1,date("Y"))));
        $this->smarty->assign("edate",date("Y-m",mktime(0,0,0,date("m"),1,date("Y"))));
		$this->forward = "monthly";
        
	}
    
    /**
    *
    * @do ajax 获取月浏览数据
    *
    * @pageview public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return json
    *
    */
    public function ajaxMonthlyPageviewJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        
        $PSys_PageviewModel = new PSys_PageviewModel();               
        $where["month_>="] = $start;
        $where["month_<="] = $end;
        $order = "month ASC";
        $result = $PSys_PageviewModel->GetList($where, $order, 0, 0, "*","rhc_view_monthly");
        $xAxis = $series = $start_times = $page_views = $per_view = $online_times = $per_online = "";
        foreach($result["allrow"] as $k=>$v){
            $xAxis .= '"'.$v["month"].'",';
            $start_times .= $v["start_times"].',';
            $page_views .= $v["page_views"].',';
            $per_view .= $v["per_view"].',';
            $online_times .= $v["online_times"].',';
            $per_online .= $v["per_online"].',';
        }
        $xAxis = trim($xAxis,",");
        $start_times = "{name:'启动平台次数',data:[".trim($start_times,",")."]},";
        $page_views = "{name:'浏览页面次数',data:[".trim($page_views,",")."]},";
        $per_view = "{name:'人均浏览量',data:[".trim($per_view,",")."]},";
        $online_times = "{name:'浏览时长',data:[".trim($online_times,",")."]},";
        $per_online = "{name:'人均浏览时长',data:[".trim($per_online,",")."]},";
        $series = $start_times.$page_views.$per_view.$online_times.$per_online;
        $data['xAxis'] = $xAxis;
        $data['series'] = $series;
        die(json_encode($data));
    }
    
    /**
    *
    * @do ajax 获取月浏览数据
    *
    * @pageview public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return html
    *
    */
    public function ajaxMonthlyPageviewHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        
        $PSys_PageviewModel = new PSys_PageviewModel();
        $PSys_PageviewRule = new PSys_PageviewRule();
        $where["month_>="] = $start;
        $where["month_<="] = $end;
        $order = "month ASC";
        $result = $PSys_PageviewModel->GetList($where, $order, 0, 0, "*","rhc_view_monthly");
        $total_start_times = $total_page_views = $total_online_times = 0;
        foreach($result["allrow"] as $k=>$v){
            $total_start_times += intval($v["start_times"]);
            $total_page_views += intval($v["page_views"]);
            $total_online_times += $v["online_times"];
            $sql = 'SELECT count(client) AS "wifi" FROM rhc_game_platform WHERE type = 1 AND station_id > 0 AND cday like "'.date('Ym',strtotime($v['month'])).'%"';
            $rs = $PSys_PageviewRule->tempQuery($sql);
            $result["allrow"][$k]['wifi'] = $rs[0]['wifi'];
            $result["allrow"][$k]['nowifi'] = $v['start_times']-$rs[0]['wifi'];
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("total_start_times",$total_start_times);
        $this->smarty->assign("total_page_views",$total_page_views);
        $this->smarty->assign("total_online_times",$total_online_times);
		$this->forward = "ajaxMonthlyPageview";
    }
    
}