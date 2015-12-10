<?php
/**
* Copyright(c) 2015
* 日    期:2015年07月21日
* 文 件 名:{comprehensive}Controller.php
* 创建时间:15:27
* 字符编码:UTF-8
* 版本信息:v1.0
* 修改日期:none
* 最后版本:v1.0
* 创 建 者:Nick (nick@rockhippo.cn)
* 修 改 者:none
* 版本地址:none
* 摘    要: 综合统计控制器
*/
class comprehensiveController extends PSys_AbstractController{
    
	public function __construct() {
		parent::__construct();
        $this->smarty->assign("gameActive","");
        $this->smarty->assign("trainActive","active");
        $this->smarty->assign("gameHidden","hidden");
        $this->smarty->assign("trainHidden","");
        $this->smarty->assign("busHidden","hidden");
        $this->smarty->assign("busActive","");
        $this->smarty->assign("marketHidden","hidden");
        $this->smarty->assign("marketActive","");
	}
    
	/**
     *
     * @do 综合统计
     *
     * @comprehensive public 
     * @author Nick
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function statisAction(){	    
	   
        //汽车站id
        $busstation = array(11,12,13,14);
	   
        $where = array();
        $order = "id ASC";
        $field = "id,stationname";
	   
        $PSys_ComprehensiveModel = new PSys_ComprehensiveModel();
        $station = $PSys_ComprehensiveModel->GetList($where, $order, 0, 0, $field, "rha_station");
        $this->smarty->assign("station",$station['allrow']);
        $this->smarty->assign("busstation",$busstation);
        
        //左菜单锁定
        $this->smarty->assign("active_f","comprehensive/index");
        $this->smarty->assign("active","comprehensive/statis");
        
		$this->forward = "statis";
        
	}
    
    /**
    *
    * @do ajax 总uv数据
    *
    * @comprehensive public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function ajaxUVStatisAction(){
        
        $PSys_ComprehensiveModel = new PSys_ComprehensiveModel();
        $where = array();
        $order = "id ASC";
        $field = "id,stationname";
        $station = $PSys_ComprehensiveModel->GetList($where, $order, 0, 0, $field, "rha_station");
        
        $fastSearch = reqnum("fastSearch",0);//快速查询
        $dataStatusFS = reqnum("datastatusFS",1);//快速查询合并、比较筛选按钮 1为合并 2为比较  默认合并
        
        $data = array();
        
        $data['xv'] = array("访问WIFI","广告1","注册页","注册(登陆)","广告2","访问首页","下载火伴");
        
        switch($fastSearch){
            
            case 1://昨日
            
                $date = date("Y-m-d",strtotime("-1 day"));
                
                $group = "";
                $dbname = "rha_wifi_daily";
                $step = 1;
                $_step[1] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step);
                 
                $date = date("Y_m_d",strtotime("-1 day"));
                $group = "";
                $dbname = "rha_count_record";
                $step = 2;
                $_step[2] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step);
                
                $group = "";
                $dbname = "rha_count_record";
                $step = 3;
                $_step[3] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step);
                
                $group = "";
                $dbname = "rha_count_record";
                $step = 4;
                $_step[4] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step);
                
                $group = "";
                $dbname = "rha_count_record";
                $step = 5;
                $_step[5] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step);
                
                $group = "";
                $dbname = "rha_count_record";
                $step = 6;
                $_step[6] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step);
                
                $group = "";
                $dbname = "rha_count_record";
                $step = 7;
                $_step[7] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step);
                
                //合并的数据节点设置
                $data['data'][1]['name'] = '所有站点';
                $data['data'][1]['marker'] = "square";
                
                foreach($data['xv'] as $key=>$val){
                    if($_step[$key][0]['total'] != ''){
                        $m = " <span style='color:#BFBFBF'>(" . round($_step[$key+1][0]['total']/$_step[$key][0]['total']*100,2) ."%)</span>";
                    }
                    $data['data']['table'][1][] = array('date'=>$val,'total'=>$_step[$key+1][0]['total'].$m);
                    $data['data'][1]['data'][] = $_step[$key+1][0]['total'] != '' ? intval($_step[$key+1][0]['total']) : 0;
                }
                
                $data['data']['num'] = 1;
                $data['data']['key'] = array("流程","人数 (占上个流程的比例)");//table 栏目提示 array按顺序
                
                $stationChoice = array();
                $stationChoice[0] = 1;
                
                break;
            
            case 2://本周
            
                $date = date("Y-m-d");
                $bdate = date("Y-m-d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y")));
                $edate = date("Y-m-d",mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y"))); 

                if(date("N",strtotime($date)) == 1){
                    
                    $data['error'] = 'WEEKNODATA';
                    break;
                }
                
                $group = "";
                $dbname = "rha_wifi_daily";
                $step = 1;
                $_step[1] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step);
                 
                $bdate = date("Y_m_d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y")));
                $edate = date("Y_m_d",mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y")));  
                 
                $group = "";
                $dbname = "rha_count_record";
                $step = 2;
                $_step[2] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step);
                
                $group = "";
                $dbname = "rha_count_record";
                $step = 3;
                $_step[3] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step);
                
                $group = "";
                $dbname = "rha_count_record";
                $step = 4;
                $_step[4] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step);
                
                
                $group = "";
                $dbname = "rha_count_record";
                $step = 5;
                $_step[5] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step);
                
                $group = "";
                $dbname = "rha_count_record";
                $step = 6;
                $_step[6] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step);
                
                $group = "";
                $dbname = "rha_count_record";
                $step = 7;
                $_step[7] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step);
                
                //合并的数据节点设置
                $data['data'][1]['name'] = '所有站点';
                $data['data'][1]['marker'] = "square";
                
                foreach($data['xv'] as $key=>$val){
                    if($_step[$key][0]['total'] != ''){
                        $m = " <span style='color:#BFBFBF'>(" . round($_step[$key+1][0]['total']/$_step[$key][0]['total']*100,2) ."%)</span>";
                    }
                    $data['data']['table'][1][] = array('date'=>$val,'total'=>$_step[$key+1][0]['total'].$m);
                    $data['data'][1]['data'][] = $_step[$key+1][0]['total'] != '' ? intval($_step[$key+1][0]['total']) : 0;
                }
                
                $data['data']['num'] = 1;
                $data['data']['key'] = array("流程","人数");//table 栏目提示 array按顺序
            
                $stationChoice = array();
                $stationChoice[0] = 1;
            
                break;
            
            case 3://本月
            
                $bdate = date("Y-m-d",mktime(0, 0 , 0,date("m"),1,date("Y")));
                $edate = date("Y-m-d",mktime(23,59,59,date("m"),date("t"),date("Y"))); 
                
                if(date("j") == 1){
                    
                    $data['error'] = 'MONTHNODATA';
                    break;
                }
                
                $group = "";
                $dbname = "rha_wifi_daily";
                $step = 1;
                $_step[1] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step);
                 
                $bdate = date("Y_m_d",mktime(0, 0 , 0,date("m"),1,date("Y")));
                $edate = date("Y_m_d",mktime(23,59,59,date("m"),date("t"),date("Y"))); 
                 
                $group = "";
                $dbname = "rha_count_record";
                $step = 2;
                $_step[2] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step);
                
                $group = "";
                $dbname = "rha_count_record";
                $step = 3;
                $_step[3] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step);
                
                $group = "";
                $dbname = "rha_count_record";
                $step = 4;
                $_step[4] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step);
                
                
                $group = "";
                $dbname = "rha_count_record";
                $step = 5;
                $_step[5] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step);
                
                $group = "";
                $dbname = "rha_count_record";
                $step = 6;
                $_step[6] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step);
                
                $group = "";
                $dbname = "rha_count_record";
                $step = 7;
                $_step[7] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step);
                
                //合并的数据节点设置
                $data['data'][1]['name'] = '所有站点';
                $data['data'][1]['marker'] = "square";
                
                foreach($data['xv'] as $key=>$val){
                    if($_step[$key][0]['total'] != ''){
                        $m = " <span style='color:#BFBFBF'>(" . round($_step[$key+1][0]['total']/$_step[$key][0]['total']*100,2) ."%)</span>";
                    }
                    $data['data']['table'][1][] = array('date'=>$val,'total'=>$_step[$key+1][0]['total'].$m);
                    $data['data'][1]['data'][] = $_step[$key+1][0]['total'] != '' ? intval($_step[$key+1][0]['total']) : 0;
                }
                
                $data['data']['num'] = 1;
                $data['data']['key'] = array("流程","人数");//table 栏目提示 array按顺序
                
                $stationChoice = array();
                $stationChoice[0] = 1;
            
                break;
            
            default:
            
                $dateSearch = reqstr("dateSearch","");
                $datastatus = reqnum("datastatus",1);
                $stationC = reqarray("stationC");
                $screening['station'] = "";
                $stationChoice = array();
                    
                if($stationC == array()){//若为空则为不排除站点
                    
                    foreach($station['allrow'] as $key=>$val){
                        $screening['station'] .= '<a class="btn" id="close-station-'.$val['id'].'" onclick="screeningSpan(\'close-station\','.$val['id'].');">'.$val['stationname'].' <i class="icon-remove"></i><input type="hidden" name="stationCheck[]" value="'.$val['id'].'" /></a>&nbsp;&nbsp;&nbsp;';
                        $stationChoice[] = $val['id'];//选中的站点 
                    }
                
                }else{
                    
                    foreach($station['allrow'] as $key=>$val){
                        if(in_array($val['id'],$stationC)){
                            $screening['station'] .= '<a class="btn" id="close-station-'.$val['id'].'" onclick="screeningSpan(\'close-station\','.$val['id'].');">'.$val['stationname'].' <i class="icon-remove"></i><input type="hidden" name="stationCheck[]" value="'.$val['id'].'" /></a>&nbsp;&nbsp;&nbsp;';
                            $stationChoice[] = $val['id'];//选中的站点
                        } 
                    }
                    
                }
                
                switch($dateSearch){
                    
                    case "oneday":
                    
                        $date = reqstr("oneday");
                        
                        if($datastatus == 1){
                             
                            $group = "";
                            $dbname = "rha_wifi_daily";
                            $step = 1;
                            $_step[1] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step,$stationChoice);
                             
                            $date = str_replace("-","_",$date);
                            $group = "";
                            $dbname = "rha_count_record";
                            $step = 2;
                            $_step[2] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step,$stationChoice);
                            
                            $group = "";
                            $dbname = "rha_count_record";
                            $step = 3;
                            $_step[3] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step,$stationChoice);
                            
                            $group = "";
                            $dbname = "rha_count_record";
                            $step = 4;
                            $_step[4] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step,$stationChoice);
                            
                            $group = "";
                            $dbname = "rha_count_record";
                            $step = 5;
                            $_step[5] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step,$stationChoice);
                            
                            $group = "";
                            $dbname = "rha_count_record";
                            $step = 6;
                            $_step[6] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step,$stationChoice);
                            
                            $group = "";
                            $dbname = "rha_count_record";
                            $step = 7;
                            $_step[7] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step,$stationChoice);
                            
                            //合并的数据节点设置
                            $data['data'][1]['name'] = '所有站点';
                            $data['data'][1]['marker'] = "square";
                            
                            foreach($data['xv'] as $key=>$val){
                                if($_step[$key][0]['total'] != ''){
                                    $m = " <span style='color:#BFBFBF'>(" . round($_step[$key+1][0]['total']/$_step[$key][0]['total']*100,2) ."%)</span>";
                                }
                                $data['data']['table'][1][] = array('date'=>$val,'total'=>$_step[$key+1][0]['total'].$m);
                                $data['data'][1]['data'][] = $_step[$key+1][0]['total'] != '' ? intval($_step[$key+1][0]['total']) : 0;
                            }
                            
                            $data['data']['num'] = 1;
                            $data['data']['key'] = array("流程","人数 (占上个流程的比例)");//table 栏目提示 array按顺序
                            
                            $stationChoice = array();
                            $stationChoice[0] = 1;
                            
                        }else{
                            
                            $group = "station";
                            $dbname = "rha_wifi_daily";
                            $step = 1;
                            $_step[1] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step,$stationChoice);
                             
                            $date = str_replace("-","_",$date);
                            $group = "stationid";
                            $dbname = "rha_count_record";
                            $step = 2;
                            $_step[2] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step,$stationChoice);
                            
                            $group = "stationid";
                            $dbname = "rha_count_record";
                            $step = 3;
                            $_step[3] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step,$stationChoice);
                            
                            $group = "stationid";
                            $dbname = "rha_count_record";
                            $step = 4;
                            $_step[4] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step,$stationChoice);
                            
                            $group = "stationid";
                            $dbname = "rha_count_record";
                            $step = 5;
                            $_step[5] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step,$stationChoice);
                            
                            $group = "stationid";
                            $dbname = "rha_count_record";
                            $step = 6;
                            $_step[6] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step,$stationChoice);
                            
                            $group = "stationid";
                            $dbname = "rha_count_record";
                            $step = 7;
                            $_step[7] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step,$stationChoice);
                            
                            //合并的数据节点设置
                            foreach($station['allrow'] as $key=>$val){
                                if(in_array($val['id'],$stationChoice)){
                                    $data['data'][$key+1]['name'] = $val['stationname'];
                                    $data['data'][$key+1]['marker'] = "square";
                                }
                            }
                            
                            foreach($data['xv'] as $key=>$val){
                                
                                foreach($stationChoice as $key2=>$val2){
                                    
                                    if($_step[$key][0]['total'] != ''){
                                        $m = " <span style='color:#BFBFBF'>(" . round($_step[$key+1][$key2]['total']/$_step[$key][$key2]['total']*100,2) ."%)</span>";
                                    }
                                    $data['data']['table'][$val2][] = array('date'=>$val,'total'=>$_step[$key+1][$key2]['total'].$m);
                                    $data['data'][$val2]['data'][] = $_step[$key+1][$key2]['total'] != '' ? intval($_step[$key+1][$key2]['total']) : 0;
                                        
                                }
                                
                            }
                            
                            $data['data']['num'] = count($stationChoice);
                            $data['data']['key'] = array("流程","人数 (占上个流程的比例)");//table 栏目提示 array按顺序
                        
                        }
                        
                        $data['screening']['date'] = '<a class="btn" onclick="screeningSpan(\'close-date\');">单日/'.str_replace("_","-",$date).' <i class="icon-remove"></i><input type="hidden" name="dateSearch" value="oneday" /><input type="hidden" name="oneday" value="'.str_replace("_","-",$date).'" /></a>&nbsp;&nbsp;&nbsp;';
                        
                    break;
                    
                    case "manyday":
                    
                        if($datastatus == 1){
                        
                            $bdate = reqstr("bmanyday");
                            $edate = reqstr("emanyday");
                            
                            $group = "";
                            $dbname = "rha_wifi_daily";
                            $step = 1;
                            $_step[1] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step,$stationChoice);
                             
                            $bdate = str_replace("-","_",$bdate);
                            $edate = str_replace("-","_",$edate);
                            
                            $group = "";
                            $dbname = "rha_count_record";
                            $step = 2;
                            $_step[2] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step,$stationChoice);
                            
                            $group = "";
                            $dbname = "rha_count_record";
                            $step = 3;
                            $_step[3] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step,$stationChoice);
                            
                            $group = "";
                            $dbname = "rha_count_record";
                            $step = 4;
                            $_step[4] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step,$stationChoice);
                            
                            $group = "";
                            $dbname = "rha_count_record";
                            $step = 5;
                            $_step[5] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step,$stationChoice);
                            
                            $group = "";
                            $dbname = "rha_count_record";
                            $step = 6;
                            $_step[6] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step,$stationChoice);
                            
                            $group = "";
                            $dbname = "rha_count_record";
                            $step = 7;
                            $_step[7] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step,$stationChoice);
                            
                            //合并的数据节点设置
                            $data['data'][1]['name'] = '所有站点';
                            $data['data'][1]['marker'] = "square";
                            
                            foreach($data['xv'] as $key=>$val){
                                if($_step[$key][0]['total'] != ''){
                                    $m = " <span style='color:#BFBFBF'>(" . round($_step[$key+1][0]['total']/$_step[$key][0]['total']*100,2) ."%)</span>";
                                }
                                $data['data']['table'][1][] = array('date'=>$val,'total'=>$_step[$key+1][0]['total'].$m);
                                $data['data'][1]['data'][] = $_step[$key+1][0]['total'] != '' ? intval($_step[$key+1][0]['total']) : 0;
                            }
                            
                            $data['data']['num'] = 1;
                            $data['data']['key'] = array("流程","人数 (占上个流程的比例)");//table 栏目提示 array按顺序
                            
                            $stationChoice = array();
                            $stationChoice[0] = 1;
                            
                        }else{
                            
                            $bdate = reqstr("bmanyday");
                            $edate = reqstr("emanyday");
                            
                            $group = "station";
                            $dbname = "rha_wifi_daily";
                            $step = 1;
                            $_step[1] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step,$stationChoice);
                             
                            $bdate = str_replace("-","_",$bdate);
                            $edate = str_replace("-","_",$edate);
                            
                            $group = "stationid";
                            $dbname = "rha_count_record";
                            $step = 2;
                            $_step[2] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step,$stationChoice);
                            
                            $group = "stationid";
                            $dbname = "rha_count_record";
                            $step = 3;
                            $_step[3] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step,$stationChoice);
                            
                            $group = "stationid";
                            $dbname = "rha_count_record";
                            $step = 4;
                            $_step[4] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step,$stationChoice);
                            
                            $group = "stationid";
                            $dbname = "rha_count_record";
                            $step = 5;
                            $_step[5] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step,$stationChoice);
                            
                            $group = "stationid";
                            $dbname = "rha_count_record";
                            $step = 6;
                            $_step[6] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step,$stationChoice);
                            
                            $group = "stationid";
                            $dbname = "rha_count_record";
                            $step = 7;
                            $_step[7] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step,$stationChoice);
                            
                            //合并的数据节点设置
                            foreach($station['allrow'] as $key=>$val){
                                if(in_array($val['id'],$stationChoice)){
                                    $data['data'][$key+1]['name'] = $val['stationname'];
                                    $data['data'][$key+1]['marker'] = "square";
                                }
                            }
                            
                            foreach($data['xv'] as $key=>$val){
                                
                                foreach($stationChoice as $key2=>$val2){
                                    
                                    if($_step[$key][0]['total'] != ''){
                                        $m = " <span style='color:#BFBFBF'>(" . round($_step[$key+1][$key2]['total']/$_step[$key][$key2]['total']*100,2) ."%)</span>";
                                    }
                                    $data['data']['table'][$val2][] = array('date'=>$val,'total'=>$_step[$key+1][$key2]['total'].$m);
                                    $data['data'][$val2]['data'][] = $_step[$key+1][$key2]['total'] != '' ? intval($_step[$key+1][$key2]['total']) : 0;
                                        
                                }
                                
                            }
                            
                            $data['data']['num'] = count($stationChoice);
                            $data['data']['key'] = array("流程","人数 (占上个流程的比例)");//table 栏目提示 array按顺序
                            
                            
                        }
                        
                        $data['screening']['date'] = '<a class="btn" onclick="screeningSpan(\'close-date\');">多日/'.str_replace("_","-",$bdate) .' ~ '.str_replace("_","-",$edate).' <i class="icon-remove"></i><input type="hidden" name="dateSearch" value="manyday" /><input type="hidden" name="manyday" value="'.$date.'" /></a>&nbsp;&nbsp;&nbsp;';
                        
                    break;
                    
                }
                
                if(!$data['error']){                
                    $data['screening']['station'] = $screening['station'];
                }else{                                                            
                    $data['screening']['station'] = array();                                       
                }
        }
        
        
        
        if(!$data['error']){
            $data['data']['station'] = $stationChoice;
        }else{
            $data['data']['station'] = array();
        }
        
        die(json_encode($data));
        
    }
    
    /**
    *
    * @do ajax 访问 某时段 数据
    *
    * @comprehensive public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function ajaxUVChainDiagramAction(){
        
        $PSys_ComprehensiveModel = new PSys_ComprehensiveModel();
        
        $stationC = reqarray("stationC");
        $fastSearch = reqnum("fastSearch",0);
        $id = reqstr("id");
        
        //$station   
        $where = array();
        //$where['id_IN'] = $stationChoice;
        $order = "id ASC";
        $field = "id,stationname";
        $station = $PSys_ComprehensiveModel->GetList($where, $order, 0, 0, $field, "rha_station");
        
        if($stationC == array()){//若为空则为不排除站点
            
            foreach($station['allrow'] as $key=>$val){
                $stationChoice[] = $val['id'];//选中的站点 
                $stationArr[$val['id']] = $val['stationname'];
            }
        
        }else{
            
            foreach($station['allrow'] as $key=>$val){
                if(in_array($val['id'],$stationC)){
                    $stationChoice[] = $val['id'];//选中的站点
                    $stationArr[$val['id']] = $val['stationname'];
                } 
            }
            
        }
        
        //判断xv的值
        switch ($fastSearch){
            
            case 1:
            
                $dbname = "rha_aclog_hour";
                $result2 = $PSys_ComprehensiveModel->getDayChainDiagramDetail($id,$dbname);
                $result = $PSys_ComprehensiveModel->getDayChainDiagram($result2,$dbname,$stationChoice);
                
                //数据节点设置
                foreach($result as $key=>$val){
                    $data['data'][$key+1]['name'] = $stationArr[$val['stationid']];
                    $data['data'][$key+1]['marker'] = "square";
                    $data['data'][$key+1]['data'][] = intval($val['num']);
                    $data['data']['station'][] = $key+1;
                }
                
                //print_r($data['data']);
 
                $data['data']['num'] = count($result);
                $data['xv'] = array($result2[0]['hour'].' hour');
                $data['title'] = array($result2[0]['date'] . " " . $result2[0]['hour'] . 'h');
                
            
            break;
            
            case 2:
            
                $dbname = "rha_wifi_daily";
                $result2 = $PSys_ComprehensiveModel->getWeekChainDiagramDetail($id,$dbname);
                $result = $PSys_ComprehensiveModel->getWeekChainDiagram($result2,$dbname,$stationChoice);
                
                //数据节点设置
                foreach($result as $key=>$val){
                    $data['data'][$key+1]['name'] = $stationArr[$val['station']];
                    $data['data'][$key+1]['marker'] = "square";
                    $data['data'][$key+1]['data'][] = intval($val['total']);
                    $data['data']['station'][] = $key+1;
                }
                
                //print_r($data['data']);
 
                $data['data']['num'] = count($result);
                $data['xv'] = array($result2[0]['date']);
                $data['title'] = array($result2[0]['date']);
            
            break;
            
            case 3:
            
                $dbname = "rha_wifi_daily";
                $result2 = $PSys_ComprehensiveModel->getWeekChainDiagramDetail($id,$dbname);
                $result = $PSys_ComprehensiveModel->getWeekChainDiagram($result2,$dbname,$stationChoice);
                
                //数据节点设置
                foreach($result as $key=>$val){
                    $data['data'][$key+1]['name'] = $stationArr[$val['station']];
                    $data['data'][$key+1]['marker'] = "square";
                    $data['data'][$key+1]['data'][] = intval($val['total']);
                    $data['data']['station'][] = $key+1;
                }
                
                //print_r($data['data']);
 
                $data['data']['num'] = count($result);
                $data['xv'] = array($result2[0]['date']);
                $data['title'] = array($result2[0]['date']);
                
            break;
            
            default:
            
                $dateSearch = reqstr("dateSearch");
            
                switch ($dateSearch){
                    
                    case "oneday" :
                    
                        $dbname = "rha_aclog_hour";
                        $result2 = $PSys_ComprehensiveModel->getDayChainDiagramDetail($id,$dbname);
                        $result = $PSys_ComprehensiveModel->getDayChainDiagram($result2,$dbname,$stationChoice);
                        
                        //数据节点设置
                        foreach($result as $key=>$val){
                            $data['data'][$key+1]['name'] = $stationArr[$val['stationid']];
                            $data['data'][$key+1]['marker'] = "square";
                            $data['data'][$key+1]['data'][] = intval($val['num']);
                            $data['data']['station'][] = $key+1;
                        }
                        
                        //print_r($data['data']);
         
                        $data['data']['num'] = count($result);
                        $data['xv'] = array($result2[0]['hour'].' hour');
                        $data['title'] = array($result2[0]['date'] . " " . $result2[0]['hour'] . 'h');
      
                    break;
                    
                    case "manyday" :
                    
                        $dbname = "rha_wifi_daily";
                        $result2 = $PSys_ComprehensiveModel->getWeekChainDiagramDetail($id,$dbname);
                        $result = $PSys_ComprehensiveModel->getWeekChainDiagram($result2,$dbname,$stationChoice);
                        
                        //数据节点设置
                        foreach($result as $key=>$val){
                            $data['data'][$key+1]['name'] = $stationArr[$val['station']];
                            $data['data'][$key+1]['marker'] = "square";
                            $data['data'][$key+1]['data'][] = intval($val['total']);
                            $data['data']['station'][] = $key+1;
                        }
                        
                        //print_r($data['data']);
         
                        $data['data']['num'] = count($result);
                        $data['xv'] = array($result2[0]['date']);
                        $data['title'] = array($result2[0]['date']);
      
                    break;
                    
                    case "oneweek" :
                    
                        $dbname = "rha_wifi_daily";
                        $result2 = $PSys_ComprehensiveModel->getWeekChainDiagramDetail($id,$dbname);
                        $result = $PSys_ComprehensiveModel->getWeekChainDiagram($result2,$dbname,$stationChoice);
                        
                        //数据节点设置
                        foreach($result as $key=>$val){
                            $data['data'][$key+1]['name'] = $stationArr[$val['station']];
                            $data['data'][$key+1]['marker'] = "square";
                            $data['data'][$key+1]['data'][] = intval($val['total']);
                            $data['data']['station'][] = $key+1;
                        }
                        
                        //print_r($data['data']);
         
                        $data['data']['num'] = count($result);
                        $data['xv'] = array($result2[0]['date']);
                        $data['title'] = array($result2[0]['date']);
      
                    break;
                    
                    case "manyweek" :
                    
                        $dbname = "rha_wifi_daily";
                        $result2 = $PSys_ComprehensiveModel->getManyWeekChainDiagramDetail($id,$dbname);
                        $result = $PSys_ComprehensiveModel->getManyWeekChainDiagram($result2,$dbname,$stationChoice);
                        
                        //数据节点设置
                        foreach($result as $key=>$val){
                            $data['data'][$key+1]['name'] = $stationArr[$val['station']];
                            $data['data'][$key+1]['marker'] = "square";
                            $data['data'][$key+1]['data'][] = intval($val['total']);
                            $data['data']['station'][] = $key+1;
                        }
                        
                        //print_r($data['data']);
         
                        $data['data']['num'] = count($result);
                        $data['xv'] = array("");
                        $data['title'] = array($result2['bdate'].' 至 '.$result2['edate']);
      
                    break;
                    
                    case "onemonth" :
                    
                        $dbname = "rha_wifi_daily";
                        $result2 = $PSys_ComprehensiveModel->getOneMonthChainDiagramDetail($id,$dbname);
                        $result = $PSys_ComprehensiveModel->getOneMonthChainDiagram($result2,$dbname,$stationChoice);
                        
                        //数据节点设置
                        foreach($result as $key=>$val){
                            $data['data'][$key+1]['name'] = $stationArr[$val['station']];
                            $data['data'][$key+1]['marker'] = "square";
                            $data['data'][$key+1]['data'][] = intval($val['total']);
                            $data['data']['station'][] = $key+1;
                        }
                        
                        //print_r($data['data']);
         
                        $data['data']['num'] = count($result);
                        $data['xv'] = array($result2['date']);
                        $data['title'] = array($result2['date']);
      
                    break;
                    
                    case "manymonth" :
                    
                        $dbname = "rha_wifi_daily";
                        $result2 = $PSys_ComprehensiveModel->getManyMonthChainDiagramDetail($id,$dbname);
                        $result = $PSys_ComprehensiveModel->getManyMonthChainDiagram($result2,$dbname,$stationChoice);
                        
                        //数据节点设置
                        foreach($result as $key=>$val){
                            $data['data'][$key+1]['name'] = $stationArr[$val['station']];
                            $data['data'][$key+1]['marker'] = "square";
                            $data['data'][$key+1]['data'][] = intval($val['total']);
                            $data['data']['station'][] = $key+1;
                        }
                        
                        //print_r($data['data']);
         
                        $data['data']['num'] = count($result);
                        $data['xv'] = array("");
                        $data['title'] = array(date("Y-m",strtotime($result2['bdate'])));
      
                    break;
                    
                    
                }
            
            
            
        }
        
        die(json_encode($data));
        
    }
    
    /**
    *
    * @do 导出excel
    *
    * @comprehensive public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function ajaxOutputExcelAction(){
        
        $wordArr = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
        
        $PSys_ComprehensiveModel = new PSys_ComprehensiveModel();
        
        //站点数据
        $where = array();
        $order = "id ASC";
        $field = "id,stationname";
	   
        $PSys_ComprehensiveModel = new PSys_ComprehensiveModel();
        $station = $PSys_ComprehensiveModel->GetList($where, $order, 0, 0, $field, "rha_station");
        
        $fastSearch = reqnum("fastSearch",0);//快速查询
        $dataStatusFS = reqnum("datastatusFS",1);//快速查询合并、比较筛选按钮 1为合并 2为比较  默认合并
        
        $data['xv'] = array("访问WIFI","广告1","注册页","注册(登陆)","广告2","访问首页","下载火伴");
        
        switch($fastSearch){
            
            case 1://昨日
            
            $date = date("Y-m-d",strtotime("-1 day"));
            
            $group = "";
            $dbname = "rha_wifi_daily";
            $step = 1;
            $_step[1] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step);
             
            $date = date("Y_m_d",strtotime("-1 day"));
            $group = "";
            $dbname = "rha_count_record";
            $step = 2;
            $_step[2] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step);
            
            $group = "";
            $dbname = "rha_count_record";
            $step = 3;
            $_step[3] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step);
            
            $group = "";
            $dbname = "rha_count_record";
            $step = 4;
            $_step[4] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step);
            
            $group = "";
            $dbname = "rha_count_record";
            $step = 5;
            $_step[5] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step);
            
            $group = "";
            $dbname = "rha_count_record";
            $step = 6;
            $_step[6] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step);
            
            $group = "";
            $dbname = "rha_count_record";
            $step = 7;
            $_step[7] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step);
            
            $XPhpExcel = new XPhpExcel();
            $Creator = "statis.rockhippo.com";
            $LastModifiedBy = $Creator;
            $Title = '综合统计-流程综合统计';
            $Subject = '昨日-合并';
            $Description = '综合统计-流程综合统计(昨日-合并)';
            $Keywords = $Description;
            $Category = $Description;
            $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
            $XPhpExcel->setSheet(0,$Subject);
            
            $XPhpExcel->setValue("A1","流程");
            $XPhpExcel->setValue("B1","人数");
            
            $XPhpExcel->setWidth("A",20,2);
            $XPhpExcel->setWidth("B",20,2);
            
            foreach($data['xv'] as $key=>$val){
                if($_step[$key][0]['total'] != ''){
                    $m = "(" . round($_step[$key+1][0]['total']/$_step[$key][0]['total']*100,2) ."%)";
                }
                $XPhpExcel->setValue("A".($key+2),$val);
                $XPhpExcel->setValue("B".($key+2),strval($_step[$key+1][0]['total'].$m . " "));
                
            } 
        
            //清空输出缓存                    
            ob_clean();                    
            $XPhpExcel->output(str_replace("_","-",$date)."/".$Description);
            unset($XPhpExcel);//销毁
            
            break;
        
            case 2://本周
        
            $date = date("Y-m-d");
            $bdate = date("Y-m-d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y")));
            $edate = date("Y-m-d",mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y"))); 

            if(date("N",strtotime($date)) == 1){
                
                $data['error'] = 'WEEKNODATA';
                break;
            }
            
            $group = "";
            $dbname = "rha_wifi_daily";
            $step = 1;
            $_step[1] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step);
             
            $bdate = date("Y_m_d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y")));
            $edate = date("Y_m_d",mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y")));  
             
            $group = "";
            $dbname = "rha_count_record";
            $step = 2;
            $_step[2] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step);
            
            $group = "";
            $dbname = "rha_count_record";
            $step = 3;
            $_step[3] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step);
            
            $group = "";
            $dbname = "rha_count_record";
            $step = 4;
            $_step[4] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step);
            
            
            $group = "";
            $dbname = "rha_count_record";
            $step = 5;
            $_step[5] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step);
            
            $group = "";
            $dbname = "rha_count_record";
            $step = 6;
            $_step[6] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step);
            
            $group = "";
            $dbname = "rha_count_record";
            $step = 7;
            $_step[7] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step);
            
            $XPhpExcel = new XPhpExcel();
            $Creator = "statis.rockhippo.com";
            $LastModifiedBy = $Creator;
            $Title = '综合统计-流程综合统计';
            $Subject = '本周-合并';
            $Description = '综合统计-流程综合统计(本周-合并)';
            $Keywords = $Description;
            $Category = $Description;
            $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
            $XPhpExcel->setSheet(0,$Subject);
            
            $XPhpExcel->setValue("A1","流程");
            $XPhpExcel->setValue("B1","人数");
            
            $XPhpExcel->setWidth("A",20,2);
            $XPhpExcel->setWidth("B",20,2);
            
            foreach($data['xv'] as $key=>$val){
                if($_step[$key][0]['total'] != ''){
                    $m = "(" . round($_step[$key+1][0]['total']/$_step[$key][0]['total']*100,2) ."%)";
                }
                $XPhpExcel->setValue("A".($key+2),$val);
                $XPhpExcel->setValue("B".($key+2),strval($_step[$key+1][0]['total'].$m . " "));
                
            } 
        
            //清空输出缓存                    
            ob_clean();                    
            $XPhpExcel->output(str_replace("_","/",$bdate) . "~" . str_replace("_","/",$edate) ."/".$Description);
            unset($XPhpExcel);//销毁
            
            break;
        
        case 3://本月
        
            $bdate = date("Y-m-d",mktime(0, 0 , 0,date("m"),1,date("Y")));
            $edate = date("Y-m-d",mktime(23,59,59,date("m"),date("t"),date("Y"))); 
            
            $date = date("Y-m");
            
            $group = "";
            $dbname = "rha_wifi_daily";
            $step = 1;
            $_step[1] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step);
             
            $bdate = date("Y_m_d",mktime(0, 0 , 0,date("m"),1,date("Y")));
            $edate = date("Y_m_d",mktime(23,59,59,date("m"),date("t"),date("Y"))); 
             
            $group = "";
            $dbname = "rha_count_record";
            $step = 2;
            $_step[2] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step);
            
            $group = "";
            $dbname = "rha_count_record";
            $step = 3;
            $_step[3] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step);
            
            $group = "";
            $dbname = "rha_count_record";
            $step = 4;
            $_step[4] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step);
            
            
            $group = "";
            $dbname = "rha_count_record";
            $step = 5;
            $_step[5] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step);
            
            $group = "";
            $dbname = "rha_count_record";
            $step = 6;
            $_step[6] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step);
            
            $group = "";
            $dbname = "rha_count_record";
            $step = 7;
            $_step[7] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step);
            
            $XPhpExcel = new XPhpExcel();
            $Creator = "statis.rockhippo.com";
            $LastModifiedBy = $Creator;
            $Title = '综合统计-流程综合统计';
            $Subject = '本月-合并';
            $Description = '综合统计-流程综合统计(本月-合并)';
            $Keywords = $Description;
            $Category = $Description;
            $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
            $XPhpExcel->setSheet(0,$Subject);
            
            $XPhpExcel->setValue("A1","流程");
            $XPhpExcel->setValue("B1","人数");
            
            $XPhpExcel->setWidth("A",20,2);
            $XPhpExcel->setWidth("B",20,2);
            
            foreach($data['xv'] as $key=>$val){
                if($_step[$key][0]['total'] != ''){
                    $m = "(" . round($_step[$key+1][0]['total']/$_step[$key][0]['total']*100,2) ."%)";
                }
                $XPhpExcel->setValue("A".($key+2),$val);
                $XPhpExcel->setValue("B".($key+2),strval($_step[$key+1][0]['total'].$m . " "));
                
            } 
        
            //清空输出缓存                    
            ob_clean();                    
            $XPhpExcel->output($date ."/".$Description);
            unset($XPhpExcel);//销毁
    
            break;
        
        default:
        
            $dateSearch = reqstr("dateSearch","");
            $datastatus = reqnum("datastatus",1);
            $stationC = reqstr("stationC");
            $stationC = explode(",",$stationC);
            $stationChoice = array();
                
            if($stationC == array()){//若为空则为不排除站点
                
                foreach($station['allrow'] as $key=>$val){
                    $stationChoice[] = $val['id'];//选中的站点 
                }
            
            }else{
                
                foreach($station['allrow'] as $key=>$val){
                    if(in_array($val['id'],$stationC)){
                        $stationChoice[] = $val['id'];//选中的站点
                    } 
                }
                
            }
            
            switch($dateSearch){
                
                case "oneday":
                
                    $date = reqstr("oneday");
                        
                    $group = "";
                    $dbname = "rha_wifi_daily";
                    $step = 1;
                    $_step[1] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step,$stationChoice);
                     
                    $date = date("Y_m_d",strtotime($date));
                    $group = "";
                    $dbname = "rha_count_record";
                    $step = 2;
                    $_step[2] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step,$stationChoice);
                    
                    $group = "";
                    $dbname = "rha_count_record";
                    $step = 3;
                    $_step[3] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step,$stationChoice);
                    
                    $group = "";
                    $dbname = "rha_count_record";
                    $step = 4;
                    $_step[4] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step,$stationChoice);
                    
                    $group = "";
                    $dbname = "rha_count_record";
                    $step = 5;
                    $_step[5] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step,$stationChoice);
                    
                    $group = "";
                    $dbname = "rha_count_record";
                    $step = 6;
                    $_step[6] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step,$stationChoice);
                    
                    $group = "";
                    $dbname = "rha_count_record";
                    $step = 7;
                    $_step[7] = $PSys_ComprehensiveModel->getDay($date,$group,$dbname,$step,$stationChoice);
                    
                    $XPhpExcel = new XPhpExcel();
                    $Creator = "statis.rockhippo.com";
                    $LastModifiedBy = $Creator;
                    $Title = '综合统计-流程综合统计';
                    $Subject = '单日-合并';
                    $Description = '综合统计-流程综合统计(单日-合并)';
                    $Keywords = $Description;
                    $Category = $Description;
                    $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
                    $XPhpExcel->setSheet(0,$Subject);
                    
                    $XPhpExcel->setValue("A1","流程");
                    $XPhpExcel->setValue("B1","人数");
                    
                    $XPhpExcel->setWidth("A",20,2);
                    $XPhpExcel->setWidth("B",20,2);
                    
                    foreach($data['xv'] as $key=>$val){
                        if($_step[$key][0]['total'] != ''){
                            $m = "(" . round($_step[$key+1][0]['total']/$_step[$key][0]['total']*100,2) ."%)";
                        }
                        $XPhpExcel->setValue("A".($key+2),$val);
                        $XPhpExcel->setValue("B".($key+2),strval($_step[$key+1][0]['total'].$m . " "));
                        
                    } 
                
                    //清空输出缓存                    
                    ob_clean();                    
                    $XPhpExcel->output(str_replace("_","-",$date)."/".$Description);
                    unset($XPhpExcel);//销毁
                    
                break;
                
                case "manyday":
                
                    $bdate = reqstr("bmanyday");
                    $edate = reqstr("emanyday");
                    //计算天数差
                    $Days  = round((strtotime($edate)-strtotime($bdate))/3600/24);
    
                    $group = "";
                    $dbname = "rha_wifi_daily";
                    $step = 1;
                    $_step[1] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step,$stationChoice);
                     
                    $bdate = str_replace("-","_",$bdate);
                    $edate = str_replace("-","_",$edate);
                     
                    $group = "";
                    $dbname = "rha_count_record";
                    $step = 2;
                    $_step[2] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step,$stationChoice);
                    
                    $group = "";
                    $dbname = "rha_count_record";
                    $step = 3;
                    $_step[3] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step,$stationChoice);
                    
                    $group = "";
                    $dbname = "rha_count_record";
                    $step = 4;
                    $_step[4] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step,$stationChoice);
                    
                    $group = "";
                    $dbname = "rha_count_record";
                    $step = 5;
                    $_step[5] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step,$stationChoice);
                    
                    $group = "";
                    $dbname = "rha_count_record";
                    $step = 6;
                    $_step[6] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step,$stationChoice);
                    
                    $group = "";
                    $dbname = "rha_count_record";
                    $step = 7;
                    $_step[7] = $PSys_ComprehensiveModel->getDays($bdate,$edate,$group,$dbname,$step,$stationChoice);
                    
                    $XPhpExcel = new XPhpExcel();
                    $Creator = "statis.rockhippo.com";
                    $LastModifiedBy = $Creator;
                    $Title = '综合统计-流程综合统计';
                    $Subject = '多日-合并';
                    $Description = '综合统计-流程综合统计(多日-合并)';
                    $Keywords = $Description;
                    $Category = $Description;
                    $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
                    $XPhpExcel->setSheet(0,$Subject);
                    
                    $XPhpExcel->setValue("A1","流程");
                    $XPhpExcel->setValue("B1","人数");
                    
                    $XPhpExcel->setWidth("A",20,2);
                    $XPhpExcel->setWidth("B",20,2);
                    
                    foreach($data['xv'] as $key=>$val){
                        if($_step[$key][0]['total'] != ''){
                            $m = "(" . round($_step[$key+1][0]['total']/$_step[$key][0]['total']*100,2) ."%)";
                        }
                        $XPhpExcel->setValue("A".($key+2),$val);
                        $XPhpExcel->setValue("B".($key+2),strval($_step[$key+1][0]['total'].$m . " "));
                        
                    } 
                
                    //清空输出缓存                    
                    ob_clean();                    
                    $XPhpExcel->output(str_replace("_","/",$bdate) . "~" . str_replace("_","/",$edate) ."/".$Description);
                    unset($XPhpExcel);//销毁
            
                break;
                
            }
        
        }   
        
    }
	
}