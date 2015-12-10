<?php
/**
* 车上统计展示
*
*/
class busController extends PSys_AbstractController{
    
	public function __construct() {
		parent::__construct();
		$this->smarty->assign("busActive","active");
        $this->smarty->assign("trainActive","");
        $this->smarty->assign("gameActive","");
        $this->smarty->assign("gameHidden","hidden");
        $this->smarty->assign("trainHidden","hidden");
        $this->smarty->assign("busHidden",""); 
        $this->smarty->assign("marketHidden","hidden");
        $this->smarty->assign("marketActive","");
	}

	/**
	 * 根据carid获取车次
	 */
	public function GetBusName($carid,$type){
		$obj = new PSys_BusModel(); 
        if ($type == 'bus') {
            $obj->SetDb('bus_db');
            if ($carid) {
                $number = $obj->GetOne(array('id'=>$carid),'number','rb_car');
                return $number['number'];
            }
        }elseif($type == 'station'){
            $obj->SetDb('rha_admin');
            if ($carid) {
                $number = $obj->GetOne(array('id'=>$carid),'stationname','rha_station');
                return $number['stationname'];
            }
        }
		
		return 'ALL';
	}



	/**
	 * 流程显示页面
	 */
    public function processAction(){
        $this->smarty->assign("active","bus/process");  
        $this->smarty->assign("active_menu","process");  	
        $this->forward = 'process';
    }  


    /**
     * 流程显示页面1
     */
    public function process1Action(){
        
        $PSys_BusModel = new PSys_BusModel();
        
        $where = array();
        $where['isbus'] = 1;
        $where['isvalide'] = 1;
        
        $bus1 = $PSys_BusModel->GetList($where,"id ASC",0,0,"number,id","rb_car");
        $this->smarty->assign("bus1",$bus1['allrow']);
        
        $where = array();
        $where['isbus'] = 0;
        $where['isvalide'] = 1;
        
        $bus2 = $PSys_BusModel->GetList($where,"id ASC",0,0,"number,id","rb_car");
        $this->smarty->assign("bus2",$bus2['allrow']);
        
        
        $this->smarty->assign("active","bus/process1");
        $this->smarty->assign("active_menu","process1");
        $this->forward = 'process1';
    }
    /**
	 * 流程显示数据1
	 */
    public function processdata1Action(){
        $data = reqstr('data');		
		$data = urldecode($data);
		$info = array();
		parse_str($data,$info);        

        $sdate = trim($info['sdate'])?trim($info['sdate']):'';
        $edate = trim($info['edate'])?trim($info['edate']):'';

    
        if($sdate && !$edate){
            
            $date = $sdate;
            
        }elseif(!$sdate && $edate){
            
            $date = $edate;
            
        }else{
            
            $date = $sdate . "~" . $edate;
            
        }
         
        if (empty($info['carid'])) {
            return array('status'=>0,'msg'=>'请选择查询汽车');
        }
		$cid = implode(',',$info['carid']);
		
		$obj = new PSys_BusModel();
        $obj->SetDb('bus_db');

        $arr = array(
            'wifi_num'=>'rb_wificonnect',
            'ad1_num'=>'rb_into_ad1',
            'jump_ad1'=>'rb_jump_ad1',
            'reg_page_num'=>'rb_into_register',
            'send_code_num'=>'rb_send_code',
            'reg_success_num'=>'rb_reg_success',
            'sindex_num'=>'rb_into_sindex'
        );
        foreach($arr as $key=>$tb){
            // $res[$key] = $obj->processdata1($sdate,$edate,$cid,$key,$tb);
            $res[$key] = 0;
            //单个汽车
            foreach($info['carid'] as $c_id){
                
                $num = $obj->processdata1($sdate,$edate,$c_id,$key,$tb,1);
                $res_table[$c_id][$key] = $num;
                $res[$key] += $num;
          
            }  
            
        } 
        

        //表格数据展示
        $result['table_title'] = array('日期','汽车','连接数','广告1','跳过广告1','注册页','短信发送成功','注册成功','主页','手机号');

        //根据carid获取车次
        foreach($res_table as $key => &$val){
            
            //总和            
            $carname = $this->GetBusName($key,'bus');
            $val['phone'] = '<a href="#choice" data-toggle="modal" carid="'.$key.'" sdate="'.$sdate.'" edate="'.$edate.'" class="phone">查看手机号</a>';
            array_unshift($val,$carname);
            array_unshift($val,$date);
            $val = array_values($val);
            
        }   	
        
        

    	$datas[0]['name'] = '当前选择的所有汽车';
    	$result['x_cat'] = array('连接数','广告1','跳过广告1','注册页','短信发送成功','注册成功','主页','伙伴下载');
        $datas[0]['data'] = array_values($res);        
    	$result['y_data'] = $datas;

    	$table_data = array_values($res_table);
         
        foreach($table_data as $key=>$val1){
            
            
            if( ($val1[2] < $val1[3] ) || ($val1[3] < $val1[4] ) || ($val1[3] < $val1[5] ) || ($val1[5] < $val1[6] ) || ($val1[6] < $val1[7] ) ){
                
                $table_data[$key][0] = '<font style="color:red;">'.$val1[0].'</font>';
                $table_data[$key][1] = '<font style="color:red;">'.$val1[1].'</font>';
                $table_data[$key][2] = '<font style="color:red;">'.$val1[2].'</font>';
                $table_data[$key][3] = '<font style="color:red;">'.$val1[3].'</font>';
                $table_data[$key][4] = '<font style="color:red;">'.$val1[4].'</font>';
                $table_data[$key][5] = '<font style="color:red;">'.$val1[5].'</font>';
                $table_data[$key][6] = '<font style="color:red;">'.$val1[6].'</font>';
                $table_data[$key][7] = '<font style="color:red;">'.$val1[7].'</font>';
                $table_data[$key][8] = '<font style="color:red;">'.$val1[8].'</font>';
                
            }else{
                
                $table_data[$key] = $val1;
                
            }
            
        }
       
    	$result['table_data'] =$table_data;
        if ($isexport) {           
            require COMMON_PATH.'XExportExcel.php';
            $excel = new Excel();
            $excel->addHeader($result['table_title']);
            $excel->addBody($table_data);
            $excel->downLoad();
        }
    	return $result;
    	
    }


