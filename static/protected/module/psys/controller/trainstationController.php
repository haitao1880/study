<?php
/**
* 车站统计展示
*
*/
class trainstationController extends PSys_AbstractController{
    protected $obj;
	public function __construct() {
		parent::__construct();
        $this->smarty->assign("trainstationActive","active");
		$this->smarty->assign("gameActive","");
        $this->smarty->assign("trainActive","");
        $this->smarty->assign("gameHidden","hidden");
        $this->smarty->assign("trainstationHidden","");
        $this->smarty->assign("trainHidden","hidden");
        $this->smarty->assign("busHidden","hidden");
        $this->smarty->assign("busActive","");
        $this->smarty->assign("marketHidden","hidden");
        $this->smarty->assign("marketActive","");
        $this->obj = new PSys_TrainstationModel();
	}

	/**
     *伙伴分类下载统计展示页面 
     */
    public function trainappdownAction(){
        $this->smarty->assign("active","trainstation/trainappdown");  
        $this->smarty->assign("active_menu","trainappdown"); 
        $this->forward = 'trainappdown';
    }

    /**
     * 根据车站类型获取其下的所有车站
     */
    public function getstationtypeAction(){
        $stationtype = reqnum('stationtype',1);
        if (!$stationtype) {
            return;
        }
        $station = $this->obj->GetList(array('stationtype'=>$stationtype),'',0,0,'id,stationname','rha_station');

        $this->smarty->assign('stations',$station['allrow']);
        $this->forward = 'getstationtype';
    }

    /**
     * 伙伴分类下载详情
     * @return
     */
    public function downtrainappinfoAction(){
        $data = reqstr('data');        
        $data = urldecode($data);
        $info = array();
        parse_str($data,$info);        

        $sdate = trim($info['sdate'])?trim($info['sdate']):'';
        $edate = trim($info['edate'])?trim($info['edate']):'';
        if (!$sdate || !$edate) {
            $edate = date('Y-m-d');
            $sdate = $edate;
        }
        if ($edate < $sdate) {
            return array('status'=>0,'msg'=>'时间区间选择有误');
        }        

        if (empty($info['stationid'])) {
            $stationids = 1;
        }else{
           $stationids = implode(',',$info['stationid']); 
        }

        $res = $this->obj->DownTrainappInfo($sdate,$edate,$stationids);
        foreach($res as $v){
            $table_data[] = array_values($v);
        }        
        
        $result['table_title'] = array('日期','车站','总下载数','电影下载','活动下载'); 
        $result['table_data'] =$table_data;
        // print_r($table_data);
        return $result;  
        
    }

    public function exportdownappAction(){       
        $sdate = reqstr('sdate');     
        $edate = reqstr('edate');
        $stationids =  reqstr('stationid');

        if (!$sdate || !$edate) {
            $edate = date('Y-m-d');
            $sdate = $edate;
        }
        if ($edate < $sdate) {
            return array('status'=>0,'msg'=>'时间区间选择有误');
        } 

        $stationids = rtrim($stationids,',');
        if (!$stationids) {
            return array('status'=>0,'msg'=>'请选择车站');
        }

        $res = $this->obj->DownTrainappInfo($sdate,$edate,$stationids);
        foreach($res as $v){
            $table_data[] = array_values($v);
        }        
        
        $result['table_title'] = array('日期','车站','总下载数','电影下载','活动下载'); 

        require COMMON_PATH.'XExportExcel.php';
        $excel = new Excel();
        $excel->addHeader($result['table_title']);
        $excel->addBody($table_data);
        $excel->downLoad('伙伴分类下载');
    }


    /**
     * 人寿保单注册流程展示页面
     */
    public function BdRegisterProcessAction(){

        $TrainStation = $this->obj->GetList(array('stationtype'=>1),"id ASC",0,0,"id,stationname","rha_station");

        $BusStation = $this->obj->GetList(array('stationtype'=>2),"id ASC",0,0,"id,stationname","rha_station");

        $this->smarty->assign('trainstation',$TrainStation['allrow']);
        $this->smarty->assign('busstation',$BusStation['allrow']);
        $this->forward = 'BdRegisterProcess';

    }

