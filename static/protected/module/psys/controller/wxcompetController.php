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
class wxcompetController extends PSys_AbstractController{
    
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
        $this->smarty->assign("active","wxcompet/index");
        $this->smarty->assign("active_menu","wxcompet");
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
    public function userAction(){
        $this->smarty->assign("active","wxcompet/user");
        $this->smarty->assign("active_menu","wxcompet");
        $this->smarty->assign("sdate",date('Y-m-d',strtotime("-6 days")));
        $this->smarty->assign("edate",date('Y-m-d'));
        $this->forward = "user";
        
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
    public function ajaxCompetJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $PSys_signModel = new PSys_PageviewModel();
        $where["day_>="] = $start;
        $where["day_<="] = $end;
        $order = "day ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_wxcompet_daily");
        
        $xAxis = $series = $view_times = $open_num = $open_times = $first_open = $second_open = $third_open = $fourth_open = $fifth_open = $sixth_open = $seventh_open = $eighth_open = $ninth_open = $tenth_open = $pass_5 = $pass_10 = $pass_15 = $pass_20 = $pass_25 = $pass_30 = $pass_35 = $pass_40 = $pass_45 = $pass_50 = $pass_other = "";
        foreach($result["allrow"] as $k=>$v){
            $xAxis .= '"'.$v["day"].'",';
            $view_times .= $v["view_times"].',';
            $open_num .= $v["open_num"].',';
            $open_times .= $v["open_times"].',';
            $open_person .= $v["open_person"].',';
            $first_open .= $v["first_open"].',';
            $second_open .= $v["second_open"].',';
            $third_open .= $v["third_open"].',';
            $fourth_open .= $v["fourth_open"].',';
            $fifth_open .= $v["fifth_open"].',';
            $sixth_open .= $v["sixth_open"].',';
            $seventh_open .= $v["seventh_open"].',';
            $eighth_open .= $v["eighth_open"].',';
            $ninth_open .= $v["ninth_open"].',';
            $tenth_open .= $v["tenth_open"].',';
            $pass_5 .= $v["pass_5"].',';
            $pass_10 .= $v["pass_10"].',';
            $pass_15 .= $v["pass_15"].',';
            $pass_20 .= $v["pass_20"].',';
            $pass_25 .= $v["pass_25"].',';
            $pass_30 .= $v["pass_30"].',';
            $pass_35 .= $v["pass_35"].',';
            $pass_40 .= $v["pass_40"].',';
            $pass_45 .= $v["pass_45"].',';  
            $pass_50 .= $v["pass_50"].',';  
            $pass_other .= $v["pass_other"].',';  
        }
        $xAxis = trim($xAxis,",");
        $view_times = "{name:'开启竞技页面次数',data:[".trim($view_times,",")."]},";
        $open_num = "{name:'开启竞技页面人数',data:[".trim($open_num,",")."]},";
        $open_times = "{name:'竞技总次数',data:[".trim($open_times,",")."]},";
        $open_person = "{name:'竞技总人数',data:[".trim($open_person,",")."]},";
        $first_open = "{name:'首次竞技',data:[".trim($first_open,",")."]},";
        $second_open = "{name:'2次竞技次数',data:[".trim($second_open,",")."]},";
        $third_open = "{name:'3次竞技次数',data:[".trim($third_open,",")."]},";
        $fourth_open = "{name:'4次竞技次数',data:[".trim($fourth_open,",")."]},";
        $fifth_open = "{name:'5次竞技次数',data:[".trim($fifth_open,",")."]},";
        $sixth_open = "{name:'6次竞技次数',data:[".trim($sixth_open,",")."]},";
        $seventh_open = "{name:'7次竞技次数',data:[".trim($seventh_open,",")."]},";
        $eighth_open = "{name:'8次竞技次数',data:[".trim($eighth_open,",")."]},";
        $ninth_open = "{name:'9次竞技次数',data:[".trim($ninth_open,",")."]},";
        $tenth_open = "{name:'10次竞技次数',data:[".trim($tenth_open,",")."]},";
        $pass_5 = "{name:'过5关人数',data:[".trim($pass_5,",")."]},";
        $pass_10 = "{name:'过10关人数',data:[".trim($pass_10,",")."]},";
        $pass_15 = "{name:'过15关人数',data:[".trim($pass_15,",")."]},";
        $pass_20 = "{name:'过20关人数',data:[".trim($pass_20,",")."]},";
        $pass_25 = "{name:'过25关人数',data:[".trim($pass_25,",")."]},";
        $pass_30 = "{name:'过30关人数',data:[".trim($pass_30,",")."]},";
        