    /**
     * 导出流程数据Excel
     */
    public function exportexcelAction(){
        $sdate = reqstr('sdate');     
        $edate = reqstr('edate'); 
        $filename = reqstr('filename');    
         
        if (!$sdate || !$edate) {
            $edate = date('Y-m-d');
            $sdate = $edate;
        }        

        if ($edate < $sdate) {
            return array('status'=>0,'msg'=>'时间区间选择有误');
        }
        
        $cid = reqstr('carid');
        $cid = rtrim($cid,',');
        if (!$cid) {
            return array('status'=>0,'msg'=>'请选择查询汽车');
        }
        // $cid = implode(',',$info['carid']);       
        $obj = new PSys_BusModel();
        $obj->SetDb('bus_db');

        $arr = array(
            'wifi_num'=>'rb_wificonnect',
            'ad1_num'=>'rb_into_ad1',
            'jump_ad1'=>'rb_jump_ad1',
            'reg_page_num'=>'rb_into_register',
            'send_code_num'=>'rb_send_code',
            'reg_success_num'=>'rb_reg_success',
            'sindex_num'=>'rb_into_sindex',
            'down_trainapp_num'=>'rb_trainapp_down'
        );        
        $carids = explode(',',$cid);

        foreach($arr as $key=>$tb){
            // $res[$key] = $obj->processdata1($sdate,$edate,$cid,$key,$tb);
            $res[$key] = 0;
            //单个汽车
            foreach($carids as $c_id){
                $num = $obj->processdata1($sdate,$edate,$c_id,$key,$tb,1);
                $res_table[$c_id][$key] = $num;
                $res[$key] += $num;
            }            
            
        } 

        //表格数据展示
        $result['table_title'] = array('时间段','汽车','连接数','广告1','跳过广告1','注册页','短信发送成功','注册成功','主页','伙伴下载');        

        $sdate = date('Ymd',strtotime($sdate));
        $edate = date('Ymd',strtotime($edate));
        $datearea = $sdate.'-'.$edate; 

        //根据carid获取车次
        foreach($res_table as $key => &$val){
            //总和            
            $carname = $this->GetBusName($key,'bus');            
            array_unshift($val,$datearea,$carname);
            $val = array_values($val);

        }

        require COMMON_PATH.'XExportExcel.php';
        $excel = new Excel();
        $excel->addHeader($result['table_title']);
        $excel->addBody($res_table);
        $excel->downLoad('手机注册流程统计');
       
    }


    /**
     * 查看电话号码
     */
    public function showphoneAction(){
        $sdate = reqstr('sdate');
        $edate = reqstr('edate');
        $carid = reqstr('carid');
        if ( ( !$sdate && !$edate ) || !$carid) {
            return array('status'=>0,'msg'=>'非法操作');
        }
        
        if($sdate && !$edate){
            $where['date'] = $sdate;
        }
        
        if($edate && !$sdate){
            $where['date'] = $edate;
        }
        
        if($edate && $sdate){
            $where['date_>='] = $sdate;
            $where['date_<='] = $edate;
        }
        
        $where['carid'] = $carid;
        $where['phone_!='] = '';
    
        $obj = new PSys_BusModel();
        $obj->SetDb('bus_db'); 
        $res = $obj->GetList($where,'',0,0,'phone,gonettime','rb_gonet_time');
        $carname = $this->GetBusName($carid,'bus');

        foreach($res['allrow'] as &$v){
            array_unshift($v,$carname);
            $v = array_values($v);
        }

        $result['table_title'] = array('汽车','手机号','获取验证码到成功上网的时间');
        $result['table_data'] = $res['allrow'];
        return $result;
        
    }


    /**
     * 导出电话号码到excel
     */
    public function exportphoneAction(){
        $sdate = reqstr('sdate');
        $edate = reqstr('edate');
        $carid = reqstr('carid');
        if (!$sdate || !$edate || !$carid) {
            return array('status'=>0,'msg'=>'非法操作');
        }
        $where['date_>='] = $sdate;
        $where['date_<='] = $edate;
        $where['carid'] = $carid;

        $obj = new PSys_BusModel();
        $obj->SetDb('bus_db'); 
        $res = $obj->GetList($where,'',0,0,'date,phone,gonettime','rb_gonet_time');
        $carname = $this->GetBusName($carid,'bus');
        
        if (empty($res['allrow'])) {
            return array('status'=>0,'msg'=>'没有数据');
        }
        foreach($res['allrow'] as &$v){
            array_push($v,$carname);
            $v = array_values($v);
        }

        $result['table_title'] = array('日期','手机号','获取验证码到成功上网的时间','汽车');
        $result['table_data'] = $res['allrow'];
        require COMMON_PATH.'XExportExcel.php';
        $excel = new Excel();
        $excel->addHeader($result['table_title']);
        $excel->addBody($result['table_data']);
        $excel->downLoad($carname.'手机号');
    }

