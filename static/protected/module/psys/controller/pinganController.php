<?php
/**
* Copyright(c) 2015
* 日    期:2015年07月21日
* 文 件 名:{pingan}Controller.php
* 创建时间:15:27
* 字符编码:UTF-8
* 版本信息:v1.0
* 修改日期:none
* 最后版本:v1.0
* 创 建 者:Jerry (Jerry@rockhippo.cn)
* 修 改 者:none
* 版本地址:none
* 摘    要: 综合统计控制器
*/
class pinganController extends PSys_AbstractController{
    
	public function __construct() {
		parent::__construct(); 
	}
    
    /**
     *
     * @do 导出综合数据
     *
     * @pingan public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
    */
    public function ajaxExportAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $order = "";
        $where = $stationArr = array();
        if($start){$where["date_>="] = $start;}
        if($end){$where["date_<="] = $end;}
        if(!$start || !end){exit();}
        $PSys_PinganModel = new PSys_PinganModel();
        $PSys_PinganRule = new PSys_PinganRule();
        $stationlist = $PSys_PinganModel->GetList(array(), $order, 0, 0, "id,stationname,appkey","rha_station");
        foreach ($stationlist['allrow'] as $k => $v) {
            $temp_key = $v['id'];
            $stationArr[$temp_key]['name'] = $v['stationname'];
            $stationArr[$temp_key]['appkey'] = $v['appkey'];
        }
        $result = $PSys_PinganModel->GetList($where, 'date DESC,station ASC', 0, 0, "*","rha_aclogview");
        foreach ($result['allrow'] as $k => $v) {
            $temp_key = $v['station'];
            $result['allrow'][$k]['station'] = $stationArr[$temp_key]['name'];
            $tempArr = array();
            $tempArr['stationid'] = $v['station'];
            $tempArr['model'] = 'ad';
            $tempArr['action'] = 'visit';
            $tempArr['detail'] = 'uv';
            $tempArr['date'] = date('Y_m_d',strtotime($v['date']));
            $tempad = $PSys_PinganModel->getOne($tempArr,'dtime','rha_count_process');
            $result['allrow'][$k]['adnum'] = $tempad['dtime'];
            $tempArr = array();
            $tempArr['stationid'] = $v['station'];
            $tempArr['model'] = 'register';
            $tempArr['action'] = 'visit';
            $tempArr['detail'] = 'uv';
            $tempArr['date'] = date('Y_m_d',strtotime($v['date']));
            $tempad = $PSys_PinganModel->getOne($tempArr,'dtime','rha_count_process');
            $result['allrow'][$k]['regnum'] = $tempad['dtime'];
            $tempArr = array();
            $tempArr['appkey'] = $stationArr[$temp_key]['appkey'];
            $tempArr['type'] = '603';
            $tempArr['pid'] = '0';
            $tempArr['cday'] = date('Ymd',strtotime($v['date']));
            $tempRs = $PSys_PinganRule->getLogDataNum($tempArr);
            $result['allrow'][$k]['overnum'] = $tempRs[0]['total'];
            $tempArr = array();
            $tempArr['appkey'] = $stationArr[$temp_key]['appkey'];
            $tempArr['type'] = '202';
            $tempArr['pid'] = '0';
            $tempArr['cday'] = date('Ymd',strtotime($v['date']));
            $tempRs = $PSys_PinganRule->getLogDataNum($tempArr);
            $result['allrow'][$k]['phonenum'] = $tempRs[0]['total'];
            $tempArr = array();
            $tempArr['appkey'] = $stationArr[$temp_key]['appkey'];
            $tempArr['type'] = '205';
            $tempArr['pid'] = '0';
            $tempArr['cday'] = date('Ymd',strtotime($v['date']));
            $tempRs = $PSys_PinganRule->getDataNum($tempArr);
            $result['allrow'][$k]['bdnum'] = $tempRs[0]['total'];
        }
        //清空输出缓存                    
        ob_clean();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("rockhippo")
                    ->setLastModifiedBy("jerry")
                    ->setTitle("平安保单综合数据")
                    ->setSubject("平安数据")
                    ->setDescription("")
                    ->setKeywords("excel")
                    ->setCategory("result file");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1','日 期')    
                        ->setCellValue('B1','车 站')
                        ->setCellValue('C1','SSID')
                        ->setCellValue('D1','广告1(UV)')
                        ->setCellValue('E1','平安页面(UV)')
                        ->setCellValue('F1','跳过次数')
                        ->setCellValue('G1','保单认证注册数')
                        ->setCellValue('H1','手机认证注册数');  
        foreach($result['allrow'] as $k => $v){
            $num=$k+2;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$num, $v['date'])    
                        ->setCellValue('B'.$num, $v['station'])
                        ->setCellValue('C'.$num, $v['num'])
                        ->setCellValue('D'.$num, $v['adnum'])
                        ->setCellValue('E'.$num, $v['regnum'])
                        ->setCellValue('F'.$num, $v['overnum'])
                        ->setCellValue('G'.$num, $v['bdnum'])
                        ->setCellValue('H'.$num, $v['phonenum']);
        }
        $objPHPExcel->getActiveSheet()->setTitle('User');
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(13);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(13);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(16);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="平安保单综合数据.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;  
    }

    /**
     *
     * @do 导出综合数据
     *
     * @pingan public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
    */
    public function ajaxStatisExportAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $page = reqnum('page',1);
        $order = "";
        $where = $stationArr = array();
        if($start){$where["date_>="] = $start;}
        if($end){$where["date_<="] = $end;}
        $PSys_PinganModel = new PSys_PinganModel();
        $PSys_PinganRule = new PSys_PinganRule();
        $stationlist = $PSys_PinganModel->GetList(array(), $order, 0, 0, "id,stationname,appkey","rha_station");
        foreach ($stationlist['allrow'] as $k => $v) {
            $temp_key = $v['id'];
            $stationArr[$temp_key]['name'] = $v['stationname'];
            $stationArr[$temp_key]['appkey'] = $v['appkey'];
        }
        $result = $PSys_PinganModel->GetList($where, 'date DESC,station ASC', 0, 0, "*","rha_aclogview");
        foreach ($result['allrow'] as $k => $v) {
            $temp_key = $v['station'];
            $result['allrow'][$k]['station'] = $stationArr[$temp_key]['name'];
            $tempArr = array();
            $tempArr['stationid'] = $v['station'];
            $tempArr['model'] = 'ad';
            $tempArr['action'] = 'visit';
            $tempArr['detail'] = 'uv';
            $tempArr['date'] = date('Y_m_d',strtotime($v['date']));
            $tempad = $PSys_PinganModel->getOne($tempArr,'dtime','rha_count_process');
            $result['allrow'][$k]['adnum'] = intval($tempad['dtime']);
            $tempArr = array();
            $tempArr['appkey'] = $stationArr[$temp_key]['appkey'];
            $tempArr['type'] = '202';
            $tempArr['pid'] = '0';
            $tempArr['cday'] = date('Ymd',strtotime($v['date']));
            $tempRs = $PSys_PinganRule->getLogDataNum($tempArr);
            $result['allrow'][$k]['phonenum'] = intval($tempRs[0]['total']);
            $tempArr = array();
            $tempArr['appkey'] = $stationArr[$temp_key]['appkey'];
            $tempArr['type'] = '204';
            $tempArr['cday'] = date('Ymd',strtotime($v['date']));
            $tempRs = $PSys_PinganRule->getLogDataNum($tempArr);
            $result['allrow'][$k]['yzmnum'] = intval($tempRs[0]['total']);
            $tempArr = array();
            $tempArr['appkey'] = $stationArr[$temp_key]['appkey'];
            $tempArr['type'] = '205';
            $tempArr['pid'] = '0';
            $tempArr['cday'] = date('Ymd',strtotime($v['date']));
            $tempRs = $PSys_PinganRule->getDataNum($tempArr);
            $result['allrow'][$k]['bdnum'] = intval($tempRs[0]['total']);
            $tempArr = array();
            $tempArr['appkey'] = $stationArr[$temp_key]['appkey'];
            $tempArr['pid'] = '0';
            $tempArr['cday'] = date('Ymd',strtotime($v['date']));
            $tempRs = $PSys_PinganRule->getSumTime($tempArr);
            $result['allrow'][$k]['staytime'] = intval($tempRs[0]['total']);

        }
        
        $micrstart = strtotime($start);
        $micrend = strtotime($end);
        $days = 0;
        for($micrstart; $micrstart<=$micrend; $micrstart+=86400){
            $days++;
            $i++;
        }
        $ssid = $ad1 = $yzmnum = $bdnum = $phonenum = $staytime = 0;
        foreach ($result['allrow'] as $k => $v) {
            $ssid += intval($v['num']);
            $ad1 += intval($v['adnum']); 
            $yzmnum += intval($v['yzmnum']); 
            $bdnum += intval($v['bdnum']); 
            $phonenum += intval($v['phonenum']); 
            $staytime += intval($v['staytime']); 
        }
        ob_clean();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("rockhippo")
                    ->setLastModifiedBy("jerry")
                    ->setTitle("平安保单汇总数据")
                    ->setSubject("平安数据")
                    ->setDescription("")
                    ->setKeywords("excel")
                    ->setCategory("result file");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1','日 期')    
                        ->setCellValue('B1','SSID')
                        ->setCellValue('C1','广告1')
                        ->setCellValue('D1','获取验证码')
                        ->setCellValue('E1','保单认证注册')
                        ->setCellValue('F1','手机认证注册')
                        ->setCellValue('G1','平均页面停留时间(s)');  
        
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', $start.' 至 '.$end)
                    ->setCellValue('B2', $ssid)
                    ->setCellValue('C2', $ad1)
                    ->setCellValue('D2', $yzmnum)
                    ->setCellValue('E2', $bdnum)
                    ->setCellValue('F2', $phonenum)
                    ->setCellValue('G2', round($staytime/$days,2));
        
        $objPHPExcel->getActiveSheet()->setTitle('User');
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(13);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="平安保单汇总数据.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;  
    }

    /**
     *
     * @do 导出平安用户数据
     *
     * @pingan public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
    */
    public function ajaxUserExportAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $page = reqnum('page',1);
        $pagesize = 20;
        $order = "";
        $where = $stationArr = array();
        if($start){$where["create_time_>="] = strtotime($start);}
        if($end){$where["create_time_<"] = (strtotime($end)+24*3600);}
        $where['type_in'] = array(202,205);
        $where['pid'] = 0;
        $PSys_PinganModel = new PSys_PinganModel();
        $PSys_PinganRule = new PSys_PinganRule();
        $stationlist = $PSys_PinganModel->GetList(array(), $order, 0, 0, "id,stationname,appkey","rha_station");
        foreach ($stationlist['allrow'] as $k => $v) {
            $temp_key = $v['appkey'];
            $stationArr[$temp_key]['name'] = $v['stationname'];
        }
        $sex = $bx_type = array();
        $sex[1] = '男';
        $sex[2] = '女';
        $bx_type['1'] = '公交意外险';
        $bx_type['2'] = '驾驶员意外险';
        $result = $PSys_PinganModel->GetList($where, 'create_time DESC', 0, 0, "*");
        foreach ($result['allrow'] as $k => $v) {
            $temp_key = $v['appkey'];
            $result['allrow'][$k]['station'] = $stationArr[$temp_key]['name'];
            $result['allrow'][$k]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
            $temp_sex = $v['sex'];
            $result['allrow'][$k]['sex'] = $sex[$temp_sex];
            $result['allrow'][$k]['type_gj'] = 0;
            $result['allrow'][$k]['type_yw'] = 0;
            if($v['type_baoxian'] == 1){
                $result['allrow'][$k]['type_gj'] = 1;
            }
            if($v['type_baoxian'] == 2){
                $result['allrow'][$k]['type_yw'] = 1;
            }   
            $result['allrow'][$k]['type_bd'] = 0;
            $result['allrow'][$k]['type_sj'] = 0;
            if($v['type'] == 202){
                $result['allrow'][$k]['type_sj'] = 1;
            }
            if($v['type'] == 205){
                $result['allrow'][$k]['type_bd'] = 1;
            }
        }

        //清空输出缓存                    
        ob_clean();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("rockhippo")
                    ->setLastModifiedBy("jerry")
                    ->setTitle("平安保单用户数据")
                    ->setSubject("平安数据")
                    ->setDescription("")
                    ->setKeywords("excel")
                    ->setCategory("result file");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1','日 期')    
                        ->setCellValue('B1','注册地点')
                        ->setCellValue('C1','手机号')
                        ->setCellValue('D1','公交意外险')
                        ->setCellValue('E1','驾驶员意外险')
                        ->setCellValue('F1','姓名')
                        ->setCellValue('G1','性别')
                        ->setCellValue('H1','城市')
                        ->setCellValue('I1','年龄')
                        ->setCellValue('J1','页面停留时间(s)')
                        ->setCellValue('K1','手机注册')
                        ->setCellValue('L1','保单注册');  
        foreach($result['allrow'] as $k => $v){
            $num=$k+2;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$num, $v['create_time'])    
                        ->setCellValue('B'.$num, $v['station'])
                        ->setCellValue('C'.$num, $v['mobile'])
                        ->setCellValue('D'.$num, $v['type_gj'])
                        ->setCellValue('E'.$num, $v['type_yw'])
                        ->setCellValue('F'.$num, $v['name'])
                        ->setCellValue('G'.$num, $v['sex'])
                        ->setCellValue('H'.$num, $v['shi'])
                        ->setCellValue('I'.$num, $v['age'])
                        ->setCellValue('J'.$num, $v['stay_time'])
                        ->setCellValue('K'.$num, $v['type_sj'])
                        ->setCellValue('L'.$num, $v['type_bd']);
        }
        $objPHPExcel->getActiveSheet()->setTitle('User');
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="平安保单用户数据.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;  

    }

        /**
     *
     * @do 导出人寿用户数据
     *
     * @pingan public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
    */
    public function ajaxRensUserExportAction(){
        $start = reqstr("start","");
        $end = reqstr("end","");
        $page = reqnum('page',1);
        $pagesize = 20;
        $order = "";
        $where = $stationArr = array();
        if($start){$where["create_time_>="] = strtotime($start);}
        if($end){$where["create_time_<"] = (strtotime($end)+24*3600);}
        $where['type'] = 206;
        $where['pid'] = 0;
        $PSys_PinganModel = new PSys_PinganModel();
        $PSys_PinganRule = new PSys_PinganRule();
        $stationlist = $PSys_PinganModel->GetList(array(), $order, 0, 0, "id,stationname,appkey","rha_station");
        foreach ($stationlist['allrow'] as $k => $v) {
            $temp_key = $v['appkey'];
            $stationArr[$temp_key]['name'] = $v['stationname'];
        }
        $sex = $bx_type = array();
        $sex[1] = '男';
        $sex[2] = '女';
        $result = $PSys_PinganModel->GetList($where, 'create_time DESC', 0, 0, "*");
        foreach ($result['allrow'] as $k => $v) {
            $temp_key = $v['appkey'];
            $result['allrow'][$k]['station'] = $stationArr[$temp_key]['name'];
            $result['allrow'][$k]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
            $temp_sex = $v['sex'];
            $result['allrow'][$k]['sex'] = $sex[$temp_sex];
        }

        //清空输出缓存                    
        ob_clean();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("rockhippo")
                    ->setLastModifiedBy("jerry")
                    ->setTitle("人寿保单用户数据")
                    ->setSubject("人寿数据")
                    ->setDescription("")
                    ->setKeywords("excel")
                    ->setCategory("result file");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1','日 期')    
                        ->setCellValue('B1','注册地点')
                        ->setCellValue('C1','手机号')
                        ->setCellValue('D1','姓名')
                        ->setCellValue('E1','性别')
                        ->setCellValue('F1','省份')
                        ->setCellValue('G1','城市')
                        ->setCellValue('H1','年龄');  
        foreach($result['allrow'] as $k => $v){
            $num=$k+2;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$num, $v['create_time'])    
                        ->setCellValue('B'.$num, $v['station'])
                        ->setCellValue('C'.$num, $v['mobile'])
                        ->setCellValue('D'.$num, $v['name'])
                        ->setCellValue('E'.$num, $v['sex'])
                        ->setCellValue('F'.$num, $v['sheng'])
                        ->setCellValue('G'.$num, $v['shi'])
                        ->setCellValue('H'.$num, $v['age']);
        }
        $objPHPExcel->getActiveSheet()->setTitle('User');
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="人寿保单用户数据.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;  

    }

    /**
     *
     * @do 导出人寿用户数据
     *
     * @pingan public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
    */
    public function ajaxRensExportAction(){
        $start = reqstr("start","");
        $micstart = strtotime($start);
        $end = reqstr("end","");
        $micend = strtotime($end);
        $PSys_PinganRule = new PSys_PinganRule();
        $i = 0;
        for($x=$micstart; $x<=$micend; $x += 86400) {
            $result['allrow'][$i]['create_time'] = date('Y-m-d',$x);
            $sql_jn = "SELECT count(id) as total FROM rha_log_pingan WHERE type = 206 AND pid = 0 AND appkey = 'jn' AND cday = ".date('Ymd',$x);
            $jnRs = $PSys_PinganRule->rensQuery($sql_jn);
            $result['allrow'][$i]['jn'] = $jnRs[0]['total'];
            $sql_jnx = "SELECT count(id) as total FROM rha_log_pingan WHERE type = 206 AND pid = 0 AND appkey = 'jnx' AND cday = ".date('Ymd',$x);
            $jnxRs = $PSys_PinganRule->rensQuery($sql_jnx);
            $result['allrow'][$i]['jnx'] = $jnxRs[0]['total'];
            $sql_wf = "SELECT count(id) as total FROM rha_log_pingan WHERE type = 206 AND pid = 0 AND appkey = 'wf' AND cday = ".date('Ymd',$x);
            $wfRs = $PSys_PinganRule->rensQuery($sql_wf);
            $result['allrow'][$i]['wf'] = $wfRs[0]['total'];
            $sql_zb = "SELECT count(id) as total FROM rha_log_pingan WHERE type = 206 AND pid = 0 AND appkey = 'zb' AND cday = ".date('Ymd',$x);
            $zbRs = $PSys_PinganRule->rensQuery($sql_zb);
            $result['allrow'][$i]['zb'] = $zbRs[0]['total'];
            $i++;
        }
        //清空输出缓存                    
        ob_clean();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("rockhippo")
                    ->setLastModifiedBy("jerry")
                    ->setTitle("人寿认证保单用户数据")
                    ->setSubject("人寿数据")
                    ->setDescription("")
                    ->setKeywords("excel")
                    ->setCategory("result file");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1','日 期')    
                        ->setCellValue('B1','济南')
                        ->setCellValue('C1','济南西')
                        ->setCellValue('D1','潍坊')
                        ->setCellValue('E1','淄博'); 
        foreach($result['allrow'] as $k => $v){
            $num=$k+2;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$num, $v['create_time'])    
                        ->setCellValue('B'.$num, $v['jn'])
                        ->setCellValue('C'.$num, $v['jnx'])
                        ->setCellValue('D'.$num, $v['wf'])
                        ->setCellValue('E'.$num, $v['zb']);
        }
        
        $objPHPExcel->getActiveSheet()->setTitle('User');
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="人寿认证保单用户数据.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;  

    }

    /**
     *
     * @do 导出人寿用户数据
     *
     * @pingan public 
     * @author jerry
     * @copyright rockhippo
     * @param -
     * @return -
     *
    */
    public function ajaxRensHourExportAction(){
        $start = reqstr("start","");
        $micstart = strtotime($start);
        $end = reqstr("end","");
        $micend = strtotime($end);
        if($micstart != $micend){
            exit('请选择单日！');
        }
        $micend += 86400;
        $PSys_PinganRule = new PSys_PinganRule();
        $i = 0;
        for($x=$micstart; $x<=$micend; $x += 3600) {
            if($i == 24){ break;}
            $result['allrow'][$i]['create_time'] = date('Y-m-d H:i:s',$x).' ~ '.date('Y-m-d H:i:s',$x+3600);
            $sql_jn = "SELECT count(id) as total FROM rha_log_pingan WHERE type = 206 AND pid = 0 AND appkey = 'jn' AND cday = ".date('Ymd',$x)." AND create_time >= ".$x." AND create_time < ".($x+3600);
            $jnRs = $PSys_PinganRule->rensQuery($sql_jn);
            $result['allrow'][$i]['jn'] = $jnRs[0]['total'];
            $sql_jnx = "SELECT count(id) as total FROM rha_log_pingan WHERE type = 206 AND pid = 0 AND appkey = 'jnx' AND cday = ".date('Ymd',$x)." AND create_time >= ".$x." AND create_time < ".($x+3600);
            $jnxRs = $PSys_PinganRule->rensQuery($sql_jnx);
            $result['allrow'][$i]['jnx'] = $jnxRs[0]['total'];
            $sql_wf = "SELECT count(id) as total FROM rha_log_pingan WHERE type = 206 AND pid = 0 AND appkey = 'wf' AND cday = ".date('Ymd',$x)." AND create_time >= ".$x." AND create_time < ".($x+3600);
            $wfRs = $PSys_PinganRule->rensQuery($sql_wf);
            $result['allrow'][$i]['wf'] = $wfRs[0]['total'];
            $sql_zb = "SELECT count(id) as total FROM rha_log_pingan WHERE type = 206 AND pid = 0 AND appkey = 'zb' AND cday = ".date('Ymd',$x)." AND create_time >= ".$x." AND create_time < ".($x+3600);
            $zbRs = $PSys_PinganRule->rensQuery($sql_zb);
            $result['allrow'][$i]['zb'] = $zbRs[0]['total'];
            $i++;
        }
        //清空输出缓存                    
        ob_clean();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("rockhippo")
                    ->setLastModifiedBy("jerry")
                    ->setTitle("人寿认证保单时段用户数据")
                    ->setSubject("人寿数据")
                    ->setDescription("")
                    ->setKeywords("excel")
                    ->setCategory("result file");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1','日期时段')    
                        ->setCellValue('B1','济南')
                        ->setCellValue('C1','济南西')
                        ->setCellValue('D1','潍坊')
                        ->setCellValue('E1','淄博'); 
        foreach($result['allrow'] as $k => $v){
            $num=$k+2;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$num, $v['create_time'])    
                        ->setCellValue('B'.$num, $v['jn'])
                        ->setCellValue('C'.$num, $v['jnx'])
                        ->setCellValue('D'.$num, $v['wf'])
                        ->setCellValue('E'.$num, $v['zb']);
        }
        
        $objPHPExcel->getActiveSheet()->setTitle('User');
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="人寿认证保单'.$start.'时段用户数据.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;  

    }

}