        $series = $view_times.$open_num.$open_times.$open_person.$first_open.$second_open.$third_open.$fourth_open.$fifth_open.$sixth_open.$seventh_open.$eighth_open.$ninth_open.$tenth_open.$pass_5.$pass_10.$pass_15.$pass_20.$pass_25.$pass_30;
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
    public function ajaxUserJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $PSys_signModel = new PSys_PageviewModel();
        $where["day_>="] = $start;
        $where["day_<="] = $end;
        $order = "day ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_wxcompet_daily");
        
        $xAxis = $series = $open_num = $share_times = $share_ok = $dl_click = $dj_click = $dl_ad_click = $dl_ios_click = $start_dl_times = $new_num = $new_reg = "";
        foreach($result["allrow"] as $k=>$v){
            $xAxis .= '"'.$v["day"].'",';
            $open_num .= $v["open_num"].',';
            $share_times .= $v["share_times"].',';
            $share_ok .= $v["share_ok"].',';
            $dl_click .= $v["dl_click"].',';
            $dj_click .= $v["dj_click"].',';
            $dl_ad_click .= $v["dl_ad_click"].',';
            $dl_ios_click .= $v["dl_ios_click"].',';
            $start_dl_times .= $v["start_dl_times"].',';
            $new_num .= $v["new_num"].',';
            $new_reg .= $v["new_reg"].',';
        }
        $xAxis = trim($xAxis,",");
        $open_num = "{name:'开启竞技页人数',data:[".trim($open_num,",")."]},";
        $new_reg = "{name:'注册用户数',data:[".trim($new_reg,",")."]},";
        $new_num = "{name:'新开启人数',data:[".trim($new_num,",")."]},";
        $share_times = "{name:'分享活动',data:[".trim($share_times,",")."]},";
        $share_ok = "{name:'分享成功',data:[".trim($share_ok,",")."]},";
        $dl_click = "{name:'点击下载',data:[".trim($dl_click,",")."]},";
        $dj_click = "{name:'点击兑奖',data:[".trim($dj_click,",")."]},";
        $dl_ad_click = "{name:'点击安卓下载',data:[".trim($dl_ad_click,",")."]},";
        $dl_ios_click = "{name:'点击IOS下载',data:[".trim($dl_ios_click,",")."]},";
        $start_dl_times = "{name:'分享后开启APP',data:[".trim($start_dl_times,",")."]},";
        
        $series = $open_num.$new_reg.$new_num.$share_times.$share_ok.$dl_click.$dj_click.$dl_ad_click.$dl_ios_click.$start_dl_times;
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
    public function ajaxCompetHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $PSys_signModel = new PSys_PageviewModel();
        $where["day_>="] = $start;
        $where["day_<="] = $end;
        $order = "day ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_wxcompet_daily");
        $view_times = $open_num = $open_times = $first_open = $second_open = $third_open = $fourth_open = $fifth_open = $sixth_open = $seventh_open = $eighth_open = $ninth_open = $tenth_open = $pass_5 = $pass_10 = $pass_15 = $pass_20 = $pass_25 = $pass_30 = $pass_35 = $pass_40 = $pass_45 = $pass_50 = $pass_other = "";
        
        foreach($result["allrow"] as $k=>$v){
            $view_times += intval($v["view_times"]);
            $open_num += intval($v["open_num"]);
            $open_times += intval($v["open_times"]);
            $open_person += intval($v["open_person"]);
            $first_open += intval($v["first_open"]);
            $second_open += intval($v["second_open"]);
            $third_open += intval($v["third_open"]);
            $fourth_open += intval($v["fourth_open"]);
            $fifth_open += intval($v["fifth_open"]);
            $sixth_open += intval($v["sixth_open"]);
            $seventh_open += intval($v["seventh_open"]);
            $eighth_open += intval($v["eighth_open"]);
            $ninth_open += intval($v["ninth_open"]);
            $tenth_open += intval($v["tenth_open"]);
            $pass_5 += intval($v["pass_5"]);
            $pass_10 += intval($v["pass_10"]);
            $pass_15 += intval($v["pass_15"]);
            $pass_20 += intval($v["pass_20"]);
            $pass_25 += intval($v["pass_25"]);
            $pass_30 += intval($v["pass_30"]);
            $pass_35 += intval($v["pass_35"]);
            $pass_40 += intval($v["pass_40"]);
            $pass_45 += intval($v["pass_45"]);
            $pass_50 += intval($v["pass_50"]);
            $pass_other += intval($v["pass_other"]);
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("view_times",$view_times);
        $this->smarty->assign("open_num",$open_num);
        $this->smarty->assign("open_times",$open_times);
        $this->smarty->assign("open_person",$open_person);
        $this->smarty->assign("first_open",$first_open);
        $this->smarty->assign("second_open",$second_open);
        $this->smarty->assign("third_open",$third_open);
        $this->smarty->assign("fourth_open",$fourth_open);
        $this->smarty->assign("fifth_open",$fifth_open);
        $this->smarty->assign("sixth_open",$sixth_open);
        $this->smarty->assign("seventh_open",$seventh_open);
        $this->smarty->assign("eighth_open",$eighth_open);
        $this->smarty->assign("ninth_open",$ninth_open);
        $this->smarty->assign("tenth_open",$tenth_open);
        $this->smarty->assign("pass_5",$pass_5);
        $this->smarty->assign("pass_10",$pass_10);
        $this->smarty->assign("pass_15",$pass_15);
        $this->smarty->assign("pass_20",$pass_20);
        $this->smarty->assign("pass_25",$pass_25);
        $this->smarty->assign("pass_30",$pass_30);
		$this->forward = "ajaxCompet";
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
    public function ajaxUserHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $PSys_signModel = new PSys_PageviewModel();
        $where["day_>="] = $start;
        $where["day_<="] = $end;
        $order = "day ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_wxcompet_daily");
        $open_num = $share_times = $share_ok = $dl_click = $dj_click = $dl_ad_click = $dl_ios_click = $start_dl_times = $new_num = $new_reg = "";
        
        foreach($result["allrow"] as $k=>$v){
            $open_num += intval($v["open_num"]);
            $new_reg += intval($v["new_reg"]);
            $new_num += intval($v["new_num"]);
            $share_times += intval($v["share_times"]);
            $share_ok += intval($v["share_ok"]);
            $dl_click += intval($v["dl_click"]);
            $dj_click += intval($v["dj_click"]);
            $dl_ad_click += intval($v["dl_ad_click"]);
            $dl_ios_click += intval($v["dl_ios_click"]);
            $start_dl_times += intval($v["start_dl_times"]);
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("open_num",$open_num);
        $this->smarty->assign("new_reg",$new_reg);
        $this->smarty->assign("new_num",$new_num);
        $this->smarty->assign("share_times",$share_times);
        $this->smarty->assign("share_ok",$share_ok);
        $this->smarty->assign("dl_click",$dl_click);
        $this->smarty->assign("dj_click",$dj_click);
        $this->smarty->assign("dl_ad_click",$dl_ad_click);
        $this->smarty->assign("dl_ios_click",$dl_ios_click);
        $this->smarty->assign("start_dl_times",$start_dl_times);
        $this->forward = "ajaxUser";
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
        $this->smarty->assign("active","wxcompet/weekly");
        $this->smarty->assign("active_menu","wxcompet");
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
    public function uweeklyAction(){
        $this->smarty->assign("active","wxcompet/uweekly");
        $this->smarty->assign("active_menu","wxcompet");
        $this->smarty->assign("sdate",date('Y-m-d',strtotime("-14 days")));
        $this->smarty->assign("edate",date('Y-m-d'));
        $this->forward = "uweekly";
        
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
    public function ajaxWeeklycompetJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $tempdate=date('Y-m-d',strtotime("$start Monday")); 
        $start=date('Y-m-d',strtotime("$tempdate -7 days"));
        $end=date('Y-m-d',strtotime("$end Sunday"));
        
        $PSys_signModel = new PSys_PageviewModel();               
        $where["startday_>="] = $start;
        $where["endday_<="] = $end;
        $order = "startday ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_wxcompet_weekly");
        
        $xAxis = $series = $view_times = $open_num = $open_times = $first_open = $second_open = $third_open = $fourth_open = $fifth_open = $sixth_open = $seventh_open = $eighth_open = $ninth_open = $tenth_open = $pass_5 = $pass_10 = $pass_15 = $pass_20 = $pass_25 = $pass_30 = $pass_35 = $pass_40 = $pass_45 = $pass_50 = $pass_other = "";
        foreach($result["allrow"] as $k=>$v){
            $xtemp = $v["startday"];
            $mictime = strtotime($v["startday"]);
            $endtemp = date('/m/d',strtotime("$xtemp Sunday"));
            $xAxis .= '"'.date('Y/m/d',$mictime).' - '.$endtemp.'",';
            $view_times .= $v["view_times"].',';
            $open_num .= $v["open_num"].',';
            $open_times .= $v["open_times"].',';
            $first_open .= $v["first_open"].',';
            $second_open .= $v["second_open"].',';
            $third_open .= $v["third_open"].',';
            $fourth_open .= $v["fourth_open"].',';
            $fifth_open .= $v["fifth_open"].',';
            $sixth_open .= $v["sixth_open"].',';
            $seventh_open .= $v["seventh_open"].',';
            $eighth_open .= $v["eighth_open"].',';
            $ninth_open .= $v["ninth_open"].',';
            $tenth_open .= $v["tenth_open"].',';
            $pass_5 .= $v["pass_5"].',';
            $pass_10 .= $v["pass_10"].',';
            $pass_15 .= $v["pass_15"].',';
            $pass_20 .= $v["pass_20"].',';
            $pass_25 .= $v["pass_25"].',';
            $pass_30 .= $v["pass_30"].',';
            $pass_35 .= $v["pass_35"].',';
            $pass_40 .= $v["pass_40"].',';
            $pass_45 .= $v["pass_45"].',';  
            $pass_50 .= $v["pass_50"].',';  
            $pass_other .= $v["pass_other"].',';  
        }
        $xAxis = trim($xAxis,",");
        $view_times = "{name:'开启竞技次数',data:[".trim($view_times,",")."]},";
        $open_num = "{name:'开启竞技人数',data:[".trim($open_num,",")."]},";
        $open_times = "{name:'竞技总数',data:[".trim($open_times,",")."]},";
        $first_open = "{name:'首次竞技',data:[".trim($first_open,",")."]},";
        $second_open = "{name:'二次竞技',data:[".trim($second_open,",")."]},";
        $third_open = "{name:'三次竞技',data:[".trim($third_open,",")."]},";
        $fourth_open = "{name:'四次竞技',data:[".trim($fourth_open,",")."]},";
        $fifth_open = "{name:'五次竞技',data:[".trim($fifth_open,",")."]},";
        $sixth_open = "{name:'六次竞技',data:[".trim($sixth_open,",")."]},";
        $seventh_open = "{name:'七次竞技',data:[".trim($seventh_open,",")."]},";
        $eighth_open = "{name:'八次竞技',data:[".trim($eighth_open,",")."]},";
        $ninth_open = "{name:'九次竞技',data:[".trim($ninth_open,",")."]},";
        $tenth_open = "{name:'十次竞技',data:[".trim($tenth_open,",")."]},";
        $pass_5 = "{name:'过5关',data:[".trim($pass_5,",")."]},";
        $pass_10 = "{name:'过10关',data:[".trim($pass_10,",")."]},";
        $pass_15 = "{name:'过15关',data:[".trim($pass_15,",")."]},";
        $pass_20 = "{name:'过20关',data:[".trim($pass_20,",")."]},";
        $pass_25 = "{name:'过25关',data:[".trim($pass_25,",")."]},";
        $pass_30 = "{name:'过30关',data:[".trim($pass_30,",")."]},";
        $pass_35 = "{name:'过35关',data:[".trim($pass_35,",")."]},";
        $pass_40 = "{name:'过40关',data:[".trim($pass_40,",")."]},";
        $pass_45 = "{name:'过45关',data:[".trim($pass_45,",")."]},";
        $pass_50 = "{name:'过50关',data:[".trim($pass_50,",")."]},";
        $pass_other = "{name:'过50关以上',data:[".trim($pass_other,",")."]},";
        
        $series = $view_times.$open_num.$open_times.$first_open.$second_open.$third_open.$fourth_open.$fifth_open.$sixth_open.$seventh_open.$eighth_open.$ninth_open.$tenth_open.$pass_5.$pass_10.$pass_15.$pass_20.$pass_25.$pass_30.$pass_35.$pass_40.$pass_45.$pass_50.$pass_other;
        

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
    public function ajaxWeeklyuserJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $tempdate=date('Y-m-d',strtotime("$start Monday")); 
        $start=date('Y-m-d',strtotime("$tempdate -7 days"));
        $end=date('Y-m-d',strtotime("$end Sunday"));
        
        $PSys_signModel = new PSys_PageviewModel();               
        $where["startday_>="] = $start;
        $where["endday_<="] = $end;
        $order = "startday ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_wxcompet_weekly");

        $xAxis = $series = $open_num = $share_times = $share_ok = $dl_click = $dj_click = $dl_ad_click = $dl_ios_click = $start_dl_times = $new_num = $new_reg = "";
        foreach($result["allrow"] as $k=>$v){
            $xtemp = $v["startday"];
            $mictime = strtotime($v["startday"]);
            $endtemp = date('/m/d',strtotime("$xtemp Sunday"));
            $xAxis .= '"'.date('Y/m/d',$mictime).' - '.$endtemp.'",';
            $open_num .= $v["open_num"].',';
            $share_times .= $v["share_times"].',';
            $share_ok .= $v["share_ok"].',';
            $dl_click .= $v["dl_click"].',';
            $dj_click .= $v["dj_click"].',';
            $dl_ad_click .= $v["dl_ad_click"].',';
            $dl_ios_click .= $v["dl_ios_click"].',';
            $start_dl_times .= $v["start_dl_times"].',';
            $new_num .= $v["new_num"].',';
            $new_reg .= $v["new_reg"].',';
        }
        $xAxis = trim($xAxis,",");
        $open_num = "{name:'开启竞技页人数',data:[".trim($open_num,",")."]},";
        $new_reg = "{name:'注册用户数',data:[".trim($new_reg,",")."]},";
        $new_num = "{name:'新开启人数',data:[".trim($new_num,",")."]},";
        $share_times = "{name:'分享活动',data:[".trim($share_times,",")."]},";
        $share_ok = "{name:'分享成功',data:[".trim($share_ok,",")."]},";
        $dl_click = "{name:'点击下载',data:[".trim($dl_click,",")."]},";
        $dj_click = "{name:'点击兑奖',data:[".trim($dj_click,",")."]},";
        $dl_ad_click = "{name:'点击安卓下载',data:[".trim($dl_ad_click,",")."]},";
        $dl_ios_click = "{name:'点击IOS下载',data:[".trim($dl_ios_click,",")."]},";
        $start_dl_times = "{name:'分享后开启APP',data:[".trim($start_dl_times,",")."]},";
        
        $series = $open_num.$new_reg.$new_num.$share_times.$share_ok.$dl_click.$dj_click.$dl_ad_click.$dl_ios_click.$start_dl_times;

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
    public function ajaxWeeklycompetHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $tempdate=date('Y-m-d',strtotime("$start Monday")); 
        $start=date('Y-m-d',strtotime("$tempdate -7 days"));
        $end=date('Y-m-d',strtotime("$end Sunday"));
        
        $PSys_signModel = new PSys_PageviewModel();
        $where["startday_>="] = $start;
        $where["endday_<="] = $end;
        $order = "startday ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_wxcompet_weekly");
        $view_times = $open_num = $open_times = $first_open = $second_open = $third_open = $fourth_open = $fifth_open = $sixth_open = $seventh_open = $eighth_open = $ninth_open = $tenth_open = $pass_5 = $pass_10 = $pass_15 = $pass_20 = $pass_25 = $pass_30 = $pass_35 = $pass_40 = $pass_45 = $pass_50 = $pass_other = "";
        
        foreach($result["allrow"] as $k=>$v){
            $view_times += intval($v["view_times"]);
            $open_num += intval($v["open_num"]);
            $open_times += intval($v["open_times"]);
            $first_open += intval($v["first_open"]);
            $second_open += intval($v["second_open"]);
            $third_open += intval($v["third_open"]);
            $fourth_open += intval($v["fourth_open"]);
            $fifth_open += intval($v["fifth_open"]);
            $sixth_open += intval($v["sixth_open"]);
            $seventh_open += intval($v["seventh_open"]);
            $eighth_open += intval($v["eighth_open"]);
            $ninth_open += intval($v["ninth_open"]);
            $tenth_open += intval($v["tenth_open"]);
            $pass_5 += intval($v["pass_5"]);
            $pass_10 += intval($v["pass_10"]);
            $pass_15 += intval($v["pass_15"]);
            $pass_20 += intval($v["pass_20"]);
            $pass_25 += intval($v["pass_25"]);
            $pass_30 += intval($v["pass_30"]);
            $pass_35 += intval($v["pass_35"]);
            $pass_40 += intval($v["pass_40"]);
            $pass_45 += intval($v["pass_45"]);
            $pass_50 += intval($v["pass_50"]);
            $pass_other += intval($v["pass_other"]);
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("view_times",$view_times);
        $this->smarty->assign("open_num",$open_num);
        $this->smarty->assign("open_times",$open_times);
        $this->smarty->assign("first_open",$first_open);
        $this->smarty->assign("second_open",$second_open);
        $this->smarty->assign("third_open",$third_open);
        $this->smarty->assign("fourth_open",$fourth_open);
        $this->smarty->assign("fifth_open",$fifth_open);
        $this->smarty->assign("sixth_open",$sixth_open);
        $this->smarty->assign("seventh_open",$seventh_open);
        $this->smarty->assign("eighth_open",$eighth_open);
        $this->smarty->assign("ninth_open",$ninth_open);
        $this->smarty->assign("tenth_open",$tenth_open);
        $this->smarty->assign("pass_5",$pass_5);
        $this->smarty->assign("pass_10",$pass_10);
        $this->smarty->assign("pass_15",$pass_15);
        $this->smarty->assign("pass_20",$pass_20);
        $this->smarty->assign("pass_25",$pass_25);
        $this->smarty->assign("pass_30",$pass_30);
        $this->smarty->assign("pass_35",$pass_35);
        $this->smarty->assign("pass_40",$pass_40);
        $this->smarty->assign("pass_45",$pass_45);
        $this->smarty->assign("pass_50",$pass_50);
        $this->smarty->assign("pass_other",$pass_other);
		$this->forward = "ajaxWeeklyCompet";
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
    public function ajaxWeeklyuserHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $tempdate=date('Y-m-d',strtotime("$start Monday")); 
        $start=date('Y-m-d',strtotime("$tempdate -7 days"));
        $end=date('Y-m-d',strtotime("$end Sunday"));
        
        $PSys_signModel = new PSys_PageviewModel();
        $where["startday_>="] = $start;
        $where["endday_<="] = $end;
        $order = "startday ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_wxcompet_weekly");
        $open_num = $share_times = $share_ok = $dl_click = $dj_click = $dl_ad_click = $dl_ios_click = $start_dl_times = $new_num = $new_reg = "";
        
        foreach($result["allrow"] as $k=>$v){
            $open_num += intval($v["open_num"]);
            $new_reg += intval($v["new_reg"]);
            $new_num += intval($v["new_num"]);
            $share_times += intval($v["share_times"]);
            $share_ok += intval($v["share_ok"]);
            $dl_click += intval($v["dl_click"]);
            $dj_click += intval($v["dj_click"]);
            $dl_ad_click += intval($v["dl_ad_click"]);
            $dl_ios_click += intval($v["dl_ios_click"]);
            $start_dl_times += intval($v["start_dl_times"]);
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("open_num",$open_num);
        $this->smarty->assign("new_reg",$new_reg);
        $this->smarty->assign("new_num",$new_num);
        $this->smarty->assign("share_times",$share_times);
        $this->smarty->assign("share_ok",$share_ok);
        $this->smarty->assign("dl_click",$dl_click);
        $this->smarty->assign("dj_click",$dj_click);
        $this->smarty->assign("dl_ad_click",$dl_ad_click);
        $this->smarty->assign("dl_ios_click",$dl_ios_click);
        $this->smarty->assign("start_dl_times",$start_dl_times);
        $this->forward = "ajaxWeeklyUser";
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
        $this->smarty->assign("active","wxcompet/monthly");
        $this->smarty->assign("active_menu","wxcompet");
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
    public function umonthlyAction(){
        $this->smarty->assign("active","wxcompet/umonthly");
        $this->smarty->assign("active_menu","wxcompet");
        $this->smarty->assign("sdate",date("Y-m",mktime(0,0,0,date("m")-2,1,date("Y"))));
        $this->smarty->assign("edate",date("Y-m",mktime(0,0,0,date("m"),1,date("Y"))));
        $this->forward = "umonthly";
        
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
    public function ajaxMonthlycompetJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        
        $PSys_signModel = new PSys_PageviewModel();               
        $where["month_>="] = $start;
        $where["month_<="] = $end;
        $order = "month ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_wxcompet_monthly");
        $xAxis = $series = $view_times = $open_num = $open_times = $first_open = $second_open = $third_open = $fourth_open = $fifth_open = $sixth_open = $seventh_open = $eighth_open = $ninth_open = $tenth_open = $pass_5 = $pass_10 = $pass_15 = $pass_20 = $pass_25 = $pass_30 = $pass_35 = $pass_40 = $pass_45 = $pass_50 = $pass_other = "";
        foreach($result["allrow"] as $k=>$v){
           
            $xAxis .= '"'.$v["month"].'",';
            $view_times .= $v["view_times"].',';
            $open_num .= $v["open_num"].',';
            $open_times .= $v["open_times"].',';
            $first_open .= $v["first_open"].',';
            $second_open .= $v["second_open"].',';
            $third_open .= $v["third_open"].',';
            $fourth_open .= $v["fourth_open"].',';
            $fifth_open .= $v["fifth_open"].',';
            $sixth_open .= $v["sixth_open"].',';
            $seventh_open .= $v["seventh_open"].',';
            $eighth_open .= $v["eighth_open"].',';
            $ninth_open .= $v["ninth_open"].',';
            $tenth_open .= $v["tenth_open"].',';
            $pass_5 .= $v["pass_5"].',';
            $pass_10 .= $v["pass_10"].',';
            $pass_15 .= $v["pass_15"].',';
            $pass_20 .= $v["pass_20"].',';
            $pass_25 .= $v["pass_25"].',';
            $pass_30 .= $v["pass_30"].',';
            $pass_35 .= $v["pass_35"].',';
            $pass_40 .= $v["pass_40"].',';
            $pass_45 .= $v["pass_45"].',';  
            $pass_50 .= $v["pass_50"].',';  
            $pass_other .= $v["pass_other"].',';  
        }
        $xAxis = trim($xAxis,",");
        $view_times = "{name:'开启竞技次数',data:[".trim($view_times,",")."]},";
        $open_num = "{name:'开启竞技人数',data:[".trim($open_num,",")."]},";
        $open_times = "{name:'竞技总数',data:[".trim($open_times,",")."]},";
        $first_open = "{name:'首次竞技',data:[".trim($first_open,",")."]},";
        $second_open = "{name:'二次竞技',data:[".trim($second_open,",")."]},";
        $third_open = "{name:'三次竞技',data:[".trim($third_open,",")."]},";
        $fourth_open = "{name:'四次竞技',data:[".trim($fourth_open,",")."]},";
        $fifth_open = "{name:'五次竞技',data:[".trim($fifth_open,",")."]},";
        $sixth_open = "{name:'六次竞技',data:[".trim($sixth_open,",")."]},";
        $seventh_open = "{name:'七次竞技',data:[".trim($seventh_open,",")."]},";
        $eighth_open = "{name:'八次竞技',data:[".trim($eighth_open,",")."]},";
        $ninth_open = "{name:'九次竞技',data:[".trim($ninth_open,",")."]},";
        $tenth_open = "{name:'十次竞技',data:[".trim($tenth_open,",")."]},";
        $pass_5 = "{name:'过5关',data:[".trim($pass_5,",")."]},";
        $pass_10 = "{name:'过10关',data:[".trim($pass_10,",")."]},";
        $pass_15 = "{name:'过15关',data:[".trim($pass_15,",")."]},";
        $pass_20 = "{name:'过20关',data:[".trim($pass_20,",")."]},";
        $pass_25 = "{name:'过25关',data:[".trim($pass_25,",")."]},";
        $pass_30 = "{name:'过30关',data:[".trim($pass_30,",")."]},";
        $pass_35 = "{name:'过35关',data:[".trim($pass_35,",")."]},";
        $pass_40 = "{name:'过40关',data:[".trim($pass_40,",")."]},";
        $pass_45 = "{name:'过45关',data:[".trim($pass_45,",")."]},";
        $pass_50 = "{name:'过50关',data:[".trim($pass_50,",")."]},";
        $pass_other = "{name:'过50关以上',data:[".trim($pass_other,",")."]},";
        
        $series = $view_times.$open_num.$open_times.$first_open.$second_open.$third_open.$fourth_open.$fifth_open.$sixth_open.$seventh_open.$eighth_open.$ninth_open.$tenth_open.$pass_5.$pass_10.$pass_15.$pass_20.$pass_25.$pass_30.$pass_35.$pass_40.$pass_45.$pass_50.$pass_other;
        
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
    public function ajaxMonthlyuserJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        
        $PSys_signModel = new PSys_PageviewModel();               
        $where["month_>="] = $start;
        $where["month_<="] = $end;
        $order = "month ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_wxcompet_monthly");
        $xAxis = $series = $open_num = $share_times = $share_ok = $dl_click = $dj_click = $dl_ad_click = $dl_ios_click = $start_dl_times = $new_num = $new_reg = "";
        foreach($result["allrow"] as $k=>$v){
            $xAxis .= '"'.$v["month"].'",';
            $open_num .= $v["open_num"].',';
            $share_times .= $v["share_times"].',';
            $share_ok .= $v["share_ok"].',';
            $dl_click .= $v["dl_click"].',';
            $dj_click .= $v["dj_click"].',';
            $dl_ad_click .= $v["dl_ad_click"].',';
            $dl_ios_click .= $v["dl_ios_click"].',';
            $start_dl_times .= $v["start_dl_times"].',';
            $new_num .= $v["new_num"].',';
            $new_reg .= $v["new_reg"].',';
        }
        $xAxis = trim($xAxis,",");
        $open_num = "{name:'开启抽奖页人数',data:[".trim($open_num,",")."]},";
        $new_reg = "{name:'注册用户数',data:[".trim($new_reg,",")."]},";
        $new_num = "{name:'新开启人数',data:[".trim($new_num,",")."]},";
        $share_times = "{name:'分享活动',data:[".trim($share_times,",")."]},";
        $share_ok = "{name:'分享成功',data:[".trim($share_ok,",")."]},";
        $dl_click = "{name:'点击下载',data:[".trim($dl_click,",")."]},";
        $dj_click = "{name:'点击兑奖',data:[".trim($dj_click,",")."]},";
        $dl_ad_click = "{name:'点击安卓下载',data:[".trim($dl_ad_click,",")."]},";
        $dl_ios_click = "{name:'点击IOS下载',data:[".trim($dl_ios_click,",")."]},";
        $start_dl_times = "{name:'分享后开启APP',data:[".trim($start_dl_times,",")."]},";
        
        $series = $open_num.$new_reg.$new_num.$share_times.$share_ok.$dl_click.$dj_click.$dl_ad_click.$dl_ios_click.$start_dl_times;
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
    public function ajaxMonthlycompetHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        
        $PSys_signModel = new PSys_PageviewModel();
        $where["month_>="] = $start;
        $where["month_<="] = $end;
        $order = "month ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_wxcompet_monthly");
        $view_times = $open_num = $open_times = $first_open = $second_open = $third_open = $fourth_open = $fifth_open = $sixth_open = $seventh_open = $eighth_open = $ninth_open = $tenth_open = $pass_5 = $pass_10 = $pass_15 = $pass_20 = $pass_25 = $pass_30 = $pass_35 = $pass_40 = $pass_45 = $pass_50 = $pass_other = "";
        
        foreach($result["allrow"] as $k=>$v){
            $view_times += intval($v["view_times"]);
            $open_num += intval($v["open_num"]);
            $open_times += intval($v["open_times"]);
            $first_open += intval($v["first_open"]);
            $second_open += intval($v["second_open"]);
            $third_open += intval($v["third_open"]);
            $fourth_open += intval($v["fourth_open"]);
            $fifth_open += intval($v["fifth_open"]);
            $sixth_open += intval($v["sixth_open"]);
            $seventh_open += intval($v["seventh_open"]);
            $eighth_open += intval($v["eighth_open"]);
            $ninth_open += intval($v["ninth_open"]);
            $tenth_open += intval($v["tenth_open"]);
            $pass_5 += intval($v["pass_5"]);
            $pass_10 += intval($v["pass_10"]);
            $pass_15 += intval($v["pass_15"]);
            $pass_20 += intval($v["pass_20"]);
            $pass_25 += intval($v["pass_25"]);
            $pass_30 += intval($v["pass_30"]);
            $pass_35 += intval($v["pass_35"]);
            $pass_40 += intval($v["pass_40"]);
            $pass_45 += intval($v["pass_45"]);
            $pass_50 += intval($v["pass_50"]);
            $pass_other += intval($v["pass_other"]);
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("view_times",$view_times);
        $this->smarty->assign("open_num",$open_num);
        $this->smarty->assign("open_times",$open_times);
        $this->smarty->assign("first_open",$first_open);
        $this->smarty->assign("second_open",$second_open);
        $this->smarty->assign("third_open",$third_open);
        $this->smarty->assign("fourth_open",$fourth_open);
        $this->smarty->assign("fifth_open",$fifth_open);
        $this->smarty->assign("sixth_open",$sixth_open);
        $this->smarty->assign("seventh_open",$seventh_open);
        $this->smarty->assign("eighth_open",$eighth_open);
        $this->smarty->assign("ninth_open",$ninth_open);
        $this->smarty->assign("tenth_open",$tenth_open);
        $this->smarty->assign("pass_5",$pass_5);
        $this->smarty->assign("pass_10",$pass_10);
        $this->smarty->assign("pass_15",$pass_15);
        $this->smarty->assign("pass_20",$pass_20);
        $this->smarty->assign("pass_25",$pass_25);
        $this->smarty->assign("pass_30",$pass_30);
        $this->smarty->assign("pass_35",$pass_35);
        $this->smarty->assign("pass_40",$pass_40);
        $this->smarty->assign("pass_45",$pass_45);
        $this->smarty->assign("pass_50",$pass_50);
        $this->smarty->assign("pass_other",$pass_other);
		$this->forward = "ajaxMonthlyCompet";
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
    public function ajaxMonthlyuserHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        
        $PSys_signModel = new PSys_PageviewModel();
        $where["month_>="] = $start;
        $where["month_<="] = $end;
        $order = "month ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_wxcompet_monthly");
        $open_num = $share_times = $share_ok = $dl_click = $dj_click = $dl_ad_click = $dl_ios_click = $start_dl_times = $new_num = $new_reg = "";
        
        foreach($result["allrow"] as $k=>$v){
            $open_num += intval($v["open_num"]);
            $new_reg += intval($v["new_reg"]);
            $new_num += intval($v["new_num"]);
            $share_times += intval($v["share_times"]);
            $share_ok += intval($v["share_ok"]);
            $dl_click += intval($v["dl_click"]);
            $dj_click += intval($v["dj_click"]);
            $dl_ad_click += intval($v["dl_ad_click"]);
            $dl_ios_click += intval($v["dl_ios_click"]);
            $start_dl_times += intval($v["start_dl_times"]);
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("open_num",$open_num);
        $this->smarty->assign("new_reg",$new_reg);
        $this->smarty->assign("new_num",$new_num);
        $this->smarty->assign("share_times",$share_times);
        $this->smarty->assign("share_ok",$share_ok);
        $this->smarty->assign("dl_click",$dl_click);
        $this->smarty->assign("dj_click",$dj_click);
        $this->smarty->assign("dl_ad_click",$dl_ad_click);
        $this->smarty->assign("dl_ios_click",$dl_ios_click);
        $this->smarty->assign("start_dl_times",$start_dl_times);
        $this->forward = "ajaxMonthlyUser";
    }
    
}