    /**
     * 流程显示数据
     */
    public function processdataAction(){
        $data = reqstr('data');     
        $data = urldecode($data);
        $info = array();
        parse_str($data,$info);

        $date = trim($info['date'])?trim($info['date']):date('Y-m-d',strtotime("-1 hour"));
        $hour = trim($info['hour'])?trim($info['hour']):'';
        $cid = (int)$info['carid']?(int)$info['carid']:0;
        $bus = (int)$info['show']?(int)$info['show']:0;

        /**
         *如果cid为0，bus为1表示查询公交下的总数
         *如果cid为0，bus为2表示查询长途车下的总数
         **/
        if (!$cid && $bus) {
            if ($bus == 1) {
                $where = array('isbus'=>1);
            }
            if ($bus == 2) {
                $where = array('isbus'=>0);
            }

            $obj = new PSys_BusModel();
            $obj->SetDb('bus_db');            
            $buses = $obj->GetList($where,'',0,0,"id",'rb_car');
            foreach($buses['allrow'] as $carid){
                $cids .= $carid['id'].',';
            }
            $cid = rtrim($cids,',');
        }

        $obj = new PSys_BusModel(); 
        $arr = array(
            'wifi_num'=>'rb_wificonnect',
            'ad1_num'=>'rb_into_ad1',
            'reg_page_num'=>'rb_into_register',
            'send_code_num'=>'rb_send_code',
            'reg_success_num'=>'rb_reg_success',
            'ad2_num'=>'rb_into_ad2',
            'sindex_num'=>'rb_into_sindex',
            'down_trainapp_num'=>'rb_trainapp_down'
        );
        foreach($arr as $key=>$tb){
            $res[$key] = $obj->processdata($date,$hour,$cid,$key,$tb);
        }       

        $datas[0]['name'] = $this->GetBusName($cid,'bus');
        $result['table_title'] = $result['x_cat'] = array('连接数','广告1','注册页','验证码','注册数','广告2','主页','伙伴下载');
        $datas[0]['data'] = array_values($res);
        
        $result['y_data'] = $datas;
        $table_data = array_values($res);
        $result['table_data'] =$table_data;
        return $result;
        
    }
    /**
     * 获取汽车类型，长途车还是公交车
     */
    public function getbustypeAction(){
    	
    	//0全部，1公交车，2长途汽车
    	$bus = reqnum('bus',0);//默认是全部
    	
    	if (!$bus) {
    		$where = array();
    	}
    	if ($bus == 1) {
    		$where = array('isbus'=>1);
    	}

    	if ($bus == 2) {
    		$where = array('isbus'=>0);
    	}

    	$obj = new PSys_BusModel();
    	$obj->SetDb('bus_db');
    	$where['mac_!='] = '';
    	$buses = $obj->GetList($where,'',0,0,"id,number",'rb_car');
    	$this->smarty->assign("buses",$buses['allrow']); 
    	$this->forward = 'buses';	

    }


    /**
     * 获取汽车类型，长途车还是公交车
     */
    public function getbustype1Action(){
        
        //0全部，1公交车，2长途汽车
        $bus = reqnum('bus',0);//默认是全部
        $isall = reqnum('isall',0);        
        if (!$bus) {
            $where = array();
        }
        
        if (!$isall) {
            $where = array('isbus'=>1);
        }

        if ($bus == 1) {
            $where = array('isbus'=>1);
        }

        if ($bus == 2) {
            $where = array('isbus'=>0);
        }

        $obj = new PSys_BusModel();
        $obj->SetDb('bus_db');
        $where['mac_!='] = '';
        $buses = $obj->GetList($where,'',0,0,"id,number",'rb_car');
        $this->smarty->assign("buses",$buses['allrow']); 
        $this->forward = 'buses1';   

    }


    /**
     * 新增平安保险注册流程页面
     * 
     */
    public function newregisterAction(){
        $this->smarty->assign("active","bus/newregister");  
        $this->smarty->assign("active_menu","process");  
        $this->forward = 'newregister';
    }


    /**
     * 根据类型获取对应的车站或者汽车
     */
    public function getstationtypeAction(){
        
        //1火车站，1汽车站，3汽车上
        $bus = reqnum('stationtype',1);//默认火车站
        $obj = new PSys_BusModel();

        $tb = 'rha_station';
        $obj->SetDb('rha_admin');
        $getfiled = 'id,stationname';
        
        switch ($bus) {
            case '1':                
                $where = array('stationtype'=>1);
                break;
            
            case '2':
                $where = array('stationtype'=>2);
                break;
            case '3':
                $tb = 'rb_car';               
                $obj->SetDb('bus_db');
                $getfiled = 'id,number';
                break;
        }       
             
        $buses = $obj->GetList($where,'',0,0,$getfiled,$tb);
        if ($bus != 3) {
            foreach($buses['allrow'] as &$v){
                $v['number'] = $v['stationname'];
            }
        }
        $this->smarty->assign("buses",$buses['allrow']); 
        $this->forward = 'buses';   

    }



