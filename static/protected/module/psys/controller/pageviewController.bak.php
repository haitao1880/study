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
* 创 建 者:Nick (nick@rockhippo.cn)
* 修 改 者:Jerry (Jerry@rockhippo.cn)
* 版本地址:none
* 摘    要: 访问统计控制器
*/
class pageviewController extends PSys_AbstractController{
    
	public function __construct() {
		parent::__construct();
	}
    
	/**
     *
     * @do 访问人数 uv
     *
     * @pageview public 
     * @author Nick
     * @copyright rockhippo
     * @param -
     * @return -
     *
     */
	public function indexAction(){	    
        $this->smarty->assign("active","pageview/index");
		$this->forward = "index";
        
	}
    
    /**
    *
    * @do ajax 访问人数 uv数据
    *
    * @pageview public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function ajaxUVpageviewAction(){
        $PSys_PageviewModel = new PSys_PageviewModel();
        $where = array();
       
        $fastSearch = reqnum("fastSearch",0);//快速查询
        $dataStatusFS = reqnum("datastatusFS",1);//快速查询合并、比较筛选按钮 1为合并 2为比较  默认合并
        
        switch($fastSearch){
            
            case 1://当天
                $date = date("Y-m-d");
                
                $data = array();
                
                //x轴数据
                $data['xv'] = array('00'=>'0 hour',
                                    '01'=>'1 hour',
                                    '02'=>'2 hour',
                                    '03'=>'3 hour',
                                    '04'=>'4 hour',
                                    '05'=>'5 hour',
                                    '06'=>'6 hour',
                                    '07'=>'7 hour',
                                    '08'=>'8 hour',
                                    '09'=>'9 hour',
                                    '10'=>'10 hour',
                                    '11'=>'11 hour',
                                    '12'=>'12 hour',
                                    '13'=>'13 hour',
                                    '14'=>'14 hour',
                                    '15'=>'15 hour',
                                    '16'=>'16 hour',
                                    '17'=>'17 hour',
                                    '18'=>'18 hour',
                                    '19'=>'19 hour',
                                    '20'=>'20 hour',
                                    '21'=>'21 hour',
                                    '22'=>'22 hour',
                                    '23'=>'23 hour');
                 
                if($dataStatusFS == 1){
                    
                    $group = "`hour`";
                    $dbname = "rha_aclog_hour";
                    $result = $PSys_PageviewModel->getDay($date,$group,$dbname);
                    
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
                    $result = $PSys_PageviewModel->getDay($date,$group,$dbname);
                    
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
            
                $bdate = date("Y-m-d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y")));
                $edate = date("Y-m-d",mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y"))); 

                if(date("N",strtotime($bdate)) == 1){
                    $data = array();
                    $data['error'] = 'WEEKNODATA';
                    break;
                }

                $data = array();
                
                if($dataStatusFS == 1){
                    
                    $group = "date";
                    $dbname = "rha_wifi_daily";
                
                    $result = $PSys_PageviewModel->getWeek($bdate,$edate,$group,$dbname);
                    
                    //x轴数据
                    $data['xv'] = array();
                    $data['data'][1]['name'] = "所有站点";
                    $data['data'][1]['marker'] = "square";
                    
                    foreach($result as $key=>$val){
                        $total[$val['date']] = $val['total'];
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
            
                    $result = $PSys_PageviewModel->getWeek($bdate,$edate,$group,$dbname);
            
                    foreach($result as $key=>$val){
                        $total[$val['station']][$val['date']] = $val['total'];
                        //$data['data'][$val['station']]['data'][] = intval($val['total']);
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
                
                if($dataStatusFS == 1){
                    
                    $group = "date";
                    $dbname = "rha_wifi_daily";
                
                    $result = $PSys_PageviewModel->getWeek($bdate,$edate,$group,$dbname);
                    
                    //x轴数据
                    $data['xv'] = array();
                    $data['data'][1]['name'] = "所有站点";
                    $data['data'][1]['marker'] = "square";
                    //遍历数据
                    foreach($result as $key=>$val){
                        $total[$val['date']] = $val['total'];
                        $data['data']['table'][1][] = array('date'=>$val['date'],'total'=>$val['total']);
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
                
                    $result = $PSys_PageviewModel->getWeek($bdate,$edate,$group,$dbname);
                    
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
                        
                        //x轴数据
                        $data['xv'] = array('00'=>'0 hour',
                                            '01'=>'1 hour',
                                            '02'=>'2 hour',
                                            '03'=>'3 hour',
                                            '04'=>'4 hour',
                                            '05'=>'5 hour',
                                            '06'=>'6 hour',
                                            '07'=>'7 hour',
                                            '08'=>'8 hour',
                                            '09'=>'9 hour',
                                            '10'=>'10 hour',
                                            '11'=>'11 hour',
                                            '12'=>'12 hour',
                                            '13'=>'13 hour',
                                            '14'=>'14 hour',
                                            '15'=>'15 hour',
                                            '16'=>'16 hour',
                                            '17'=>'17 hour',
                                            '18'=>'18 hour',
                                            '19'=>'19 hour',
                                            '20'=>'20 hour',
                                            '21'=>'21 hour',
                                            '22'=>'22 hour',
                                            '23'=>'23 hour');
                         
                        if($datastatus == 1){
                            
                            $group = "`hour`";
                            $dbname = "rha_aclog_hour";
                            $result = $PSys_PageviewModel->getDay($date,$group,$dbname,$stationChoice);
                            
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
                            
                            $stationChoice = array();
                            $stationChoice[0] = 1;
                            
                        }else{
                            
                            $group = "`hour`,`stationid`";
                            $dbname = "rha_aclog_hour";
                            $result = $PSys_PageviewModel->getDay($date,$group,$dbname,$stationChoice);
            
                            foreach($result as $key=>$val){
                                $total[$val['stationid']][$val['date']] = $val['total'];
                                //$data['data'][$val['stationid']]['data'][] = intval($val['total']);
                                $data['data'][$val['stationid']]['id'][] = $val['id'];
                                $data['data']['table'][$val['stationid']][] = array('date'=>$val['date'],'total'=>$val['total']);
                            }
                            
                            //重过滤$station   
                            $where = array();
                            $where['id_IN'] = $stationChoice;
                            $order = "id ASC";
                            $field = "id,stationname";  
                            $station = $PSys_PageviewModel->GetList($where, $order, 0, 0, $field, "rha_station");
                            
                            //比较的数据节点设置
                            foreach($station['allrow'] as $key=>$val){
                                $data['data'][$val['id']]['name'] = $val['stationname'];
                                $data['data'][$val['id']]['marker'] = "square";
                                $data['data']['table'][$val['id']] = is_array($data['data']['table'][$val['id']]) ? $data['data']['table'][$val['id']] : array();
                                
                                foreach($data['xv'] as $k=>$v){
                                
                                    $data['data'][$val['id']]['data'][] = $total[$val['id']][$k] != '' ? intval($total[$val['id']][$k]) : 0;
                                
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
                        
                        if($datastatus == 1){
                            
                            $group = "date";
                            $dbname = "rha_wifi_daily";
                        
                            $result = $PSys_PageviewModel->getWeek($bdate,$edate,$group,$dbname,$stationChoice);
                            
                            //print_r($result);
                            
                            //x轴数据
                            $data['xv'] = array();
                            $data['data'][1]['name'] = "所有站点";
                            $data['data'][1]['marker'] = "square";
                            
                            foreach($result as $key=>$val){
                                $total[$val['date']] = $val['total'];
                                $data['data']['table'][1][] = array('date'=>$val['date'],'total'=>$val['total']);
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
                    
                            $result = $PSys_PageviewModel->getWeek($bdate,$edate,$group,$dbname);
                            
                            foreach($result as $key=>$val){
                                $total[$val['station']][$val['date']] = $val['total'];
                                //$data['data'][$val['station']]['data'][] = intval($val['total']);
                                $data['data']['table'][$val['station']][] = array('date'=>$val['date'],'total'=>$val['total']);
                            }
                            
                            //x轴数据
                            $data['xv'] = array();
                            //比较的数据节点设置
                            foreach($station['allrow'] as $key=>$val){
                                $data['data'][$val['id']]['name'] = $val['stationname'];
                                $data['data'][$val['id']]['marker'] = "square";
                                $data['data']['table'][$val['id']] = is_array($data['data']['table'][$val['id']]) ? $data['data']['table'][$val['id']] : array();
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
                        
                        if($datastatus == 1){
                            
                            $group = "date";
                            $dbname = "rha_wifi_daily";
                        
                            $result = $PSys_PageviewModel->getWeek($bdate,$edate,$group,$dbname);
                            
                            //x轴数据
                            $data['xv'] = array();
                            $data['data'][1]['name'] = "所有站点";
                            $data['data'][1]['marker'] = "square";
                            
                            foreach($result as $key=>$val){
                                $total[$val['date']] = $val['total'];
                                $data['data']['table'][1][] = array('date'=>$val['date'],'total'=>$val['total']);
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
                    
                            $result = $PSys_PageviewModel->getWeek($bdate,$edate,$group,$dbname);
                            
                            foreach($result as $key=>$val){
                                $total[$val['station']][$val['date']] = $val['total'];
                                //$data['data'][$val['station']]['data'][] = intval($val['total']);
                                $data['data']['table'][$val['station']][] = array('date'=>$val['date'],'total'=>$val['total']);
                            }
                            
                            //x轴数据
                            $data['xv'] = array();
                            //比较的数据节点设置
                            foreach($station['allrow'] as $key=>$val){
                                $data['data'][$val['id']]['name'] = $val['stationname'];
                                $data['data'][$val['id']]['marker'] = "square";
                                $data['data']['table'][$val['id']] = is_array($data['data']['table'][$val['id']]) ? $data['data']['table'][$val['id']] : array();
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
                        
                        if($datastatus == 1){
                            
                            $group = "date";
                            $dbname = "rha_wifi_daily";
                        
                            $result = $PSys_PageviewModel->getWeek($bdate,$edate,$group,$dbname);
                            
                            //x轴数据
                            $data['xv'] = array();
                            $data['data'][1]['name'] = "所有站点";
                            $data['data'][1]['marker'] = "square";
                            
                            foreach($result as $key=>$val){
                                $total[$val['date']] = $val['total'];
                                $data['data']['table'][1][] = array('date'=>$val['date'],'total'=>$val['total']);
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
                            }
                            
                            $data['data']['num'] = 1;
                            
                            $stationChoice = array();
                            $stationChoice[0] = 1; //to do...
                            
                        }else{
                            
                            $group = "`date`,`station`";
                            $dbname = "rha_wifi_daily";
                    
                            $result = $PSys_PageviewModel->getWeek($bdate,$edate,$group,$dbname);
                            
                            foreach($result as $key=>$val){
                                $total[$val['station']][$val['date']] = $val['total'];
                                //$data['data'][$val['station']]['data'][] = intval($val['total']);
                                $data['data']['table'][$val['station']][] = array('date'=>$val['date'],'total'=>$val['total']);
                            }
                            
                            //x轴数据
                            $data['xv'] = array();
                            //比较的数据节点设置
                            foreach($station['allrow'] as $key=>$val){
                                $data['data'][$val['id']]['name'] = $val['stationname'];
                                $data['data'][$val['id']]['marker'] = "square";
                                $data['data']['table'][$val['id']] = is_array($data['data']['table'][$val['id']]) ? $data['data']['table'][$val['id']] : array();
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
                        
                        if($datastatus == 1){
                            
                            $group = "date";
                            $dbname = "rha_wifi_daily";
                        
                            $result = $PSys_PageviewModel->getWeek($bdate,$edate,$group,$dbname);
                            
                            //x轴数据
                            $data['xv'] = array();
                            $data['data'][1]['name'] = "所有站点";
                            $data['data'][1]['marker'] = "square";
                            //遍历数据
                            foreach($result as $key=>$val){
                                $total[$val['date']] = $val['total'];
                                $data['data']['table'][1][] = array('date'=>$val['date'],'total'=>$val['total']);
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
                        
                            $result = $PSys_PageviewModel->getWeek($bdate,$edate,$group,$dbname);
                            
                            //x轴数据
                            $data['xv'] = array();
                            //比较的数据节点设置
                            foreach($station['allrow'] as $key=>$val){
                                $data['data'][$val['id']]['name'] = $val['stationname'];
                                $data['data'][$val['id']]['marker'] = "square";
                                $data['data']['table'][$val['id']] = array();
                            }
                            //遍历数据
                            foreach($result as $key=>$val){
                                $total[$val['date']][$val['station']] = $val['total'];
                                $data['data']['table'][$val['station']][] = array('date'=>$val['date'],'total'=>$val['total']);
                            }
                           
                            
                            for($i=1;$i<=date("t",strtotime($date));$i++){
                                $day = $i - 1;
                                $data['xv'][] = date("d",strtotime("$date + $day day"));
                                
                                //遍历数据
                                foreach($station['allrow'] as $key=>$val){
                                    $data['data'][$val['id']]['data'][] = $total[date("Y-m-d",strtotime("$bdate +$day day"))][$val['id']] ? intval($total[date("Y-m-d",strtotime("$bdate +$day day"))][$val['id']]) : 0;
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
                        
                        if($datastatus == 1){
                            
                            $group = "date";
                            $dbname = "rha_wifi_daily";
                        
                            $result = $PSys_PageviewModel->getWeek($bdate,$edate,$group,$dbname);
                            
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
                                for($j=1;$j<=$num;$j++){
                                    if($j == 1){
                                        $data['xv'][$i] = date("Y/m",strtotime("$bday +$i month"));
                                    }
                                    //echo $day."<br/>";
                                    //$data['data'][1]['data'][] = intval($result[$i-1]['total']);
                                    $data['data'][1]['data'][$i] += $total[date("Y-m-d",strtotime("$bday +$day day"))] ? intval($total[date("Y-m-d",strtotime("$bday +$day day"))]) : 0;
                                    $day++;
                                }
                            }
                            
                            $data['data']['num'] = 1;
                            
                            $stationChoice = array();
                            $stationChoice[0] = 1; //to do...
                            
                        }else{
                            
                            $group = "`date`,`station`";
                            $dbname = "rha_wifi_daily";
                    
                            $result = $PSys_PageviewModel->getWeek($bdate,$edate,$group,$dbname);
                            
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
                            
                            //x轴数据
                            $data['xv'] = array();
                            //比较的数据节点设置
                            foreach($station['allrow'] as $key=>$val){
                                $data['data'][$val['id']]['name'] = $val['stationname'];
                                $data['data'][$val['id']]['marker'] = "square";
                                $data['data']['table'][$val['id']] = is_array($data['data']['table'][$val['id']]) ? $data['data']['table'][$val['id']] : array();
                            }
                            
                            $day = 0;
                            
                            //print_r($total[1][date("Y-m-d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y")))]);
                            for($i=0;$i<$Months;$i++){
                                
                                $num = date("t",strtotime("$bday +$i month"));
                                for($j=1;$j<=$num;$j++){
                                    if($j == 1){
                                        $data['xv'][$i] = date("Y/m",strtotime("$bday +$i month"));
                                    }
                                    //echo $day."<br/>";
                                    //$data['data'][1]['data'][] = intval($result[$i-1]['total']);
                                    foreach($station['allrow'] as $key=>$val){
                                        $data['data'][$val['id']]['data'][$i] += $total[$val['id']][date("Y-m-d",strtotime("$bday +$day day"))] ? intval($total[$val['id']][date("Y-m-d",strtotime("$bday +$day day"))]) : 0;
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
    
}