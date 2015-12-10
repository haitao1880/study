<?php
/**
* Copyright(c) 2015
* 日    期:2015年07月07日
* 文 件 名:{dlclick}Controller.php
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
class dlclickController extends PSys_AbstractController{
    
	public function __construct() {
		parent::__construct();
        $PSys_GameModel = new PSys_GameModel();
        $order = "id ASC";
        $where['appcol'] = 1;
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
     * @dlclick public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function indexAction(){
        $this->smarty->assign("active","dlclick/index");
        $this->smarty->assign("active_menu","dlclick");
		$this->forward = "index";
        
	}
    
    /**
    *
    * @do ajax 获取日浏览数据
    *
    * @download public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return json
    *
    */
    public function ajaxDownloadJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $game = reqstr("game","");
        $PSys_downloadModel = new PSys_PageviewModel();
        $where["day_>="] = $start;
        $where["day_<="] = $end;
        $where["game"] = $game;
        $order = "day ASC";
        $result = $PSys_downloadModel->GetList($where, $order, 0, 0, "*","rhc_download_daily");
       
        $xAxis = $series = $total_download = $dl_completed = $dl_nocompleted = $dl_noinstalled = $dl_installed = $dl_confirmed = $dl_webinstalled = $dl_webopened = "";
        foreach($result["allrow"] as $k=>$v){
            $xAxis .= '"'.$v["day"].'",';
            $total_download .= $v["total_download"].',';
            $dl_completed .= $v["dl_completed"].',';
            $dl_nocompleted .= $v["dl_nocompleted"].',';
            $dl_noinstalled .= $v["dl_noinstalled"].',';
            $dl_installed .= $v["dl_installed"].',';
            $dl_webinstalled .= $v["dl_webinstalled"].',';
            $dl_webopened .= $v["dl_webopened"].',';
            $dl_confirmed .= $v["dl_confirmed"].',';
        }
        
        $xAxis = trim($xAxis,",");
        $total_download = "{name:'【web】点击下载',data:[".trim($total_download,",")."]},";
        $dl_webinstalled = "{name:'【web】点击安装',data:[".trim($dl_webinstalled,",")."]},";
        $dl_webopened = "{name:'【web】点击打开',data:[".trim($dl_webopened,",")."]},";
        $dl_confirmed = "{name:'确定下载',data:[".trim($dl_confirmed,",")."]},";
        $dl_completed = "{name:'下载完成',data:[".trim($dl_completed,",")."]},";
        $dl_nocompleted = "{name:'下载未完成',data:[".trim($dl_nocompleted,",")."]},";
        $dl_noinstalled = "{name:'下载未安装',data:[".trim($dl_noinstalled,",")."]},";
        $dl_installed = "{name:'下载并安装',data:[".trim($dl_installed,",")."]},";
        $series = $total_download.$dl_webinstalled.$dl_webopened.$dl_confirmed.$dl_completed.$dl_nocompleted.$dl_noinstalled.$dl_installed;
        $data['xAxis'] = $xAxis;
        $data['series'] = $series;
        die(json_encode($data));
    }
    
    /**
    *
    * @do ajax 获取浏览数据
    *
    * @download public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return html
    *
    */
    public function ajaxDownloadHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $game = reqstr("game","");
        
        $PSys_downloadModel = new PSys_PageviewModel();
        $where["day_>="] = $start;
        $where["day_<="] = $end;
        $where["game"] = $game;
        $order = "day ASC";
        $result = $PSys_downloadModel->GetList($where, $order, 0, 0, "*","rhc_download_daily");
        $total_download = $dl_completed = $dl_nocompleted = $dl_noinstalled = $dl_installed = $dl_webinstalled = $dl_webopened = $dl_confirmed = "";
        foreach($result["allrow"] as $k=>$v){
            $total_download += intval($v["total_download"]);
            $dl_completed += intval($v["dl_completed"]);
            $dl_nocompleted += intval($v["dl_nocompleted"]);
            $dl_noinstalled += intval($v["dl_noinstalled"]);
            $dl_installed += intval($v["dl_installed"]);
            $dl_webinstalled += intval($v["dl_webinstalled"]);
            $dl_webopened += intval($v["dl_webopened"]);
            $dl_confirmed += intval($v["dl_confirmed"]);
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("total_download",$total_download);
        $this->smarty->assign("dl_completed",$dl_completed);
        $this->smarty->assign("dl_nocompleted",$dl_nocompleted);
        $this->smarty->assign("dl_noinstalled",$dl_noinstalled);
        $this->smarty->assign("dl_installed",$dl_installed);
        $this->smarty->assign("dl_webinstalled",$dl_webinstalled);
        $this->smarty->assign("dl_webopened",$dl_webopened);
        $this->smarty->assign("dl_confirmed",$dl_confirmed);
		$this->forward = "ajaxDownload";
    }
    
    /**
     *
     * @do 展示周浏览数据
     *
     * @dlclick public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function weeklyAction(){
        $this->smarty->assign("active","dlclick/weekly");
        $this->smarty->assign("active_menu","dlclick");
		$this->forward = "weekly";
        
	}
    
    /**
    *
    * @do ajax 获取周浏览数据
    *
    * @dlclick public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return json
    *
    */
    public function ajaxWeeklyDownloadJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $game = reqstr("game","");
        $tempdate=date('Y-m-d',strtotime("$start Monday")); 
        $start=date('Y-m-d',strtotime("$tempdate -7 days"));
        $end=date('Y-m-d',strtotime("$end Sunday"));
        
        $PSys_downloadModel = new PSys_PageviewModel();               
        $where["startday_>="] = $start;
        $where["endday_<="] = $end;
        $where["game"] = $game;
        $order = "startday ASC";
        $result = $PSys_downloadModel->GetList($where, $order, 0, 0, "*","rhc_download_weekly");
        $xAxis = $series = $total_download = $dl_completed = $dl_nocompleted = $dl_noinstalled = $dl_installed = $dl_confirmed = $dl_webinstalled = $dl_webopened = "";
        foreach($result["allrow"] as $k=>$v){
            $xtemp = $v["startday"];
            $mictime = strtotime($v["startday"]);
            $endtemp = date('/m/d',strtotime("$xtemp Sunday"));
            $xAxis .= '"'.date('Y/m/d',$mictime).' - '.$endtemp.'",';
            $total_download .= $v["total_download"].',';
            $dl_completed .= $v["dl_completed"].',';
            $dl_nocompleted .= $v["dl_nocompleted"].',';
            $dl_noinstalled .= $v["dl_noinstalled"].',';
            $dl_installed .= $v["dl_installed"].',';
            $dl_webinstalled .= $v["dl_webinstalled"].',';
            $dl_webopened .= $v["dl_webopened"].',';
            $dl_confirmed .= $v["dl_confirmed"].',';
        }
        
        $xAxis = trim($xAxis,",");
        $total_download = "{name:'【web】点击下载',data:[".trim($total_download,",")."]},";
        $dl_webinstalled = "{name:'【web】点击安装',data:[".trim($dl_webinstalled,",")."]},";
        $dl_webopened = "{name:'【web】点击打开',data:[".trim($dl_webopened,",")."]},";
        $dl_confirmed = "{name:'确定下载',data:[".trim($dl_confirmed,",")."]},";
        $dl_completed = "{name:'下载完成',data:[".trim($dl_completed,",")."]},";
        $dl_nocompleted = "{name:'下载未完成',data:[".trim($dl_nocompleted,",")."]},";
        $dl_noinstalled = "{name:'下载未安装',data:[".trim($dl_noinstalled,",")."]},";
        $dl_installed = "{name:'下载并安装',data:[".trim($dl_installed,",")."]},";
        $series = $total_download.$dl_webinstalled.$dl_webopened.$dl_confirmed.$dl_completed.$dl_nocompleted.$dl_noinstalled.$dl_installed;
        $data['xAxis'] = $xAxis;
        $data['series'] = $series;
        
        die(json_encode($data));
    }
    
    /**
    *
    * @do ajax 获取周浏览数据
    *
    * @dlclick public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return html
    *
    */
    public function ajaxWeeklyDownloadHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $game = reqstr("game","");
        $tempdate=date('Y-m-d',strtotime("$start Monday")); 
        $start=date('Y-m-d',strtotime("$tempdate -7 days"));
        $end=date('Y-m-d',strtotime("$end Sunday"));
        
        $PSys_downloadModel = new PSys_PageviewModel();
        $where["startday_>="] = $start;
        $where["endday_<="] = $end;
        $where["game"] = $game;
        $order = "startday ASC";
        $result = $PSys_downloadModel->GetList($where, $order, 0, 0, "*","rhc_download_weekly");
        $total_download = $dl_completed = $dl_nocompleted = $dl_noinstalled = $dl_installed = $dl_webinstalled = $dl_webopened = $dl_confirmed = "";
        foreach($result["allrow"] as $k=>$v){
            $total_download += intval($v["total_download"]);
            $dl_completed += intval($v["dl_completed"]);
            $dl_nocompleted += intval($v["dl_nocompleted"]);
            $dl_noinstalled += intval($v["dl_noinstalled"]);
            $dl_installed += intval($v["dl_installed"]);
            $dl_webinstalled += intval($v["dl_webinstalled"]);
            $dl_webopened += intval($v["dl_webopened"]);
            $dl_confirmed += intval($v["dl_confirmed"]);
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("total_download",$total_download);
        $this->smarty->assign("dl_completed",$dl_completed);
        $this->smarty->assign("dl_nocompleted",$dl_nocompleted);
        $this->smarty->assign("dl_noinstalled",$dl_noinstalled);
        $this->smarty->assign("dl_installed",$dl_installed);
        $this->smarty->assign("dl_webinstalled",$dl_webinstalled);
        $this->smarty->assign("dl_webopened",$dl_webopened);
        $this->smarty->assign("dl_confirmed",$dl_confirmed);
		$this->forward = "ajaxWeeklyDownload";
    }
    
    /**
     *
     * @do 展示月浏览数据
     *
     * @dlclick public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function monthlyAction(){
        $this->smarty->assign("active","dlclick/monthly");
        $this->smarty->assign("active_menu","dlclick");
		$this->forward = "monthly";
        
	}
    
    /**
    *
    * @do ajax 获取月浏览数据
    *
    * @dlclick public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return json
    *
    */
    public function ajaxMonthlyDownloadJsonAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $game = reqstr("game","");
        $PSys_downloadModel = new PSys_PageviewModel();               
        $where["month_>="] = $start;
        $where["month_<="] = $end;
        $where["game"] = $game;
        $order = "month ASC";
        $result = $PSys_downloadModel->GetList($where, $order, 0, 0, "*","rhc_download_monthly");
        $xAxis = $series = $total_download = $dl_completed = $dl_nocompleted = $dl_noinstalled = $dl_installed = $dl_webinstalled = $dl_webopened = $dl_confirmed = "";
        foreach($result["allrow"] as $k=>$v){
            $xAxis .= '"'.$v["month"].'",';
            $total_download .= $v["total_download"].',';
            $dl_completed .= $v["dl_completed"].',';
            $dl_nocompleted .= $v["dl_nocompleted"].',';
            $dl_noinstalled .= $v["dl_noinstalled"].',';
            $dl_installed .= $v["dl_installed"].',';
            $dl_webinstalled .= $v["dl_webinstalled"].',';
            $dl_webopened .= $v["dl_webopened"].',';
            $dl_confirmed .= $v["dl_confirmed"].',';
        }
        
        $xAxis = trim($xAxis,",");
        $total_download = "{name:'【web】点击下载',data:[".trim($total_download,",")."]},";
        $dl_webinstalled = "{name:'【web】点击安装',data:[".trim($dl_webinstalled,",")."]},";
        $dl_webopened = "{name:'【web】点击打开',data:[".trim($dl_webopened,",")."]},";
        $dl_confirmed = "{name:'确定下载',data:[".trim($dl_confirmed,",")."]},";
        $dl_completed = "{name:'下载完成',data:[".trim($dl_completed,",")."]},";
        $dl_nocompleted = "{name:'下载未完成',data:[".trim($dl_nocompleted,",")."]},";
        $dl_noinstalled = "{name:'下载未安装',data:[".trim($dl_noinstalled,",")."]},";
        $dl_installed = "{name:'下载并安装',data:[".trim($dl_installed,",")."]},";
        $series = $total_download.$dl_webinstalled.$dl_webopened.$dl_confirmed.$dl_completed.$dl_nocompleted.$dl_noinstalled.$dl_installed;
        $data['xAxis'] = $xAxis;
        $data['series'] = $series;
        die(json_encode($data));
    }
    
    /**
    *
    * @do ajax 获取月浏览数据
    *
    * @dlclick public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return html
    *
    */
    public function ajaxMonthlyDownloadHtmlAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $game = reqstr("game","");
        $PSys_downloadModel = new PSys_PageviewModel();
        $where["month_>="] = $start;
        $where["month_<="] = $end;
        $where["game"] = $game;
        $order = "month ASC";
        $result = $PSys_downloadModel->GetList($where, $order, 0, 0, "*","rhc_download_monthly");
        $total_download = $dl_completed = $dl_nocompleted = $dl_noinstalled = $dl_installed = $dl_webinstalled = $dl_webopened = $dl_confirmed = "";
        foreach($result["allrow"] as $k=>$v){
            $total_download += intval($v["total_download"]);
            $dl_completed += intval($v["dl_completed"]);
            $dl_nocompleted += intval($v["dl_nocompleted"]);
            $dl_noinstalled += intval($v["dl_noinstalled"]);
            $dl_installed += intval($v["dl_installed"]);
            $dl_webinstalled += intval($v["dl_webinstalled"]);
            $dl_webopened += intval($v["dl_webopened"]);
            $dl_confirmed += intval($v["dl_confirmed"]);
        }
        $this->smarty->assign("data",$result["allrow"]);
        $this->smarty->assign("total_download",$total_download);
        $this->smarty->assign("dl_completed",$dl_completed);
        $this->smarty->assign("dl_nocompleted",$dl_nocompleted);
        $this->smarty->assign("dl_noinstalled",$dl_noinstalled);
        $this->smarty->assign("dl_installed",$dl_installed);
        $this->smarty->assign("dl_webinstalled",$dl_webinstalled);
        $this->smarty->assign("dl_webopened",$dl_webopened);
        $this->smarty->assign("dl_confirmed",$dl_confirmed);
		$this->forward = "ajaxMonthlyDownload";
    }
    
    /**
    *
    * @do ajax 获取数据
    *
    * @dlclick public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return html
    *
    */
    public function ajaxGetTotalAction(){
        $PSys_GameRule = new PSys_GameRule();
        $start = reqstr("start","");
        $end = reqstr("end","");
        $sql = 'SELECT count(id) as "dl_total" FROM rhc_game_platform WHERE type = 206 AND cday BETWEEN '.date('Ymd',strtotime($start)).' AND '.date('Ymd',strtotime($end));
        $rs = $PSys_GameRule->gameQuery($sql);
        echo '下载总数：'.$rs[0]['dl_total'];
        
	}
    
    /**
    *
    * @do ajax 获取数据
    *
    * @dlclick public 
    * @author jerry
    * @copyright rockhippo
    * @param -
    * @return html
    *
    */
    public function ajaxGetMonTotalAction(){
        $PSys_GameRule = new PSys_GameRule();
        $start = reqstr("start","");
        $end = reqstr("end","");
        $end = date('Y-m-t',strtotime($end));
        $sql = 'SELECT count(id) as "dl_total" FROM rhc_game_platform WHERE type = 206 AND cday BETWEEN '.date('Ymd',strtotime($start)).' AND '.date('Ymd',strtotime($end));
        $rs = $PSys_GameRule->gameQuery($sql);
        echo '下载总数：'.$rs[0]['dl_total'];
        
	}
    
}