     /**
     * 流程显示数据
     */
    public function newprocessdataAction(){
        $data = reqstr('data');     
        $data = urldecode($data);
        $info = array();
        parse_str($data,$info);

        $date = trim($info['date'])?trim($info['date']):date('Y-m-d',strtotime("-1 day"));

        //默认是某个类型下的全部车站
        $staid = $stationid = (int)$info['carid']?(int)$info['carid']:0;
        $stationtype = (int)$info['stationtype']?(int)$info['stationtype']:1;

        $obj = new PSys_BusModel();

        //如果是汽车站或者火车站
        if (in_array($stationtype,array(1,2))) {
            $db_tb = array('db'=>'rhc_log_pingan');
            $wifi_num = array('rha_admin'=>'rha_aclogview');
            $ad1_num = array('rha_admin'=>'rha_count');

            $obj->SetDb('rha_admin');
            $where = array('stationtype'=>$stationtype); 
            $tb = 'rha_station';

        //如果是汽车
        }elseif($stationtype == 3){
            $db_tb = array('bus_db'=>'rb_log_pingan');
            $wifi_num = array('bus_db'=>'rb_wificonnect');
            $ad1_num = array('bus_db'=>'rb_into_ad1');

            $obj->SetDb('bus_db');
            $where = array(); 
            $tb = 'rb_car';
        }
        
        //如果是ALL则取出对应栏目下的所有车或车站
        if (!$stationid) {
            $buses = $obj->GetList($where,'',0,0,"id",$tb);
            foreach($buses['allrow'] as $carid){
                $cids .= $carid['id'].',';
            }
            $stationid = rtrim($cids,',');
        } 
       
        $arr = array(
            'wifi_num'=>$wifi_num,
            'ad1_num'=>$ad1_num,
            'send_code_num'=>$db_tb,
            'baodan_reg'=>$db_tb,
            'phone_reg'=>$db_tb,
            'baodan_stay_time'=>$db_tb,
            'phone_stay_time'=>$db_tb
            
        );

        foreach($arr as $key=>$tbs){
            $res[$key] = $obj->newprocessdata($date,$stationid,$key,$tbs,$stationtype);
        }        
        // print_r($res);
        $res['baodan_stay_time'] = round((int)$res['baodan_stay_time']/(int)$res['baodan_reg'],2);
        $res['phone_stay_time'] = round((int)$res['phone_stay_time']/(int)$res['phone_reg'],2);
        
        // print_r($res);
        $datas[0]['name'] = $this->GetBusName($staid,'station');
        $result['table_title'] = $result['x_cat'] = array('连接数','广告1','验证码','保单注册','手机注册','保单注册时间','手机注册时间');
        $datas[0]['data'] = array_values($res);
        
        $result['y_data'] = $datas;
        $table_data = array_values($res);
        $result['table_data'] = $table_data;
        return $result;
    }
    
    /**
    *
    * 
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function selfappAction(){
        
        $PSys_BusModel = new PSys_BusModel();
        
        $where = array();
        $where['isbus'] = 1;
        $where['isvalide'] = 1;
        
        $bus1 = $PSys_BusModel->GetList($where,"id ASC",0,0,"number,id","rb_car");
        $this->smarty->assign("bus1",$bus1['allrow']);
        
        $where = array();
        $where['isbus'] = 0;
        $where['isvalide'] = 1;
        
        $bus2 = $PSys_BusModel->GetList($where,"id ASC",0,0,"number,id","rb_car");
        $this->smarty->assign("bus2",$bus2['allrow']);
        
        $this->forward = "selfapp";
        
    }
    
    public function getselfappdataAction($output = false){
     
        
        $PSys_BusModel = new PSys_BusModel();
        
        $where = " 1 ";
        
        $bmanyday = $bday = reqstr("bmanyday");
        $emanyday = $eday = reqstr("emanyday");
        
        $bmanyday = str_replace("-","",$bmanyday);
        $emanyday = str_replace("-","",$emanyday);
        
        if($bmanyday && !$emanyday){
            
            $where .= " AND dates = {$bmanyday} "; 
            
            $this->smarty->assign("time",$bday);
            
        }elseif(!$bmanyday && $emanyday){
            
            $where .= " AND dates = {$emanyday} ";
            
            $this->smarty->assign("time",$eday);
            
        }elseif($bmanyday && $emanyday){
            
            $where .= " AND dates >= {$bmanyday} AND dates <= {$emanyday} ";
            
            $this->smarty->assign("time",$bday . " ~ " . $eday);
            
        }
        
        $v = reqstr("v");
        
        $carid = $car_id = reqarray("carid");
        $carid = implode(",",$carid);
        $where .= " AND car_id IN ({$carid})";
        
        if($v == 'uv'){
            
            $field = "bkfwl_uv,bktjsj_uv,bkfhl_uv,czapp_uv,ios_uv,android_uv";
            
        }elseif($v == 'pv'){
            
            $field = "bkfwl_pv,bktjsj_pv,bkfhl_pv,czapp_pv,ios_pv,android_pv";
            
        }
        
        $result = $PSys_BusModel->GetList($where,"car_id ASC",0,0,"*","rb_v10_apppage_static");
        $datas   = $result['allrow'];
        
        
        
        //汇总统计
        //*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*//
        foreach($datas as $key=>$val){
            
            $data[$val['car_id']]['id'] = $val['car_id'];
            $data[$val['car_id']]['number'] = $val['number'];
            $data[$val['car_id']]['bkfwl'] = $val['bkfwl_'.$v];
            $data[$val['car_id']]['bktjsj'] = $val['bktjsj_'.$v];
            $data[$val['car_id']]['bkfhl'] = $val['bkfhl_'.$v];
            $data[$val['car_id']]['czapp'] = $val['czapp_'.$v];
            $data[$val['car_id']]['ios']   = $val['ios_'.$v];
            $data[$val['car_id']]['android']   = $val['android_'.$v];
            
        }
        
        
        if($output){
            
            
            return $data;
            exit;
            
        }
        
        $this->smarty->assign("data",$data);
        //*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*//
        
