<?php
/**
* Copyright(c) 2015
* 日    期:2015年07月07日
* 文 件 名:{access}Controller.php
* 创建时间:16:04
* 字符编码:UTF-8
* 版本信息:v1.0
* 修改日期:none
* 最后版本:v1.0
* 创 建 者:Nick (nick@rockhippo.cn)
* 修 改 者:none
* 版本地址:none
* 摘    要: 访问统计控制器
*/
class accessController extends PSys_AbstractController{
    
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
     * @do 访问人数 uv
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
	   
        $PSys_AccessModel = new PSys_AccessModel();
        $station = $PSys_AccessModel->GetList($where, $order, 0, 0, $field, "rha_station");
        $this->smarty->assign("station",$station['allrow']);
        $this->smarty->assign("busstation",$busstation);
        
        //左菜单锁定
        $this->smarty->assign("active_f","access/index");
        $this->smarty->assign("active","access/uv");
        
		$this->forward = "uv";
        
	}
    
    /**
    *
    * @do ajax 访问人数 uv数据
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function ajaxUVAccessAction(){
        
        $PSys_AccessModel = new PSys_AccessModel();
        $where = array();
        $order = "id ASC";
        $field = "id,stationname";
        $station = $PSys_AccessModel->GetList($where, $order, 0, 0, $field, "rha_station");
        
        $fastSearch = reqnum("fastSearch",0);//快速查询
        $dataStatusFS = reqnum("datastatusFS",1);//快速查询合并、比较筛选按钮 1为合并 2为比较  默认合并
        
        switch($fastSearch){
            
            case 1://当天
                $date = date("Y-m-d");
                
                $data = array();
                
                //x轴数据
                $data['xv'] = array('00'=>'0',
                                    '01'=>'1',
                                    '02'=>'2',
                                    '03'=>'3',
                                    '04'=>'4',
                                    '05'=>'5',
                                    '06'=>'6',
                                    '07'=>'7',
                                    '08'=>'8',
                                    '09'=>'9',
                                    '10'=>'10',
                                    '11'=>'11',
                                    '12'=>'12',
                                    '13'=>'13',
                                    '14'=>'14',
                                    '15'=>'15',
                                    '16'=>'16',
                                    '17'=>'17',
                                    '18'=>'18',
                                    '19'=>'19',
                                    '20'=>'20',
                                    '21'=>'21',
                                    '22'=>'22',
                                    '23'=>'23');
                           
                //客流数据                    
                //无
                
                if($dataStatusFS == 1){
                    
                    $group = "`hour`";
                    $dbname = "rha_aclog_hour";
                    $result = $PSys_AccessModel->getDay($date,$group,$dbname);
                    
                    //合并的数据节点设置
                    $data['data'][1]['name'] = '所有站点';
                    $data['data'][1]['marker'] = "square";
     
                    foreach($result as $key=>$val){
                        $total[$val['date']] = $val['total'];
                        $data['data'][1]['id'][] = $val['id'];
                        $data['data']['table'][1][] = array('date'=>$val['date'],'total'=>$val['total']);
                    }
                    
                    foreach($data['xv'] as $k=>$v){
                    
                        $data['data'][1]['data'][] = $total[$k] != '' ? intval($total[$k]) : 0;
                    
                    }
                    
                    $data['data']['num'] = 1;
                    
                }else{
                    
                    $group = "`hour`,`stationid`";
                    $dbname = "rha_aclog_hour";
                    $result = $PSys_AccessModel->getDay($date,$group,$dbname);
                    
                    foreach($result as $key=>$val){
                        $total[$val['stationid']][$val['date']] = $val['total'];
                        //$data['data'][$val['stationid']]['data'][] = intval($val['total']);
                        $data['data'][$val['stationid']]['id'][] = $val['id'];
                        $data['data']['table'][$val['stationid']][] = array('date'=>$val['date'],'total'=>$val['total']);
                    }
                    
                    //比较的数据节点设置
                    foreach($station['allrow'] as $key=>$val){
                        $data['data'][$val['id']]['name'] = $val['stationname'];
                        $data['data'][$val['id']]['marker'] = "square";
                        $data['data']['table'][$val['id']] = is_array($data['data']['table'][$val['id']]) ? $data['data']['table'][$val['id']] : array();
                        $stationChoice[] = $val['id'];
                        foreach($data['xv'] as $k=>$v){
                        
                            $data['data'][$val['id']]['data'][] = $total[$val['id']][$k] != '' ? intval($total[$val['id']][$k]) : 0;
                        
                        }
                        
                    }
                    
                    unset($data['data'][0]);
                    unset($data['data']['table'][0]);
                    
                    $data['data']['num'] = count($station['allrow']);
                    
                }
                
                $data['data']['key'] = array("小时","人数");//table 栏目提示 array按顺序
                
                break;
            
            case 2://本周
            
                $date = date("Y-m-d");
                $bdate = date("Y-m-d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y")));
                $edate = date("Y-m-d",mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y"))); 

                if(date("N",strtotime($date)) == 1){
                    $data = array();
                    $data['error'] = 'WEEKNODATA';
                    break;
                }

                $data = array();
                
                //客流数据                 
                $group = "";
                $dbname = "rha_traffic_daily";
                $pie = $PSys_AccessModel->getPIE($bdate,$edate,$group,$dbname);
                
                foreach($pie as $key=>$val){
                        
                    $data['data_pie'][1]['data'] = $val['newuser'];
                    $data['data_pie'][1]['name'] = '新访客';
                    $data['data_pie'][2]['data'] = $val['olduser']; 
                    $data['data_pie'][2]['name'] = '老访客';
                    
                }
                
                $data['xv_pie_length'] = 1;
                
                if($dataStatusFS == 1){
                    
                    $group = "date";
                    $dbname = "rha_wifi_daily";
                
                    $result = $PSys_AccessModel->getWeek($bdate,$edate,$group,$dbname);
                    
                    //x轴数据
                    $data['xv'] = array();
                    $data['data'][1]['name'] = "所有站点";
                    $data['data'][1]['marker'] = "square";
                    
                    foreach($result as $key=>$val){
                        $total[$val['date']] = $val['total'];
                        $data['data'][1]['id'][] = $val['id'];
                        $data['data']['table'][1][] = array('date'=>$val['date'],'total'=>$val['total']);
                    }
                    
                    for($i=1;$i<=7;$i++){
                        $data['xv'][] = date("Y-m-d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+$i,date("Y")));
                        //$data['data'][1]['data'][] = intval($result[$i-1]['total']);
                        $data['data'][1]['data'][] = $total[date("Y-m-d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+$i,date("Y")))] ? intval($total[date("Y-m-d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+$i,date("Y")))]) : 0;
                    }
                    
                    $data['data']['num'] = 1;
                    $data['data']['key'] = array("日期","人数");//table 栏目提示 array按顺序
                }else{
                    
                    $group = "`date`,`station`";
                    $dbname = "rha_wifi_daily";
            
                    $result = $PSys_AccessModel->getWeek($bdate,$edate,$group,$dbname);
            
                    foreach($result as $key=>$val){
                        $total[$val['station']][$val['date']] = $val['total'];
                        //$data['data'][$val['station']]['data'][] = intval($val['total']);
                        $data['data'][$val['station']]['id'][] = $val['id'];
                        $data['data']['table'][$val['station']][] = array('date'=>$val['date'],'total'=>$val['total']);
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
                            $data['data'][$val['id']]['data'][] = $total[$val['id']][date("Y-m-d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+$i,date("Y")))] ? intval($total[$val['id']][date("Y-m-d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+$i,date("Y")))]) : 0;
                        }
                    }
                    
                    $data['data']['num'] = count($station['allrow']);
                    $data['data']['key'] = array("日期","人数");//table 栏目提示 array按顺序
                }
            
                break;
            
            case 3://本月
            
                $bdate = date("Y-m-d",mktime(0, 0 , 0,date("m"),1,date("Y")));
                $edate = date("Y-m-d",mktime(23,59,59,date("m"),date("t"),date("Y"))); 
                
                if(date("j") == 1){
                    $data = array();
                    $data['error'] = 'MONTHNODATA';
                    break;
                }
                
                $data = array();
                
                //客流数据                 
                $group = "";
                $dbname = "rha_traffic_daily";
                $pie = $PSys_AccessModel->getPIE($bdate,$edate,$group,$dbname);
                
                foreach($pie as $key=>$val){
                        
                    $data['data_pie'][1]['data'] = $val['newuser'];
                    $data['data_pie'][1]['name'] = '新访客';
                    $data['data_pie'][2]['data'] = $val['olduser']; 
                    $data['data_pie'][2]['name'] = '老访客';
                    
                }
                
                $data['xv_pie_length'] = 1;
                
                if($dataStatusFS == 1){
                    
                    $group = "date";
                    $dbname = "rha_wifi_daily";
                
                    $result = $PSys_AccessModel->getWeek($bdate,$edate,$group,$dbname);
                    
                    //x轴数据
                    $data['xv'] = array();
                    $data['data'][1]['name'] = "所有站点";
                    $data['data'][1]['marker'] = "square";
                    //遍历数据
                    foreach($result as $key=>$val){
                        $total[$val['date']] = $val['total'];
                        $data['data']['table'][1][] = array('date'=>$val['date'],'total'=>$val['total']);
                        $data['data'][1]['id'][] = $val['id'];
                    }
                    for($i=1;$i<=date("t");$i++){
                        $data['xv'][] = date("d",mktime(0, 0 , 0,date("m"),$i,date("Y")));
                        $data['data'][1]['data'][] = $total[ date("Y-m-d",mktime(0, 0 , 0,date("m"),$i,date("Y")))] ? intval($total[ date("Y-m-d",mktime(0, 0 , 0,date("m"),$i,date("Y")))]) : 0;
                    }
                    $data['data']['num'] = 1;
                    $data['data']['key'] = array("日期","人数");//table 栏目提示 array按顺序
                
                }else{
                    
                    $group = "`date`,`station`";
                    $dbname = "rha_wifi_daily";
                
                    $result = $PSys_AccessModel->getWeek($bdate,$edate,$group,$dbname);
                    
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
                        $total[$val['date']][$val['station']] = $val['total'];
                        $data['data']['table'][$val['station']][] = array('date'=>$val['date'],'total'=>$val['total']);
                        $data['data'][$val['station']]['id'][] = $val['id'];
                    }
                   
                    
                    for($i=1;$i<=date("t");$i++){
                        $data['xv'][] = date("d",mktime(0, 0 , 0,date("m"),$i,date("Y")));
                        //遍历数据
                        foreach($station['allrow'] as $key=>$val){
                            $data['data'][$val['id']]['data'][] = $total[date("Y-m-d",mktime(0, 0 , 0,date("m"),$i,date("Y")))][$val['id']] == '' ? 0 : intval($total[date("Y-m-d",mktime(0, 0 , 0,date("m"),$i,date("Y")))][$val['id']]);
                            
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
                    
                    case "oneday":
                    
                        $date = reqstr("oneday");
                    
                        $data = array();
                        
                        //客流数据                 
                        $group = "";
                        $dbname = "rha_traffic_daily";
                        $pie = $PSys_AccessModel->getPIE($date,$date,$group,$dbname,$stationChoice);

                        foreach($pie as $key=>$val){
                                
                            $data['data_pie'][1]['data'] = $val['newuser'];
                            $data['data_pie'][1]['name'] = '新访客';
                            $data['data_pie'][2]['data'] = $val['olduser']; 
                            $data['data_pie'][2]['name'] = '老访客';
                            
                        }
                        
                        $data['xv_pie_length'] = 1;
                        
                        //x轴数据
                        $data['xv'] = array('00'=>'0',
                                            '01'=>'1',
                                            '02'=>'2',
                                            '03'=>'3',
                                            '04'=>'4',
                                            '05'=>'5',
                                            '06'=>'6',
                                            '07'=>'7',
                                            '08'=>'8',
                                            '09'=>'9',
                                            '10'=>'10',
                                            '11'=>'11',
                                            '12'=>'12',
                                            '13'=>'13',
                                            '14'=>'14',
                                            '15'=>'15',
                                            '16'=>'16',
                                            '17'=>'17',
                                            '18'=>'18',
                                            '19'=>'19',
                                            '20'=>'20',
                                            '21'=>'21',
                                            '22'=>'22',
                                            '23'=>'23');
                         
                        if($datastatus == 1){
                            
                            $group = "`hour`";
                            $dbname = "rha_aclog_hour";
                            $result = $PSys_AccessModel->getDay($date,$group,$dbname,$stationChoice);
                            
                            //合并的数据节点设置
                            $data['data'][1]['name'] = '所有站点';
                            $data['data'][1]['marker'] = "square";
                            
                            foreach($result as $key=>$val){
                                $total[$val['date']] = $val['total'];
                                $d[$val['date']] = $val['id'];
                                $data['data']['table'][1][] = array('date'=>$val['date'],'total'=>$val['total']);
                            }
                            
                            foreach($data['xv'] as $k=>$v){
                                $data['data'][1]['id'][$v] = $d[$k] ? $d[$k] : 0;
                                $data['data'][1]['data'][] = $total[$k] != '' ? intval($total[$k]) : 0;
                            }
                            
                            $data['data']['num'] = 1;
                            
                            $stationChoice = array();
                            $stationChoice[0] = 1;
                            
                        }else{
                            
                            $group = "`hour`,`stationid`";
                            $dbname = "rha_aclog_hour";
                            $result = $PSys_AccessModel->getDay($date,$group,$dbname,$stationChoice);
            
                            foreach($result as $key=>$val){
                                $total[$val['stationid']][$val['date']] = $val['total'];
                                //$data['data'][$val['stationid']]['data'][] = intval($val['total']);
                                //$data['data'][$val['stationid']]['id'][] = $val['id'];
                                $d[$val['stationid']][$val['date']] = $val['id'];
                                $data['data']['table'][$val['stationid']][] = array('date'=>$val['date'],'total'=>$val['total']);
                            }
                            
                            //重过滤$station   
                            $where = array();
                            $where['id_IN'] = $stationChoice;
                            $order = "id ASC";
                            $field = "id,stationname";  
                            $station = $PSys_AccessModel->GetList($where, $order, 0, 0, $field, "rha_station");
                            
                            //比较的数据节点设置
                            foreach($station['allrow'] as $key=>$val){
                                $data['data'][$val['id']]['name'] = $val['stationname'];
                                $data['data'][$val['id']]['marker'] = "square";
                                if(in_array($val['id'],$stationChoice)){
                                    $data['data']['table'][$val['id']] = is_array($data['data']['table'][$val['id']]) ? $data['data']['table'][$val['id']] : array();
                                }
                                foreach($data['xv'] as $k=>$v){
                                
                                    $data['data'][$val['id']]['data'][] = $total[$val['id']][$k] != '' ? intval($total[$val['id']][$k]) : 0;
                                    $data['data'][$val['id']]['id'][] = $d[$val['id']][$k];
                                
                                }
                                
                            }
                            
                            unset($data['data'][0]);
                            unset($data['data']['table'][0]);
                            
                            $data['data']['num'] = count($station['allrow']);
                            
                        }
                        
                        $data['data']['key'] = array("小时","人数");//table 栏目提示 array按顺序
                        
                        $data['screening']['date'] = '<a class="btn" onclick="screeningSpan(\'close-date\');">单日/'.$date.' <i class="icon-remove"></i><input type="hidden" name="dateSearch" value="oneday" /><input type="hidden" name="oneday" value="'.$date.'" /></a>&nbsp;&nbsp;&nbsp;';
                        
                    break;
                    
                    case "manyday":
                    
                        $bdate = reqstr("bmanyday");
                        $edate = reqstr("emanyday");
                        //计算天数差
                        $Days  = round((strtotime($edate)-strtotime($bdate))/3600/24);
        
                        $data = array();
                        
                        //客流数据                 
                        $group = "";
                        $dbname = "rha_traffic_daily";
                        $pie = $PSys_AccessModel->getPIE($bdate,$edate,$group,$dbname,$stationChoice);

                        foreach($pie as $key=>$val){
                                
                            $data['data_pie'][1]['data'] = $val['newuser'];
                            $data['data_pie'][1]['name'] = '新访客';
                            $data['data_pie'][2]['data'] = $val['olduser']; 
                            $data['data_pie'][2]['name'] = '老访客';
                            
                        }
                        
                        $data['xv_pie_length'] = 1;
                        
                        if($datastatus == 1){
                            
                            $group = "date";
                            $dbname = "rha_wifi_daily";
                        
                            $result = $PSys_AccessModel->getWeek($bdate,$edate,$group,$dbname,$stationChoice);
                            
                            //print_r($result);
                            
                            //x轴数据
                            $data['xv'] = array();
                            $data['data'][1]['name'] = "所有站点";
                            $data['data'][1]['marker'] = "square";
                            
                            foreach($result as $key=>$val){
                                $total[$val['date']] = $val['total'];
                                $data['data']['table'][1][] = array('date'=>$val['date'],'total'=>$val['total']);
                                $data['data'][$val['station']]['id'][] = $val['id'];
                            }
                            for($i=0;$i<=$Days;$i++){
                                $day = $i;
                                $data['xv'][] = date("Y-m-d",strtotime("$bdate +$day day"));
                                //$data['data'][1]['data'][] = intval($result[$i-1]['total']);
                                $data['data'][1]['data'][] = $total[date("Y-m-d",strtotime("$bdate +$day day"))] ? intval($total[date("Y-m-d",strtotime("$bdate +$day day"))]) : 0;
                            }
                            
                            $data['data']['num'] = 1;
                            
                            $stationChoice = array();
                            $stationChoice[0] = 1; //to do...
                            
                        }else{
                            
                            $group = "`date`,`station`";
                            $dbname = "rha_wifi_daily";
                    
                            $result = $PSys_AccessModel->getWeek($bdate,$edate,$group,$dbname,$stationChoice);
                            
                            foreach($result as $key=>$val){
                                $total[$val['station']][$val['date']] = $val['total'];
                                //$data['data'][$val['station']]['data'][] = intval($val['total']);
                                if(in_array($val['station'],$stationChoice)){
                                    $data['data']['table'][$val['station']][] = array('date'=>$val['date'],'total'=>$val['total']);
                                }
                                $data['data'][$val['station']]['id'][] = $val['id'];
                            }
                            
                            //重过滤$station   
                            $where = array();
                            $where['id_IN'] = $stationChoice;
                            $order = "id ASC";
                            $field = "id,stationname";  
                            $station = $PSys_AccessModel->GetList($where, $order, 0, 0, $field, "rha_station");
                            
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
                                $data['xv'][] = date("Y-m-d",strtotime("$bdate +$day day"));
                                //$data['data'][1]['data'][] = intval($result[$i-1]['total']);
                                foreach($station['allrow'] as $key=>$val){
                                    $data['data'][$val['id']]['data'][] = $total[$val['id']][date("Y-m-d",strtotime("$bdate +$day day"))] ? intval($total[$val['id']][date("Y-m-d",strtotime("$bdate +$day day"))]) : 0;
                                }
                            }
                            
                            $data['data']['num'] = count($station['allrow']);
                            
                        }
                        
                        $data['data']['key'] = array("日期","人数");//table 栏目提示 array按顺序
                        $data['screening']['date'] = '<a class="btn" onclick="screeningSpan(\'close-date\');">多日/'.$bdate .' ~ '.$edate.' <i class="icon-remove"></i><input type="hidden" name="dateSearch" value="manyday" /><input type="hidden" name="manyday" value="'.$date.'" /></a>&nbsp;&nbsp;&nbsp;';
                        
                    break;
                    
                    case "oneweek":
               
                        $day = reqstr("oneweek");
                        $edate=date('Y-m-d',strtotime("$day Sunday")); 
                        $bdate=date('Y-m-d',strtotime("$edate -6 days"));
                        //计算天数差
                        $Days  = round((strtotime($edate)-strtotime($bdate))/3600/24);
                        
                        if(date("N",strtotime($bdate)) == 1 && $bdate == date("Y-m-d")){
                            $data = array();
                            $data['error'] = 'WEEKNODATA';
                            break;
                        }

                        $data = array();
                        
                        //客流数据                 
                        $group = "";
                        $dbname = "rha_traffic_daily";
                        $pie = $PSys_AccessModel->getPIE($bdate,$edate,$group,$dbname,$stationChoice);

                        foreach($pie as $key=>$val){
                                
                            $data['data_pie'][1]['data'] = $val['newuser'];
                            $data['data_pie'][1]['name'] = '新访客';
                            $data['data_pie'][2]['data'] = $val['olduser']; 
                            $data['data_pie'][2]['name'] = '老访客';
                            
                        }
                        
                        $data['xv_pie_length'] = 1;
                        
                        if($datastatus == 1){
                            
                            $group = "date";
                            $dbname = "rha_wifi_daily";
                        
                            $result = $PSys_AccessModel->getWeek($bdate,$edate,$group,$dbname,$stationChoice);
                            
                            //x轴数据
                            $data['xv'] = array();
                            $data['data'][1]['name'] = "所有站点";
                            $data['data'][1]['marker'] = "square";
                            
                            foreach($result as $key=>$val){
                                $total[$val['date']] = $val['total'];
                                $data['data']['table'][1][] = array('date'=>$val['date'],'total'=>$val['total']);
                                $data['data'][$val['station']]['id'][] = $val['id'];
                            }
                            for($i=0;$i<=$Days;$i++){
                                $day = $i;
                                $data['xv'][] = date("Y-m-d",strtotime("$bdate +$day day"));
                                //$data['data'][1]['data'][] = intval($result[$i-1]['total']);
                                $data['data'][1]['data'][] = $total[date("Y-m-d",strtotime("$bdate +$day day"))] ? intval($total[date("Y-m-d",strtotime("$bdate +$day day"))]) : 0;
                                
                            }
                            
                            $data['data']['num'] = 1;
                            
                            $stationChoice = array();
                            $stationChoice[0] = 1; //to do...
                            
                        }else{
                            
                            $group = "`date`,`station`";
                            $dbname = "rha_wifi_daily";
                    
                            $result = $PSys_AccessModel->getWeek($bdate,$edate,$group,$dbname,$stationChoice);
                            
                            foreach($result as $key=>$val){
                                $total[$val['station']][$val['date']] = $val['total'];
                                //$data['data'][$val['station']]['data'][] = intval($val['total']);
                                $data['data']['table'][$val['station']][] = array('date'=>$val['date'],'total'=>$val['total']);
                                $data['data'][$val['station']]['id'][] = $val['id'];
                            }
                            
                            //重过滤$station   
                            $where = array();
                            $where['id_IN'] = $stationChoice;
                            $order = "id ASC";
                            $field = "id,stationname";  
                            $station = $PSys_AccessModel->GetList($where, $order, 0, 0, $field, "rha_station");
                            
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
                                $data['xv'][] = date("Y-m-d",strtotime("$bdate +$day day"));
                                //$data['data'][1]['data'][] = intval($result[$i-1]['total']);
                                foreach($station['allrow'] as $key=>$val){
                                    $data['data'][$val['id']]['data'][] = $total[$val['id']][date("Y-m-d",strtotime("$bdate +$day day"))] ? intval($total[$val['id']][date("Y-m-d",strtotime("$bdate +$day day"))]) : 0;
                                }
                            }
                            
                            $data['data']['num'] = count($station['allrow']);

                        }
                        
                        $data['data']['key'] = array("日期","人数");//table 栏目提示 array按顺序
                        $data['screening']['date'] = '<a class="btn" onclick="screeningSpan(\'close-date\');">单周/'.$bdate .' ~ '.$edate.' <i class="icon-remove"></i><input type="hidden" name="dateSearch" value="oneweek" /><input type="hidden" name="oneweek" value="'.$date.'" /></a>&nbsp;&nbsp;&nbsp;';
                        
                    break;
                    
                    case "manyweek":
                    
                        $bday = reqstr("bmanyweek");
                        $edate2=date('Y-m-d',strtotime("$bday Sunday")); 
                        $bdate=date('Y-m-d',strtotime("$edate2 -6 days"));
                        
                        $eday = reqstr("emanyweek");
                        $edate=date('Y-m-d',strtotime("$eday Sunday")); 
                        $bdate2=date('Y-m-d',strtotime("$edate -6 days"));
                        
                        //计算周数差
                        $Weeks  = round((strtotime($edate)-strtotime($bdate))/3600/24/7);
                        
                        $data = array();
                        
                        //客流数据                 
                        $group = "";
                        $dbname = "rha_traffic_daily";
                        $pie = $PSys_AccessModel->getPIE($bdate,$edate,$group,$dbname,$stationChoice);

                        foreach($pie as $key=>$val){
                                
                            $data['data_pie'][1]['data'] = $val['newuser'];
                            $data['data_pie'][1]['name'] = '新访客';
                            $data['data_pie'][2]['data'] = $val['olduser']; 
                            $data['data_pie'][2]['name'] = '老访客';
                            
                        }
                        
                        $data['xv_pie_length'] = 1;
                        
                        if($datastatus == 1){
                            
                            $group = "date";
                            $dbname = "rha_wifi_daily";
                        
                            $result = $PSys_AccessModel->getWeek($bdate,$edate,$group,$dbname,$stationChoice);
                            
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
                                        $data['xv'][$i] = date("Y/m/d",strtotime("$bdate +$day day"));
                                    }
                                    if($j == 7){
                                        $data['xv'][$i] .= ' - ' . date("m/d",strtotime("$bdate +$day day"));
                                    }
                                    //$data['data'][1]['data'][] = intval($result[$i-1]['total']);
                                    $data['data'][1]['data'][$i] += $total[date("Y-m-d",strtotime("$bdate +$day day"))] ? intval($total[date("Y-m-d",strtotime("$bdate +$day day"))]) : 0;
                                }
                                
                                $data['data'][1]['id'][] = $result[$i*7]['date'];
                                $data['data']['table'][1][] = array('date'=>$data['xv'][$i],'total'=>$data['data'][1]['data'][$i]);
                            }
                            
                            $data['data']['num'] = 1;
                            
                            $stationChoice = array();
                            $stationChoice[0] = 1; //to do...
                            
                        }else{
                            
                            $group = "`date`,`station`";
                            $dbname = "rha_wifi_daily";
                    
                            $result = $PSys_AccessModel->getWeek($bdate,$edate,$group,$dbname,$stationChoice);
                            
                            foreach($result as $key=>$val){
                                $total[$val['station']][$val['date']] = $val['total'];
                            }
                            
                            //重过滤$station   
                            $where = array();
                            $where['id_IN'] = $stationChoice;
                            $order = "id ASC";
                            $field = "id,stationname";  
                            $station = $PSys_AccessModel->GetList($where, $order, 0, 0, $field, "rha_station");
                            
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
                                        $data['xv'][$i] = date("Y/m/d",strtotime("$bdate +$day day"));
                                    }
                                    if($j == 7){
                                        $data['xv'][$i] .= ' - ' . date("m/d",strtotime("$bdate +$day day"));
                                    }
                                    //to do ..... 2周还是读天数表  see you tomorrow
                                    //$data['data'][1]['data'][] = intval($result[$i-1]['total']);
                                    foreach($station['allrow'] as $key=>$val){
                                        $data['data'][$val['id']]['data'][$i] += $total[$val['id']][date("Y-m-d",strtotime("$bdate +$day day"))] ? intval($total[$val['id']][date("Y-m-d",strtotime("$bdate +$day day"))]) : 0;
                                        $data['data'][$val['id']]['id'][$i] = $result[$i*7*count($station['allrow'])]['date'];
                                        $data['data']['table'][$val['id']][$i]['date'] = $data['xv'][$i];
                                        $data['data']['table'][$val['id']][$i]['total'] += intval($total[$val['id']][date("Y-m-d",strtotime("$bdate +$day day"))]);
                                    }
                                    
                                }
                                
                            }
                            
                            $data['data']['num'] = count($station['allrow']);
                            
                        }
                        $data['data']['key'] = array("日期","人数");//table 栏目提示 array按顺序
                        $data['screening']['date'] = '<a class="btn" onclick="screeningSpan(\'close-date\');">多周/'.$bdate .' ~ '.$edate.' <i class="icon-remove"></i><input type="hidden" name="dateSearch" value="manyweek" /><input type="hidden" name="manyweek" value="'.$date.'" /></a>&nbsp;&nbsp;&nbsp;';
                        
                    break;
                    
                    case "onemonth":
                    
                        $date = reqstr("onemonth");
                        $bdate = date("Y-m-01",strtotime($date));
                        $edate = date("Y-m-d",strtotime("$bdate +1 month -1 day"));
                        
                        $data = array();
                        
                        //客流数据                 
                        $group = "";
                        $dbname = "rha_traffic_daily";
                        $pie = $PSys_AccessModel->getPIE($bdate,$edate,$group,$dbname,$stationChoice);

                        foreach($pie as $key=>$val){
                                
                            $data['data_pie'][1]['data'] = $val['newuser'];
                            $data['data_pie'][1]['name'] = '新访客';
                            $data['data_pie'][2]['data'] = $val['olduser']; 
                            $data['data_pie'][2]['name'] = '老访客';
                            
                        }
                        
                        $data['xv_pie_length'] = 1;
                        
                        if($datastatus == 1){
                            
                            $group = "date";
                            $dbname = "rha_wifi_daily";
                        
                            $result = $PSys_AccessModel->getWeek($bdate,$edate,$group,$dbname,$stationChoice);
                            
                            //x轴数据
                            $data['xv'] = array();
                            $data['data'][1]['name'] = "所有站点";
                            $data['data'][1]['marker'] = "square";
                            //遍历数据
                            foreach($result as $key=>$val){
                                $total[$val['date']] = $val['total'];
                                $data['data']['table'][1][] = array('date'=>$val['date'],'total'=>$val['total']);
                                $data['data'][1]['id'][] = $val['date'];
                            }
                            for($i=1;$i<=date("t",strtotime($date));$i++){
                                $day = $i - 1;
                                $data['xv'][] = date("d",strtotime("$date + $day day"));
                                $data['data'][1]['data'][] = $total[date("Y-m-d",strtotime("$bdate +$day day"))] ? intval($total[date("Y-m-d",strtotime("$bdate +$day day"))]) : 0;
                            }
                            $data['data']['num'] = 1;
                            
                            $stationChoice = array();
                            $stationChoice[0] = 1; //to do...
                        
                        }else{
                            
                            $group = "`date`,`station`";
                            $dbname = "rha_wifi_daily";
                        
                            $result = $PSys_AccessModel->getWeek($bdate,$edate,$group,$dbname,$stationChoice);
                            
                            //重过滤$station   
                            $where = array();
                            $where['id_IN'] = $stationChoice;
                            $order = "id ASC";
                            $field = "id,stationname";  
                            $station = $PSys_AccessModel->GetList($where, $order, 0, 0, $field, "rha_station");
                            
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
                                $total[$val['date']][$val['station']] = $val['total'];
                                if(in_array($val['station'],$stationChoice)){
                                    $data['data']['table'][$val['station']][] = array('date'=>$val['date'],'total'=>$val['total']);
                                }
                                $data['data'][$val['station']]['id'][] = $val['date'];
                            }
                                     
                            for($i=1;$i<=date("t",strtotime($date));$i++){
                                $day = $i - 1;
                                $data['xv'][] = date("d",strtotime("$date + $day day"));
                                
                                //遍历数据
                                foreach($station['allrow'] as $key=>$val){
                                    $data['data'][$val['id']]['data'][] = $total[date("Y-m-d",strtotime("$bdate +$day day"))][$val['id']] ? intval($total[date("Y-m-d",strtotime("$bdate +$day day"))][$val['id']]) : 0;
                                    //$data['data'][$val['id']]['id'][$i] = $result[$i*date("t",strtotime($date))*count($station['allrow'])]['date'];
                                }
                            }
                                                        
                            
                            $data['data']['num'] = count($station['allrow']);
                        }
                        
                        $data['data']['key'] = array("日期","人数");//table 栏目提示 array按顺序
                        $data['screening']['date'] = '<a class="btn" onclick="screeningSpan(\'close-date\');">单月/'.$bdate .' <i class="icon-remove"></i><input type="hidden" name="dateSearch" value="onemonth" /><input type="hidden" name="onemonth" value="'.$date.'" /></a>&nbsp;&nbsp;&nbsp;';
                        
                    break;
                    
                    case "manymonth":
                    
                        $bday = reqstr("bmanymonth");
                        $bdate = date("Y-m-01",strtotime($bday));
                        
                        $eday = reqstr("emanymonth");
                        $edate = date("Y-m-d",strtotime("$eday +1 month -1 day"));
                        
                        //计算月数差
                        $Months = (date("Y",strtotime($eday)) - date("Y",strtotime($bday)))*12+(date("m",strtotime($eday))-date("m",strtotime($bday))) + 1;
                        
                        for($i=0;$i<$Months;$i++){
                            $dateArr[$i] = date("Y-m",strtotime("$bday +$i month"));
                        }
                        
                        $data = array();
                        
                        //客流数据                 
                        $group = "";
                        $dbname = "rha_traffic_daily";
                        $pie = $PSys_AccessModel->getPIE($bdate,$edate,$group,$dbname,$stationChoice);

                        foreach($pie as $key=>$val){
                                
                            $data['data_pie'][1]['data'] = $val['newuser'];
                            $data['data_pie'][1]['name'] = '新访客';
                            $data['data_pie'][2]['data'] = $val['olduser']; 
                            $data['data_pie'][2]['name'] = '老访客';
                            
                        }
                        
                        $data['xv_pie_length'] = 1;
                        
                        if($datastatus == 1){
                            
                            $group = "date";
                            $dbname = "rha_wifi_daily";
                        
                            $result = $PSys_AccessModel->getWeek($bdate,$edate,$group,$dbname,$stationChoice);
                            
                            //x轴数据
                            $data['xv'] = array();
                            $data['data'][1]['name'] = "所有站点";
                            $data['data'][1]['marker'] = "square";

                            foreach($result as $key=>$val){
                                $total[$val['date']] = $val['total'];
                                for($i=0;$i<count($dateArr);$i++){
                                    if($dateArr[$i] == date("Y-m",strtotime($val['date']))){
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
                                    $data['data'][1]['data'][$i] += $total[date("Y-m-d",strtotime("$bday +$day day"))] ? intval($total[date("Y-m-d",strtotime("$bday +$day day"))]) : 0;
                                    $day++;
                                }
                                $data['data'][1]['id'][] = $d;
                            }
                            
                            $data['data']['num'] = 1;
                            
                            $stationChoice = array();
                            $stationChoice[0] = 1; //to do...
                            
                        }else{
                            
                            $group = "`date`,`station`";
                            $dbname = "rha_wifi_daily";
                    
                            $result = $PSys_AccessModel->getWeek($bdate,$edate,$group,$dbname,$stationChoice);
                            
                            foreach($result as $key=>$val){
                                $total[$val['station']][$val['date']] = $val['total'];
                                //$data['data'][$val['station']]['data'][] = intval($val['total']);
                                //$data['data']['table'][$val['station']][] = array('date'=>$val['date'],'total'=>$val['total']);
                                for($i=0;$i<count($dateArr);$i++){
                                    
                                    //创建初始array
                                    if($data['data']['table'][$val['station']][$i]['date'] == ''){
                                        $data['data']['table'][$val['station']][$i]['date'] = $dateArr[$i];
                                        $data['data']['table'][$val['station']][$i]['total'] = 0;
                                    }
                                    
                                    
                                    if($dateArr[$i] == date("Y-m",strtotime($val['date']))){
                                        $data['data']['table'][$val['station']][$i]['date'] = $dateArr[$i];
                                        $data['data']['table'][$val['station']][$i]['total'] += $val['total'];
                                    }
                                }
                            }
                            
                            //重过滤$station   
                            $where = array();
                            $where['id_IN'] = $stationChoice;
                            $order = "id ASC";
                            $field = "id,stationname";  
                            $station = $PSys_AccessModel->GetList($where, $order, 0, 0, $field, "rha_station");
                            
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
                                $d = date("Y-m-01",strtotime("$bday +$i month"));
                                for($j=1;$j<=$num;$j++){
                                    if($j == 1){
                                        $data['xv'][$i] = date("Y/m",strtotime("$bday +$i month"));
                                    }
                                    //echo $day."<br/>";
                                    //$data['data'][1]['data'][] = intval($result[$i-1]['total']);
                                    foreach($station['allrow'] as $key=>$val){
                                        $data['data'][$val['id']]['data'][$i] += $total[$val['id']][date("Y-m-d",strtotime("$bday +$day day"))] ? intval($total[$val['id']][date("Y-m-d",strtotime("$bday +$day day"))]) : 0;
                                        $data['data'][$val['id']]['id'][$i] = $d;
                                    }
                                    $day++;
                                }
                                
                            }
                            
                            $data['data']['num'] = count($station['allrow']);
                            
                        }
                        
                        $data['data']['key'] = array("日期","人数");//table 栏目提示 array按顺序
                        $data['screening']['date'] = '<a class="btn" onclick="screeningSpan(\'close-date\');">多月/'.$bdate . "~" . $edate .' <i class="icon-remove"></i><input type="hidden" name="dateSearch" value="manymonth" /><input type="hidden" name="manymonth" value="'.$date.'" /></a>&nbsp;&nbsp;&nbsp;';
                    
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
    * @do ajax 访问 某时段 数据
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function ajaxUVChainDiagramAction(){
        
        $PSys_AccessModel = new PSys_AccessModel();
        
        $stationC = reqarray("stationC");
        $fastSearch = reqnum("fastSearch",0);
        $id = reqstr("id");
        
        //$station   
        $where = array();
        //$where['id_IN'] = $stationChoice;
        $order = "id ASC";
        $field = "id,stationname";
        $station = $PSys_AccessModel->GetList($where, $order, 0, 0, $field, "rha_station");
        
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
                $result2 = $PSys_AccessModel->getDayChainDiagramDetail($id,$dbname);
                $result = $PSys_AccessModel->getDayChainDiagram($result2,$dbname,$stationChoice);
                
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
                $result2 = $PSys_AccessModel->getWeekChainDiagramDetail($id,$dbname);
                $result = $PSys_AccessModel->getWeekChainDiagram($result2,$dbname,$stationChoice);
                
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
                $result2 = $PSys_AccessModel->getWeekChainDiagramDetail($id,$dbname);
                $result = $PSys_AccessModel->getWeekChainDiagram($result2,$dbname,$stationChoice);
                
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
                        $result2 = $PSys_AccessModel->getDayChainDiagramDetail($id,$dbname);
                        $result = $PSys_AccessModel->getDayChainDiagram($result2,$dbname,$stationChoice);
                        
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
                        $result2 = $PSys_AccessModel->getWeekChainDiagramDetail($id,$dbname);
                        $result = $PSys_AccessModel->getWeekChainDiagram($result2,$dbname,$stationChoice);
                        
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
                        $result2 = $PSys_AccessModel->getWeekChainDiagramDetail($id,$dbname);
                        $result = $PSys_AccessModel->getWeekChainDiagram($result2,$dbname,$stationChoice);
                        
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
                        $result2 = $PSys_AccessModel->getManyWeekChainDiagramDetail($id,$dbname);
                        $result = $PSys_AccessModel->getManyWeekChainDiagram($result2,$dbname,$stationChoice);
                        
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
                        $result2 = $PSys_AccessModel->getOneMonthChainDiagramDetail($id,$dbname);
                        $result = $PSys_AccessModel->getOneMonthChainDiagram($result2,$dbname,$stationChoice);
                        
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
                        $result2 = $PSys_AccessModel->getManyMonthChainDiagramDetail($id,$dbname);
                        $result = $PSys_AccessModel->getManyMonthChainDiagram($result2,$dbname,$stationChoice);
                        
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
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function ajaxOutputExcelAction(){
        
        $wordArr = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
        
        $PSys_AccessModel = new PSys_AccessModel();
        
        //站点数据
        $where = array();
        $order = "id ASC";
        $field = "id,stationname";
	   
        $PSys_AccessModel = new PSys_AccessModel();
        $station = $PSys_AccessModel->GetList($where, $order, 0, 0, $field, "rha_station");
        
        
        $fastSearch = reqnum("fastSearch",0);//快速查询
        $dataStatusFS = reqnum("datastatusFS",1);//快速查询合并、比较筛选按钮 1为合并 2为比较  默认合并
        
        $xv = array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23');
        
        switch($fastSearch){
            
            case 1://当天
            $date = date("Y-m-d");
            
            $data = array();
            
            if($dataStatusFS == 1){
                
                $group = "`hour`";
                $dbname = "rha_aclog_hour";
                $result = $PSys_AccessModel->getDay($date,$group,$dbname);
                
                $XPhpExcel = new XPhpExcel();
                $Creator = "statis.rockhippo.com";
                $LastModifiedBy = $Creator;
                $Title = '访问统计-访问人数';
                $Subject = '本日-合并';
                $Description = '访问统计-访问人数(本日-合并)';
                $Keywords = $Description;
                $Category = $Description;
                $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
                $XPhpExcel->setSheet(0,$Subject);
                
                $XPhpExcel->setValue("A1","小时");
                $XPhpExcel->setValue("B1","人数");
                
                $XPhpExcel->setWidth("B",20,2);
                
                foreach($result as $key=>$val){
                    
                    $XPhpExcel->setValue("A".($key+2),$val["date"]);
                    $XPhpExcel->setValue("B".($key+2),$val["total"]);
                    
                } 
            
                //清空输出缓存                    
                ob_clean();                    
                $XPhpExcel->output($date."/".$Description);
                unset($XPhpExcel);//销毁

            }else{
                
                $group = "`hour`,`stationid`";
                $dbname = "rha_aclog_hour";
                $result = $PSys_AccessModel->getDay($date,$group,$dbname);
                
                $XPhpExcel = new XPhpExcel();
                $Creator = "statis.rockhippo.com";
                $LastModifiedBy = $Creator;
                $Title = '访问统计-访问人数';
                $Subject = '本日-比较';
                $Description = '访问统计-访问人数(本日-比较)';
                $Keywords = $Description;
                $Category = $Description;
                $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
                $XPhpExcel->setSheet(0,$Subject);
                $XPhpExcel->setValue("A1","小时");
                
                for($i=0;$i<count($xv);$i++){
                    $XPhpExcel->setValue("A".($i+2),$i);
                }
                
                $data = array();
                
                //遍历数据获取所需结构
                foreach($result as $key=>$val){
                    $data[$val['stationid']][$val['date']] = $val['total'];
                } 
                
                foreach($station['allrow'] as $key=>$val){
                    $XPhpExcel->setValue($wordArr[$val['id']]."1",$val['stationname']."/人数");
                    $XPhpExcel->setWidth($wordArr[$val['id']],20,2);
                    for($i=0;$i<count($xv);$i++){
                        //echo $wordArr[$val['id']].($i+2) . "~" . $data[$val['id']][$xv[$i]] . "<br>";
                        $XPhpExcel->setValue($wordArr[$val['id']].($i+2),$data[$val['id']][$xv[$i]]);
                    }
                }

                //清空输出缓存                    
                ob_clean();    
                              
                $XPhpExcel->output($date."/".$Description);
                unset($XPhpExcel);//销毁
                
            }
            
            break;
        
        case 2://本周
        
            $date = date("Y-m-d");
            $bdate = date("Y-m-d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y")));
            $edate = date("Y-m-d",mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y"))); 

            $data = array();
            
            if($dataStatusFS == 1){
                
                $group = "date";
                $dbname = "rha_wifi_daily";
            
                $result = $PSys_AccessModel->getWeek($bdate,$edate,$group,$dbname);
                
                $XPhpExcel = new XPhpExcel();
                $Creator = "statis.rockhippo.com";
                $LastModifiedBy = $Creator;
                $Title = '访问统计-访问人数';
                $Subject = '本周-合并';
                $Description = '访问统计-访问人数(本周-合并)';
                $Keywords = $Description;
                $Category = $Description;
                $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
                $XPhpExcel->setSheet(0,$Subject);
                
                $XPhpExcel->setValue("A1","日期");
                $XPhpExcel->setValue("B1","人数");
                
                $XPhpExcel->setWidth("A",14,2);
                $XPhpExcel->setWidth("B",20,2);
                
                foreach($result as $key=>$val){
                    
                    $XPhpExcel->setValue("A".($key+2),$val["date"]);
                    $XPhpExcel->setValue("B".($key+2),$val["total"]);
                    
                } 
            
                //清空输出缓存                    
                ob_clean();                    
                $XPhpExcel->output($bdate."~".$edate."/".$Description);
                unset($XPhpExcel);//销毁
                
            }else{
                
                $group = "`date`,`station`";
                $dbname = "rha_wifi_daily";
        
                $result = $PSys_AccessModel->getWeek($bdate,$edate,$group,$dbname);
        
                $XPhpExcel = new XPhpExcel();
                $Creator = "statis.rockhippo.com";
                $LastModifiedBy = $Creator;
                $Title = '访问统计-访问人数';
                $Subject = '本周-比较';
                $Description = '访问统计-访问人数(本周-比较)';
                $Keywords = $Description;
                $Category = $Description;
                $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
                $XPhpExcel->setSheet(0,$Subject);
                $XPhpExcel->setValue("A1","日期");
                $XPhpExcel->setWidth("A",14,2);
                
                for($i=0;$i<7;$i++){
                    $XPhpExcel->setValue("A".($i+2),date("Y-m-d",strtotime("$bdate +$i day")));
                }
                
                $data = array();
                
                //遍历数据获取所需结构
                foreach($result as $key=>$val){
                    $data[$val['station']][$val['date']] = $val['total'];
                } 
                
                foreach($station['allrow'] as $key=>$val){
                    $XPhpExcel->setValue($wordArr[$val['id']]."1",$val['stationname']."/人数");
                    $XPhpExcel->setWidth($wordArr[$val['id']],20,2);
                    for($i=0;$i<7;$i++){
                        //echo $wordArr[$val['id']].($i+2) . "~" . $data[$val['id']][$xv[$i]] . "<br>";
                        $XPhpExcel->setValue($wordArr[$val['id']].($i+2),$data[$val['id']][date("Y-m-d",strtotime("$bdate +$i day"))]);
                    }
                }

                //清空输出缓存                    
                ob_clean();    
                              
                $XPhpExcel->output($date."/".$Description);
                unset($XPhpExcel);//销毁
                
                
            }
        
            break;
        
        case 3://本月
        
            $bdate = date("Y-m-d",mktime(0, 0 , 0,date("m"),1,date("Y")));
            $edate = date("Y-m-d",mktime(23,59,59,date("m"),date("t"),date("Y"))); 
            
            $date = date("Y-m");
            
            $data = array();
            
            if($dataStatusFS == 1){
                
                $group = "date";
                $dbname = "rha_wifi_daily";
            
                $result = $PSys_AccessModel->getWeek($bdate,$edate,$group,$dbname);
                
                $XPhpExcel = new XPhpExcel();
                $Creator = "statis.rockhippo.com";
                $LastModifiedBy = $Creator;
                $Title = '访问统计-访问人数';
                $Subject = '本月-合并';
                $Description = '访问统计-访问人数(本月-合并)';
                $Keywords = $Description;
                $Category = $Description;
                $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
                $XPhpExcel->setSheet(0,$Subject);
                
                $XPhpExcel->setValue("A1","日期");
                $XPhpExcel->setValue("B1","人数");
                
                $XPhpExcel->setWidth("A",14,2);
                $XPhpExcel->setWidth("B",20,2);
                
                foreach($result as $key=>$val){
                    
                    $XPhpExcel->setValue("A".($key+2),$val["date"]);
                    $XPhpExcel->setValue("B".($key+2),$val["total"]);
                    
                } 
            
                //清空输出缓存                    
                ob_clean();                    
                $XPhpExcel->output($date."/".$Description);
                unset($XPhpExcel);//销毁
            
            }else{
                
                $group = "`date`,`station`";
                $dbname = "rha_wifi_daily";
            
                $result = $PSys_AccessModel->getWeek($bdate,$edate,$group,$dbname);
                
                $XPhpExcel = new XPhpExcel();
                $Creator = "statis.rockhippo.com";
                $LastModifiedBy = $Creator;
                $Title = '访问统计-访问人数';
                $Subject = '本月-比较';
                $Description = '访问统计-访问人数(本月-比较)';
                $Keywords = $Description;
                $Category = $Description;
                $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
                $XPhpExcel->setSheet(0,$Subject);
                $XPhpExcel->setValue("A1","日期");
                $XPhpExcel->setWidth("A",14,2);
                
                for($i=0;$i<date("t");$i++){
                    $XPhpExcel->setValue("A".($i+2),date("Y-m-d",strtotime("$bdate +$i day")));
                }
                
                $data = array();
                
                //遍历数据获取所需结构
                foreach($result as $key=>$val){
                    $data[$val['station']][$val['date']] = $val['total'];
                } 
                
                foreach($station['allrow'] as $key=>$val){
                    $XPhpExcel->setValue($wordArr[$val['id']]."1",$val['stationname']."/人数");
                    $XPhpExcel->setWidth($wordArr[$val['id']],20,2);
                    for($i=0;$i<date("t");$i++){
                        //echo $wordArr[$val['id']].($i+2) . "~" . $data[$val['id']][$xv[$i]] . "<br>";
                        $XPhpExcel->setValue($wordArr[$val['id']].($i+2),$data[$val['id']][date("Y-m-d",strtotime("$bdate +$i day"))]);
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
                
                case "oneday":
                
                    $date = reqstr("oneday");
                
                    $data = array();
                    
                    //x轴数据
                    $xv = array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23');
                     
                    if($datastatus == 1){
                        
                        $group = "`hour`";
                        $dbname = "rha_aclog_hour";
                        $result = $PSys_AccessModel->getDay($date,$group,$dbname,$stationChoice);
                        
                        $XPhpExcel = new XPhpExcel();
                        $Creator = "statis.rockhippo.com";
                        $LastModifiedBy = $Creator;
                        $Title = '访问统计-访问人数';
                        $Subject = '单日-合并';
                        $Description = '访问统计-访问人数(单日-合并)';
                        $Keywords = $Description;
                        $Category = $Description;
                        $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
                        $XPhpExcel->setSheet(0,$Subject);
                        
                        $XPhpExcel->setValue("A1","小时");
                        $XPhpExcel->setValue("B1","人数");
                        
                        $XPhpExcel->setWidth("B",20,2);
                        
                        foreach($result as $key=>$val){
                            
                            $XPhpExcel->setValue("A".($key+2),$val["date"]);
                            $XPhpExcel->setValue("B".($key+2),$val["total"]);
                            
                        } 
                    
                        //清空输出缓存                    
                        ob_clean();                    
                        $XPhpExcel->output($date."/".$Description);
                        unset($XPhpExcel);//销毁
                        
                    }else{
                        
                        $group = "`hour`,`stationid`";
                        $dbname = "rha_aclog_hour";
                        $result = $PSys_AccessModel->getDay($date,$group,$dbname,$stationChoice);
        
                        $XPhpExcel = new XPhpExcel();
                        $Creator = "statis.rockhippo.com";
                        $LastModifiedBy = $Creator;
                        $Title = '访问统计-访问人数';
                        $Subject = '单日-比较';
                        $Description = '访问统计-访问人数(单日-比较)';
                        $Keywords = $Description;
                        $Category = $Description;
                        $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
                        $XPhpExcel->setSheet(0,$Subject);
                        $XPhpExcel->setValue("A1","小时");
                        
                        for($i=0;$i<count($xv);$i++){
                            $XPhpExcel->setValue("A".($i+2),$i);
                        }
                        
                        $data = array();
                        
                        //遍历数据获取所需结构
                        foreach($result as $key=>$val){
                            $data[$val['stationid']][$val['date']] = $val['total'];
                        } 
                    
                        foreach($station['allrow'] as $key=>$val){
                            $XPhpExcel->setValue($wordArr[$val['id']]."1",$val['stationname']."/人数");
                            $XPhpExcel->setWidth($wordArr[$val['id']],20,2);
                            for($i=0;$i<count($xv);$i++){
                                //echo $wordArr[$val['id']].($i+2) . "~" . $data[$val['id']][$xv[$i]] . "<br>";
                                $XPhpExcel->setValue($wordArr[$val['id']].($i+2),$data[$val['id']][$xv[$i]]);
                            }
                        }
        
                        //清空输出缓存                    
                        ob_clean();    
                                      
                        $XPhpExcel->output($date."/".$Description);
                        unset($XPhpExcel);//销毁
                        
                    }
                    
                break;
                
                case "manyday":
                
                    $bdate = reqstr("bmanyday");
                    $edate = reqstr("emanyday");
                    //计算天数差
                    $Days  = round((strtotime($edate)-strtotime($bdate))/3600/24);
    
                    $data = array();
                    
                    if($datastatus == 1){
                        
                        $group = "date";
                        $dbname = "rha_wifi_daily";
                    
                        $result = $PSys_AccessModel->getWeek($bdate,$edate,$group,$dbname,$stationChoice);
                        
                        $XPhpExcel = new XPhpExcel();
                        $Creator = "statis.rockhippo.com";
                        $LastModifiedBy = $Creator;
                        $Title = '访问统计-访问人数';
                        $Subject = '多日-合并';
                        $Description = '访问统计-访问人数(多日-合并)';
                        $Keywords = $Description;
                        $Category = $Description;
                        $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
                        $XPhpExcel->setSheet(0,$Subject);
                        
                        $XPhpExcel->setValue("A1","日期");
                        $XPhpExcel->setValue("B1","人数");
                        
                        $XPhpExcel->setWidth("A",14,2);
                        $XPhpExcel->setWidth("B",20,2);
                        
                        foreach($result as $key=>$val){
                            
                            $XPhpExcel->setValue("A".($key+2),$val["date"]);
                            $XPhpExcel->setValue("B".($key+2),$val["total"]);
                            
                        } 
                        
                        //清空输出缓存                    
                        ob_clean();    
                                      
                        $XPhpExcel->output($bdate."~".$edate."/".$Description);
                        unset($XPhpExcel);//销毁
                        
                    }else{
                        
                        $group = "`date`,`station`";
                        $dbname = "rha_wifi_daily";
                
                        $result = $PSys_AccessModel->getWeek($bdate,$edate,$group,$dbname,$stationChoice);
                        
                        $XPhpExcel = new XPhpExcel();
                        $Creator = "statis.rockhippo.com";
                        $LastModifiedBy = $Creator;
                        $Title = '访问统计-访问人数';
                        $Subject = '多日-比较';
                        $Description = '访问统计-访问人数(多日-比较)';
                        $Keywords = $Description;
                        $Category = $Description;
                        $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
                        $XPhpExcel->setSheet(0,$Subject);
                        $XPhpExcel->setValue("A1","日期");
                        $XPhpExcel->setWidth("A",14,2);
                        
                        for($i=0;$i<$Days;$i++){
                            $XPhpExcel->setValue("A".($i+2),date("Y-m-d",strtotime("$bdate +$i day")));
                        }
                        
                        $data = array();
                        
                        //遍历数据获取所需结构
                        foreach($result as $key=>$val){
                            $data[$val['station']][$val['date']] = $val['total'];
                        } 
                        
                    ////重过滤$station   
//                        $where = array();
//                        $where['id_IN'] = $stationChoice;
//                        $order = "id ASC";
//                        $field = "id,stationname";  
//                        $station = $PSys_AccessModel->GetList($where, $order, 0, 0, $field, "rha_station");
                        
                        foreach($station['allrow'] as $key=>$val){
                            $XPhpExcel->setValue($wordArr[$val['id']]."1",$val['stationname']."/人数");
                            $XPhpExcel->setWidth($wordArr[$val['id']],20,2);
                            for($i=0;$i<$Days;$i++){
                                //echo $wordArr[$val['id']].($i+2) . "~" . $data[$val['id']][$xv[$i]] . "<br>";
                                $XPhpExcel->setValue($wordArr[$val['id']].($i+2),$data[$val['id']][date("Y-m-d",strtotime("$bdate +$i day"))]);
                            }
                        }
        
                        //清空输出缓存                    
                        ob_clean();    
                                      
                        $XPhpExcel->output($bdate."~".$edate."/".$Description);
                        unset($XPhpExcel);//销毁
                        
                    }
                    
                break;
                
                case "oneweek":
           
                    $day = reqstr("oneweek");
                    $edate=date('Y-m-d',strtotime("$day Sunday")); 
                    $bdate=date('Y-m-d',strtotime("$edate -6 days"));
                    //计算天数差
                    $Days  = round((strtotime($edate)-strtotime($bdate))/3600/24);

                    $data = array();
                    
                    if($datastatus == 1){
                        
                        $group = "date";
                        $dbname = "rha_wifi_daily";
                    
                        $result = $PSys_AccessModel->getWeek($bdate,$edate,$group,$dbname,$stationChoice);
                        
                        
                        $XPhpExcel = new XPhpExcel();
                        $Creator = "statis.rockhippo.com";
                        $LastModifiedBy = $Creator;
                        $Title = '访问统计-访问人数';
                        $Subject = '单周-合并';
                        $Description = '访问统计-访问人数(单周-合并)';
                        $Keywords = $Description;
                        $Category = $Description;
                        $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
                        $XPhpExcel->setSheet(0,$Subject);
                        
                        $XPhpExcel->setValue("A1","日期");
                        $XPhpExcel->setValue("B1","人数");
                        
                        $XPhpExcel->setWidth("A",14,2);
                        $XPhpExcel->setWidth("B",20,2);
                        
                        foreach($result as $key=>$val){
                            
                            $XPhpExcel->setValue("A".($key+2),$val["date"]);
                            $XPhpExcel->setValue("B".($key+2),$val["total"]);
                            
                        } 
                        
                        //清空输出缓存                    
                        ob_clean();    
                                      
                        $XPhpExcel->output($bdate."~".$edate."/".$Description);
                        unset($XPhpExcel);//销毁
                        
                    }else{
                        
                        $group = "`date`,`station`";
                        $dbname = "rha_wifi_daily";
                
                        $result = $PSys_AccessModel->getWeek($bdate,$edate,$group,$dbname,$stationChoice);
                        
                        $XPhpExcel = new XPhpExcel();
                        $Creator = "statis.rockhippo.com";
                        $LastModifiedBy = $Creator;
                        $Title = '访问统计-访问人数';
                        $Subject = '单周-比较';
                        $Description = '访问统计-访问人数(单周-比较)';
                        $Keywords = $Description;
                        $Category = $Description;
                        $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
                        $XPhpExcel->setSheet(0,$Subject);
                        $XPhpExcel->setValue("A1","日期");
                        $XPhpExcel->setWidth("A",14,2);
                        
                        for($i=0;$i<7;$i++){
                            $XPhpExcel->setValue("A".($i+2),date("Y-m-d",strtotime("$bdate +$i day")));
                        }
                        
                        $data = array();
                        
                        //遍历数据获取所需结构
                        foreach($result as $key=>$val){
                            $data[$val['station']][$val['date']] = $val['total'];
                        } 
                        
                    ////重过滤$station   
//                        $where = array();
//                        $where['id_IN'] = $stationChoice;
//                        $order = "id ASC";
//                        $field = "id,stationname";  
//                        $station = $PSys_AccessModel->GetList($where, $order, 0, 0, $field, "rha_station");
                        
                        foreach($station['allrow'] as $key=>$val){
                            $XPhpExcel->setValue($wordArr[$val['id']]."1",$val['stationname']."/人数");
                            $XPhpExcel->setWidth($wordArr[$val['id']],20,2);
                            for($i=0;$i<7;$i++){
                                //echo $wordArr[$val['id']].($i+2) . "~" . $data[$val['id']][$xv[$i]] . "<br>";
                                $XPhpExcel->setValue($wordArr[$val['id']].($i+2),$data[$val['id']][date("Y-m-d",strtotime("$bdate +$i day"))]);
                            }
                        }
        
                        //清空输出缓存                    
                        ob_clean();    
                                      
                        $XPhpExcel->output($bdate."~".$edate."/".$Description);
                        unset($XPhpExcel);//销毁

                    }
                    
                break;
                
                case "manyweek":
                
                    $bday = reqstr("bmanyweek");
                    $edate2=date('Y-m-d',strtotime("$bday Sunday")); 
                    $bdate=date('Y-m-d',strtotime("$edate2 -6 days"));
                    
                    $eday = reqstr("emanyweek");
                    $edate=date('Y-m-d',strtotime("$eday Sunday")); 
                    $bdate2=date('Y-m-d',strtotime("$edate -6 days"));
                    
                    //计算周数差
                    $Weeks  = round((strtotime($edate)-strtotime($bdate))/3600/24/7);

                    $data = array();
                    
                    if($datastatus == 1){
                        
                        $group = "date";
                        $dbname = "rha_wifi_daily";
                    
                        $result = $PSys_AccessModel->getWeek($bdate,$edate,$group,$dbname,$stationChoice);
                        
                        foreach($result as $key=>$val){
                            $data[$val['date']] = $val['total'];
                        } 

                        $XPhpExcel = new XPhpExcel();
                        $Creator = "statis.rockhippo.com";
                        $LastModifiedBy = $Creator;
                        $Title = '访问统计-访问人数';
                        $Subject = '多周-合并';
                        $Description = '访问统计-访问人数(多周-合并)';
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
                                $dt = date("Y-m-d",strtotime("$bdate +$day day"));
                                $num += $data[$dt];
                                
                                if($j == 1){
                                    $d = date("Y/m/d",strtotime("$bdate +$day day"));
                                }
                                if($j == 7){
                                    $d .= ' - ' . date("m/d",strtotime("$bdate +$day day"));
                                    $XPhpExcel->setValue("A".($i+2),$d);
                                    $XPhpExcel->setValue("B".($i+2),$num);
                                }
                                
                            }
                        }    
                        
                        //清空输出缓存                    
                        ob_clean();    
                                      
                        $XPhpExcel->output($bdate."~".$edate."/".$Description);
                        unset($XPhpExcel);//销毁
                        
                    }else{
                        
                        $group = "`date`,`station`";
                        $dbname = "rha_wifi_daily";
                
                        $result = $PSys_AccessModel->getWeek($bdate,$edate,$group,$dbname,$stationChoice);
                        
                        foreach($result as $key=>$val){
                            $data[$val['station']][$val['date']] = $val['total'];
                        } 

                        $XPhpExcel = new XPhpExcel();
                        $Creator = "statis.rockhippo.com";
                        $LastModifiedBy = $Creator;
                        $Title = '访问统计-访问人数';
                        $Subject = '多周-比较';
                        $Description = '访问统计-访问人数(多周-比较)';
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
                                    $dt = date("Y-m-d",strtotime("$bdate +$day day"));
                                    $num += $data[$val['id']][$dt];
                                    
                                    if($j == 1){
                                        $d = date("Y/m/d",strtotime("$bdate +$day day"));
                                    }
                                    if($j == 7){
                                        $d .= ' - ' . date("m/d",strtotime("$bdate +$day day"));
                                        $XPhpExcel->setValue("A".($i+2),$d);
                                        $XPhpExcel->setValue($wordArr[$val['id']].($i+2),$num);
                                    }
                                    
                                }
                            }    
    
                        }
                        
                        //to do  nick 2030 celeste

                        
                        
                        //清空输出缓存                    
                        ob_clean();    
                                      
                        $XPhpExcel->output($bdate."~".$edate."/".$Description);
                        unset($XPhpExcel);//销毁
                        
                    }
                    
                break;
                
                case "onemonth":
                
                    $date = reqstr("onemonth");
                    $bdate = date("Y-m-01",strtotime($date));
                    $edate = date("Y-m-d",strtotime("$bdate +1 month -1 day"));
                    $date = date("Y-m",strtotime($date));
                    
                    $Days = date("t",strtotime($date));
                    
                    $data = array();
                    
                    if($datastatus == 1){
                        
                        $group = "date";
                        $dbname = "rha_wifi_daily";
                    
                        $result = $PSys_AccessModel->getWeek($bdate,$edate,$group,$dbname,$stationChoice);
                        
                        $XPhpExcel = new XPhpExcel();
                        $Creator = "statis.rockhippo.com";
                        $LastModifiedBy = $Creator;
                        $Title = '访问统计-访问人数';
                        $Subject = '单月-合并';
                        $Description = '访问统计-访问人数(单月-合并)';
                        $Keywords = $Description;
                        $Category = $Description;
                        $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
                        $XPhpExcel->setSheet(0,$Subject);
                        
                        $XPhpExcel->setValue("A1","日期");
                        $XPhpExcel->setValue("B1","人数");
                        
                        $XPhpExcel->setWidth("A",14,2);
                        $XPhpExcel->setWidth("B",20,2);
                        
                        foreach($result as $key=>$val){
                            
                            $XPhpExcel->setValue("A".($key+2),$val["date"]);
                            $XPhpExcel->setValue("B".($key+2),$val["total"]);
                            
                        } 
                        
                        //清空输出缓存                    
                        ob_clean();    
                                      
                        $XPhpExcel->output($date."/".$Description);
                        unset($XPhpExcel);//销毁
                        
                    }else{
                        
                        $group = "`date`,`station`";
                        $dbname = "rha_wifi_daily";
                
                        $result = $PSys_AccessModel->getWeek($bdate,$edate,$group,$dbname,$stationChoice);
                        
                        $XPhpExcel = new XPhpExcel();
                        $Creator = "statis.rockhippo.com";
                        $LastModifiedBy = $Creator;
                        $Title = '访问统计-访问人数';
                        $Subject = '单月-比较';
                        $Description = '访问统计-访问人数(单月-比较)';
                        $Keywords = $Description;
                        $Category = $Description;
                        $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
                        $XPhpExcel->setSheet(0,$Subject);
                        $XPhpExcel->setValue("A1","日期");
                        $XPhpExcel->setWidth("A",14,2);
                        
                        for($i=0;$i<$Days;$i++){
                            $XPhpExcel->setValue("A".($i+2),date("Y-m-d",strtotime("$bdate +$i day")));
                        }
                        
                        $data = array();
                        
                        //遍历数据获取所需结构
                        foreach($result as $key=>$val){
                            $data[$val['station']][$val['date']] = $val['total'];
                        } 
                        
                    ////重过滤$station   
//                        $where = array();
//                        $where['id_IN'] = $stationChoice;
//                        $order = "id ASC";
//                        $field = "id,stationname";  
//                        $station = $PSys_AccessModel->GetList($where, $order, 0, 0, $field, "rha_station");
                        
                        foreach($station['allrow'] as $key=>$val){
                            $XPhpExcel->setValue($wordArr[$val['id']]."1",$val['stationname']."/人数");
                            $XPhpExcel->setWidth($wordArr[$val['id']],20,2);
                            for($i=0;$i<$Days;$i++){
                                //echo $wordArr[$val['id']].($i+2) . "~" . $data[$val['id']][$xv[$i]] . "<br>";
                                $XPhpExcel->setValue($wordArr[$val['id']].($i+2),$data[$val['id']][date("Y-m-d",strtotime("$bdate +$i day"))]);
                            }
                        }
        
                        //清空输出缓存                    
                        ob_clean();    
                                      
                        $XPhpExcel->output($bdate."~".$edate."/".$Description);
                        unset($XPhpExcel);//销毁
                        
                    }
                    
                    
                break;
                
                case "manymonth":
                
                    $bday = reqstr("bmanymonth");
                    $bdate = date("Y-m-01",strtotime($bday));
                    
                    $eday = reqstr("emanymonth");
                    $edate = date("Y-m-d",strtotime("$eday +1 month -1 day"));
                    
                    //计算月数差
                    $Months = (date("Y",strtotime($eday)) - date("Y",strtotime($bday)))*12+(date("m",strtotime($eday))-date("m",strtotime($bday))) + 1;
                    
                    for($i=0;$i<$Months;$i++){
                        $dateArr[$i] = date("Y-m",strtotime("$bday +$i month"));
                    }
                    
                    $data = array();
                    
                    
                    if($datastatus == 1){
                        
                        $group = "date";
                        $dbname = "rha_wifi_daily";
                    
                        $result = $PSys_AccessModel->getWeek($bdate,$edate,$group,$dbname,$stationChoice);
                        
                        foreach($result as $key=>$val){
                            $data[$val['date']] = $val['total'];
                        } 

                        $XPhpExcel = new XPhpExcel();
                        $Creator = "statis.rockhippo.com";
                        $LastModifiedBy = $Creator;
                        $Title = '访问统计-访问人数';
                        $Subject = '多月-合并';
                        $Description = '访问统计-访问人数(多月-合并)';
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
                                $nums += $data[date("Y-m-d",strtotime("$bday +$day day"))];
                                $day++;
                            }
                            
                            $XPhpExcel->setValue("B".($i+2),$nums);
                            
                        }
                        
                        //清空输出缓存                    
                        ob_clean();    
                                      
                        $XPhpExcel->output($bdate."~".$edate."/".$Description);
                        unset($XPhpExcel);//销毁
                        
                    }else{
                        
                        $group = "`date`,`station`";
                        $dbname = "rha_wifi_daily";
                
                        $result = $PSys_AccessModel->getWeek($bdate,$edate,$group,$dbname,$stationChoice);
                        
                        foreach($result as $key=>$val){
                            $data[$val['station']][$val['date']] = $val['total'];
                        } 

                        $XPhpExcel = new XPhpExcel();
                        $Creator = "statis.rockhippo.com";
                        $LastModifiedBy = $Creator;
                        $Title = '访问统计-访问人数';
                        $Subject = '多月-比较';
                        $Description = '访问统计-访问人数(多月-比较)';
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
                                    $nums += $data[$val['id']][date("Y-m-d",strtotime("$bday +$day day"))];
                                    $day++;
                                }
                                
                                $XPhpExcel->setValue($wordArr[$val['id']].($i+2),$nums);
                                
                            }
                            
                        }
                        
                        //清空输出缓存                    
                        ob_clean();    
                                      
                        $XPhpExcel->output($bdate."~".$edate."/".$Description);
                        unset($XPhpExcel);//销毁

                    }
                    
                break;
                
            }
        
        }   
        
    }
	
}