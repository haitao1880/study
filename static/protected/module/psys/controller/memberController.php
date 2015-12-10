<?php
/**
* Copyright(c) 2015
* 日    期:2015年07月07日
* 文 件 名:{member}Controller.php
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
class memberController extends PSys_AbstractController{
    
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
     * @do 获取用户列表
     *
     * @member public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function indexAction(){
        $this->smarty->assign("active","member/index");
        $this->smarty->assign("active_menu","member");
		$this->forward = "index";
	}
    
    /**
    *
    * @do ajax 获取用户列表数据
    *
    * @member public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return html
    *
    */
    public function ajaxMemberHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $call = reqstr("call","");
        $PSys_memberModel = new PSys_MemberModel();
        $page = reqnum('page',1);
		$pagesize = 20;
        $where = array();
        $where["regfrom"] = 2;
        if($start){$where["regtime_>="] = strtotime($start);}
        if($end){$where["regtime_<="] = strtotime($end);}
        if($call){$where["phoneno_like"] = $call;}
        $order = "regtime DESC";
        $result = $PSys_memberModel->GetList($where, $order, $page, $pagesize, "username,nickname,phoneno,sex,regtime,regip,trainno");
        if($result['allnum']%$pagesize){
            $last = floor($result['allnum']/$pagesize) + 1;
        }else{
           $last = $result['allnum']/$pagesize;
        }
        if($page > 1){
            $this->smarty->assign("pre",$page - 1);
        }else{
            $this->smarty->assign("pre",1);
        } 
        if($page == $last){
            $this->smarty->assign("next",$last);
        }else{
            $this->smarty->assign("next",$page + 1);
        }
        $PSys_PointModel = new PSys_PointModel();
        foreach($result["allrow"] as $k=>$v){
            $cond['username'] = $v['username'];
            $pointsRs = $PSys_PointModel->GetOne($cond,'points','rhi_account');
            $result["allrow"][$k]['points'] = $pointsRs['points'];
            $result["allrow"][$k]['key'] = $k+1;
        }
        $this->smarty->assign("last",$last);
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("total_num",$result["allnum"]);
		$this->forward = "ajaxMember";
    }
    
    /**
    *
    * @do ajax 获取用户积分数据
    *
    * @member public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return html
    *
    */
    public function ajaxPointDetailsHtmlAction(){
        $username = reqstr("username","");
        $PSys_PointModel = new PSys_PointModel();
        $where = array();
        if($username){$where["username"] = $username;}
        $order = "id DESC";
        $result = $PSys_PointModel->GetList($where, $order, 0, 0, "points,type,optype,ctime","rhi_points");
        $typeArr = array(0=>'购买',1=>'签到',2=>'下载',3=>'礼包',4=>'火伴',5=>'话费',6=>'其它');
        $optypeArr = array(0=>'',1=>'+');
        foreach($result["allrow"] as $k=>$v){
            $tempType = $v["type"];
            $tempOptype = $v["optype"];
            $result["allrow"][$k]["type"] = $typeArr[$tempType];
            $result["allrow"][$k]["optype"] = $optypeArr[$tempOptype];
        }
        $this->smarty->assign("data",$result["allrow"]);
		$this->forward = "ajaxPointDetails";
    }
    
    /**
     *
     * @do 获取留存率
     *
     * @member public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function retrateAction(){
        $this->smarty->assign("active","member/retrate");
        $this->smarty->assign("active_menu","member");
        $this->smarty->assign("sdate",date('Y-m-d',strtotime("-7 days")));
        $this->smarty->assign("edate",date('Y-m-d',strtotime("-1 days")));
		$this->forward = "retrate";
	}
    
    /**
    *
    * @do ajax 获取用户留存率
    *
    * @member public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return html
    *
    */
    public function ajaxRateHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $start = strtotime($start);
        $end = strtotime($end);
        $PSys_memberModel = new PSys_MemberModel();
        $dayArr = array();
        $i = 0;
        for($start; $start<=$end; $start+=86400){
            $dayArr[$i]['day'] =  date('Y-m-d',$start);
            $rateRs = $PSys_memberModel->getRate($start);
            $dayArr[$i]['regnums'] =  $rateRs['new_user'];
            $dayArr[$i]['day1'] =  $rateRs['num1'];
            $dayArr[$i]['day3'] =  $rateRs['num3'];
            $dayArr[$i]['day7'] =  $rateRs['num7'];
            $dayArr[$i]['day30'] =  $rateRs['num30'];
            if($rateRs['new_user']){
                $dayArr[$i]['rate1'] =  round($rateRs['num1']/$rateRs['new_user']*100,2);
                $dayArr[$i]['rate3'] =  round($rateRs['num3']/$rateRs['new_user']*100,2);
                $dayArr[$i]['rate7'] =  round($rateRs['num7']/$rateRs['new_user']*100,2);
                $dayArr[$i]['rate30'] =  round($rateRs['num30']/$rateRs['new_user']*100,2);
            }else{
                $dayArr[$i]['rate1'] =  0;
                $dayArr[$i]['rate3'] =  0;
                $dayArr[$i]['rate7'] =  0;
                $dayArr[$i]['rate30'] =  0;
            }
            $i++;
        }
        $this->smarty->assign("data",$dayArr);
		$this->forward = "ajaxRateHtml";
    }
    
    /**
     *
     * @do 获取用户流失率
     *
     * @member public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function lossrateAction(){
        $this->smarty->assign("active","member/lossrate");
        $this->smarty->assign("active_menu","member");
        $this->smarty->assign("sdate",date('Y-m-d',strtotime("-7 days")));
        $this->smarty->assign("edate",date('Y-m-d',strtotime("-1 days")));
		$this->forward = "lossrate";
	}
    
    /**
    *
    * @do ajax 获取用户流失率
    *
    * @member public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return html
    *
    */
    public function ajaxLossrateHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $start = strtotime($start);
        $end = strtotime($end);
        $PSys_memberModel = new PSys_MemberModel();
        $dayArr = array();
        $i = 0;
        for($start; $start<=$end; $start+=86400){
            $dayArr[$i]['day'] =  date('Y-m-d',$start);
            $lossRs = $PSys_memberModel->getLossrate($start);
            $dayArr[$i]['wklossrate'] =  $lossRs['wklossrate'];
            $dayArr[$i]['monlossrate'] =  $lossRs['monlossrate'];
            $dayArr[$i]['weeknums'] =  $lossRs['weeknums'];
            $dayArr[$i]['weekloss'] =  $lossRs['weekloss'];
            $dayArr[$i]['monthnums'] =  $lossRs['monthnums'];
            $dayArr[$i]['monthloss'] =  $lossRs['monthloss'];
            $i++;
        }
        $this->smarty->assign("data",$dayArr);
		$this->forward = "ajaxLossrateHtml";
    }
    
    /**
     *
     * @do 展示浏览数据
     *
     * @member public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function dailyAction(){
        $this->smarty->assign("active","member/daily");
        $this->smarty->assign("active_menu","member");
        $this->smarty->assign("sdate",date('Y-m-d',strtotime("-6 days")));
        $this->smarty->assign("edate",date('Y-m-d'));
		$this->forward = "daily";
        
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
    public function ajaxDailyMemberJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $PSys_memberModel = new PSys_PageviewModel();
        $where["day_>="] = $start;
        $where["day_<="] = $end;
        $order = "day ASC";
        $result = $PSys_memberModel->GetList($where, $order, 0, 0, "*","rhc_member_daily");
        
        $xAxis = $series = $new_user = $active_user = $old_user = "";
        foreach($result["allrow"] as $k=>$v){
            $xAxis .= '"'.$v["day"].'",';
            $new_user .= $v["new_user"].',';
            $old_user .= $v["old_user"].',';
            $active_user .= $v["active_user"].',';
            $open_num .= $v["open_num"].',';
        }
        $xAxis = trim($xAxis,",");
        $new_user = "{name:'开启平台新用户',data:[".trim($new_user,",")."]},";
        $old_user = "{name:'开启平台老用户',data:[".trim($old_user,",")."]},";
        $active_user = "{name:'开启平台用户数',data:[".trim($active_user,",")."]},";
        $open_num = "{name:'开启平台次数',data:[".trim($open_num,",")."]},";
        $series = $new_user.$old_user.$active_user.$open_num;
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
    public function ajaxDailyMemberHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $PSys_memberModel = new PSys_PageviewModel();
        $where["day_>="] = $start;
        $where["day_<="] = $end;
        $order = "day ASC";
        $result = $PSys_memberModel->GetList($where, $order, 0, 0, "*","rhc_member_daily");
        $total_active_user = $total_new_user = $total_old_user = 0;
        foreach($result["allrow"] as $k=>$v){
            $total_new_user += intval($v["new_user"]);
            $total_old_user += intval($v["old_user"]);
            $total_active_user += intval($v["active_user"]);
            $total_open_num += intval($v["open_num"]);
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("total_new_user",$total_new_user);
        $this->smarty->assign("total_old_user",$total_old_user);
        $this->smarty->assign("total_active_user",$total_active_user);
        $this->smarty->assign("total_open_num",$total_open_num);
		$this->forward = "ajaxDailyMember";
    }
    
    /**
     *
     * @do 展示周浏览数据
     *
     * @member public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function weeklyAction(){
        $this->smarty->assign("active","member/weekly");
        $this->smarty->assign("active_menu","member");
        $this->smarty->assign("sdate",date('Y-m-d',strtotime("-14 days")));
        $this->smarty->assign("edate",date('Y-m-d'));
		$this->forward = "weekly";
        
	}
    
    /**
    *
    * @do ajax 获取周浏览数据
    *
    * @member public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return json
    *
    */
    public function ajaxWeeklymemberJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $tempdate=date('Y-m-d',strtotime("$start Monday")); 
        $start=date('Y-m-d',strtotime("$tempdate -7 days"));
        $end=date('Y-m-d',strtotime("$end Sunday"));
        
        $PSys_memberModel = new PSys_PageviewModel();               
        $where["startday_>="] = $start;
        $where["endday_<="] = $end;
        $order = "startday ASC";
        $result = $PSys_memberModel->GetList($where, $order, 0, 0, "*","rhc_member_weekly");
        $xAxis = $series = $new_user = $active_user = $old_user = "";
        foreach($result["allrow"] as $k=>$v){
            $xtemp = $v["startday"];
            $mictime = strtotime($v["startday"]);
            $endtemp = date('/m/d',strtotime("$xtemp Sunday"));
            $xAxis .= '"'.date('Y/m/d',$mictime).' - '.$endtemp.'",';
            $new_user .= $v["new_user"].',';
            $old_user .= $v["old_user"].',';
            $active_user .= $v["active_user"].',';
            $open_num .= $v["open_num"].',';
        }
        $xAxis = trim($xAxis,",");
        $new_user = "{name:'开启平台新用户',data:[".trim($new_user,",")."]},";
        $old_user = "{name:'开启平台老用户',data:[".trim($old_user,",")."]},";
        $active_user = "{name:'开启平台用户数',data:[".trim($active_user,",")."]},";
        $open_num = "{name:'开启平台次数',data:[".trim($open_num,",")."]},";
        $series = $new_user.$old_user.$active_user.$open_num;
        $data['xAxis'] = $xAxis;
        $data['series'] = $series;
        
        die(json_encode($data));
    }
    
    /**
    *
    * @do ajax 获取周浏览数据
    *
    * @member public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return html
    *
    */
    public function ajaxWeeklymemberHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $tempdate=date('Y-m-d',strtotime("$start Monday")); 
        $start=date('Y-m-d',strtotime("$tempdate -7 days"));
        $end=date('Y-m-d',strtotime("$end Sunday"));
        
        $PSys_memberModel = new PSys_PageviewModel();
        $where["startday_>="] = $start;
        $where["endday_<="] = $end;
        $order = "startday ASC";
        $result = $PSys_memberModel->GetList($where, $order, 0, 0, "*","rhc_member_weekly");
        $total_active_user = $total_new_user = $total_old_user = 0;
        foreach($result["allrow"] as $k=>$v){
            $total_new_user += intval($v["new_user"]);
            $total_old_user += intval($v["old_user"]);
            $total_active_user += intval($v["active_user"]);
            $total_open_num += intval($v["open_num"]);
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("total_new_user",$total_new_user);
        $this->smarty->assign("total_old_user",$total_old_user);
        $this->smarty->assign("total_active_user",$total_active_user);
        $this->smarty->assign("total_open_num",$total_open_num);
		$this->forward = "ajaxWeeklyMember";
    }
    
    /**
     *
     * @do 展示月浏览数据
     *
     * @member public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function monthlyAction(){
        $this->smarty->assign("active","member/monthly");
        $this->smarty->assign("active_menu","member");
        $this->smarty->assign("sdate",date("Y-m",mktime(0,0,0,date("m")-2,1,date("Y"))));
        $this->smarty->assign("edate",date("Y-m",mktime(0,0,0,date("m"),1,date("Y"))));
		$this->forward = "monthly";
        
	}
    
    /**
    *
    * @do ajax 获取月浏览数据
    *
    * @member public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return json
    *
    */
    public function ajaxMonthlymemberJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        
        $PSys_memberModel = new PSys_PageviewModel();               
        $where["month_>="] = $start;
        $where["month_<="] = $end;
        $order = "month ASC";
        $result = $PSys_memberModel->GetList($where, $order, 0, 0, "*","rhc_member_monthly");
        $xAxis = $series = $new_user = $active_user = $old_user = "";
        foreach($result["allrow"] as $k=>$v){
            $xAxis .= '"'.$v["month"].'",';
            $new_user .= $v["new_user"].',';
            $old_user .= $v["old_user"].',';
            $active_user .= $v["active_user"].',';
            $open_num .= $v["open_num"].',';
        }
        $xAxis = trim($xAxis,",");
        $new_user = "{name:'开启平台新用户',data:[".trim($new_user,",")."]},";
        $old_user = "{name:'开启平台老用户',data:[".trim($old_user,",")."]},";
        $active_user = "{name:'开启平台用户数',data:[".trim($active_user,",")."]},";
        $open_num = "{name:'开启平台次数',data:[".trim($open_num,",")."]},";
        $series = $new_user.$old_user.$active_user.$open_num;
        $data['xAxis'] = $xAxis;
        $data['series'] = $series;
        die(json_encode($data));
    }
    
    /**
    *
    * @do ajax 获取月浏览数据
    *
    * @member public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return html
    *
    */
    public function ajaxMonthlymemberHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        
        $PSys_memberModel = new PSys_PageviewModel();
        $where["month_>="] = $start;
        $where["month_<="] = $end;
        $order = "month ASC";
        $result = $PSys_memberModel->GetList($where, $order, 0, 0, "*","rhc_member_monthly");
     
        $total_active_user = $total_new_user = $total_old_user = 0;
        foreach($result["allrow"] as $k=>$v){
            $total_new_user += intval($v["new_user"]);
            $total_old_user += intval($v["old_user"]);
            $total_active_user += intval($v["active_user"]);
            $total_open_num += intval($v["open_num"]);
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("total_new_user",$total_new_user);
        $this->smarty->assign("total_old_user",$total_old_user);
        $this->smarty->assign("total_active_user",$total_active_user);
        $this->smarty->assign("total_open_num",$total_open_num);
		$this->forward = "ajaxMonthlyMember";
    }
    
}