        $this->forward = "getselfappdata";
        
    }
    
    /**
    *
    * @do 交运宣传页数据统计 导出
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getselfappdataoutputAction(){
        
        $result = $this->getselfappdataAction(true);
        
        $XPhpExcel = new XPhpExcel();
        $Creator = "adm.wonaonao.com";
        $LastModifiedBy = $Creator;
        $Title = '交运宣传页数据统计';
        $Subject = '交运宣传页数据统计';
        $Description = '交运宣传页数据统计';
        $Keywords = $Description;
        $Category = $Description;
        $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
        $XPhpExcel->setSheet(0,$Subject);
        $XPhpExcel->setValue("A1","车号");
        //$XPhpExcel->setWidth("A",14,2);
        $XPhpExcel->setValue("B1",'日期');
        $XPhpExcel->setValue("C1",'板块访问量');
        $XPhpExcel->setValue("D1",'板块停留时长');
        $XPhpExcel->setValue("E1",'车站服务返回(点击数)');
        $XPhpExcel->setValue("F1",'手机售票APP(下载数)');
        $XPhpExcel->setValue("G1",'iOS');
        $XPhpExcel->setValue("H1",'Android');
        
        $XPhpExcel->setWidth("A",14,2);
        $XPhpExcel->setWidth("B",25,2);
        $XPhpExcel->setWidth("C",20,2);
        $XPhpExcel->setWidth("D",20,2);
        $XPhpExcel->setWidth("E",25,2);
        $XPhpExcel->setWidth("F",25,2);
        $XPhpExcel->setWidth("G",15,2);
        $XPhpExcel->setWidth("H",15,2);
         	 
        $bmanyday = $bday = reqstr("bmanyday");
        $emanyday = $eday = reqstr("emanyday");
        
        if($bmanyday && !$emanyday){
            
            $time = $bday;
            
        }elseif(!$bmanyday && $emanyday){
            
            $time = $eday;
            
        }elseif($bmanyday && $emanyday){
            
            $time = $bday . " ~ " . $eday;
            
        }     
        
        $num = 1;
        
        foreach($result as $key=>$val){

                $XPhpExcel->setValue('A'.($num+1),$val['number']);
                $XPhpExcel->setValue('B'.($num+1),$time);
                $XPhpExcel->setValue('C'.($num+1),$val['bkfwl']);
                $XPhpExcel->setValue('D'.($num+1),$val['bktjsj']);
                $XPhpExcel->setValue('E'.($num+1),$val['bkfhl']);
                $XPhpExcel->setValue('F'.($num+1),$val['czapp']);
                $XPhpExcel->setValue('G'.($num+1),$val['ios']);
                $XPhpExcel->setValue('H'.($num+1),$val['android']);
                $num++;
                
        }
       
        //清空输出缓存                    
        ob_clean();    
                      
        $XPhpExcel->output($time."/".$Description);
        unset($XPhpExcel);//销毁
        
    }
    
    public function movieAction(){

        
        $PSys_BusModel = new PSys_BusModel();
        
        $where = array();
        $where['isbus'] = 1;
        $where['isvalide'] = 1;
        
        $bus1 = $PSys_BusModel->GetList($where,"id ASC",0,0,"number,id","rb_car");
        $this->smarty->assign("bus1",$bus1['allrow']);
        
        $where = array();
        $where['isbus'] = 0;
        $where['isvalide'] = 1;
        
        $bus2 = $PSys_BusModel->GetList($where,"id ASC",0,0,"number,id","rb_car");
        $this->smarty->assign("bus2",$bus2['allrow']);
        
        $this->forward = "movie";
        
    }
    
    /**
    *
    * @do 电影查询数据
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getmoviedataAction($output = false){
     
        
        $PSys_BusModel = new PSys_BusModel();
        
        $where = " 1 ";
        
        $bmanyday = $bday = reqstr("bmanyday");
        $emanyday = $eday = reqstr("emanyday");
        
        $bmanyday = str_replace("-","",$bmanyday);
        $emanyday = str_replace("-","",$emanyday);
        
        if($bmanyday && !$emanyday){
            
            $where .= " AND dates = {$bmanyday} "; 
            
            $this->smarty->assign("time",$bday);
            
        }elseif(!$bmanyday && $emanyday){
            
            $where .= " AND dates = {$emanyday} ";
            
            $this->smarty->assign("time",$eday);
            
        }elseif($bmanyday && $emanyday){
            
            $where .= " AND dates >= {$bmanyday} AND dates <= {$emanyday} ";
            
            $this->smarty->assign("time",$bday . " ~ " . $eday);
            
        }
        
        $v = reqstr("v");
        
        $carid = $car_id = reqarray("carid");
        $carid = implode(",",$carid);
        $where .= " AND car_id IN ({$carid})";
        
        if($v == 'uv'){
            
            $field = "bkfwl_uv,bktjsj_uv,click_uv,tlsj_uv,back_uv,ios_uv,anroid_uv";
            
        }elseif($v == 'pv'){
            
            $field = "bkfwl_pv,bktjsj_pv,click_pv,tlsj_pv,back_pv,ios_pv,anroid_pv";
            
        }
        
        $result = $PSys_BusModel->GetList($where,"id ASC",0,0,"*","rb_v10_movie_static");
        $datas   = $result['allrow'];
        
        
        
        //汇总统计
        //*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*//
        foreach($datas as $key=>$val){
            
            $data[$val['car_id']][$val['movieid']]['id'] += $val['car_id'];
            $data[$val['car_id']][$val['movieid']]['number'] = $val['number'];
            $data[$val['car_id']][$val['movieid']]['bkfwl'] += $val['bkfwl_'.$v];
            $data[$val['car_id']][$val['movieid']]['bktjsj'] += $val['bktjsj_'.$v];
            $data[$val['car_id']][$val['movieid']]['moviename'] = $val['moviename'];
            $data[$val['car_id']][$val['movieid']]['click'] += $val['click_'.$v];
            $data[$val['car_id']][$val['movieid']]['tlsj'] += $val['tlsj_'.$v];
            $data[$val['car_id']][$val['movieid']]['back']   += $val['back_'.$v];
            $data[$val['car_id']][$val['movieid']]['ios']   += $val['ios_'.$v];
            $data[$val['car_id']][$val['movieid']]['android']   += $val['android_'.$v];
     
        }
        
        
        if($output){
            
            
            return $data;
            exit;
            
        }
        
        $this->smarty->assign("data",$data);
        //*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*//
        
        $this->forward = "getmoviedata";
        
    }
    
    
    /**
    *
    * @do movie数据统计 导出
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getmoviedataoutputAction(){
        
        $result = $this->getmoviedataAction(true);

        $XPhpExcel = new XPhpExcel();
        $Creator = "adm.wonaonao.com";
        $LastModifiedBy = $Creator;
        $Title = '交运电影数据统计';
        $Subject = '交运电影数据统计';
        $Description = '交运电影数据统计';
        $Keywords = $Description;
        $Category = $Description;
        $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
        $XPhpExcel->setSheet(0,$Subject);
        $XPhpExcel->setValue("A1","车号");
        //$XPhpExcel->setWidth("A",14,2);
        $XPhpExcel->setValue("B1",'日期');
        $XPhpExcel->setValue("C1",'板块访问量');
        $XPhpExcel->setValue("D1",'板块停留时长');
        $XPhpExcel->setValue("E1",'电影');
        $XPhpExcel->setValue("F1",'点击次数');
        $XPhpExcel->setValue("G1",'停留时长');
        $XPhpExcel->setValue("H1",'电影返回');
        $XPhpExcel->setValue("I1",'iOS');
        $XPhpExcel->setValue("J1",'Android');
        
        $XPhpExcel->setWidth("A",14,2);
        $XPhpExcel->setWidth("B",25,2);
        $XPhpExcel->setWidth("C",20,2);
        $XPhpExcel->setWidth("D",20,2);
        $XPhpExcel->setWidth("E",25,2);
        $XPhpExcel->setWidth("F",25,2);
        $XPhpExcel->setWidth("G",25,2);
        $XPhpExcel->setWidth("H",25,2);
        $XPhpExcel->setWidth("I",15,2);
        $XPhpExcel->setWidth("J",15,2);
         	 
        $bmanyday = $bday = reqstr("bmanyday");
        $emanyday = $eday = reqstr("emanyday");
        
        if($bmanyday && !$emanyday){
            
            $time = $bday;
            
        }elseif(!$bmanyday && $emanyday){
            
            $time = $eday;
            
        }elseif($bmanyday && $emanyday){
            
            $time = $bday . " ~ " . $eday;
            
        }     
        
        $num = 1;
        
        foreach($result as $key2=>$val2){
            
            foreach($val2 as $key=>$val){

                $XPhpExcel->setValue('A'.($num+1),$val['number']);
                $XPhpExcel->setValue('B'.($num+1),$time);
                $XPhpExcel->setValue('C'.($num+1),$val['bkfwl']);
                $XPhpExcel->setValue('D'.($num+1),$val['bktjsj']);
                $XPhpExcel->setValue('E'.($num+1),$val['moviename']);
                $XPhpExcel->setValue('F'.($num+1),$val['click']);
                $XPhpExcel->setValue('G'.($num+1),$val['tlsj']);
                $XPhpExcel->setValue('H'.($num+1),$val['back']);
                $XPhpExcel->setValue('I'.($num+1),$val['ios']);
                $XPhpExcel->setValue('J'.($num+1),$val['android']);
                $num++;
                
            }
                
        }
       
        //清空输出缓存                    
        ob_clean();    
                      
        $XPhpExcel->output($time."/".$Description);
        unset($XPhpExcel);//销毁
        
    }
    
    public function gameAction(){

        
        $PSys_BusModel = new PSys_BusModel();
        
        $where = array();
        $where['isbus'] = 1;
        $where['isvalide'] = 1;
        
        $bus1 = $PSys_BusModel->GetList($where,"id ASC",0,0,"number,id","rb_car");
        $this->smarty->assign("bus1",$bus1['allrow']);
        
        $where = array();
        $where['isbus'] = 0;
        $where['isvalide'] = 1;
        
        $bus2 = $PSys_BusModel->GetList($where,"id ASC",0,0,"number,id","rb_car");
        $this->smarty->assign("bus2",$bus2['allrow']);
        
        $this->forward = "game";
        
    }
    
    /**
    *
    * @do 游戏查询数据
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getgamedataAction($output = false){
     
        
        $PSys_BusModel = new PSys_BusModel();
        
        $where = " 1 ";
        
        $bmanyday = $bday = reqstr("bmanyday");
        $emanyday = $eday = reqstr("emanyday");
        
        $bmanyday = str_replace("-","",$bmanyday);
        $emanyday = str_replace("-","",$emanyday);
        
        if($bmanyday && !$emanyday){
            
            $where .= " AND dates = {$bmanyday} "; 
            
            $this->smarty->assign("time",$bday);
            
        }elseif(!$bmanyday && $emanyday){
            
            $where .= " AND dates = {$emanyday} ";
            
            $this->smarty->assign("time",$eday);
            
        }elseif($bmanyday && $emanyday){
            
            $where .= " AND dates >= {$bmanyday} AND dates <= {$emanyday} ";
            
            $this->smarty->assign("time",$bday . " ~ " . $eday);
            
        }
        
        $v = reqstr("v");
        
        $carid = $car_id = reqarray("carid");
        $carid = implode(",",$carid);
        $where .= " AND car_id IN ({$carid})";
        
        if($v == 'uv'){
            
            $field = "bkfwl_uv,bktjsj_uv,click_uv,tlsj_uv,back_uv,ios_uv,anroid_uv";
            
        }elseif($v == 'pv'){
            
            $field = "bkfwl_pv,bktjsj_pv,click_pv,tlsj_pv,back_pv,ios_pv,anroid_pv";
            
        }
        
        $result = $PSys_BusModel->GetList($where,"id ASC",0,0,"*","rb_v10_game_static");
        $datas   = $result['allrow'];
        
        
        
        //汇总统计
        //*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*//
        foreach($datas as $key=>$val){
            
            $data[$val['car_id']][$val['gameid']]['id'] = $val['car_id'];
            $data[$val['car_id']][$val['gameid']]['number'] = $val['number'];
            $data[$val['car_id']][$val['gameid']]['bkfwl'] += $val['bkfwl_'.$v];
            $data[$val['car_id']][$val['gameid']]['bktjsj'] += $val['bktjsj_'.$v];
            $data[$val['car_id']][$val['gameid']]['gamename'] = $val['gamename'];
            $data[$val['car_id']][$val['gameid']]['click'] += $val['click_'.$v];
            $data[$val['car_id']][$val['gameid']]['tlsj'] += $val['tlsj_'.$v];
            $data[$val['car_id']][$val['gameid']]['back']   += $val['back_'.$v];
            $data[$val['car_id']][$val['gameid']]['ios']   += $val['ios_'.$v];
            $data[$val['car_id']][$val['gameid']]['android']   += $val['android_'.$v];
            
        }
        
        
        if($output){
            
            
            return $data;
            exit;
            
        }
        
        $this->smarty->assign("data",$data);
        //*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*//
        
        $this->forward = "getgamedata";
        
    }
    
    /**
    *
    * @do game数据统计 导出
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getgamedataoutputAction(){
        
        $result = $this->getgamedataAction(true);

        $XPhpExcel = new XPhpExcel();
        $Creator = "adm.wonaonao.com";
        $LastModifiedBy = $Creator;
        $Title = '交运游戏数据统计';
        $Subject = '交运游戏数据统计';
        $Description = '交运游戏数据统计';
        $Keywords = $Description;
        $Category = $Description;
        $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
        $XPhpExcel->setSheet(0,$Subject);
        $XPhpExcel->setValue("A1","车号");
        //$XPhpExcel->setWidth("A",14,2);
        $XPhpExcel->setValue("B1",'日期');
        $XPhpExcel->setValue("C1",'板块访问量');
        $XPhpExcel->setValue("D1",'板块停留时长');
        $XPhpExcel->setValue("E1",'游戏');
        $XPhpExcel->setValue("F1",'点击次数');
        $XPhpExcel->setValue("G1",'停留时长');
        $XPhpExcel->setValue("H1",'游戏返回');
        $XPhpExcel->setValue("I1",'iOS');
        $XPhpExcel->setValue("J1",'Android');
        
        $XPhpExcel->setWidth("A",14,2);
        $XPhpExcel->setWidth("B",25,2);
        $XPhpExcel->setWidth("C",20,2);
        $XPhpExcel->setWidth("D",20,2);
        $XPhpExcel->setWidth("E",25,2);
        $XPhpExcel->setWidth("F",25,2);
        $XPhpExcel->setWidth("G",25,2);
        $XPhpExcel->setWidth("H",25,2);
        $XPhpExcel->setWidth("I",15,2);
        $XPhpExcel->setWidth("J",15,2);
         	 
        $bmanyday = $bday = reqstr("bmanyday");
        $emanyday = $eday = reqstr("emanyday");
        
        if($bmanyday && !$emanyday){
            
            $time = $bday;
            
        }elseif(!$bmanyday && $emanyday){
            
            $time = $eday;
            
        }elseif($bmanyday && $emanyday){
            
            $time = $bday . " ~ " . $eday;
            
        }     
        
        $num = 1;
        
        foreach($result as $key2=>$val2){
            
            foreach($val2 as $key=>$val){

                $XPhpExcel->setValue('A'.($num+1),$val['number']);
                $XPhpExcel->setValue('B'.($num+1),$time);
                $XPhpExcel->setValue('C'.($num+1),$val['bkfwl']);
                $XPhpExcel->setValue('D'.($num+1),$val['bktjsj']);
                $XPhpExcel->setValue('E'.($num+1),$val['gamename']);
                $XPhpExcel->setValue('F'.($num+1),$val['click']);
                $XPhpExcel->setValue('G'.($num+1),$val['tlsj']);
                $XPhpExcel->setValue('H'.($num+1),$val['back']);
                $XPhpExcel->setValue('I'.($num+1),$val['ios']);
                $XPhpExcel->setValue('J'.($num+1),$val['android']);
                $num++;
                
            }
                
        }
       
        //清空输出缓存                    
        ob_clean();    
                      
        $XPhpExcel->output($time."/".$Description);
        unset($XPhpExcel);//销毁
        
    }
    
    
    public function activityAction(){

        
        $PSys_BusModel = new PSys_BusModel();
        
        $where = array();
        $where['isbus'] = 1;
        $where['isvalide'] = 1;
        
        $bus1 = $PSys_BusModel->GetList($where,"id ASC",0,0,"number,id","rb_car");
        $this->smarty->assign("bus1",$bus1['allrow']);
        
        $where = array();
        $where['isbus'] = 0;
        $where['isvalide'] = 1;
        
        $bus2 = $PSys_BusModel->GetList($where,"id ASC",0,0,"number,id","rb_car");
        $this->smarty->assign("bus2",$bus2['allrow']);
        
        $this->forward = "activity";
        
    }
    
    /**
    *
    * @do 游戏查询数据
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getactivitydataAction($output = false){
     
        
        $PSys_BusModel = new PSys_BusModel();
        
        $where = " 1 ";
        
        $bmanyday = $bday = reqstr("bmanyday");
        $emanyday = $eday = reqstr("emanyday");
        
        $bmanyday = str_replace("-","",$bmanyday);
        $emanyday = str_replace("-","",$emanyday);
        
        if($bmanyday && !$emanyday){
            
            $where .= " AND dates = {$bmanyday} "; 
            
            $this->smarty->assign("time",$bday);
            
        }elseif(!$bmanyday && $emanyday){
            
            $where .= " AND dates = {$emanyday} ";
            
            $this->smarty->assign("time",$eday);
            
        }elseif($bmanyday && $emanyday){
            
            $where .= " AND dates >= {$bmanyday} AND dates <= {$emanyday} ";
            
            $this->smarty->assign("time",$bday . " ~ " . $eday);
            
        }
        
        $v = reqstr("v");
        
        $carid = $car_id = reqarray("carid");
        $carid = implode(",",$carid);
        $where .= " AND car_id IN ({$carid})";
        
        if($v == 'uv'){
            
            $field = "click_uv,ios_uv,anroid_uv";
            
        }elseif($v == 'pv'){
            
            $field = "click_pv,ios_pv,anroid_pv";
            
        }
        
        $result = $PSys_BusModel->GetList($where,"id ASC",0,0,"*","rb_v10_activity_static");
        $datas   = $result['allrow'];
        
        //汇总统计
        //*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*//
        foreach($datas as $key=>$val){
            
            $data[$val['car_id']][$val['activityid']]['id'] = $val['car_id'];
            $data[$val['car_id']][$val['activityid']]['number'] = $val['number'];
            $data[$val['car_id']][$val['activityid']]['activityname'] = $val['activityname'];
            $data[$val['car_id']][$val['activityid']]['click'] += $val['click_'.$v];
            $data[$val['car_id']][$val['activityid']]['ios']   += $val['ios_'.$v];
            $data[$val['car_id']][$val['activityid']]['android']   += $val['android_'.$v];
            
        }
        
        
        if($output){
            
            
            return $data;
            exit;
            
        }
        
        $this->smarty->assign("data",$data);
        //*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*//
        
        $this->forward = "getactivitydata";
        
    }
    
    /**
    *
    * @do game数据统计 导出
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getactivitydataoutputAction(){
        
        $result = $this->getactivitydataAction(true);

        $XPhpExcel = new XPhpExcel();
        $Creator = "adm.wonaonao.com";
        $LastModifiedBy = $Creator;
        $Title = '交运活动数据统计';
        $Subject = '交运活动数据统计';
        $Description = '交运活动数据统计';
        $Keywords = $Description;
        $Category = $Description;
        $XPhpExcel->setBasicAttr($Creator,$LastModifiedBy,$Title,$Subject,$Description,$Keywords,$Category);
        $XPhpExcel->setSheet(0,$Subject);
        $XPhpExcel->setValue("A1","车号");
        //$XPhpExcel->setWidth("A",14,2);
        $XPhpExcel->setValue("B1",'日期');
        $XPhpExcel->setValue("C1",'活动');
        $XPhpExcel->setValue("D1",'点击次数');
        $XPhpExcel->setValue("E1",'iOS');
        $XPhpExcel->setValue("F1",'Android');
        
        $XPhpExcel->setWidth("A",14,2);
        $XPhpExcel->setWidth("B",25,2);
        $XPhpExcel->setWidth("C",20,2);
        $XPhpExcel->setWidth("D",20,2);
        $XPhpExcel->setWidth("E",15,2);
        $XPhpExcel->setWidth("F",15,2);
         	 
        $bmanyday = $bday = reqstr("bmanyday");
        $emanyday = $eday = reqstr("emanyday");
        
        if($bmanyday && !$emanyday){
            
            $time = $bday;
            
        }elseif(!$bmanyday && $emanyday){
            
            $time = $eday;
            
        }elseif($bmanyday && $emanyday){
            
            $time = $bday . " ~ " . $eday;
            
        }     
        
        $num = 1;
        
        foreach($result as $key2=>$val2){
            
            foreach($val2 as $key=>$val){

                $XPhpExcel->setValue('A'.($num+1),$val['number']);
                $XPhpExcel->setValue('B'.($num+1),$time);
                $XPhpExcel->setValue('C'.($num+1),$val['activityname']);
                $XPhpExcel->setValue('D'.($num+1),$val['click']);
                $XPhpExcel->setValue('E'.($num+1),$val['ios']);
                $XPhpExcel->setValue('F'.($num+1),$val['android']);
                $num++;
                
            }
                
        }
       
        //清空输出缓存                    
        ob_clean();    
                      
        $XPhpExcel->output($time."/".$Description);
        unset($XPhpExcel);//销毁
        
    }
    
    
 
    
}