    /**
     * 人寿保单注册流程展示数据
     */
    public function BdRegisterProcessDataAction(){
        $data = reqstr('data');     
        $data = urldecode($data);
        $info = array();
        parse_str($data,$info);        

        $sdate = trim($info['sdate'])?trim($info['sdate']):'';
        $edate = trim($info['edate'])?trim($info['edate']):'';
        if (!$sdate || !$edate) {
            if (!$sdate) {
                $sdate = date('Y-m-d',strtotime('-1 day'));
            }            
            $edate = $sdate;
        }        
       
        
        if ($edate < $sdate) {
            return array('status'=>0,'msg'=>'时间区间选择有误');
        }
        
        if (empty($info['stationid'])) {
            return array('status'=>0,'msg'=>'请选择查询汽车');
        }
        $stationids = implode(',',$info['stationid']);
        $where['cday_>='] = $sdate ;
        $where['cday_<='] = $edate ;
        $where['station_id_IN'] = $stationids;
        $where['type'] = 4;
        $fild = 'cday,station_id,connect_wifi,ad1_page,register_page,send_sms_success,register_success,free_wifi_click,sindex';

        $res = $this->obj->GetList($where,'station_id ASC',0,0,$fild,'rha_allprocess');


        //表格数据展示
        $result['table_title'] = array('日期','车站','连接数','广告1','人寿保单注册页','短信发送成功','保单领取成功','点击上网人数','首页');

        //根据carid获取车次 
        $total = array(
                    'connect_wifi'=>0,
                    'ad1_page'=>0,
                    'register_page'=>0,
                    'send_sms_success'=>0,
                    'register_success'=>0,
                    'free_wifi_click'=>0,
                    'sindex'=>0
         );       
        foreach($res['allrow'] as $key => &$val){ 

            $val['station_id'] = $this->obj->GetStationName($val['station_id']);            
            $total['connect_wifi'] += $val['connect_wifi'];
            $total['ad1_page'] += $val['ad1_page'];
            $total['register_page'] += $val['register_page'];
            $total['send_sms_success'] += $val['send_sms_success'];
            $total['register_success'] += $val['register_success'];
            $total['free_wifi_click'] += $val['free_wifi_click'];
            $total['sindex'] += $val['sindex'];
            $val = array_values($val);
        }

        $datas[0]['name'] = '当前选择的所有车站';
        $result['x_cat'] = array('连接数','广告1','人寿保单注册页','短信发送成功','保单领取成功','点击上网人数','首页');
        $datas[0]['data'] = array_values($total);        
        $result['y_data'] = $datas;



        $table_data = $res['allrow'];
        
        
        $result['table_data'] =$table_data;
       
        return $result;
    }

    /**
     * 导出保单流程数据
     */
    public function exportbddataAction(){
        $data = reqstr('data');     
        $data = urldecode($data);
        $info = array();
        parse_str($data,$info);        

        $sdate = reqstr('sdate','');
        $edate = reqstr('edate','');
        if (!$sdate || !$edate) {
            if (!$sdate) {
                $sdate = date('Y-m-d',strtotime('-1 day'));
            }            
            $edate = $sdate;
        }        
       
        
        if ($edate < $sdate) {
            return array('status'=>0,'msg'=>'时间区间选择有误');
        }
        
        $stationids = reqstr('stationid','');
        $stationids = rtrim($stationids,',');
        $where['cday_>='] = $sdate ;
        $where['cday_<='] = $edate ;
        $where['station_id_IN'] = $stationids;
        $where['type'] = 4;
        $fild = 'cday,station_id,connect_wifi,ad1_page,register_page,send_sms_success,register_success,free_wifi_click,sindex';

        $res = $this->obj->GetList($where,'station_id ASC',0,0,$fild,'rha_allprocess');


        //表格数据展示
        $result['table_title'] = array('日期','车站','连接数','广告1','人寿保单注册页','短信发送成功','保单领取成功','点击上网人数','首页');

      
        foreach($res['allrow'] as $key => &$val){            
            $val['station_id'] = $this->obj->GetStationName($val['station_id']);
            $val = array_values($val);
        }
        $table_data = $res['allrow'];       
        
        $isexport = reqnum('isexport',0);
        
        if ($isexport) {           
            require COMMON_PATH.'XExportExcel.php';
            $excel = new Excel();
            $excel->addHeader($result['table_title']);
            $excel->addBody($table_data);
            $excel->downLoad('人寿保单注册流程');
        }
    }

 
    
}