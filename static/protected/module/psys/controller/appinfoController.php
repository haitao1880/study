<?php
/**
* Copyright(c) 2015
* 日    期:2015年07月07日
* 文 件 名:{appinfo}Controller.php
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
class appinfoController extends PSys_AbstractController{
    
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
     * @appinfo public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function indexAction(){
        $this->smarty->assign("active","appinfo/index");
        $this->smarty->assign("active_menu","appinfo");
        $this->smarty->assign("sdate",date('Y-m-d',strtotime("-6 days")));
        $this->smarty->assign("edate",date('Y-m-d'));
		$this->forward = "index";
        
	}

    /**
     *
     * @do 展示浏览数据
     *
     * @appinfo public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
    public function userAction(){
        $this->smarty->assign("active","appinfo/user");
        $this->smarty->assign("active_menu","appinfo");
        $this->smarty->assign("sdate",date('Y-m-d',strtotime("-6 days")));
        $this->smarty->assign("edate",date('Y-m-d'));
        $this->forward = "user";
        
    }
    
    /**
    *
    * @do ajax 获取日浏览数据
    *
    * @member public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return json
    *
    */
    public function ajaxAppuserJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $PSys_memberModel = new PSys_PageviewModel();
        $where["day_>="] = $start;
        $where["day_<="] = $end;
        $order = "day ASC";
        $result = $PSys_memberModel->GetList($where, $order, 0, 0, "*","rhc_member_daily");
        
        $xAxis = $series = "";
        foreach($result["allrow"] as $k=>$v){
            $xAxis .= '"'.$v["day"].'",';
            $open_app .= $v["open_app"].',';
            $new_appuser .= $v["new_appuser"].',';
            $active_appuser .= $v["active_appuser"].',';
            $old_appuser .= intval($v["active_appuser"]-$v["new_appuser"]).',';
        }
        $xAxis = trim($xAxis,",");
        $open_app = "{name:'开启APP次数',data:[".trim($open_app,",")."]},";
        $new_appuser = "{name:'开启APP新用户',data:[".trim($new_appuser,",")."]},";
        $old_appuser = "{name:'开启APP老用户',data:[".trim($old_appuser,",")."]},";
        $active_appuser = "{name:'开启平台人数',data:[".trim($active_appuser,",")."]},";
        $series = $open_app.$new_appuser.$old_appuser.$active_appuser;
        $data['xAxis'] = $xAxis;
        $data['series'] = $series;
        die(json_encode($data));
    }

    /**
    *
    * @do ajax 获取浏览数据
    *
    * @member public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return html
    *
    */
    public function ajaxAppuserHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $PSys_memberModel = new PSys_PageviewModel();
        $where["day_>="] = $start;
        $where["day_<="] = $end;
        $order = "day ASC";
        $result = $PSys_memberModel->GetList($where, $order, 0, 0, "*","rhc_member_daily");
        $open_app = $active_user = $total_old_user = 0;
        foreach($result["allrow"] as $k=>$v){
            $result["allrow"][$k]['old_appuser'] = intval($v["active_appuser"]-$v["new_appuser"]);
            $open_app += intval($v["open_app"]);
            $new_appuser += intval($v["new_appuser"]);
            $active_appuser += intval($v["active_appuser"]);
            $old_appuser += intval($v["active_appuser"]-$v["new_appuser"]);
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("open_app",$open_app);
        $this->smarty->assign("new_appuser",$new_appuser);
        $this->smarty->assign("active_appuser",$active_appuser);
        $this->smarty->assign("old_appuser",$old_appuser);
        $this->forward = "ajaxAppuser";
    }

    /**
    *
    * @do ajax 获取日浏览数据
    *
    * @appinfo public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return json
    *
    */
    public function ajaxAppinfoJsonAction(){
        $start = reqstr("start","");
        $micstart = strtotime($start);
        $end = reqstr("end","");
        $micend = strtotime($end);
        $PSys_PageviewRule = new PSys_PageviewRule();
        $xAxis = $series = "";
        for($x=$micstart; $x<=$micend; $x += 86400) {
            $xAxis .= '"'.date('Y-m-d',$x).'",';
            $sql_qzupdate = 'select sum(total) as total from rhc_game_platform_collect where type=401 and pid = 1 and cday = '.date('Ymd',$x);
            $qzRs = $PSys_PageviewRule->collectQuery($sql_qzupdate);
            $qzupdate_num .= intval($qzRs[0]["total"]).',';
            $sql_ljupdate = 'select sum(total) as total from rhc_game_platform_collect where type=401 and pid = 2 and cday = '.date('Ymd',$x);
            $ljRs = $PSys_PageviewRule->collectQuery($sql_ljupdate);
            $ljupdate_num .= intval($ljRs[0]["total"]).',';
            $sql_yhupdate = 'select sum(total) as total from rhc_game_platform_collect where type=401 and pid = 3 and cday = '.date('Ymd',$x);
            $yhRs = $PSys_PageviewRule->collectQuery($sql_yhupdate);
            $yhupdate_num .= intval($yhRs[0]["total"]).',';
            $sql_tcclose = 'select sum(total) as total from rhc_game_platform_collect where type=405 and pid = 0 and cday = '.date('Ymd',$x);
            $tcRs = $PSys_PageviewRule->collectQuery($sql_tcclose);
            $tcclose_num .= intval($tcRs[0]["total"]).',';
            $sql_tcin = 'select sum(total) as total from rhc_game_platform_collect where type=404 and pid = 0 and cday = '.date('Ymd',$x);
            $tcinRs = $PSys_PageviewRule->collectQuery($sql_tcin);
            $tcin_num .= intval($tcinRs[0]["total"]).',';
            $sql_yxjj = 'select sum(total) as total from rhc_game_platform_collect where type=502 and pid = 0 and cday = '.date('Ymd',$x);
            $yxjjRs = $PSys_PageviewRule->collectQuery($sql_yxjj);
            $yxjj_num .= intval($yxjjRs[0]["total"]).',';
            $sql_hdzq = 'select sum(total) as total from rhc_game_platform_collect where type=501 and pid = 0 and cday = '.date('Ymd',$x);
            $hdzqRs = $PSys_PageviewRule->collectQuery($sql_hdzq);
            $hdzq_num .= intval($hdzqRs[0]["total"]).',';
        }
        
        $xAxis = trim($xAxis,",");
        $qzupdate_num = "{name:'强制升级',data:[".trim($qzupdate_num,",")."]},";
        $ljupdate_num = "{name:'立即升级',data:[".trim($ljupdate_num,",")."]},";
        $yhupdate_num = "{name:'以后再说',data:[".trim($yhupdate_num,",")."]},";
        $tcclose_num = "{name:'弹框关闭',data:[".trim($tcclose_num,",")."]},";
        $tcin_num = "{name:'弹框进入',data:[".trim($tcin_num,",")."]},";
        $hdzq_num = "{name:'点击活动专区',data:[".trim($hdzq_num,",")."]},";
        $yxjj_num = "{name:'点击游戏竞技',data:[".trim($yxjj_num,",")."]},";
        $series = $qzupdate_num.$ljupdate_num.$yhupdate_num.$tcclose_num.$tcin_num.$hdzq_num.$yxjj_num;
        $data['xAxis'] = $xAxis;
        $data['series'] = $series;
        die(json_encode($data));
    }
    
    /**
    *
    * @do ajax 获取浏览数据
    *
    * @appinfo public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return html
    *
    */
    public function ajaxAppinfoHtmlAction(){
        $start = reqstr("start","");
        $micstart = strtotime($start);
        $end = reqstr("end","");
        $micend = strtotime($end);
        $PSys_PageviewRule = new PSys_PageviewRule();
        $qzupdate_num = $ljupdate_num = $yhupdate_num = 0;
        $i = 0;
        for($x=$micstart; $x<=$micend; $x += 86400) {
            $xAxis = date('Y-m-d',$x);
            $result["allrow"][$i]['day'] = $xAxis;
            $sql_qzupdate = 'select sum(total) as total from rhc_game_platform_collect where type=401 and pid = 1 and cday = '.date('Ymd',$x);
            $qzRs = $PSys_PageviewRule->collectQuery($sql_qzupdate);
            $result["allrow"][$i]['qzupdate_num'] = intval($qzRs[0]["total"]);
            $qzupdate_num += intval($qzRs[0]["total"]).',';
            $sql_ljupdate = 'select sum(total) as total from rhc_game_platform_collect where type=401 and pid = 2 and cday = '.date('Ymd',$x);
            $ljRs = $PSys_PageviewRule->collectQuery($sql_ljupdate);
            $result["allrow"][$i]['ljupdate_num'] = intval($ljRs[0]["total"]);
            $ljupdate_num += intval($ljRs[0]["total"]).',';
            $sql_yhupdate = 'select sum(total) as total from rhc_game_platform_collect where type=401 and pid = 3 and cday = '.date('Ymd',$x);
            $yhRs = $PSys_PageviewRule->collectQuery($sql_yhupdate);
            $result["allrow"][$i]['yhupdate_num'] = intval($yhRs[0]["total"]);
            $yhupdate_num += intval($yhRs[0]["total"]).',';
            $sql_tcclose = 'select sum(total) as total from rhc_game_platform_collect where type=405 and pid = 0 and cday = '.date('Ymd',$x);
            $tcRs = $PSys_PageviewRule->collectQuery($sql_tcclose);
            $result["allrow"][$i]['tcclose_num'] = intval($tcRs[0]["total"]);
            $tcclose_num += intval($tcRs[0]["total"]).',';
            $sql_tcin = 'select sum(total) as total from rhc_game_platform_collect where type=404 and pid = 0 and cday = '.date('Ymd',$x);
            $tcinRs = $PSys_PageviewRule->collectQuery($sql_tcin);
            $result["allrow"][$i]['tcin_num'] = intval($tcinRs[0]["total"]);
            $tcin_num += intval($tcinRs[0]["total"]).',';
            $sql_yxjj = 'select sum(total) as total from rhc_game_platform_collect where type=502 and pid = 0 and cday = '.date('Ymd',$x);
            $yxjjRs = $PSys_PageviewRule->collectQuery($sql_yxjj);
            $result["allrow"][$i]['yxjj_num'] = intval($yxjjRs[0]["total"]);
            $yxjj_num += intval($yxjjRs[0]["total"]).',';
            $sql_hdzq = 'select sum(total) as total from rhc_game_platform_collect where type=501 and pid = 0 and cday = '.date('Ymd',$x);
            $hdzqRs = $PSys_PageviewRule->collectQuery($sql_hdzq);
            $result["allrow"][$i]['hdzq_num'] = intval($hdzqRs[0]["total"]);
            $hdzq_num += intval($hdzqRs[0]["total"]).',';
            $i++;
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("qzupdate_num",$qzupdate_num);
        $this->smarty->assign("ljupdate_num",$ljupdate_num);
        $this->smarty->assign("yhupdate_num",$yhupdate_num);
        $this->smarty->assign("tcclose_num",$tcclose_num);
        $this->smarty->assign("tcin_num",$tcin_num);
        $this->smarty->assign("adv1_num",$adv1_num);
        $this->smarty->assign("adv2_num",$adv2_num);
        $this->smarty->assign("adv3_num",$adv3_num);
        $this->smarty->assign("adv4_num",$adv4_num);
        $this->smarty->assign("adv5_num",$adv5_num);
        $this->smarty->assign("hdzq_num",$hdzq_num);
        $this->smarty->assign("yxjj_num",$yxjj_num);
		$this->forward = "ajaxAppinfo";
    }
    
    
}