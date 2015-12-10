<?php
/**
* Copyright(c) 2015
* 日    期:2015年07月20日
* 文 件 名:{registe}Controller.php
* 创建时间:12:06
* 字符编码:UTF-8
* 版本信息:v1.0
* 修改日期:none
* 最后版本:v1.0
* 创 建 者:Nick (nick@rockhippo.cn)
* 修 改 者:none
* 版本地址:none
* 摘    要: 注册统计控制器
*/
class registeController extends PSys_AbstractController{
    
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
     * @do 注册uv
     *
     * @access public 
     * @author Nick
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function uvAction(){	    
	   
        //汽车站id
        $busstation = array(11,12,13,14);
	   
        $where = array();
        $order = "id ASC";
        $field = "id,stationname";
	   
        $PSys_RegisteModel = new PSys_RegisteModel();
        $station = $PSys_RegisteModel->GetList($where, $order, 0, 0, $field, "rha_station");
        $this->smarty->assign("station",$station['allrow']);
        $this->smarty->assign("busstation",$busstation);
        
        //左菜单锁定
        $this->smarty->assign("active_f","registe/index");
        $this->smarty->assign("active","registe/uv");
        
		$this->forward = "uv";
        
	}
    
	/**
     *
     * @do 登陆首页成功数据
     *
     * @access public 
     * @author Nick
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function sindexAction(){	    
	   
        //汽车站id
        $busstation = array(11,12,13,14);
	   
        $where = array();
        $order = "id ASC";
        $field = "id,stationname";
	   
        $PSys_RegisteModel = new PSys_RegisteModel();
        $station = $PSys_RegisteModel->GetList($where, $order, 0, 0, $field, "rha_station");
        $this->smarty->assign("station",$station['allrow']);
        $this->smarty->assign("busstation",$busstation);
        
        //左菜单锁定
        $this->smarty->assign("active_f","registe/index");
        $this->smarty->assign("active","registe/sindex");
        
		$this->forward = "sindex";
        
	}
    
	/**
     *
     * @do 注册success
     *
     * @access public 
     * @author Nick
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function successAction(){	    
	   
        //汽车站id
        $busstation = array(11,12,13,14);
	   
        $where = array();
        $order = "id ASC";
        $field = "id,stationname";
	   
        $PSys_RegisteModel = new PSys_RegisteModel();
        $station = $PSys_RegisteModel->GetList($where, $order, 0, 0, $field, "rha_station");
        $this->smarty->assign("station",$station['allrow']);
        $this->smarty->assign("busstation",$busstation);
        
        //左菜单锁定
        $this->smarty->assign("active_f","registe/index");
        $this->smarty->assign("active","registe/success");
        
		$this->forward = "success";
        
	}
    
    /**
    *
    * @do ajax 注册数据 统计
    * @distinguish sql条件   
    * 
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function ajaxREGAction(){
        
        $PSys_RegisteModel = new PSys_RegisteModel();
        $where = array();
        $order = "id ASC";
        $field = "id,stationname";
        $station = $PSys_RegisteModel->GetList($where, $order, 0, 0, $field, "rha_station");
        
        $fastSearch = reqnum("fastSearch",0);//快速查询
        $dataStatusFS = reqnum("datastatusFS",1);//快速查询合并、比较筛选按钮 1为合并 2为比较  默认合并
        $distinguish = reqnum("distinguish",1);
        
        switch($fastSearch){
            
            case 2://本周
            
                $date = date("Y-m-d");
                $bdate = date("Y_m_d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y")));
                $edate = date("Y_m_d",mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y"))); 

                if(date("N",strtotime($date)) == 1){
                    $data = array();
                    $data['error'] = 'WEEKNODATA';
                    break;
                }

                $data = array();
                
                if($dataStatusFS == 1){
                    
                    $group = "date";
                    $dbname = "rha_count_record";
                
                    $result = $PSys_RegisteModel->getWeek($bdate,$edate,$group,$dbname,$distinguish);
                    
                    //x轴数据
                    $data['xv'] = array();
                    $data['data'][1]['name'] = "所有站点";
                    $data['data'][1]['marker'] = "square";
                    
                    foreach($result as $key=>$val){
                        $total[$val['date']] = $val['total'];
                        $data['data'][1]['id'][] = $val['id'];
                        $data['data']['table'][1][] = array('date'=>str_replace("_","-",$val['date']),'total'=>$val['total']);
                    }
                    
                    for($i=1;$i<=7;$i++){
                        $data['xv'][] = date("Y-m-d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+$i,date("Y")));
                        //$data['data'][1]['data'][] = intval($result[$i-1]['total']);
                        $data['data'][1]['data'][] = $total[date("Y_m_d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+$i,date("Y")))] ? intval($total[date("Y_m_d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+$i,date("Y")))]) : 0;
                    }
                    
                    $data['data']['num'] = 1;
                    $data['data']['key'] = array("日期","人数");//table 栏目提示 array按顺序
                }else{
                    
                    $group = "`date`,`stationid`";
                    $dbname = "rha_count_record";
            
                    $result = $PSys_RegisteModel->getWeek($bdate,$edate,$group,$dbname,$distinguish);
            
                    foreach($result as $key=>$val){
                        $total[$val['stationid']][$val['date']] = $val['total'];
                        //$data['data'][$val['station']]['data'][] = intval($val['total']);
                        $data['data'][$val['stationid']]['id'][] = $val['id'];
                        $data['data']['table'][$val['stationid']][] = array('date'=>str_replace("_","-",$val['date']),'total'=>$val['total']);
                    }
                    
                    //x轴数据
                    $data['xv'] = array();
                    //比较的数据节点设置
                    foreach($station['allrow'] as $key=>$val){
                        $data['data'][$val['id']]['name'] = $val['stationname'];
                        $data['data'][$val['id']]['marker'] = "square";
                        $data['data']['table'][$val['id']] = is_array($data['data']['table'][$val['id']]) ? $data['data']['table'][$val['id']] : array();
                        $stationChoice[] = $val['id'];
                    }
                    //print_r($total[1][date("Y-m-d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y")))]);
                    for($i=1;$i<=7;$i++){
                        $data['xv'][] = date("Y-m-d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+$i,date("Y")));
                        foreach($station['allrow'] as $key=>$val){
                            $data['data'][$val['id']]['data'][] = $total[$val['id']][date("Y_m_d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+$i,date("Y")))] ? intval($total[$val['id']][date("Y_m_d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+$i,date("Y")))]) : 0;
                        }
                    }
                    
                    $data['data']['num'] = count($station['allrow']);
                    $data['data']['key'] = array("日期","人数");//table 栏目提示 array按顺序
                }
            
                break;
            
            case 3://本月
            
                $bdate = date("Y_m_d",mktime(0, 0 , 0,date("m"),1,date("Y")));
                $edate = date("Y_m_d",mktime(23,59,59,date("m"),date("t"),date("Y"))); 
                
                if(date("j") == 1){
                    $data = array();
                    $data['error'] = 'MONTHNODATA';
                    break;
                }
                
                $data = array();
                
                if($dataStatusFS == 1){
                    
                    $group = "date";
                    $dbname = "rha_count_record";
                
                    $result = $PSys_RegisteModel->getWeek($bdate,$edate,$group,$dbname,$distinguish);
                    
                    //x轴数据
                    $data['xv'] = array();
                    $data['data'][1]['name'] = "所有站点";
                    $data['data'][1]['marker'] = "square";
                    //遍历数据
                    foreach($result as $key=>$val){
                        $total[$val['date']] = $val['total'];
                        $data['data']['table'][1][] = array('date'=>str_replace("_","-",$val['date']),'total'=>$val['total']);
                        $data['data'][1]['id'][] = $val['id'];
                    }
                    for($i=1;$i<=date("t");$i++){
                        $data['xv'][] = date("d",mktime(0, 0 , 0,date("m"),$i,date("Y")));
                        $data['data'][1]['data'][] = $total[ date("Y_m_d",mktime(0, 0 , 0,date("m"),$i,date("Y")))] ? intval($total[ date("Y_m_d",mktime(0, 0 , 0,date("m"),$i,date("Y")))]) : 0;
                    }
                    $data['data']['num'] = 1;
                    $data['data']['key'] = array("日期","人数");//table 栏目提示 array按顺序
                
                }else{
                    
                    $group = "`date`,`stationid`";
                    $dbname = "rha_count_record";
                
                    $result = $PSys_RegisteModel->getWeek($bdate,$edate,$group,$dbname,$distinguish);
                    
                    //x轴数据
                    $data['xv'] = array();
                    //比较的数据节点设置
                    foreach($station['allrow'] as $key=>$val){
                        $data['data'][$val['id']]['name'] = $val['stationname'];
                        $data['data'][$val['id']]['marker'] = "square";
                        $data['data']['table'][$val['id']] = array();
                        $stationChoice[] = $val['id'];
                    }
                    //遍历数据
                    foreach($result as $key=>$val){
                        $total[$val['date']][$val['stationid']] = $val['total'];
                        $data['data']['table'][$val['stationid']][] = array('date'=>str_replace("_","-",$val['date']),'total'=>$val['total']);
                        $data['data'][$val['stationid']]['id'][] = $val['id'];
                    }
                   
                    
                    for($i=1;$i<=date("t");$i++){
                        $data['xv'][] = date("d",mktime(0, 0 , 0,date("m"),$i,date("Y")));
                        //遍历数据
                        foreach($station['allrow'] as $key=>$val){
                            $data['data'][$val['id']]['data'][] = $total[date("Y_m_d",mktime(0, 0 , 0,date("m"),$i,date("Y")))][$val['id']] == '' ? 0 : intval($total[date("Y_m_d",mktime(0, 0 , 0,date("m"),$i,date("Y")))][$val['id']]);
                            
                        }
                    }
                    
                    $data['data']['num'] = count($station['allrow']);
                    $data['data']['key'] = array("日期","人数");//table 栏目提示 array按顺序
                }

            
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
                    
                    case "manyday":
                    
                        $bdate = reqstr("bmanyday");
                        $edate = reqstr("emanyday");
                        
                        //计算天数差
                        $Days  = round((strtotime($edate)-strtotime($bdate))/3600/24);
                        
                        $bdate = str_replace("-","_",$bdate);
                        $edate = str_replace("-","_",$edate); 
                        
                        $data = array();
                        
                        if($datastatus == 1){
                            
                            $group = "date";
                            $dbname = "rha_count_record";
                        
                            $result = $PSys_RegisteModel->getWeek($bdate,$edate,$group,$dbname,$distinguish,$stationChoice);
                            
                            //x轴数据
                            $data['xv'] = array();
                            $data['data'][1]['name'] = "所有站点";
                            $data['data'][1]['marker'] = "square";
                            
                            foreach($result as $key=>$val){
                                $total[$val['date']] = $val['total'];
                                $data['data']['table'][1][] = array('date'=>str_replace("_","-",$val['date']),'total'=>$val['total']);
                                $data['data'][1]['id'][] = $val['id'];
                            }
                            
                            for($i=0;$i<=$Days;$i++){
                                $day = $i;
                                $data['xv'][] = date("Y-m-d",strtotime(str_replace("_","-",$bdate)." +$day day"));
                                //$data['data'][1]['data'][] = intval($result[$i-1]['total']);
                                $data['data'][1]['data'][] = $total[date("Y_m_d",strtotime(str_replace("_","-",$bdate)." +$day day"))] ? intval($total[date("Y_m_d",strtotime(str_replace("_","-",$bdate)." +$day day"))]) : 0;
                            }
                            
                            $data['data']['num'] = 1;
                            
                            $stationChoice = array();
                            $stationChoice[0] = 1; //to do...
                            
                        }else{
                            
                            $group = "`date`,`stationid`";
                            $dbname = "rha_count_record";
                    
                            $result = $PSys_RegisteModel->getWeek($bdate,$edate,$group,$dbname,$distinguish,$stationChoice);
                            
                            foreach($result as $key=>$val){
                                $total[$val['stationid']][$val['date']] = $val['total'];
                                //$data['data'][$val['station']]['data'][] = intval($val['total']);
                                if(in_array($val['stationid'],$stationChoice)){
                                    $data['data']['table'][$val['stationid']][] = array('date'=>str_replace("_","-",$val['date']),'total'=>$val['total']);
                                }
                                $data['data'][$val['stationid']]['id'][] = $val['id'];
                            }
                            
                            //重过滤$station   
                            $where = array();
                            $where['id_IN'] = $stationChoice;
                            $order = "id ASC";
                            $field = "id,stationname";  
                            $station = $PSys_RegisteModel->GetList($where, $order, 0, 0, $field, "rha_station");
                            
                            //x轴数据
                            $data['xv'] = array();
                            //比较的数据节点设置
                            foreach($station['allrow'] as $key=>$val){
                                $data['data'][$val['id']]['name'] = $val['stationname'];
                                $data['data'][$val['id']]['marker'] = "square";
                                if(in_array($val['id'],$stationChoice)){
                                    $data['data']['table'][$val['id']] = is_array($data['data']['table'][$val['id']]) ? $data['data']['table'][$val['id']] : array();
                                }
                            }
                            //print_r($total[1][date("Y-m-d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y")))]);
                            for($i=0;$i<=$Days;$i++){
                                $day = $i;
                                $data['xv'][] = date("Y-m-d",strtotime(str_replace("_","-",$bdate)." +$day day"));
                                //$data['data'][1]['data'][] = intval($result[$i-1]['total']);
                                foreach($station['allrow'] as $key=>$val){
                                    $data['data'][$val['id']]['data'][] = $total[$val['id']][date("Y_m_d",strtotime(str_replace("_","-",$bdate)." +$day day"))] ? intval($total[$val['id']][date("Y_m_d",strtotime(str_replace("_","-",$bdate)." +$day day"))]) : 0;
                                }
                            }
                            
                            $data['data']['num'] = count($station['allrow']);
                            
                        }
                        
                        $data['data']['key'] = array("日期","人数");//table 栏目提示 array按顺序
                        $data['screening']['date'] = '<a class="btn" onclick="screeningSpan(\'close-date\');">多日/'.str_replace("_","-",$bdate) .' ~ '.str_replace("_","-",$edate).' <i class="icon-remove"></i><input type="hidden" name="dateSearch" value="manyday" /><input type="hidden" name="manyday" value="'.$date.'" /></a>&nbsp;&nbsp;&nbsp;';
                        
                    break;
                    
                    case "oneweek":
               
                        $day = reqstr("oneweek");
                        $edate=date('Y_m_d',strtotime("$day Sunday")); 
                        $bdate=date('Y_m_d',strtotime(str_replace("_","-",$edate)." -6 days"));
                        //计算天数差
                        $Days  = round((strtotime(str_replace("_","-",$edate))-strtotime(str_replace("_","-",$bdate)))/3600/24);
                        
                        if(date("N",strtotime(str_replace("_","-",$bdate))) == 1 && $bdate == date("Y_m_d")){
                            $data = array();
                            $data['error'] = 'WEEKNODATA';
                            break;
                        }

                        $data = array();
                        
                        if($datastatus == 1){
                            
                            $group = "date";
                            $dbname = "rha_count_record";
                        
                            $result = $PSys_RegisteModel->getWeek($bdate,$edate,$group,$dbname,$distinguish,$stationChoice);
                            
                            //x轴数据
                            $data['xv'] = array();
                            $data['data'][1]['name'] = "所有站点";
                            $data['data'][1]['marker'] = "square";
                            
                            foreach($result as $key=>$val){
                                $total[$val['date']] = $val['total'];
                                $data['data']['table'][1][] = array('date'=>str_replace("_","-",$val['date']),'total'=>$val['total']);
                                $data['data'][1]['id'][] = $val['id'];
                            }
                            for($i=0;$i<=$Days;$i++){
                                $day = $i;
                                $data['xv'][] = date("Y-m-d",strtotime(str_replace("_","-",$bdate)." +$day day"));
                                //$data['data'][1]['data'][] = intval($result[$i-1]['total']);
                                $data['data'][1]['data'][] = $total[date("Y_m_d",strtotime(str_replace("_","-",$bdate)." +$day day"))] ? intval($total[date("Y_m_d",strtotime(str_replace("_","-",$bdate)." +$day day"))]) : 0;
                                
                            }
                            
                            $data['data']['num'] = 1;
                            
                            $stationChoice = array();
                            $stationChoice[0] = 1; //to do...
                            
                        }else{
                            
                            $group = "`date`,`stationid`";
                            $dbname = "rha_count_record";
                    
                            $result = $PSys_RegisteModel->getWeek($bdate,$edate,$group,$dbname,$distinguish,$stationChoice);
                            
                            foreach($result as $key=>$val){
                                $total[$val['stationid']][$val['date']] = $val['total'];
                                //$data['data'][$val['station']]['data'][] = intval($val['total']);
                                $data['data']['table'][$val['stationid']][] = array('date'=>str_replace("_","-",$val['date']),'total'=>$val['total']);
                                $data['data'][$val['stationid']]['id'][] = $val['id'];
                            }
                            
                            //重过滤$station   
                            $where = array();
                            $where['id_IN'] = $stationChoice;
                            $order = "id ASC";
                            $field = "id,stationname";  
                            $station = $PSys_RegisteModel->GetList($where, $order, 0, 0, $field, "rha_station");
                            
                            //x轴数据
                            $data['xv'] = array();
                            //比较的数据节点设置
                            foreach($station['allrow'] as $key=>$val){
                                $data['data'][$val['id']]['name'] = $val['stationname'];
                                $data['data'][$val['id']]['marker'] = "square";
                                if(in_array($val['id'],$stationChoice)){
                                    $data['data']['table'][$val['id']] = is_array($data['data']['table'][$val['id']]) ? $data['data']['table'][$val['id']] : array();
                                }
                            }
                            //print_r($total[1][date("Y-m-d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y")))]);
                            for($i=0;$i<=$Days;$i++){
                                $day = $i;
                                $data['xv'][] = date("Y-m-d",strtotime(str_replace("_","-",$bdate)." +$day day"));
                                //$data['data'][1]['data'][] = intval($result[$i-1]['total']);
                                foreach($station['allrow'] as $key=>$val){
                                    $data['data'][$val['id']]['data'][] = $total[$val['id']][date("Y_m_d",strtotime(str_replace("_","-",$bdate)." +$day day"))] ? intval($total[$val['id']][date("Y_m_d",strtotime(str_replace("_","-",$bdate)." +$day day"))]) : 0;
                                }
                            }
                            
                            $data['data']['num'] = count($station['allrow']);

                        }
                        
                        $data['data']['key'] = array("日期","人数");//table 栏目提示 array按顺序
                        $data['screening']['date'] = '<a class="btn" onclick="screeningSpan(\'close-date\');">单周/'.str_replace("_","-",$bdate) .' ~ '.str_replace("_","-",$edate).' <i class="icon-remove"></i><input type="hidden" name="dateSearch" value="oneweek" /><input type="hidden" name="oneweek" value="'.$date.'" /></a>&nbsp;&nbsp;&nbsp;';
                        
                    break;
                    
                    case "manyweek":
                    
                        $bday = reqstr("bmanyweek");
                        $edate2=date('Y-m-d',strtotime("$bday Sunday")); 
                        $bdate=date('Y_m_d',strtotime("$edate2 -6 days"));
                        
                        $eday = reqstr("emanyweek");
                        $edate=date('Y_m_d',strtotime("$eday Sunday")); 
                        $bdate2=date('Y-m-d',strtotime(str_replace("_"."-",$edate)." -6 days"));
                        
                        //计算周数差
                        $Weeks  = round((strtotime(str_replace("_","-",$edate))-strtotime(str_replace("_","-",$bdate)))/3600/24/7);

                        $data = array();
                        
                        if($datastatus == 1){
                            
                            $group = "date";
                            $dbname = "rha_count_record";
                        
                            $result = $PSys_RegisteModel->getWeek($bdate,$edate,$group,$dbname,$distinguish,$stationChoice);
                            
                            //x轴数据
                            $data['xv'] = array();
                            $data['data'][1]['name'] = "所有站点";
                            $data['data'][1]['marker'] = "square";
                            
                            foreach($result as $key=>$val){
                                $total[$val['date']] = $val['total'];
                            }

                            for($i=0;$i<$Weeks;$i++){
                                
                                for($j=1;$j<=7;$j++){
                                    $day = $i * 7 + $j - 1;
                                    if($j == 1){
                                        $data['xv'][$i] = date("Y/m/d",strtotime(str_replace("_","-",$bdate)." +$day day"));
                                    }
                                    if($j == 7){
                                        $data['xv'][$i] .= ' - ' . date("m/d",strtotime(str_replace("_","-",$bdate)." +$day day"));
                                    }
                                    //$data['data'][1]['data'][] = intval($result[$i-1]['total']);
                                    $data['data'][1]['data'][$i] += $total[date("Y_m_d",strtotime(str_replace("_","-",$bdate)." +$day day"))] ? intval($total[date("Y_m_d",strtotime(str_replace("_","-",$bdate)." +$day day"))]) : 0;
                                }
                                
                                $data['data'][1]['id'][] = $result[$i*7]['date'];
                                $data['data']['table'][1][] = array('date'=>$data['xv'][$i],'total'=>$data['data'][1]['data'][$i]);
                            }
                            
                            $data['data']['num'] = 1;
                            
                            $stationChoice = array();
                            $stationChoice[0] = 1; //to do...
                            
                        }else{
                            
                            $group = "`date`,`stationid`";
                            $dbname = "rha_count_record";
                    
                            $result = $PSys_RegisteModel->getWeek($bdate,$edate,$group,$dbname,$distinguish,$stationChoice);
                            
                            foreach($result as $key=>$val){
                                $total[$val['stationid']][$val['date']] = $val['total'];
                            }
                            
                            //重过滤$station   
                            $where = array();
                            $where['id_IN'] = $stationChoice;
                            $order = "id ASC";
                            $field = "id,stationname";  
                            $station = $PSys_RegisteModel->GetList($where, $order, 0, 0, $field, "rha_station");
                            
                            //x轴数据
                            $data['xv'] = array();
                            //比较的数据节点设置
                            foreach($station['allrow'] as $key=>$val){
                                $data['data'][$val['id']]['name'] = $val['stationname'];
                                $data['data'][$val['id']]['marker'] = "square";
                                if(in_array($val['id'],$stationChoice)){
                                    $data['data']['table'][$val['id']] = is_array($data['data']['table'][$val['id']]) ? $data['data']['table'][$val['id']] : array();
                                }
                            }
                            //print_r($total[1][date("Y-m-d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y")))]);
                            for($i=0;$i<$Weeks;$i++){
                                
                                for($j=1;$j<=7;$j++){
                                    $day = $i * 7 + $j - 1;
                                    if($j == 1){
                                        $data['xv'][$i] = date("Y/m/d",strtotime(str_replace("_","-",$bdate)." +$day day"));
                                    }
                                    if($j == 7){
                                        $data['xv'][$i] .= ' - ' . date("m/d",strtotime(str_replace("_","-",$bdate)." +$day day"));
                                    }
                                    //to do ..... 2周还是读天数表  see you tomorrow
                                    //$data['data'][1]['data'][] = intval($result[$i-1]['total']);
                                    foreach($station['allrow'] as $key=>$val){
                                        $data['data'][$val['id']]['data'][$i] += $total[$val['id']][date("Y_m_d",strtotime(str_replace("_","-",$bdate)." +$day day"))] ? intval($total[$val['id']][date("Y_m_d",strtotime(str_replace("_","-",$bdate)." +$day day"))]) : 0;
                                        $data['data'][$val['id']]['id'][$i] = $result[$i*7*count($station['allrow'])]['date'];
                                        $data['data']['table'][$val['id']][$i]['date'] = $data['xv'][$i];
                                        $data['data']['table'][$val['id']][$i]['total'] += intval($total[$val['id']][date("Y_m_d",strtotime(str_replace("_","-",$bdate)." +$day day"))]);
                                    }
                                    
                                }
                                
                            }
                            
                            $data['data']['num'] = count($station['allrow']);
                            
                        }
                        $data['data']['key'] = array("日期","人数");//table 栏目提示 array按顺序
                        $data['screening']['date'] = '<a class="btn" onclick="screeningSpan(\'close-date\');">多周/'.str_replace("_","-",$bdate) .' ~ '.str_replace("_","-",$edate).' <i class="icon-remove"></i><input type="hidden" name="dateSearch" value="manyweek" /><input type="hidden" name="manyweek" value="'.$date.'" /></a>&nbsp;&nbsp;&nbsp;';
                        
                    break;
                    
                    case "onemonth":
                    
                        $date = reqstr("onemonth");
                        $bdate = date("Y_m_01",strtotime($date));
                        $edate = date("Y_m_d",strtotime(str_replace("_","-",$bdate)." +1 month -1 day"));
                        
                        $data = array();
                        
                        if($datastatus == 1){
                            
                            $group = "date";
                            $dbname = "rha_count_record";
                        
                            $result = $PSys_RegisteModel->getWeek($bdate,$edate,$group,$dbname,$distinguish,$stationChoice);
                            
                            //x轴数据
                            $data['xv'] = array();
                            $data['data'][1]['name'] = "所有站点";
                            $data['data'][1]['marker'] = "square";
                            //遍历数据
                            foreach($result as $key=>$val){
                                $total[$val['date']] = $val['total'];
                                $data['data']['table'][1][] = array('date'=>str_replace("_","-",$val['date']),'total'=>$val['total']);
                                $data['data'][1]['id'][] = $val['date'];
                            }
                            for($i=1;$i<=date("t",strtotime($date));$i++){
                                $day = $i - 1;
                                $data['xv'][] = date("d",strtotime("$date + $day day"));
                                $data['data'][1]['data'][] = $total[date("Y_m_d",strtotime(str_replace("_","-",$bdate)." +$day day"))] ? intval($total[date("Y_m_d",strtotime(str_replace("_","-",$bdate)." +$day day"))]) : 0;
                            }
                            $data['data']['num'] = 1;
                            
                            $stationChoice = array();
                            $stationChoice[0] = 1; //to do...
                        
                        }else{
                            
                            $group = "`date`,`stationid`";
                            $dbname = "rha_count_record";
                        
                            $result = $PSys_RegisteModel->getWeek($bdate,$edate,$group,$dbname,$distinguish,$stationChoice);
                            
                            //重过滤$station   
                            $where = array();
                            $where['id_IN'] = $stationChoice;
                            $order = "id ASC";
                            $field = "id,stationname";  
                            $station = $PSys_RegisteModel->GetList($where, $order, 0, 0, $field, "rha_station");
                            
                            //x轴数据
                            $data['xv'] = array();
                            //比较的数据节点设置
                            foreach($station['allrow'] as $key=>$val){
                                $data['data'][$val['id']]['name'] = $val['stationname'];
                                $data['data'][$val['id']]['marker'] = "square";
                                if(in_array($val['id'],$stationChoice)){
                                    $data['data']['table'][$val['id']] = array();
                                }
                            }
                            //遍历数据
                            foreach($result as $key=>$val){
                                $total[$val['date']][$val['stationid']] = $val['total'];
                                if(in_array($val['stationid'],$stationChoice)){
                                    $data['data']['table'][$val['stationid']][] = array('date'=>str_replace("_","-",$val['date']),'total'=>$val['total']);
                                }
                                $data['data'][$val['stationid']]['id'][] = $val['date'];
                            }
                                     
                            for($i=1;$i<=date("t",strtotime($date));$i++){
                                $day = $i - 1;
                                $data['xv'][] = date("d",strtotime("$date + $day day"));
                                
                                //遍历数据
                                foreach($station['allrow'] as $key=>$val){
                                    $data['data'][$val['id']]['data'][] = $total[date("Y_m_d",strtotime(str_replace("_","-",$bdate)." +$day day"))][$val['id']] ? intval($total[date("Y_m_d",strtotime(str_replace("_","-",$bdate)." +$day day"))][$val['id']]) : 0;
                                    //$data['data'][$val['id']]['id'][$i] = $result[$i*date("t",strtotime($date))*count($station['allrow'])]['date'];
                                }
                            }
                                                        
                            
                            $data['data']['num'] = count($station['allrow']);
                        }
                        
                        $data['data']['key'] = array("日期","人数");//table 栏目提示 array按顺序
                        $data['screening']['date'] = '<a class="btn" onclick="screeningSpan(\'close-date\');">单月/'.str_replace("_","-",$bdate) .' <i class="icon-remove"></i><input type="hidden" name="dateSearch" value="onemonth" /><input type="hidden" name="onemonth" value="'.$date.'" /></a>&nbsp;&nbsp;&nbsp;';
                        
                    break;
                    
                    case "manymonth":
                    
                        $bday = reqstr("bmanymonth");
                        $bdate = date("Y_m_01",strtotime($bday));
                        
                        $eday = reqstr("emanymonth");
                        $edate = date("Y_m_d",strtotime("$eday +1 month -1 day"));
                        
                        //计算月数差
                        $Months = (date("Y",strtotime($eday)) - date("Y",strtotime($bday)))*12+(date("m",strtotime($eday))-date("m",strtotime($bday))) + 1;
                        
                        for($i=0;$i<$Months;$i++){
                            $dateArr[$i] = date("Y-m",strtotime("$bday +$i month"));
                        }
                        
                        $data = array();
                        
                        if($datastatus == 1){
                            
                            $group = "date";
                            $dbname = "rha_count_record";
                        
                            $result = $PSys_RegisteModel->getWeek($bdate,$edate,$group,$dbname,$distinguish,$stationChoice);
                            
                            //x轴数据
                            $data['xv'] = array();
                            $data['data'][1]['name'] = "所有站点";
                            $data['data'][1]['marker'] = "square";

                            foreach($result as $key=>$val){
                                $total[$val['date']] = $val['total'];
                                for($i=0;$i<count($dateArr);$i++){
                                    if($dateArr[$i] == date("Y-m",strtotime(str_replace("_","-",$val['date'])))){
                                        $data['data']['table'][1][$i]['date'] = $dateArr[$i];
                                        $data['data']['table'][1][$i]['total'] += $val['total'];
                                    }
                                }
                            }
                            
                            $day = 0;
                            
                            for($i=0;$i<$Months;$i++){
                                $num = date("t",strtotime("$bday +$i month"));
                                $d = date("Y-m-01",strtotime("$bday +$i month"));
                                for($j=1;$j<=$num;$j++){
                                    if($j == 1){
                                        $data['xv'][$i] = date("Y/m",strtotime("$bday +$i month"));
                                    }
                                    //echo $day."<br/>";
                                    //$data['data'][1]['data'][] = intval($result[$i-1]['total']);
                                    $data['data'][1]['data'][$i] += $total[date("Y_m_d",strtotime("$bday +$day day"))] ? intval($total[date("Y_m_d",strtotime("$bday +$day day"))]) : 0;
                                    $day++;
                                }
                                $data['data'][1]['id'][] = $d;
                            }
                            
                            $data['data']['num'] = 1;
                            
                            $stationChoice = array();
                            $stationChoice[0] = 1; //to do...
                            
                        }else{
                            
                            $group = "`date`,`stationid`";
                            $dbname = "rha_count_record";
                    
                            $result = $PSys_RegisteModel->getWeek($bdate,$edate,$group,$dbname,$distinguish,$stationChoice);
                            
                            foreach($result as $key=>$val){
                                $total[$val['stationid']][$val['date']] = $val['total'];
                                //$data['data'][$val['station']]['data'][] = intval($val['total']);
                                //$data['data']['table'][$val['station']][] = array('date'=>$val['date'],'total'=>$val['total']);
                                for($i=0;$i<count($dateArr);$i++){
                                    
                                    //创建初始array
                                    if($data['data']['table'][$val['stationid']][$i]['date'] == ''){
                                        $data['data']['table'][$val['stationid']][$i]['date'] = $dateArr[$i];
                                        $data['data']['table'][$val['stationid']][$i]['total'] = 0;
                                    }
                                    
                                    
                                    if($dateArr[$i] == date("Y-m",strtotime(str_replace("_","-",$val['date'])))){
                                        $data['data']['table'][$val['stationid']][$i]['date'] = $dateArr[$i];
                                        $data['data']['table'][$val['stationid']][$i]['total'] += $val['total'];
                                    }
                                }
                            }
                            
                            //重过滤$station   
                            $where = array();
                            $where['id_IN'] = $stationChoice;
                            $order = "id ASC";
                            $field = "id,stationname";  
                            $station = $PSys_RegisteModel->GetList($where, $order, 0, 0, $field, "rha_station");
                            
                            //x轴数据
                            $data['xv'] = array();
                            //比较的数据节点设置
                            foreach($station['allrow'] as $key=>$val){
                                $data['data'][$val['id']]['name'] = $val['stationname'];
                                $data['data'][$val['id']]['marker'] = "square";
                                if(in_array($val['id'],$stationChoice)){
                                    $data['data']['table'][$val['id']] = is_array($data['data']['table'][$val['id']]) ? $data['data']['table'][$val['id']] : array();
                                }
                            }
                            
                            $day = 0;
                            
                            //print_r($total[1][date("Y-m-d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y")))]);
                            for($i=0;$i<$Months;$i++){
                                
                                $num = date("t",strtotime("$bday +$i month"));
                                $d = date("Y_m_01",strtotime("$bday +$i month"));
                                for($j=1;$j<=$num;$j++){
                                    if($j == 1){
                                        $data['xv'][$i] = date("Y/m",strtotime("$bday +$i month"));
                                    }
                                    //echo $day."<br/>";
                                    //$data['data'][1]['data'][] = intval($result[$i-1]['total']);
                                    foreach($station['allrow'] as $key=>$val){
                                        $data['data'][$val['id']]['data'][$i] += $total[$val['id']][date("Y_m_d",strtotime("$bday +$day day"))] ? intval($total[$val['id']][date("Y_m_d",strtotime("$bday +$day day"))]) : 0;
                                        $data['data'][$val['id']]['id'][$i] = $d;
                                    }
                                    $day++;
                                }
                                
                            }
                            
                            $data['data']['num'] = count($station['allrow']);
                            
                        }
                        
                        $data['data']['key'] = array("日期","人数");//table 栏目提示 array按顺序
                        $data['screening']['date'] = '<a class="btn" onclick="screeningSpan(\'close-date\');">多月/'.str_replace("_","-",$bdate) . "~" . str_replace("_","-",$edate) .' <i class="icon-remove"></i><input type="hidden" name="dateSearch" value="manymonth" /><input type="hidden" name="manymonth" value="'.$date.'" /></a>&nbsp;&nbsp;&nbsp;';
                    
                    break;
                    
                }
                
                if(!$data['error']){                
                    $data['screening']['station'] = $screening['station'];
                }else{                                                            
                    $data['screening']['station'] = array();                    
                                    
                }
        }
        
        //将所有default = 0 的 都要写一遍 to do ...
        if(!$stationChoice){
            $stationChoice[0] = 1;
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
    * @do ajax 注册 某时段 数据
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function ajaxREGChainDiagramAction(){
        
        $PSys_RegisteModel = new PSys_RegisteModel();
        
        $stationC = reqarray("stationC");
        $fastSearch = reqnum("fastSearch",0);
        $distinguish = reqnum("distinguish",1);//sql条件区分
        $id = reqstr("id");
        
        //$station   
        $where = array();
        //$where['id_IN'] = $stationChoice;
        $order = "id ASC";
        $field = "id,stationname";
        $station = $PSys_RegisteModel->GetList($where, $order, 0, 0, $field, "rha_station");
        
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
            
            case 2:
            
                $dbname = "rha_count_record";
                $result2 = $PSys_RegisteModel->getWeekChainDiagramDetail($id,$dbname);
                $result = $PSys_RegisteModel->getWeekChainDiagram($result2,$dbname,$distinguish,$stationChoice);
                
                //数据节点设置
                foreach($result as $key=>$val){
                    $data['data'][$key+1]['name'] = $stationArr[$val['stationid']];
                    $data['data'][$key+1]['marker'] = "square";
                    $data['data'][$key+1]['data'][] = intval($val['total']);
                    $data['data']['station'][] = $key+1;
                }
                
                //print_r($data['data']);
 
                $data['data']['num'] = count($result);
                $data['xv'] = array(str_replace("_","-",$result2[0]['date']));
                $data['title'] = array(str_replace("_","-",$result2[0]['date']));
            
            break;
            
            case 3:
            
                $dbname = "rha_count_record";
                $result2 = $PSys_RegisteModel->getWeekChainDiagramDetail($id,$dbname);
                $result = $PSys_RegisteModel->getWeekChainDiagram($result2,$dbname,$distinguish,$stationChoice);
                
                //数据节点设置
                foreach($result as $key=>$val){
                    $data['data'][$key+1]['name'] = $stationArr[$val['stationid']];
                    $data['data'][$key+1]['marker'] = "square";
                    $data['data'][$key+1]['data'][] = intval($val['total']);
                    $data['data']['station'][] = $key+1;
                }
                
                //print_r($data['data']);
 
                $data['data']['num'] = count($result);
                $data['xv'] = array(str_replace("_","-",$result2[0]['date']));
                $data['title'] = array(str_replace("_","-",$result2[0]['date']));
                
            break;
            
            default:
            
                $dateSearch = reqstr("dateSearch");
            
                switch ($dateSearch){
                    
                    case "manyday" :
                    
                        $dbname = "rha_count_record";
                        $result2 = $PSys_RegisteModel->getWeekChainDiagramDetail($id,$dbname);
                        $result = $PSys_RegisteModel->getWeekChainDiagram($result2,$dbname,$distinguish,$stationChoice);
                        
                        //数据节点设置
                        foreach($result as $key=>$val){
                            $data['data'][$key+1]['name'] = $stationArr[$val['stationid']];
                            $data['data'][$key+1]['marker'] = "square";
                            $data['data'][$key+1]['data'][] = intval($val['total']);
                            $data['data']['station'][] = $key+1;
                        }
                        
                        //print_r($data['data']);
         
                        $data['data']['num'] = count($result);
                        $data['xv'] = array(str_replace("_","-",$result2[0]['date']));
                        $data['title'] = array(str_replace("_","-",$result2[0]['date']));
      
                    break;
                    
                    case "oneweek" :
                    
                        $dbname = "rha_count_record";
                        $result2 = $PSys_RegisteModel->getWeekChainDiagramDetail($id,$dbname);
                        $result = $PSys_RegisteModel->getWeekChainDiagram($result2,$dbname,$distinguish,$stationChoice);
                        
                        //数据节点设置
                        foreach($result as $key=>$val){
                            $data['data'][$key+1]['name'] = $stationArr[$val['stationid']];
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
                    
                        $dbname = "rha_count_record";
                        $result2 = $PSys_RegisteModel->getManyWeekChainDiagramDetail($id,$dbname);
                        $result = $PSys_RegisteModel->getManyWeekChainDiagram($result2,$dbname,$distinguish,$stationChoice);
                        
                        //数据节点设置
                        foreach($result as $key=>$val){
                            $data['data'][$key+1]['name'] = $stationArr[$val['stationid']];
                            $data['data'][$key+1]['marker'] = "square";
                            $data['data'][$key+1]['data'][] = intval($val['total']);
                            $data['data']['station'][] = $key+1;
                        }
                        
                        //print_r($data['data']);
         
                        $data['data']['num'] = count($result);
                        $data['xv'] = array("");
                        $data['title'] = array(str_replace("_","-",$result2['bdate']).' 至 '.str_replace("_","-",$result2['edate']));
      
                    break;
                    
                    case "onemonth" :
                    
                        $dbname = "rha_count_record";
                        $result2 = $PSys_RegisteModel->getOneMonthChainDiagramDetail($id,$dbname);
                        $result = $PSys_RegisteModel->getOneMonthChainDiagram($result2,$dbname,$distinguish,$stationChoice);
                        
                        //数据节点设置
                        foreach($result as $key=>$val){
                            $data['data'][$key+1]['name'] = $stationArr[$val['stationid']];
                            $data['data'][$key+1]['marker'] = "square";
                            $data['data'][$key+1]['data'][] = intval($val['total']);
                            $data['data']['station'][] = $key+1;
                        }
                        
                        //print_r($data['data']);
         
                        $data['data']['num'] = count($result);
                        $data['xv'] = array(str_replace("_","-",$result2['date']));
                        $data['title'] = array(str_replace("_","-",$result2['date']));
      
                    break;
                    
                    case "manymonth" :
                    
                        $dbname = "rha_count_record";
                        $result2 = $PSys_RegisteModel->getManyMonthChainDiagramDetail($id,$dbname);
                        $result = $PSys_RegisteModel->getManyMonthChainDiagram($result2,$dbname,$distinguish,$stationChoice);
                        
                        //数据节点设置
                        foreach($result as $key=>$val){
                            $data['data'][$key+1]['name'] = $stationArr[$val['stationid']];
                            $data['data'][$key+1]['marker'] = "square";
                            $data['data'][$key+1]['data'][] = intval($val['total']);
                            $data['data']['station'][] = $key+1;
                        }
                        
                        //print_r($data['data']);
         
                        $data['data']['num'] = count($result);
                        $data['xv'] = array("");
                        $data['title'] = array(date("Y-m",strtotime(str_replace("_","-",$result2['bdate']))));
      
                    break;
                    
                    
                }
            
        }
        
        die(json_encode($data));
        
    }
    
    /**
    *
    * @do 导出excel
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function ajaxOutputExcelAction(){
        
        $wordArr = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
        
        $PSys_RegisteModel = new PSys_RegisteModel();
        
        //站点数据
        $where = array();
        $order = "id ASC";
        $field = "id,stationname";
	   
        $PSys_RegisteModel = new PSys_RegisteModel();
        $station = $PSys_RegisteModel->GetList($where, $order, 0, 0, $field, "rha_station");
        
        
        $fastSearch = reqnum("fastSearch",0);//快速查询
        $dataStatusFS = reqnum("datastatusFS",1);//快速查询合并、比较筛选按钮 1为合并 2为比较  默认合并
        $distinguish = reqnum("distinguish",1); //sql条件区分
        
        if($distinguish == 1){
            $word = "注册-注册页统计";
        }elseif($distinguish == 2){
            $word = "注册-注册人数统计";
        }elseif($distinguish == 3){
            $word = "访问-访问首页统计";
        }
        
        switch($fastSearch){
        
        case 2://本周
        
            $date = date("Y-m-d");
            $bdate = date("Y_m_d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y")));
            $edate = date("Y_m_d",mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y"))); 

            $data = array();
            
            if($dataStatusFS == 1){
                
                $group = "`date`";
                $dbname = "rha_count_record";
            
                $result = $PSys_RegisteModel->getWeek($bdate,$edate,$group,$dbname,$distinguish);
                
                $XPhpExcel = new XPhpExcel();
                $Creator = "statis.rockhippo.com";
                $LastModifiedBy = $Creator;
                $Title = $word;
                $Subject = '本周-合并';
                $Description = $word.'(本周-合并)';
                $Keywords = $Description;
                $Category = $Description;
                $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
                $XPhpExcel->setSheet(0,$Subject);
                
                $XPhpExcel->setValue("A1","日期");
                $XPhpExcel->setValue("B1","人数");
                
                $XPhpExcel->setWidth("A",14,2);
                $XPhpExcel->setWidth("B",20,2);
                
                foreach($result as $key=>$val){
                    
                    $XPhpExcel->setValue("A".($key+2),str_replace("_","-",$val["date"]));
                    $XPhpExcel->setValue("B".($key+2),$val["total"]);
                    
                } 
            
                //清空输出缓存                    
                ob_clean();                    
                $XPhpExcel->output($bdate."~".$edate."/".$Description);
                unset($XPhpExcel);//销毁
                
            }else{
                
                $group = "`date`,`stationid`";
                $dbname = "rha_count_record";
        
                $result = $PSys_RegisteModel->getWeek($bdate,$edate,$group,$dbname,$distinguish);
        
                $XPhpExcel = new XPhpExcel();
                $Creator = "statis.rockhippo.com";
                $LastModifiedBy = $Creator;
                $Title = $word;
                $Subject = '本周-比较';
                $Description = $word.'(本周-比较)';
                $Keywords = $Description;
                $Category = $Description;
                $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
                $XPhpExcel->setSheet(0,$Subject);
                $XPhpExcel->setValue("A1","日期");
                $XPhpExcel->setWidth("A",14,2);
                
                for($i=0;$i<7;$i++){
                    $XPhpExcel->setValue("A".($i+2),date("Y-m-d",strtotime(str_replace("_","-",$bdate)." +$i day")));
                }
                
                $data = array();
                
                //遍历数据获取所需结构
                foreach($result as $key=>$val){
                    $data[$val['stationid']][$val['date']] = $val['total'];
                } 
                
                foreach($station['allrow'] as $key=>$val){
                    $XPhpExcel->setValue($wordArr[$val['id']]."1",$val['stationname']."/人数");
                    $XPhpExcel->setWidth($wordArr[$val['id']],20,2);
                    for($i=0;$i<7;$i++){
                        $XPhpExcel->setValue($wordArr[$val['id']].($i+2),$data[$val['id']][date("Y_m_d",strtotime(str_replace("_","-",$bdate)." +$i day"))]);
                    }
                }
               
                //清空输出缓存                    
                ob_clean();    
                              
                $XPhpExcel->output($date."/".$Description);
                unset($XPhpExcel);//销毁
                
                
            }
        
            break;
        
        case 3://本月
        
            $bdate = date("Y_m_d",mktime(0, 0 , 0,date("m"),1,date("Y")));
            $edate = date("Y_m_d",mktime(23,59,59,date("m"),date("t"),date("Y"))); 
            
            $date = date("Y-m");
            
            $data = array();
            
            if($dataStatusFS == 1){
                
                $group = "date";
                $dbname = "rha_count_record";
            
                $result = $PSys_RegisteModel->getWeek($bdate,$edate,$group,$dbname,$distinguish);
                
                $XPhpExcel = new XPhpExcel();
                $Creator = "statis.rockhippo.com";
                $LastModifiedBy = $Creator;
                $Title = $word;
                $Subject = '本月-合并';
                $Description = $word.'(本月-合并)';
                $Keywords = $Description;
                $Category = $Description;
                $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
                $XPhpExcel->setSheet(0,$Subject);
                
                $XPhpExcel->setValue("A1","日期");
                $XPhpExcel->setValue("B1","人数");
                
                $XPhpExcel->setWidth("A",14,2);
                $XPhpExcel->setWidth("B",20,2);
                
                foreach($result as $key=>$val){
                    
                    $XPhpExcel->setValue("A".($key+2),str_replace("_","-",$val["date"]));
                    $XPhpExcel->setValue("B".($key+2),$val["total"]);
                    
                } 
            
                //清空输出缓存                    
                ob_clean();                    
                $XPhpExcel->output($date."/".$Description);
                unset($XPhpExcel);//销毁
            
            }else{
                
                $group = "`date`,`stationid`";
                $dbname = "rha_count_record";
            
                $result = $PSys_RegisteModel->getWeek($bdate,$edate,$group,$dbname,$distinguish);
                
                $XPhpExcel = new XPhpExcel();
                $Creator = "statis.rockhippo.com";
                $LastModifiedBy = $Creator;
                $Title = $word;
                $Subject = '本月-比较';
                $Description = $word.'(本月-比较)';
                $Keywords = $Description;
                $Category = $Description;
                $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
                $XPhpExcel->setSheet(0,$Subject);
                $XPhpExcel->setValue("A1","日期");
                $XPhpExcel->setWidth("A",14,2);
                
                for($i=0;$i<date("t");$i++){
                    $XPhpExcel->setValue("A".($i+2),date("Y-m-d",strtotime(str_replace("_","-",$bdate)." +$i day")));
                }
                
                $data = array();
                
                //遍历数据获取所需结构
                foreach($result as $key=>$val){
                    $data[$val['stationid']][$val['date']] = $val['total'];
                } 
                
                foreach($station['allrow'] as $key=>$val){
                    $XPhpExcel->setValue($wordArr[$val['id']]."1",$val['stationname']."/人数");
                    $XPhpExcel->setWidth($wordArr[$val['id']],20,2);
                    for($i=0;$i<date("t");$i++){
                        //echo $wordArr[$val['id']].($i+2) . "~" . $data[$val['id']][$xv[$i]] . "<br>";
                        $XPhpExcel->setValue($wordArr[$val['id']].($i+2),$data[$val['id']][date("Y_m_d",strtotime(str_replace("_","-",$bdate)." +$i day"))]);
                    }
                }

                //清空输出缓存                    
                ob_clean();    
                              
                $XPhpExcel->output($date."/".$Description);
                unset($XPhpExcel);//销毁
                
            }

        
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
                
                case "manyday":
                
                    $bdate = reqstr("bmanyday");
                    $edate = reqstr("emanyday");
                    //计算天数差
                    $Days  = round((strtotime($edate)-strtotime($bdate))/3600/24);
                    
                    $bdate = str_replace("-","_",$bdate);
                    $edate = str_replace("-","_",$edate);
    
                    $data = array();
                    
                    if($datastatus == 1){
                        
                        $group = "date";
                        $dbname = "rha_count_record";
                    
                        $result = $PSys_RegisteModel->getWeek($bdate,$edate,$group,$dbname,$distinguish,$stationChoice);
                        
                        $XPhpExcel = new XPhpExcel();
                        $Creator = "statis.rockhippo.com";
                        $LastModifiedBy = $Creator;
                        $Title = $word;
                        $Subject = '多日-合并';
                        $Description = $word.'(多日-合并)';
                        $Keywords = $Description;
                        $Category = $Description;
                        $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
                        $XPhpExcel->setSheet(0,$Subject);
                        
                        $XPhpExcel->setValue("A1","日期");
                        $XPhpExcel->setValue("B1","人数");
                        
                        $XPhpExcel->setWidth("A",14,2);
                        $XPhpExcel->setWidth("B",20,2);
                        
                        foreach($result as $key=>$val){
                            
                            $XPhpExcel->setValue("A".($key+2),str_replace("_","-",$val["date"]));
                            $XPhpExcel->setValue("B".($key+2),$val["total"]);
                            
                        } 
                        
                        //清空输出缓存                    
                        ob_clean();    
                                      
                        $XPhpExcel->output($bdate."~".$edate."/".$Description);
                        unset($XPhpExcel);//销毁
                        
                    }else{
                        
                        $group = "`date`,`stationid`";
                        $dbname = "rha_count_record";
                
                        $result = $PSys_RegisteModel->getWeek($bdate,$edate,$group,$dbname,$distinguish,$stationChoice);
                        
                        $XPhpExcel = new XPhpExcel();
                        $Creator = "statis.rockhippo.com";
                        $LastModifiedBy = $Creator;
                        $Title = $word;
                        $Subject = '多日-比较';
                        $Description = $word.'(多日-比较)';
                        $Keywords = $Description;
                        $Category = $Description;
                        $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
                        $XPhpExcel->setSheet(0,$Subject);
                        $XPhpExcel->setValue("A1","日期");
                        $XPhpExcel->setWidth("A",14,2);
                        
                        for($i=0;$i<$Days;$i++){
                            $XPhpExcel->setValue("A".($i+2),date("Y-m-d",strtotime(str_replace("_","-",$bdate)." +$i day")));
                        }
                        
                        $data = array();
                        
                        //遍历数据获取所需结构
                        foreach($result as $key=>$val){
                            $data[$val['stationid']][$val['date']] = $val['total'];
                        } 
                        
                    ////重过滤$station   
//                        $where = array();
//                        $where['id_IN'] = $stationChoice;
//                        $order = "id ASC";
//                        $field = "id,stationname";  
//                        $station = $PSys_RegisteModel->GetList($where, $order, 0, 0, $field, "rha_station");
                        
                        foreach($station['allrow'] as $key=>$val){
                            $XPhpExcel->setValue($wordArr[$val['id']]."1",$val['stationname']."/人数");
                            $XPhpExcel->setWidth($wordArr[$val['id']],20,2);
                            for($i=0;$i<$Days;$i++){
                                //echo $wordArr[$val['id']].($i+2) . "~" . $data[$val['id']][$xv[$i]] . "<br>";
                                $XPhpExcel->setValue($wordArr[$val['id']].($i+2),$data[$val['id']][date("Y_m_d",strtotime(str_replace("_","-",$bdate)." +$i day"))]);
                            }
                        }
        
                        //清空输出缓存                    
                        ob_clean();    
                                      
                        $XPhpExcel->output(str_replace("_","-",$bdate)."~".str_replace("_","-",$edate)."/".$Description);
                        unset($XPhpExcel);//销毁
                        
                    }
                    
                break;
                
                case "oneweek":
           
                    $day = reqstr("oneweek");
                    $edate=date('Y_m_d',strtotime("$day Sunday")); 
                    $bdate=date('Y_m_d',strtotime(str_replace("_","-",$edate)." -6 days"));
                    //计算天数差
                    $Days  = round((strtotime($edate)-strtotime($bdate))/3600/24);

                    $data = array();
                    
                    if($datastatus == 1){
                        
                        $group = "date";
                        $dbname = "rha_count_record";
                    
                        $result = $PSys_RegisteModel->getWeek($bdate,$edate,$group,$dbname,$distinguish,$stationChoice);
                        
                        
                        $XPhpExcel = new XPhpExcel();
                        $Creator = "statis.rockhippo.com";
                        $LastModifiedBy = $Creator;
                        $Title = $word;
                        $Subject = '单周-合并';
                        $Description = $word.'(单周-合并)';
                        $Keywords = $Description;
                        $Category = $Description;
                        $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
                        $XPhpExcel->setSheet(0,$Subject);
                        
                        $XPhpExcel->setValue("A1","日期");
                        $XPhpExcel->setValue("B1","人数");
                        
                        $XPhpExcel->setWidth("A",14,2);
                        $XPhpExcel->setWidth("B",20,2);
                        
                        foreach($result as $key=>$val){
                            
                            $XPhpExcel->setValue("A".($key+2),str_replace("_","-",$val["date"]));
                            $XPhpExcel->setValue("B".($key+2),$val["total"]);
                            
                        } 
                        
                        //清空输出缓存                    
                        ob_clean();    
                                      
                        $XPhpExcel->output(str_replace("_","-",$bdate)."~".str_replace("_","-",$edate)."/".$Description);
                        unset($XPhpExcel);//销毁
                        
                    }else{
                        
                        $group = "`date`,`stationid`";
                        $dbname = "rha_count_record";
                
                        $result = $PSys_RegisteModel->getWeek($bdate,$edate,$group,$dbname,$distinguish,$stationChoice);
                        
                        $XPhpExcel = new XPhpExcel();
                        $Creator = "statis.rockhippo.com";
                        $LastModifiedBy = $Creator;
                        $Title = $word;
                        $Subject = '单周-比较';
                        $Description = $word.'(单周-比较)';
                        $Keywords = $Description;
                        $Category = $Description;
                        $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
                        $XPhpExcel->setSheet(0,$Subject);
                        $XPhpExcel->setValue("A1","日期");
                        $XPhpExcel->setWidth("A",14,2);
                        
                        for($i=0;$i<7;$i++){
                            $XPhpExcel->setValue("A".($i+2),date("Y-m-d",strtotime(str_replace("_","-",$bdate)." +$i day")));
                        }
                        
                        $data = array();
                        
                        //遍历数据获取所需结构
                        foreach($result as $key=>$val){
                            $data[$val['stationid']][$val['date']] = $val['total'];
                        } 
                        
                    ////重过滤$station   
//                        $where = array();
//                        $where['id_IN'] = $stationChoice;
//                        $order = "id ASC";
//                        $field = "id,stationname";  
//                        $station = $PSys_RegisteModel->GetList($where, $order, 0, 0, $field, "rha_station");
                        
                        foreach($station['allrow'] as $key=>$val){
                            $XPhpExcel->setValue($wordArr[$val['id']]."1",$val['stationname']."/人数");
                            $XPhpExcel->setWidth($wordArr[$val['id']],20,2);
                            for($i=0;$i<7;$i++){
                                //echo $wordArr[$val['id']].($i+2) . "~" . $data[$val['id']][$xv[$i]] . "<br>";
                                $XPhpExcel->setValue($wordArr[$val['id']].($i+2),$data[$val['id']][date("Y_m_d",strtotime(str_replace("_","-",$bdate)." +$i day"))]);
                            }
                        }
        
                        //清空输出缓存                    
                        ob_clean();    
                                      
                        $XPhpExcel->output(str_replace("_","-",$bdate)."~".str_replace("_","-",$edate)."/".$Description);
                        unset($XPhpExcel);//销毁

                    }
                    
                break;
                
                case "manyweek":
                
                    $bday = reqstr("bmanyweek");
                    $edate2=date('Y-m-d',strtotime("$bday Sunday")); 
                    $bdate=date('Y_m_d',strtotime("$edate2 -6 days"));
                    
                    $eday = reqstr("emanyweek");
                    $edate=date('Y_m_d',strtotime("$eday Sunday")); 
                    $bdate2=date('Y-m-d',strtotime(str_replace("_","-",$edate)." -6 days"));
                    
                    //计算周数差
                    $Weeks  = round((strtotime(str_replace("_","-",$edate))-strtotime(str_replace("_","-",$bdate)))/3600/24/7);

                    $data = array();
                    
                    if($datastatus == 1){
                        
                        $group = "date";
                        $dbname = "rha_count_record";
                    
                        $result = $PSys_RegisteModel->getWeek($bdate,$edate,$group,$dbname,$distinguish,$stationChoice);
                        
                        foreach($result as $key=>$val){
                            $data[$val['date']] = $val['total'];
                        } 

                        $XPhpExcel = new XPhpExcel();
                        $Creator = "statis.rockhippo.com";
                        $LastModifiedBy = $Creator;
                        $Title = $word;
                        $Subject = '多周-合并';
                        $Description = $word.'(多周-合并)';
                        $Keywords = $Description;
                        $Category = $Description;
                        $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
                        $XPhpExcel->setSheet(0,$Subject);
                        
                        $XPhpExcel->setValue("A1","日期");
                        $XPhpExcel->setValue("B1","人数");
                        
                        $XPhpExcel->setWidth("A",25,2);
                        $XPhpExcel->setWidth("B",20,2);

                        for($i=0;$i<$Weeks;$i++){

                            $num = 0;

                            for($j=1;$j<=7;$j++){
                                $day = $i * 7 + $j - 1;
                                $dt = date("Y_m_d",strtotime(str_replace("_","-",$bdate)." +$day day"));
                                $num += $data[$dt];
                                
                                if($j == 1){
                                    $d = date("Y/m/d",strtotime(str_replace("_","-",$bdate)." +$day day"));
                                }
                                if($j == 7){
                                    $d .= ' - ' . date("m/d",strtotime(str_replace("_","-",$bdate)." +$day day"));
                                    $XPhpExcel->setValue("A".($i+2),$d);
                                    $XPhpExcel->setValue("B".($i+2),$num);
                                }
                                
                            }
                        }    
                        
                        //清空输出缓存                    
                        ob_clean();    
                                      
                        $XPhpExcel->output(str_replace("_","-",$bdate)."~".str_replace("_","-",$edate)."/".$Description);
                        unset($XPhpExcel);//销毁
                        
                    }else{
                        
                        $group = "`date`,`stationid`";
                        $dbname = "rha_count_record";
                
                        $result = $PSys_RegisteModel->getWeek($bdate,$edate,$group,$dbname,$distinguish,$stationChoice);
                        
                        foreach($result as $key=>$val){
                            $data[$val['stationid']][$val['date']] = $val['total'];
                        } 

                        $XPhpExcel = new XPhpExcel();
                        $Creator = "statis.rockhippo.com";
                        $LastModifiedBy = $Creator;
                        $Title = $word;
                        $Subject = '多周-比较';
                        $Description = $word.'(多周-比较)';
                        $Keywords = $Description;
                        $Category = $Description;
                        $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
                        $XPhpExcel->setSheet(0,$Subject);
                        
                        $XPhpExcel->setValue("A1","日期");
                        $XPhpExcel->setWidth("A",25,2);
                        
                        foreach($station['allrow'] as $key=>$val){
                            $XPhpExcel->setValue($wordArr[$val['id']]."1",$val['stationname']."/人数");
                            $XPhpExcel->setWidth($wordArr[$val['id']],20,2);
                            
                            for($i=0;$i<$Weeks;$i++){
    
                                $num = 0;
    
                                for($j=1;$j<=7;$j++){
                                    $day = $i * 7 + $j - 1;
                                    $dt = date("Y_m_d",strtotime(str_replace("_","-",$bdate)." +$day day"));
                                    $num += $data[$val['id']][$dt];
                                    
                                    if($j == 1){
                                        $d = date("Y/m/d",strtotime(str_replace("_","-",$bdate)." +$day day"));
                                    }
                                    if($j == 7){
                                        $d .= ' - ' . date("m/d",strtotime(str_replace("_","-",$bdate)." +$day day"));
                                        $XPhpExcel->setValue("A".($i+2),$d);
                                        $XPhpExcel->setValue($wordArr[$val['id']].($i+2),$num);
                                    }
                                    
                                }
                            }    
    
                        }
                        
                        //清空输出缓存                    
                        ob_clean();    
                                      
                        $XPhpExcel->output(str_replace("_","-",$bdate)."~".str_replace("_","-",$edate)."/".$Description);
                        unset($XPhpExcel);//销毁
                        
                    }
                    
                break;
                
                case "onemonth":
                
                    $date = reqstr("onemonth");
                    $bdate = date("Y_m_01",strtotime($date));
                    $edate = date("Y_m_d",strtotime(str_replace("_","-",$bdate)." +1 month -1 day"));
                    $date = date("Y-m",strtotime($date));
                    
                    $Days = date("t",strtotime($date));
                    
                    $data = array();
                    
                    if($datastatus == 1){
                        
                        $group = "date";
                        $dbname = "rha_count_record";
                    
                        $result = $PSys_RegisteModel->getWeek($bdate,$edate,$group,$dbname,$distinguish,$stationChoice);
                        
                        $XPhpExcel = new XPhpExcel();
                        $Creator = "statis.rockhippo.com";
                        $LastModifiedBy = $Creator;
                        $Title = $word;
                        $Subject = '单月-合并';
                        $Description = $word.'(单月-合并)';
                        $Keywords = $Description;
                        $Category = $Description;
                        $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
                        $XPhpExcel->setSheet(0,$Subject);
                        
                        $XPhpExcel->setValue("A1","日期");
                        $XPhpExcel->setValue("B1","人数");
                        
                        $XPhpExcel->setWidth("A",14,2);
                        $XPhpExcel->setWidth("B",20,2);
                        
                        foreach($result as $key=>$val){
                            
                            $XPhpExcel->setValue("A".($key+2),str_replace("_","-",$val["date"]));
                            $XPhpExcel->setValue("B".($key+2),$val["total"]);
                            
                        } 
                        
                        //清空输出缓存                    
                        ob_clean();    
                                      
                        $XPhpExcel->output($date."/".$Description);
                        unset($XPhpExcel);//销毁
                        
                    }else{
                        
                        $group = "`date`,`stationid`";
                        $dbname = "rha_count_record";
                
                        $result = $PSys_RegisteModel->getWeek($bdate,$edate,$group,$dbname,$distinguish,$stationChoice);
                        
                        $XPhpExcel = new XPhpExcel();
                        $Creator = "statis.rockhippo.com";
                        $LastModifiedBy = $Creator;
                        $Title = $word;
                        $Subject = '单月-比较';
                        $Description = $word.'(单月-比较)';
                        $Keywords = $Description;
                        $Category = $Description;
                        $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
                        $XPhpExcel->setSheet(0,$Subject);
                        $XPhpExcel->setValue("A1","日期");
                        $XPhpExcel->setWidth("A",14,2);
                        
                        for($i=0;$i<$Days;$i++){
                            $XPhpExcel->setValue("A".($i+2),date("Y-m-d",strtotime(str_replace("_","-",$bdate)." +$i day")));
                        }
                        
                        $data = array();
                        
                        //遍历数据获取所需结构
                        foreach($result as $key=>$val){
                            $data[$val['stationid']][$val['date']] = $val['total'];
                        } 
                        
                    ////重过滤$station   
//                        $where = array();
//                        $where['id_IN'] = $stationChoice;
//                        $order = "id ASC";
//                        $field = "id,stationname";  
//                        $station = $PSys_RegisteModel->GetList($where, $order, 0, 0, $field, "rha_station");
                        
                        foreach($station['allrow'] as $key=>$val){
                            $XPhpExcel->setValue($wordArr[$val['id']]."1",$val['stationname']."/人数");
                            $XPhpExcel->setWidth($wordArr[$val['id']],20,2);
                            for($i=0;$i<$Days;$i++){
                                //echo $wordArr[$val['id']].($i+2) . "~" . $data[$val['id']][$xv[$i]] . "<br>";
                                $XPhpExcel->setValue($wordArr[$val['id']].($i+2),$data[$val['id']][date("Y_m_d",strtotime(str_replace("_","-",$bdate)." +$i day"))]);
                            }
                        }
        
                        //清空输出缓存                    
                        ob_clean();    
                                      
                        $XPhpExcel->output(str_replace("_","-",$bdate)."~".str_replace("_","-",$edate)."/".$Description);
                        unset($XPhpExcel);//销毁
                        
                    }
                    
                    
                break;
                
                case "manymonth":
                
                    $bday = reqstr("bmanymonth");
                    $bdate = date("Y_m_01",strtotime($bday));
                    
                    $eday = reqstr("emanymonth");
                    $edate = date("Y_m_d",strtotime("$eday +1 month -1 day"));
                    
                    //计算月数差
                    $Months = (date("Y",strtotime($eday)) - date("Y",strtotime($bday)))*12+(date("m",strtotime($eday))-date("m",strtotime($bday))) + 1;
                    
                    for($i=0;$i<$Months;$i++){
                        $dateArr[$i] = date("Y-m",strtotime("$bday +$i month"));
                    }
                    
                    $data = array();
                    
                    
                    if($datastatus == 1){
                        
                        $group = "date";
                        $dbname = "rha_count_record";
                    
                        $result = $PSys_RegisteModel->getWeek($bdate,$edate,$group,$dbname,$distinguish,$stationChoice);
                        
                        foreach($result as $key=>$val){
                            $data[$val['date']] = $val['total'];
                        } 

                        $XPhpExcel = new XPhpExcel();
                        $Creator = "statis.rockhippo.com";
                        $LastModifiedBy = $Creator;
                        $Title = $word;
                        $Subject = '多月-合并';
                        $Description = $word.'(多月-合并)';
                        $Keywords = $Description;
                        $Category = $Description;
                        $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
                        $XPhpExcel->setSheet(0,$Subject);
                        
                        $XPhpExcel->setValue("A1","日期");
                        $XPhpExcel->setValue("B1","人数");
                        
                        $XPhpExcel->setWidth("A",25,2);
                        $XPhpExcel->setWidth("B",20,2);
                        
                        $day = 0;
                        
                        for($i=0;$i<$Months;$i++){
                            
                            $nums = 0;
                            
                            $num = date("t",strtotime("$bday +$i month"));
                            $d = date("Y-m-01",strtotime("$bday +$i month"));
                            for($j=1;$j<=$num;$j++){
                                if($j == 1){
                                    $d = date("Y/m",strtotime("$bday +$i month"));
                                    $XPhpExcel->setValue("A".($i+2),$d);
                                }
                                //echo $day."<br/>";
                                //$data['data'][1]['data'][] = intval($result[$i-1]['total']);
                                $nums += $data[date("Y_m_d",strtotime("$bday +$day day"))];
                                $day++;
                            }
                            
                            $XPhpExcel->setValue("B".($i+2),$nums);
                            
                        }
                        
                        //清空输出缓存                    
                        ob_clean();    
                                      
                        $XPhpExcel->output($bdate."~".$edate."/".$Description);
                        unset($XPhpExcel);//销毁
                        
                    }else{
                        
                        $group = "`date`,`stationid`";
                        $dbname = "rha_count_record";
                
                        $result = $PSys_RegisteModel->getWeek($bdate,$edate,$group,$dbname,$distinguish,$stationChoice);
                        
                        foreach($result as $key=>$val){
                            $data[$val['stationid']][$val['date']] = $val['total'];
                        } 

                        $XPhpExcel = new XPhpExcel();
                        $Creator = "statis.rockhippo.com";
                        $LastModifiedBy = $Creator;
                        $Title = $word;
                        $Subject = '多月-比较';
                        $Description = $word.'(多月-比较)';
                        $Keywords = $Description;
                        $Category = $Description;
                        $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
                        $XPhpExcel->setSheet(0,$Subject);
                        
                        $XPhpExcel->setValue("A1","日期");
                        $XPhpExcel->setWidth("A",25,2);
                        
                        foreach($station['allrow'] as $key=>$val){
                            $XPhpExcel->setValue($wordArr[$val['id']]."1",$val['stationname']."/人数");
                            $XPhpExcel->setWidth($wordArr[$val['id']],20,2);
                            
                            $day = 0;
                            
                            for($i=0;$i<$Months;$i++){
                                
                                $nums = 0;
                                
                                $num = date("t",strtotime("$bday +$i month"));
                                $d = date("Y-m-01",strtotime("$bday +$i month"));
                                for($j=1;$j<=$num;$j++){
                                    if($j == 1){
                                        $d = date("Y/m",strtotime("$bday +$i month"));
                                        $XPhpExcel->setValue("A".($i+2),$d);
                                    }
                                    //echo $day."<br/>";
                                    //$data['data'][1]['data'][] = intval($result[$i-1]['total']);
                                    $nums += $data[$val['id']][date("Y_m_d",strtotime("$bday +$day day"))];
                                    $day++;
                                }
                                
                                $XPhpExcel->setValue($wordArr[$val['id']].($i+2),$nums);
                                
                            }
                            
                        }
                        
                        //清空输出缓存                    
                        ob_clean();    
                                      
                        $XPhpExcel->output(str_replace("_","-",$bdate)."~".str_replace("_","-",$edate)."/".$Description);
                        unset($XPhpExcel);//销毁

                    }
                    
                break;
                
            }
        
        }   
        
    }
	
}