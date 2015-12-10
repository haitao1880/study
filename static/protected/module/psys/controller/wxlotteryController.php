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
class wxlotteryController extends PSys_AbstractController{
    
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
        $this->smarty->assign("active","wxlottery/index");
        $this->smarty->assign("active_menu","wxlottery");
        $this->smarty->assign("sdate",date('Y-m-d',strtotime("-6 days")));
        $this->smarty->assign("edate",date('Y-m-d'));
		$this->forward = "index";
        
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
    public function pointAction(){
        $this->smarty->assign("active","wxlottery/point");
        $this->smarty->assign("active_menu","wxlottery");
        $this->smarty->assign("sdate",date('Y-m-d',strtotime("-6 days")));
        $this->smarty->assign("edate",date('Y-m-d'));
        $this->forward = "point";
        
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
    public function ajaxLotteryJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $PSys_signModel = new PSys_PageviewModel();
        $where["day_>="] = $start;
        $where["day_<="] = $end;
        $order = "day ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_wxlottery_daily");
        
        $xAxis = $series = $dl_click = $dj_click = $dl_andclick = $dl_iosclick = $sure_click = $cancel_click = $share_open_user = $view_times = $start_num = $do_times = $first_join = $second_join = $third_join = $fourth_join = $fifth_join = $sharetimes = $shareok = "";
        foreach($result["allrow"] as $k=>$v){
            $xAxis .= '"'.$v["day"].'",';
            $dl_click .= $v["dl_click"].',';
            $dj_click .= $v["dj_click"].',';
            $dl_andclick .= $v["dl_andclick"].',';
            $dl_iosclick .= $v["dl_iosclick"].',';
            $sure_click .= $v["sure_click"].',';
            $cancel_click .= $v["cancel_click"].',';
            $share_open_user .= $v["share_open_user"].',';
            $view_times .= $v["view_times"].',';
            $start_num .= $v["start_num"].',';
            $do_times .= $v["do_times"].',';
            $first_join .= $v["first_join"].',';
            $second_join .= $v["second_join"].',';
            $third_join .= $v["third_join"].',';
            $fourth_join .= $v["fourth_join"].',';
            $fifth_join .= $v["fifth_join"].',';
            $sharetimes .= $v["sharetimes"].',';
            $shareok .= $v["shareok"].',';  
        }
        $xAxis = trim($xAxis,",");
        $dl_click = "{name:'点击下载',data:[".trim($dl_click,",")."]},";
        $dj_click = "{name:'点击兑奖',data:[".trim($dj_click,",")."]},";
        $dl_andclick = "{name:'点击安卓下载',data:[".trim($dl_andclick,",")."]},";
        $dl_iosclick = "{name:'点击ISO下载',data:[".trim($dl_iosclick,",")."]},";
        $sure_click = "{name:'点击确定',data:[".trim($sure_click,",")."]},";
        $cancel_click = "{name:'点击取消',data:[".trim($cancel_click,",")."]},";
        $share_open_user = "{name:'分享后开启APP',data:[".trim($share_open_user,",")."]},";
        $view_times = "{name:'开启抽奖页次数',data:[".trim($view_times,",")."]},";
        $start_num = "{name:'开启抽奖页人数',data:[".trim($start_num,",")."]},";
        $do_times = "{name:'抽奖总数',data:[".trim($do_times,",")."]},";
        $first_join = "{name:'首次抽奖',data:[".trim($first_join,",")."]},";
        $second_join = "{name:'二次抽奖',data:[".trim($second_join,",")."]},";
        $third_join = "{name:'三次抽奖',data:[".trim($third_join,",")."]},";
        $fourth_join = "{name:'四次抽奖',data:[".trim($fourth_join,",")."]},";
        $fifth_join = "{name:'五次抽奖',data:[".trim($fifth_join,",")."]},";
        $sharetimes = "{name:'分享活动次数',data:[".trim($sharetimes,",")."]},";
        $shareok = "{name:'分享成功次数',data:[".trim($shareok,",")."]},";
        
