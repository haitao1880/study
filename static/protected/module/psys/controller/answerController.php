<?php
/**
* Copyright(c) 2015
* 日    期:2015年07月07日
* 文 件 名:{answer}Controller.php
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
class answerController extends PSys_AbstractController{
    
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

        $this->smarty->assign("active","answer/index");
        $this->smarty->assign("active_menu","answer");
        $this->smarty->assign("sdate",date('Y-m-d',strtotime("-6 days")));
        $this->smarty->assign("edate",date('Y-m-d'));
		$this->forward = "index";
        
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
    public function ajaxAnswerHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $PSys_signModel = new PSys_PageviewModel();
        $where["day_>="] = $start;
        $where["day_<="] = $end;
        $order = "day ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_answer_daily");
        $total_01 = $total_02 = $total_03 = $total_04 = $total_05 = $total_06 = $total_07 = $total_08 = $total_09 = $total_10 = $total_11 = $total_start_times = $total_submit_times = '';
        $newarr_01 = $newarr_02 = $newarr_03 = $newarr_04 = $newarr_05 = $newarr_06 = $newarr_07 = $newarr_07 = $newarr_08 = $newarr_10 = $newarr_11 = array();
        foreach($result["allrow"] as $k=>$v){
            $total_start_times += $v['start_times'];
            $total_submit_times += $v['submit_times'];
            $answer_1 = $v['answer_1'];
            $answer_2 = $v['answer_2'];
            $answer_3 = $v['answer_3'];
            $answer_4 = $v['answer_4'];
            $answer_5 = $v['answer_5'];
            $answer_6 = $v['answer_6'];
            $answer_7 = $v['answer_7'];
            $answer_8 = $v['answer_8'];
            $answer_9 = $v['answer_9'];
            $answer_10 = $v['answer_10'];
            $answer_11 = $v['answer_11'];
            $temparr_01 = explode("-",$answer_1);
            foreach ($temparr_01 as $k => $v) {
                $newarr_01[$k] += $v;
            }
            $temparr_02 = explode("-",$answer_2);
            foreach ($temparr_02 as $k => $v) {
                $newarr_02[$k] += $v;
            }
            
            $temparr_03 = explode("-",$answer_3);
            foreach ($temparr_03 as $k => $v) {
                $newarr_03[$k] += $v;
            }
            $temparr_04 = explode("-",$answer_4);
            foreach ($temparr_04 as $k => $v) {
                $newarr_04[$k] += $v;
            }
            $temparr_05 = explode("-",$answer_5);
            foreach ($temparr_05 as $k => $v) {
                $newarr_05[$k] += $v;
            }
            $temparr_06 = explode("-",$answer_6);
            foreach ($temparr_06 as $k => $v) {
                $newarr_06[$k] += $v;
            }
            $temparr_07 = explode("-",$answer_7);
            foreach ($temparr_07 as $k => $v) {
                $newarr_07[$k] += $v;
            }
            $temparr_08 = explode("-",$answer_8);
            foreach ($temparr_08 as $k => $v) {
                $newarr_08[$k] += $v;
            }
            $temparr_09 = explode("-",$answer_9);
            foreach ($temparr_09 as $k => $v) {
                $newarr_09[$k] += $v;
            }
            $temparr_10 = explode("-",$answer_10);
            foreach ($temparr_10 as $k => $v) {
                $newarr_10[$k] += $v;
            }
            $temparr_11 = explode("-",$answer_11);
            foreach ($temparr_11 as $k => $v) {
                $newarr_11[$k] += $v;
            }
        }
        foreach ($newarr_01 as $v) {
            $total_01 .= $v.'-';
        }
        foreach ($newarr_02 as $v) {
            $total_02 .= $v.'-';
        }
        foreach ($newarr_03 as $v) {
            $total_03 .= $v.'-';
        }
        foreach ($newarr_04 as $v) {
            $total_04 .= $v.'-';
        }
        foreach ($newarr_05 as $v) {
            $total_05 .= $v.'-';
        }
        foreach ($newarr_06 as $v) {
            $total_06 .= $v.'-';
        }
        foreach ($newarr_07 as $v) {
            $total_07 .= $v.'-';
        }
        foreach ($newarr_08 as $v) {
            $total_08 .= $v.'-';
        }
        foreach ($newarr_09 as $v) {
            $total_09 .= $v.'-';
        }
        foreach ($newarr_10 as $v) {
            $total_10 .= $v.'-';
        }
        foreach ($newarr_11 as $v) {
            $total_11 .= $v.'-';
        }
        $PSys_PointRule = new PSys_PointRule();
        $sql = 'select DISTINCT answer from rhi_answer where ques_id = 11 and answer not in("A","B","C","D") and cday between '.date('Ymd',strtotime($start)).' and '.date('Ymd',strtotime($end));
        //$sql = 'select answer from rhi_survey where answer_12 not in("A","B","C","D") and cday between '.date('Ymd',strtotime($start)).' and '.date('Ymd',strtotime($end));
        $gamelist = $PSys_PointRule->answerSql($sql);
        $this->smarty->assign("gamelist",$gamelist);
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("total_01",trim($total_01,'-'));
        $this->smarty->assign("total_02",trim($total_02,'-'));
        $this->smarty->assign("total_03",trim($total_03,'-'));
        $this->smarty->assign("total_04",trim($total_04,'-'));
        $this->smarty->assign("total_05",trim($total_05,'-'));
        $this->smarty->assign("total_06",trim($total_06,'-'));
        $this->smarty->assign("total_07",trim($total_07,'-'));
        $this->smarty->assign("total_08",trim($total_08,'-'));
        $this->smarty->assign("total_09",trim($total_09,'-'));
        $this->smarty->assign("total_10",trim($total_10,'-'));
        $this->smarty->assign("total_11",trim($total_11,'-'));
        $this->smarty->assign("total_start_times",$total_start_times);
        $this->smarty->assign("total_submit_times",$total_submit_times);
		$this->forward = "ajaxAnswer";
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
        $this->smarty->assign("active","answer/weekly");
        $this->smarty->assign("active_menu","answer");
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
    * @return html
    *
    */
    public function ajaxWeeklyanswerHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $tempdate=date('Y-m-d',strtotime("$start Monday")); 
        $start=date('Y-m-d',strtotime("$tempdate -7 days"));
        $end=date('Y-m-d',strtotime("$end Sunday"));
        
        $PSys_signModel = new PSys_PageviewModel();
        $where["startday_>="] = $start;
        $where["endday_<="] = $end;
        $order = "startday ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_answer_weekly");
        $total_01 = $total_02 = $total_03 = $total_04 = $total_05 = $total_06 = $total_07 = $total_08 = $total_09 = $total_10 = $total_11 = $total_start_times = $total_submit_times = '';
        $newarr_01 = $newarr_02 = $newarr_03 = $newarr_04 = $newarr_05 = $newarr_06 = $newarr_07 = $newarr_07 = $newarr_08 = $newarr_10 = $newarr_11 = array();
        foreach($result["allrow"] as $k=>$v){
            $total_start_times += $v['start_times'];
            $total_submit_times += $v['submit_times'];
            $answer_1 = $v['answer_1'];
            $answer_2 = $v['answer_2'];
            $answer_3 = $v['answer_3'];
            $answer_4 = $v['answer_4'];
            $answer_5 = $v['answer_5'];
            $answer_6 = $v['answer_6'];
            $answer_7 = $v['answer_7'];
            $answer_8 = $v['answer_8'];
            $answer_9 = $v['answer_9'];
            $answer_10 = $v['answer_10'];
            $answer_11 = $v['answer_11'];
            $temparr_01 = explode("-",$answer_1);
            foreach ($temparr_01 as $k => $v) {
                $newarr_01[$k] += $v;
            }
            $temparr_02 = explode("-",$answer_2);
            foreach ($temparr_02 as $k => $v) {
                $newarr_02[$k] += $v;
            }
            
            $temparr_03 = explode("-",$answer_3);
            foreach ($temparr_03 as $k => $v) {
                $newarr_03[$k] += $v;
            }
            $temparr_04 = explode("-",$answer_4);
            foreach ($temparr_04 as $k => $v) {
                $newarr_04[$k] += $v;
            }
            $temparr_05 = explode("-",$answer_5);
            foreach ($temparr_05 as $k => $v) {
                $newarr_05[$k] += $v;
            }
            $temparr_06 = explode("-",$answer_6);
            foreach ($temparr_06 as $k => $v) {
                $newarr_06[$k] += $v;
            }
            $temparr_07 = explode("-",$answer_7);
            foreach ($temparr_07 as $k => $v) {
                $newarr_07[$k] += $v;
            }
            $temparr_08 = explode("-",$answer_8);
            foreach ($temparr_08 as $k => $v) {
                $newarr_08[$k] += $v;
            }
            $temparr_09 = explode("-",$answer_9);
            foreach ($temparr_09 as $k => $v) {
                $newarr_09[$k] += $v;
            }
            $temparr_10 = explode("-",$answer_10);
            foreach ($temparr_10 as $k => $v) {
                $newarr_10[$k] += $v;
            }
            $temparr_11 = explode("-",$answer_11);
            foreach ($temparr_11 as $k => $v) {
                $newarr_11[$k] += $v;
            }
        }
        foreach ($newarr_01 as $v) {
            $total_01 .= $v.'-';
        }
        foreach ($newarr_02 as $v) {
            $total_02 .= $v.'-';
        }
        foreach ($newarr_03 as $v) {
            $total_03 .= $v.'-';
        }
        foreach ($newarr_04 as $v) {
            $total_04 .= $v.'-';
        }
        foreach ($newarr_05 as $v) {
            $total_05 .= $v.'-';
        }
        foreach ($newarr_06 as $v) {
            $total_06 .= $v.'-';
        }
        foreach ($newarr_07 as $v) {
            $total_07 .= $v.'-';
        }
        foreach ($newarr_08 as $v) {
            $total_08 .= $v.'-';
        }
        foreach ($newarr_09 as $v) {
            $total_09 .= $v.'-';
        }
        foreach ($newarr_10 as $v) {
            $total_10 .= $v.'-';
        }
        foreach ($newarr_11 as $v) {
            $total_11 .= $v.'-';
        }
        $PSys_PointRule = new PSys_PointRule();
        $sql = 'select DISTINCT answer from rhi_answer where ques_id = 11 and answer not in("A","B","C","D") and cday between '.date('Ymd',strtotime($start)).' and '.date('Ymd',strtotime($end));
        //$sql = 'select answer from rhi_survey where answer_12 not in("A","B","C","D") and cday between '.date('Ymd',strtotime($start)).' and '.date('Ymd',strtotime($end));
        
        $gamelist = $PSys_PointRule->answerSql($sql);
        $this->smarty->assign("gamelist",$gamelist);
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("total_01",trim($total_01,'-'));
        $this->smarty->assign("total_02",trim($total_02,'-'));
        $this->smarty->assign("total_03",trim($total_03,'-'));
        $this->smarty->assign("total_04",trim($total_04,'-'));
        $this->smarty->assign("total_05",trim($total_05,'-'));
        $this->smarty->assign("total_06",trim($total_06,'-'));
        $this->smarty->assign("total_07",trim($total_07,'-'));
        $this->smarty->assign("total_08",trim($total_08,'-'));
        $this->smarty->assign("total_09",trim($total_09,'-'));
        $this->smarty->assign("total_10",trim($total_10,'-'));
        $this->smarty->assign("total_11",trim($total_11,'-'));
        $this->smarty->assign("total_start_times",$total_start_times);
        $this->smarty->assign("total_submit_times",$total_submit_times);
		$this->forward = "ajaxWeeklyAnswer";
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
        $this->smarty->assign("active","answer/monthly");
        $this->smarty->assign("active_menu","answer");
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
    * @return html
    *
    */
    public function ajaxMonthlyanswerHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        
        $PSys_signModel = new PSys_PageviewModel();
        $where["month_>="] = $start;
        $where["month_<="] = $end;
        $order = "month ASC";
        $result = $PSys_signModel->GetList($where, $order, 0, 0, "*","rhc_answer_monthly");
        $total_01 = $total_02 = $total_03 = $total_04 = $total_05 = $total_06 = $total_07 = $total_08 = $total_09 = $total_10 = $total_11 = $total_start_times = $total_submit_times = '';
        $newarr_01 = $newarr_02 = $newarr_03 = $newarr_04 = $newarr_05 = $newarr_06 = $newarr_07 = $newarr_07 = $newarr_08 = $newarr_10 = $newarr_11 = array();
        foreach($result["allrow"] as $k=>$v){
            $total_start_times += $v['start_times'];
            $total_submit_times += $v['submit_times'];
            $answer_1 = $v['answer_1'];
            $answer_2 = $v['answer_2'];
            $answer_3 = $v['answer_3'];
            $answer_4 = $v['answer_4'];
            $answer_5 = $v['answer_5'];
            $answer_6 = $v['answer_6'];
            $answer_7 = $v['answer_7'];
            $answer_8 = $v['answer_8'];
            $answer_9 = $v['answer_9'];
            $answer_10 = $v['answer_10'];
            $answer_11 = $v['answer_11'];
            $temparr_01 = explode("-",$answer_1);
            foreach ($temparr_01 as $k => $v) {
                $newarr_01[$k] += $v;
            }
            $temparr_02 = explode("-",$answer_2);
            foreach ($temparr_02 as $k => $v) {
                $newarr_02[$k] += $v;
            }
            
            $temparr_03 = explode("-",$answer_3);
            foreach ($temparr_03 as $k => $v) {
                $newarr_03[$k] += $v;
            }
            $temparr_04 = explode("-",$answer_4);
            foreach ($temparr_04 as $k => $v) {
                $newarr_04[$k] += $v;
            }
            $temparr_05 = explode("-",$answer_5);
            foreach ($temparr_05 as $k => $v) {
                $newarr_05[$k] += $v;
            }
            $temparr_06 = explode("-",$answer_6);
            foreach ($temparr_06 as $k => $v) {
                $newarr_06[$k] += $v;
            }
            $temparr_07 = explode("-",$answer_7);
            foreach ($temparr_07 as $k => $v) {
                $newarr_07[$k] += $v;
            }
            $temparr_08 = explode("-",$answer_8);
            foreach ($temparr_08 as $k => $v) {
                $newarr_08[$k] += $v;
            }
            $temparr_09 = explode("-",$answer_9);
            foreach ($temparr_09 as $k => $v) {
                $newarr_09[$k] += $v;
            }
            $temparr_10 = explode("-",$answer_10);
            foreach ($temparr_10 as $k => $v) {
                $newarr_10[$k] += $v;
            }
            $temparr_11 = explode("-",$answer_11);
            foreach ($temparr_11 as $k => $v) {
                $newarr_11[$k] += $v;
            }
        }
        foreach ($newarr_01 as $v) {
            $total_01 .= $v.'-';
        }
        foreach ($newarr_02 as $v) {
            $total_02 .= $v.'-';
        }
        foreach ($newarr_03 as $v) {
            $total_03 .= $v.'-';
        }
        foreach ($newarr_04 as $v) {
            $total_04 .= $v.'-';
        }
        foreach ($newarr_05 as $v) {
            $total_05 .= $v.'-';
        }
        foreach ($newarr_06 as $v) {
            $total_06 .= $v.'-';
        }
        foreach ($newarr_07 as $v) {
            $total_07 .= $v.'-';
        }
        foreach ($newarr_08 as $v) {
            $total_08 .= $v.'-';
        }
        foreach ($newarr_09 as $v) {
            $total_09 .= $v.'-';
        }
        foreach ($newarr_10 as $v) {
            $total_10 .= $v.'-';
        }
        foreach ($newarr_11 as $v) {
            $total_11 .= $v.'-';
        }
        $PSys_PointRule = new PSys_PointRule();
        $sql = 'select DISTINCT answer from rhi_answer where ques_id = 11 and answer not in("A","B","C","D") and cday between '.date('Ymd',strtotime($start.'-01')).' and '.date('Ymt',strtotime($end));
        //$sql = 'select answer from rhi_survey where answer_12 not in("A","B","C","D") and cday between '.date('Ymd',strtotime($start.'-01')).' and '.date('Ymt',strtotime($end));
        $gamelist = $PSys_PointRule->answerSql($sql);
        $this->smarty->assign("gamelist",$gamelist);
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("total_01",trim($total_01,'-'));
        $this->smarty->assign("total_02",trim($total_02,'-'));
        $this->smarty->assign("total_03",trim($total_03,'-'));
        $this->smarty->assign("total_04",trim($total_04,'-'));
        $this->smarty->assign("total_05",trim($total_05,'-'));
        $this->smarty->assign("total_06",trim($total_06,'-'));
        $this->smarty->assign("total_07",trim($total_07,'-'));
        $this->smarty->assign("total_08",trim($total_08,'-'));
        $this->smarty->assign("total_09",trim($total_09,'-'));
        $this->smarty->assign("total_10",trim($total_10,'-'));
        $this->smarty->assign("total_11",trim($total_11,'-'));
        $this->smarty->assign("total_start_times",$total_start_times);
        $this->smarty->assign("total_submit_times",$total_submit_times);
		$this->forward = "ajaxMonthlyAnswer";
    }
    
}