        $series = $dl_click.$dj_click.$dl_andclick.$dl_iosclick.$sure_click.$cancel_click.$share_open_user.$view_times.$start_num.$do_times.$first_join.$second_join.$third_join.$fourth_join.$fifth_join.$sharetimes.$shareok;
        $data['xAxis'] = $xAxis;
        $data['series'] = $series;
        die(json_encode($data));
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
    public function ajaxPointJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $PSys_signModel = new PSys_PageviewModel();
        $where["day_>="] = $start;
        $where["day_<="] = $end;
        $order = "day ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_wxlottery_daily");
        
        $xAxis = $series = $start_num = $new_reg = $new_num = $cost_points = $get_points = $first_get_points = $third_get_points = $second_get_points = $fourth_get_points = $fifth_get_points = "";
        foreach($result["allrow"] as $k=>$v){
            $xAxis .= '"'.$v["day"].'",';
            $start_num .= $v["start_num"].',';
            $new_reg .= $v["new_reg"].',';
            $new_num .= $v["new_num"].',';
            $cost_points .= $v["cost_points"].',';
            $get_points .= $v["get_points"].',';
            $first_get_points .= $v["first_get_points"].',';
            $second_get_points .= $v["second_get_points"].',';
            $third_get_points .= $v["third_get_points"].',';
            $fourth_get_points .= $v["fourth_get_points"].',';
            $fifth_get_points .= $v["fifth_get_points"].',';
        }
        $xAxis = trim($xAxis,",");
        $start_num = "{name:'开启抽奖页人数',data:[".trim($start_num,",")."]},";
        $new_reg = "{name:'注册用户数',data:[".trim($new_reg,",")."]},";
        $new_num = "{name:'新开启人数',data:[".trim($new_num,",")."]},";
        $cost_points = "{name:'消耗积分',data:[".trim($cost_points,",")."]},";
        $get_points = "{name:'获取积分',data:[".trim($get_points,",")."]},";
        $first_get_points = "{name:'第一次获取积分',data:[".trim($first_get_points,",")."]},";
        $second_get_points = "{name:'第二次获取积分',data:[".trim($second_get_points,",")."]},";
        $third_get_points = "{name:'第三次获取积分',data:[".trim($third_get_points,",")."]},";
        $fourth_get_points = "{name:'第四次获取积分',data:[".trim($fourth_get_points,",")."]},";
        $fifth_get_points = "{name:'第五次获取积分',data:[".trim($fifth_get_points,",")."]},";
        
        $series = $start_num.$new_reg.$new_num.$cost_points.$get_points.$first_get_points.$second_get_points.$third_get_points.$fourth_get_points.$fifth_get_points;
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
    public function ajaxLotteryHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $PSys_signModel = new PSys_PageviewModel();
        $where["day_>="] = $start;
        $where["day_<="] = $end;
        $order = "day ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_wxlottery_daily");
        $dl_click = $dj_click = $dl_andclick = $dl_iosclick = $sure_click = $cancel_click = $share_open_user = $view_times = $start_num = $do_times = $first_join = $second_join = $third_join = $fourth_join = $fifth_join = $sharetimes = $shareok = "";
        
        foreach($result["allrow"] as $k=>$v){
            $dl_click += intval($v["dl_click"]);
            $dj_click += intval($v["dj_click"]);
            $dl_andclick += intval($v["dl_andclick"]);
            $dl_iosclick += intval($v["dl_iosclick"]);
            $sure_click += intval($v["sure_click"]);
            $cancel_click += intval($v["cancel_click"]);
            $share_open_user += intval($v["share_open_user"]);
            $view_times += intval($v["view_times"]);
            $start_num += intval($v["start_num"]);
            $do_times += intval($v["do_times"]);
            $first_join += intval($v["first_join"]);
            $second_join += intval($v["second_join"]);
            $third_join += intval($v["third_join"]);
            $fourth_join += intval($v["fourth_join"]);
            $fifth_join += intval($v["fifth_join"]);
            $sharetimes += intval($v["sharetimes"]);
            $shareok += intval($v["shareok"]);
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("dl_click",$dl_click);
        $this->smarty->assign("dj_click",$dj_click);
        $this->smarty->assign("dl_andclick",$dl_andclick);
        $this->smarty->assign("dl_iosclick",$dl_iosclick);
        $this->smarty->assign("sure_click",$sure_click);
        $this->smarty->assign("cancel_click",$cancel_click);
        $this->smarty->assign("share_open_user",$share_open_user);
        $this->smarty->assign("view_times",$view_times);
        $this->smarty->assign("start_num",$start_num);
        $this->smarty->assign("do_times",$do_times);
        $this->smarty->assign("first_join",$first_join);
        $this->smarty->assign("second_join",$second_join);
        $this->smarty->assign("third_join",$third_join);
        $this->smarty->assign("fourth_join",$fourth_join);
        $this->smarty->assign("fifth_join",$fifth_join);
        $this->smarty->assign("sharetimes",$sharetimes);
        $this->smarty->assign("shareok",$shareok);
		$this->forward = "ajaxLottery";
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
    public function ajaxPointHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $PSys_signModel = new PSys_PageviewModel();
        $where["day_>="] = $start;
        $where["day_<="] = $end;
        $order = "day ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_wxlottery_daily");
        $start_num = $new_reg = $new_num = $cost_points = $get_points = $first_get_points = $third_get_points = $second_get_points = $fourth_get_points = $fifth_get_points = "";
        
        foreach($result["allrow"] as $k=>$v){
            $start_num += intval($v["start_num"]);
            $new_reg += intval($v["new_reg"]);
            $new_num += intval($v["new_num"]);
            $cost_points += intval($v["cost_points"]);
            $get_points += intval($v["get_points"]);
            $first_get_points += intval($v["first_get_points"]);
            $third_get_points += intval($v["third_get_points"]);
            $second_get_points += intval($v["second_get_points"]);
            $fourth_get_points += intval($v["fourth_get_points"]);
            $fifth_get_points += intval($v["fifth_get_points"]);
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("start_num",$start_num);
        $this->smarty->assign("new_reg",$new_reg);
        $this->smarty->assign("new_num",$new_num);
        $this->smarty->assign("cost_points",$cost_points);
        $this->smarty->assign("get_points",$get_points);
        $this->smarty->assign("first_get_points",$first_get_points);
        $this->smarty->assign("third_get_points",$third_get_points);
        $this->smarty->assign("second_get_points",$second_get_points);
        $this->smarty->assign("fourth_get_points",$fourth_get_points);
        $this->smarty->assign("fifth_get_points",$fifth_get_points);
        $this->forward = "ajaxPoint";
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
        $this->smarty->assign("active","wxlottery/weekly");
        $this->smarty->assign("active_menu","wxlottery");
        $this->smarty->assign("sdate",date('Y-m-d',strtotime("-14 days")));
        $this->smarty->assign("edate",date('Y-m-d'));
		$this->forward = "weekly";
        
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
    public function pweeklyAction(){
        $this->smarty->assign("active","wxlottery/pweekly");
        $this->smarty->assign("active_menu","wxlottery");
        $this->smarty->assign("sdate",date('Y-m-d',strtotime("-14 days")));
        $this->smarty->assign("edate",date('Y-m-d'));
        $this->forward = "pweekly";
        
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
    public function ajaxWeeklylotteryJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $tempdate=date('Y-m-d',strtotime("$start Monday")); 
        $start=date('Y-m-d',strtotime("$tempdate -7 days"));
        $end=date('Y-m-d',strtotime("$end Sunday"));
        
        $PSys_signModel = new PSys_PageviewModel();               
        $where["startday_>="] = $start;
        $where["endday_<="] = $end;
        $order = "startday ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_wxlottery_weekly");
        $xAxis = $series = $dl_click = $dj_click = $dl_andclick = $dl_iosclick = $sure_click = $cancel_click = $share_open_user = $view_times = $start_num = $do_times = $first_join = $second_join = $third_join = $fourth_join = $fifth_join = $sharetimes = $shareok = "";

        foreach($result["allrow"] as $k=>$v){
            $xtemp = $v["startday"];
            $mictime = strtotime($v["startday"]);
            $endtemp = date('/m/d',strtotime("$xtemp Sunday"));
            $xAxis .= '"'.date('Y/m/d',$mictime).' - '.$endtemp.'",';
            $dl_click .= $v["dl_click"].',';
            $dj_click .= $v["dj_click"].',';
            $dl_andclick .= $v["dl_andclick"].',';
            $dl_iosclick .= $v["dl_iosclick"].',';
            $sure_click .= $v["sure_click"].',';
            $cancel_click .= $v["cancel_click"].',';
            $share_open_user .= $v["share_open_user"].',';
            $view_times .= $v["view_times"].',';
            $start_num .= $v["start_num"].',';
            $do_times .= $v["do_times"].',';
            $first_join .= $v["first_join"].',';
            $second_join .= $v["second_join"].',';
            $third_join .= $v["third_join"].',';
            $fourth_join .= $v["fourth_join"].',';
            $fifth_join .= $v["fifth_join"].',';
            $sharetimes .= $v["sharetimes"].',';
            $shareok .= $v["shareok"].',';  
        }
        $xAxis = trim($xAxis,",");
        $dl_click = "{name:'点击下载',data:[".trim($dl_click,",")."]},";
        $dj_click = "{name:'点击兑奖',data:[".trim($dj_click,",")."]},";
        $dl_andclick = "{name:'点击安卓下载',data:[".trim($dl_andclick,",")."]},";
        $dl_iosclick = "{name:'点击ISO下载',data:[".trim($dl_iosclick,",")."]},";
        $sure_click = "{name:'点击确定',data:[".trim($sure_click,",")."]},";
        $cancel_click = "{name:'点击取消',data:[".trim($cancel_click,",")."]},";
        $share_open_user = "{name:'分享后开启APP',data:[".trim($share_open_user,",")."]},";
        $view_times = "{name:'开启抽奖页次数',data:[".trim($view_times,",")."]},";
        $start_num = "{name:'开启抽奖页人数',data:[".trim($start_num,",")."]},";
        $do_times = "{name:'抽奖总数',data:[".trim($do_times,",")."]},";
        $first_join = "{name:'首次抽奖',data:[".trim($first_join,",")."]},";
        $second_join = "{name:'二次抽奖',data:[".trim($second_join,",")."]},";
        $third_join = "{name:'三次抽奖',data:[".trim($third_join,",")."]},";
        $fourth_join = "{name:'四次抽奖',data:[".trim($fourth_join,",")."]},";
        $fifth_join = "{name:'五次抽奖',data:[".trim($fifth_join,",")."]},";
        $sharetimes = "{name:'分享活动次数',data:[".trim($sharetimes,",")."]},";
        $shareok = "{name:'分享成功次数',data:[".trim($shareok,",")."]},";
        
        $series = $dl_click.$dj_click.$dl_andclick.$dl_iosclick.$sure_click.$cancel_click.$share_open_user.$view_times.$start_num.$do_times.$first_join.$second_join.$third_join.$fourth_join.$fifth_join.$sharetimes.$shareok;
        
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
    * @return json
    *
    */
    public function ajaxWeeklypointJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $tempdate=date('Y-m-d',strtotime("$start Monday")); 
        $start=date('Y-m-d',strtotime("$tempdate -7 days"));
        $end=date('Y-m-d',strtotime("$end Sunday"));
        
        $PSys_signModel = new PSys_PageviewModel();               
        $where["startday_>="] = $start;
        $where["endday_<="] = $end;
        $order = "startday ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_wxlottery_weekly");
        $xAxis = $series = $start_num = $new_reg = $new_num = $cost_points = $get_points = $first_get_points = $third_get_points = $second_get_points = $fourth_get_points = $fifth_get_points = "";
        foreach($result["allrow"] as $k=>$v){
            $xtemp = $v["startday"];
            $mictime = strtotime($v["startday"]);
            $endtemp = date('/m/d',strtotime("$xtemp Sunday"));
            $xAxis .= '"'.date('Y/m/d',$mictime).' - '.$endtemp.'",';
            $start_num .= $v["start_num"].',';
            $new_reg .= $v["new_reg"].',';
            $new_num .= $v["new_num"].',';
            $cost_points .= $v["cost_points"].',';
            $get_points .= $v["get_points"].',';
            $first_get_points .= $v["first_get_points"].',';
            $second_get_points .= $v["second_get_points"].',';
            $third_get_points .= $v["third_get_points"].',';
            $fourth_get_points .= $v["fourth_get_points"].',';
            $fifth_get_points .= $v["fifth_get_points"].',';
        }
        $xAxis = trim($xAxis,",");
        $start_num = "{name:'开启抽奖页人数',data:[".trim($start_num,",")."]},";
        $new_reg = "{name:'注册用户数',data:[".trim($new_reg,",")."]},";
        $new_num = "{name:'新开启人数',data:[".trim($new_num,",")."]},";
        $cost_points = "{name:'消耗积分',data:[".trim($cost_points,",")."]},";
        $get_points = "{name:'获取积分',data:[".trim($get_points,",")."]},";
        $first_get_points = "{name:'第一次获取积分',data:[".trim($first_get_points,",")."]},";
        $second_get_points = "{name:'第二次获取积分',data:[".trim($second_get_points,",")."]},";
        $third_get_points = "{name:'第三次获取积分',data:[".trim($third_get_points,",")."]},";
        $fourth_get_points = "{name:'第四次获取积分',data:[".trim($fourth_get_points,",")."]},";
        $fifth_get_points = "{name:'第五次获取积分',data:[".trim($fifth_get_points,",")."]},";
        
        $series = $start_num.$new_reg.$new_num.$cost_points.$get_points.$first_get_points.$second_get_points.$third_get_points.$fourth_get_points.$fifth_get_points;
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
    public function ajaxWeeklylotteryHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $tempdate=date('Y-m-d',strtotime("$start Monday")); 
        $start=date('Y-m-d',strtotime("$tempdate -7 days"));
        $end=date('Y-m-d',strtotime("$end Sunday"));
        
        $PSys_signModel = new PSys_PageviewModel();
        $where["startday_>="] = $start;
        $where["endday_<="] = $end;
        $order = "startday ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_wxlottery_weekly");
        $dl_click = $dj_click = $dl_andclick = $dl_iosclick = $sure_click = $cancel_click = $share_open_user = $view_times = $start_num = $do_times = $first_join = $second_join = $third_join = $fourth_join = $fifth_join = $sharetimes = $shareok = "";
        
        foreach($result["allrow"] as $k=>$v){
            $dl_click += intval($v["dl_click"]);
            $dj_click += intval($v["dj_click"]);
            $dl_andclick += intval($v["dl_andclick"]);
            $dl_iosclick += intval($v["dl_iosclick"]);
            $sure_click += intval($v["sure_click"]);
            $cancel_click += intval($v["cancel_click"]);
            $share_open_user += intval($v["share_open_user"]);
            $view_times += intval($v["view_times"]);
            $start_num += intval($v["start_num"]);
            $do_times += intval($v["do_times"]);
            $first_join += intval($v["first_join"]);
            $second_join += intval($v["second_join"]);
            $third_join += intval($v["third_join"]);
            $fourth_join += intval($v["fourth_join"]);
            $fifth_join += intval($v["fifth_join"]);
            $sharetimes += intval($v["sharetimes"]);
            $shareok += intval($v["shareok"]);
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("dl_click",$dl_click);
        $this->smarty->assign("dj_click",$dj_click);
        $this->smarty->assign("dl_andclick",$dl_andclick);
        $this->smarty->assign("dl_iosclick",$dl_iosclick);
        $this->smarty->assign("sure_click",$sure_click);
        $this->smarty->assign("cancel_click",$cancel_click);
        $this->smarty->assign("share_open_user",$share_open_user);
        $this->smarty->assign("view_times",$view_times);
        $this->smarty->assign("start_num",$start_num);
        $this->smarty->assign("do_times",$do_times);
        $this->smarty->assign("first_join",$first_join);
        $this->smarty->assign("second_join",$second_join);
        $this->smarty->assign("third_join",$third_join);
        $this->smarty->assign("fourth_join",$fourth_join);
        $this->smarty->assign("fifth_join",$fifth_join);
        $this->smarty->assign("sharetimes",$sharetimes);
        $this->smarty->assign("shareok",$shareok);
		$this->forward = "ajaxWeeklyLottery";
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
    public function ajaxWeeklypointHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $tempdate=date('Y-m-d',strtotime("$start Monday")); 
        $start=date('Y-m-d',strtotime("$tempdate -7 days"));
        $end=date('Y-m-d',strtotime("$end Sunday"));
        
        $PSys_signModel = new PSys_PageviewModel();
        $where["startday_>="] = $start;
        $where["endday_<="] = $end;
        $order = "startday ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_wxlottery_weekly");
        $start_num = $new_reg = $new_num = $cost_points = $get_points = $first_get_points = $third_get_points = $second_get_points = $fourth_get_points = $fifth_get_points = "";
        
        foreach($result["allrow"] as $k=>$v){
            $start_num += intval($v["start_num"]);
            $new_reg += intval($v["new_reg"]);
            $new_num += intval($v["new_num"]);
            $cost_points += intval($v["cost_points"]);
            $get_points += intval($v["get_points"]);
            $first_get_points += intval($v["first_get_points"]);
            $third_get_points += intval($v["third_get_points"]);
            $second_get_points += intval($v["second_get_points"]);
            $fourth_get_points += intval($v["fourth_get_points"]);
            $fifth_get_points += intval($v["fifth_get_points"]);
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("start_num",$start_num);
        $this->smarty->assign("new_reg",$new_reg);
        $this->smarty->assign("new_num",$new_num);
        $this->smarty->assign("cost_points",$cost_points);
        $this->smarty->assign("get_points",$get_points);
        $this->smarty->assign("first_get_points",$first_get_points);
        $this->smarty->assign("third_get_points",$third_get_points);
        $this->smarty->assign("second_get_points",$second_get_points);
        $this->smarty->assign("fourth_get_points",$fourth_get_points);
        $this->smarty->assign("fifth_get_points",$fifth_get_points);
        $this->forward = "ajaxWeeklyPoint";
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
        $this->smarty->assign("active","wxlottery/monthly");
        $this->smarty->assign("active_menu","wxlottery");
        $this->smarty->assign("sdate",date("Y-m",mktime(0,0,0,date("m")-2,1,date("Y"))));
        $this->smarty->assign("edate",date("Y-m",mktime(0,0,0,date("m"),1,date("Y"))));
		$this->forward = "monthly";
        
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
    public function pmonthlyAction(){
        $this->smarty->assign("active","wxlottery/pmonthly");
        $this->smarty->assign("active_menu","wxlottery");
        $this->smarty->assign("sdate",date("Y-m",mktime(0,0,0,date("m")-2,1,date("Y"))));
        $this->smarty->assign("edate",date("Y-m",mktime(0,0,0,date("m"),1,date("Y"))));
        $this->forward = "pmonthly";
        
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
    public function ajaxMonthlylotteryJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        
        $PSys_signModel = new PSys_PageviewModel();               
        $where["month_>="] = $start;
        $where["month_<="] = $end;
        $order = "month ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_wxlottery_monthly");
        $xAxis = $series = $dl_click = $dj_click = $dl_andclick = $dl_iosclick = $sure_click = $cancel_click = $share_open_user = $view_times = $start_num = $do_times = $first_join = $second_join = $third_join = $fourth_join = $fifth_join = $sharetimes = $shareok = "";
        
        foreach($result["allrow"] as $k=>$v){
            $xAxis .= '"'.$v["month"].'",';
            $dl_click .= $v["dl_click"].',';
            $dj_click .= $v["dj_click"].',';
            $dl_andclick .= $v["dl_andclick"].',';
            $dl_iosclick .= $v["dl_iosclick"].',';
            $sure_click .= $v["sure_click"].',';
            $cancel_click .= $v["cancel_click"].',';
            $share_open_user .= $v["share_open_user"].',';
            $view_times .= $v["view_times"].',';
            $start_num .= $v["start_num"].',';
            $do_times .= $v["do_times"].',';
            $first_join .= $v["first_join"].',';
            $second_join .= $v["second_join"].',';
            $third_join .= $v["third_join"].',';
            $fourth_join .= $v["fourth_join"].',';
            $fifth_join .= $v["fifth_join"].',';
            $sharetimes .= $v["sharetimes"].',';
            $shareok .= $v["shareok"].',';  
        }
        $xAxis = trim($xAxis,",");
        $dl_click = "{name:'点击下载',data:[".trim($dl_click,",")."]},";
        $dj_click = "{name:'点击兑奖',data:[".trim($dj_click,",")."]},";
        $dl_andclick = "{name:'点击安卓下载',data:[".trim($dl_andclick,",")."]},";
        $dl_iosclick = "{name:'点击ISO下载',data:[".trim($dl_iosclick,",")."]},";
        $sure_click = "{name:'点击确定',data:[".trim($sure_click,",")."]},";
        $cancel_click = "{name:'点击取消',data:[".trim($cancel_click,",")."]},";
        $share_open_user = "{name:'分享后开启APP',data:[".trim($share_open_user,",")."]},";
        $view_times = "{name:'开启抽奖页次数',data:[".trim($view_times,",")."]},";
        $start_num = "{name:'开启抽奖页人数',data:[".trim($start_num,",")."]},";
        $do_times = "{name:'抽奖总数',data:[".trim($do_times,",")."]},";
        $first_join = "{name:'首次抽奖',data:[".trim($first_join,",")."]},";
        $second_join = "{name:'二次抽奖',data:[".trim($second_join,",")."]},";
        $third_join = "{name:'三次抽奖',data:[".trim($third_join,",")."]},";
        $fourth_join = "{name:'四次抽奖',data:[".trim($fourth_join,",")."]},";
        $fifth_join = "{name:'五次抽奖',data:[".trim($fifth_join,",")."]},";
        $sharetimes = "{name:'分享活动次数',data:[".trim($sharetimes,",")."]},";
        $shareok = "{name:'分享成功次数',data:[".trim($shareok,",")."]},";
        
        $series = $dl_click.$dj_click.$dl_andclick.$dl_iosclick.$sure_click.$cancel_click.$share_open_user.$view_times.$start_num.$do_times.$first_join.$second_join.$third_join.$fourth_join.$fifth_join.$sharetimes.$shareok;
        

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
    * @return json
    *
    */
    public function ajaxMonthlypointJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        
        $PSys_signModel = new PSys_PageviewModel();               
        $where["month_>="] = $start;
        $where["month_<="] = $end;
        $order = "month ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_wxlottery_monthly");
        $xAxis = $series = $start_num = $new_reg = $new_num = $cost_points = $get_points = $first_get_points = $third_get_points = $second_get_points = $fourth_get_points = $fifth_get_points = "";
        foreach($result["allrow"] as $k=>$v){
            $xAxis .= '"'.$v["month"].'",';
            $start_num .= $v["start_num"].',';
            $new_num .= $v["new_num"].',';
            $new_reg .= $v["new_reg"].',';
            $cost_points .= $v["cost_points"].',';
            $get_points .= $v["get_points"].',';
            $first_get_points .= $v["first_get_points"].',';
            $second_get_points .= $v["second_get_points"].',';
            $third_get_points .= $v["third_get_points"].',';
            $fourth_get_points .= $v["fourth_get_points"].',';
            $fifth_get_points .= $v["fifth_get_points"].',';
        }
        $xAxis = trim($xAxis,",");
        $start_num = "{name:'开启抽奖页人数',data:[".trim($start_num,",")."]},";
        $new_reg = "{name:'注册用户数',data:[".trim($new_reg,",")."]},";
        $new_num = "{name:'新开启人数',data:[".trim($new_num,",")."]},";
        $cost_points = "{name:'消耗积分',data:[".trim($cost_points,",")."]},";
        $get_points = "{name:'获取积分',data:[".trim($get_points,",")."]},";
        $first_get_points = "{name:'第一次获取积分',data:[".trim($first_get_points,",")."]},";
        $second_get_points = "{name:'第二次获取积分',data:[".trim($second_get_points,",")."]},";
        $third_get_points = "{name:'第三次获取积分',data:[".trim($third_get_points,",")."]},";
        $fourth_get_points = "{name:'第四次获取积分',data:[".trim($fourth_get_points,",")."]},";
        $fifth_get_points = "{name:'第五次获取积分',data:[".trim($fifth_get_points,",")."]},";
        
        $series = $start_num.$new_reg.$new_num.$cost_points.$get_points.$first_get_points.$second_get_points.$third_get_points.$fourth_get_points.$fifth_get_points;


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
    public function ajaxMonthlylotteryHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        
        $PSys_signModel = new PSys_PageviewModel();
        $where["month_>="] = $start;
        $where["month_<="] = $end;
        $order = "month ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_wxlottery_monthly");
        $dl_click = $dj_click = $dl_andclick = $dl_iosclick = $sure_click = $cancel_click = $share_open_user = $view_times = $start_num = $do_times = $first_join = $second_join = $third_join = $fourth_join = $fifth_join = $sharetimes = $shareok = "";
        
        foreach($result["allrow"] as $k=>$v){
            $dl_click += intval($v["dl_click"]);
            $dj_click += intval($v["dj_click"]);
            $dl_andclick += intval($v["dl_andclick"]);
            $dl_iosclick += intval($v["dl_iosclick"]);
            $sure_click += intval($v["sure_click"]);
            $cancel_click += intval($v["cancel_click"]);
            $share_open_user += intval($v["share_open_user"]);
            $view_times += intval($v["view_times"]);
            $start_num += intval($v["start_num"]);
            $do_times += intval($v["do_times"]);
            $first_join += intval($v["first_join"]);
            $second_join += intval($v["second_join"]);
            $third_join += intval($v["third_join"]);
            $fourth_join += intval($v["fourth_join"]);
            $fifth_join += intval($v["fifth_join"]);
            $sharetimes += intval($v["sharetimes"]);
            $shareok += intval($v["shareok"]);
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("dl_click",$dl_click);
        $this->smarty->assign("dj_click",$dj_click);
        $this->smarty->assign("dl_andclick",$dl_andclick);
        $this->smarty->assign("dl_iosclick",$dl_iosclick);
        $this->smarty->assign("sure_click",$sure_click);
        $this->smarty->assign("cancel_click",$cancel_click);
        $this->smarty->assign("share_open_user",$share_open_user);
        $this->smarty->assign("view_times",$view_times);
        $this->smarty->assign("start_num",$start_num);
        $this->smarty->assign("do_times",$do_times);
        $this->smarty->assign("first_join",$first_join);
        $this->smarty->assign("second_join",$second_join);
        $this->smarty->assign("third_join",$third_join);
        $this->smarty->assign("fourth_join",$fourth_join);
        $this->smarty->assign("fifth_join",$fifth_join);
        $this->smarty->assign("sharetimes",$sharetimes);
        $this->smarty->assign("shareok",$shareok);
		$this->forward = "ajaxMonthlyLottery";
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
    public function ajaxMonthlypointHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        
        $PSys_signModel = new PSys_PageviewModel();
        $where["month_>="] = $start;
        $where["month_<="] = $end;
        $order = "month ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_wxlottery_monthly");
        $start_num = $new_reg = $new_num = $cost_points = $get_points = $first_get_points = $third_get_points = $second_get_points = $fourth_get_points = $fifth_get_points = "";
        
        foreach($result["allrow"] as $k=>$v){
            $start_num += intval($v["start_num"]);
            $new_reg += intval($v["new_reg"]);
            $new_num += intval($v["new_num"]);
            $cost_points += intval($v["cost_points"]);
            $get_points += intval($v["get_points"]);
            $first_get_points += intval($v["first_get_points"]);
            $third_get_points += intval($v["third_get_points"]);
            $second_get_points += intval($v["second_get_points"]);
            $fourth_get_points += intval($v["fourth_get_points"]);
            $fifth_get_points += intval($v["fifth_get_points"]);
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("start_num",$start_num);
        $this->smarty->assign("new_reg",$new_reg);
        $this->smarty->assign("new_num",$new_num);
        $this->smarty->assign("cost_points",$cost_points);
        $this->smarty->assign("get_points",$get_points);
        $this->smarty->assign("first_get_points",$first_get_points);
        $this->smarty->assign("third_get_points",$third_get_points);
        $this->smarty->assign("second_get_points",$second_get_points);
        $this->smarty->assign("fourth_get_points",$fourth_get_points);
        $this->smarty->assign("fifth_get_points",$fifth_get_points);
        $this->forward = "ajaxMonthlyPoint";
    }
    
}