<?php
/**
* Copyright(c) 2015
* 日    期:2015年4月7日                                                 
* 作　  者:never
* E-mail  :never@rockhippo.net
* 文 件 名:pagerecordController.php                                                
* 创建时间:下午15:58                                               
* 字符编码:UTF-8                                                   
* 脚本语言:PHP                                            
* 版本信息:                                        
* 修改日期:                               
* 最后版本:                            
* 修 改 者:                            
* 版本地址:$HeadURL: http://192.168.28.100:12000/svn/rocktrain/trunk/RockAdmin/protected/module/psys/controller/:pagerecordController.php  $                                            
* 摘    要: 广告管理                                                      
*/
class pagerecordController extends Psys_AbstractController {
        private   $Psys_PagerecordModel;
        public function __construct() {
		parent::__construct ();
             $this->Psys_PagerecordModel= new Psys_PagerecordModel();
             $stations= $this->Psys_PagerecordModel->site_appkey;
             $this->smarty->assign('stations',$stations);
         }
        //加载 一段时间的数据
	public function webCountAction()
	{
            // $Psys_StationModel = new Psys_StationModel();
            // $stations = $Psys_StationModel->station();//站点名
            //数据库站点数据不适合做下面的 以后校正后才 更改 从数据库/配置文件读取 
            //获取车站名称列表
          
             
               $page = reqnum("page",'1');
	       $pagesize = reqnum("pagesize",'10');
	       $stationid = reqstr('stationid',"qdn"); //站点名
              $data =$this->Psys_PagerecordModel->datelist($stationid,$page,$pagesize); //获取分页记录列表数据
           
               $allnum=$this->Psys_PagerecordModel-> get_allnum(); //获取总条数  
               $showpage=$this->Psys_PagerecordModel->openpage("/pagerecord/webCount",$pagesize, $allnum, $page,"&appkey={$stationid}", 3);//分页 
    	      
               $this->smarty->assign('stationid', $stationid);
               $this->smarty->assign('showpage', $showpage);
               $this->smarty->assign('totallist',$data);
               $this->forward = 'webCount';
        }
         //加载 一天的 数据 
	public function getOneDateAction()
	{
	       $stationid = reqstr('appkey',""); //站点名
               $data =$this->Psys_PagerecordModel->datelist($stationid); //获取分页记录列表数据
               $this->smarty->assign('totallist',$data);
               $this->smarty->assign('date',reqstr("date"));
               $this->forward = 'getOneDate';
        }
         /** 所有的 统计图 **/
        function allchartAction(){
             //广告1 UV
              $listaduv1= $this->Psys_PagerecordModel->getSiteTotal(1,2); //getSiteTotal($key,$pv_uv) $key 为 $Psys_PagerecordModel->where_data_config的 key；$pv_uv 为1表示 pv 为2表示uv
              $listaduv1= json_encode($listaduv1,true);
              
               //广告1 PV
              $listadpv1= $this->Psys_PagerecordModel->getSiteTotal(1,1); //1表示 pv
              $listadpv1= json_encode($listadpv1,true);
              
              //注册成功 
              $listregokuv= $this->Psys_PagerecordModel->getSiteTotal(3,2); //2表示 uv
              $listregokuv= json_encode($listregokuv,true);
              
              //下载 app
              $listdownappuv= $this->Psys_PagerecordModel->getSiteTotal(6,2); //2表示 uv
              $listdownappuv= json_encode($listdownappuv,true);
              
              //获取验证码uv
              $getcodeuv= $this->Psys_PagerecordModel->getSiteTotal("phonecodeclick",2); //2表示 uv
              $getcodeuv= json_encode($getcodeuv,true);
              
               //获取验证码pv
              $getcodepv= $this->Psys_PagerecordModel->getSiteTotal("phonecodeclick",1); //1表示 pv
              $getcodepv= json_encode($getcodepv,true);
              
              //下载流失率 
              $where_key=array("1","2","4",'5','6');// $where_key 为$Psys_PagerecordModel->where_data_config的 key组成的数组 
              $lossuv1= $this->Psys_PagerecordModel->getTotalPartTime($where_key,"2"); //2表示 uv   
              $lossuv1= $this->Psys_PagerecordModel->lossRate( $lossuv1); //转换为流失率格式 
              $lossuv1=json_encode($lossuv1,true);
              
              $losspv1= $this->Psys_PagerecordModel->getTotalPartTime($where_key,"1"); //1表示 pv   
              $losspv1= $this->Psys_PagerecordModel->lossRate( $losspv1); //转换为流失率格式 
              $losspv1=json_encode($losspv1,true);
              
             
              
              $this->smarty->assign('listaduv1',$listaduv1);
              $this->smarty->assign('listadpv1',$listadpv1);
              $this->smarty->assign('listregokuv',$listregokuv);
              $this->smarty->assign('listdownappuv',$listdownappuv);
              $this->smarty->assign('getcodeuv',$getcodeuv);
              $this->smarty->assign('getcodepv',$getcodepv);
              
              $this->smarty->assign('lossuv1',$lossuv1);
              $this->smarty->assign('losspv1',$losspv1);
              $this->forward = 'allchart';
        }
        /** 流程对比 **/     
        function compareAction(){
              $cdate=  reqstr("sdate","2015-04-30");
              $where_key=array("1","2","phonecodeclick","phonecodeok","3","4","5","6");// $where_key 为$Psys_PagerecordModel->where_data_config的 key组成的数组 
              // $where_key=array("1","2",'3');
              $list= $this->Psys_PagerecordModel->getTotalAppkeyBig($where_key, $cdate,"2"); //2表示 uv
               $sitecomparepv= $this->Psys_PagerecordModel->getTotalAppkeyBig($where_key, $cdate,"1"); //1表示 pv
               $sitecompareuv=  json_encode($list,true);
               $sitecomparepv=  json_encode($sitecomparepv,true);
               $this->smarty->assign('list',$list);
               $this->smarty->assign('sitecompareuv',$sitecompareuv);
               $this->smarty->assign('sitecomparepv',$sitecomparepv);
               $this->forward = 'compare';
        }
        /** 流程汇总 **/     
        function allsiteAction(){
           $where_key=array("1","2","3","4","5","6");// $where_key 为$Psys_PagerecordModel->where_data_config的 key组成的数组 
           $allsitelistuv=   $this->Psys_PagerecordModel->getAllSiteBig($where_key,'2');
           $allsiteuv= json_encode($allsitelistuv,true);
           $this->smarty->assign('allsitelistuv',$allsitelistuv);
            $this->smarty->assign('allsiteuv',$allsiteuv);
           $this->forward = 'allsite';
        }
        /**  总的注册 **/     
        function  regdetailAction(){
                $page = reqnum("page",'1');
	       $pagesize = reqnum("pagesize",'10');
	       $stationid = reqstr('stationid',"qdn"); //站点名
               $data =$this->Psys_PagerecordModel->datelist($stationid,$page,$pagesize); //获取分页记录列表数据
               $allnum=$this->Psys_PagerecordModel-> get_allnum(); //获取总条数  
               $showpage=$this->Psys_PagerecordModel->openpage("/pagerecord/webCount",$pagesize, $allnum, $page,"&appkey={$stationid}", 3);//分页 
    	    
               $this->smarty->assign('stationid', $stationid);
               $this->smarty->assign('showpage', $showpage);
               $this->smarty->assign('totallist',$data);
               $this->forward = 'regdetail';
        }
        
         /**  导航PV **/     
        function  navpvAction(){
          $where_key=array("1","2","3");// $where_key 为$Psys_PagerecordModel->where_data_config的 key组成的数组 
          $navpvlist= $this->Psys_PagerecordModel->getTotalPartTime($where_key,"1");//1表示 pv
          $navpv= json_encode( $navpvlist,true);
          $this->smarty->assign('navpvlist',$navpvlist);//模板格式数据 
          $this->smarty->assign('navpv',$navpv);//图表格式数据 
          $this->forward = 'navpv';
        }
       
         /**   BannerPV **/     
        function  bannerpvAction(){
          $where_key=array("1","4","6");// $where_key 为$Psys_PagerecordModel->where_data_config的 key组成的数组 
          $bannerpvlist= $this->Psys_PagerecordModel->getTotalPartTime($where_key,"1");//1表示 pv
          $bannerpv= json_encode( $bannerpvlist,true);
          $this->smarty->assign('bannerpvlist',$bannerpvlist);//模板格式数据 
          $this->smarty->assign('bannerpv',$bannerpv);//图表格式数据 
          $this->forward = 'bannerpv';
            
        }
        
        /**  广告PV **/     
        function  adpvAction(){
          $where_key=array("1","2","5");// $where_key 为$Psys_PagerecordModel->where_data_config的 key组成的数组 
          $adpvlist= $this->Psys_PagerecordModel->getTotalPartTime($where_key,"1"); //1表示 pv
          $adpv= json_encode( $adpvlist,true);
          $this->smarty->assign('adpvlist',$adpvlist);//模板格式数据 
          $this->smarty->assign('adpv',$adpv);//图表格式数据 
          $this->forward = 'adpv';
        }
         /** 日志统计模块的 ：流程数据分析->首页游戏广告流程
          * log 第一层 （只有基础的 html搜索框）  **/
        function adDetailLogAction(){

                $stationsLog=$this->Psys_PagerecordModel->getAllAppkey();
                $this->smarty->assign('stationsLog',$stationsLog);
                $this->forward = 'adDetailLog';
         }
           
        /** 首页游戏广告流程：log 第二层 （第一层里面的 ajax）**/   
         function  adDetailLogWegetAction(){
               //查询条件 begin
               $stationid= reqstr("station","");
               $get_where["stationid"]= $stationid;
               $time_begin = reqstr("sdate") ? reqstr("sdate") :false ;//为 false表示不加入搜索条件
               $time_end = reqstr("edate") ? reqstr("edate") : false;
               if($time_begin==false){
                   $day_num= reqstr("date",'0');
                   $time_begin=date("Y_m_d", strtotime("{$day_num} days ago"));
                   $time_end =date("Y_m_d", strtotime("1 days ago")); 
                 }else{
                     $time_begin=str_replace("-","_",$time_begin); //因为和数据库的日期格式不统一 
                     $time_end= str_replace("-","_",$time_end);
                 }
                $get_where[]=array(  //如果 以数组形似 为下面格式 如果是以一维数组  默认为等于转换mysql where后 符号
                   array("date",">=",$time_begin),
                   array("date","<=",$time_end),
                  );   //       添加限制条件 如 格式 array("limit"," ","100"), //值为 false 表示 屏蔽此搜索条件
                //查询条件 end 
                
                
                $where_key=array("log1","log2","log3");//$Psys_PagerecordModel->where_log_config的 key组成的数组
                $alladuv= $this->Psys_PagerecordModel->adDetailLog( $where_key,$get_where);
                $alladuvlist=array_reverse($alladuv);
                
                //倒叙 排列 begin  ---列表为倒叙  （如果列表 需要 正序 屏蔽这块代码）   
//                $series=$alladuvlist['series'];
//                $series=array_reverse($series);//不能直接用 $alladuvlist['series']= array_reverse($alladuvlist['series'])
//                $alladuvlist['series']= $series;
//                
//                $xaxis=$alladuvlist['xaxis']['data'];
//                $xaxis=array_reverse( $xaxis);
//                $alladuvlist['xaxis']['data']= $xaxis;
               //倒叙 排列 end
    
                $this->smarty->assign('alladuvlist', $alladuvlist);//模板格式数据 
                $this->smarty->assign('stationid', $stationid);//图表格式数据 
                $alladuv= json_encode($alladuv,true); 
                $this->smarty->assign('alladuv', $alladuv);//图表格式数据 
                $this->forward = 'weget/adDetailLog';
             }
        /**首页游戏广告流程： log 第三层（第二层里面的 ajax）   **/
         function adLogDayAction(){
                $get_where['date'] = reqstr("edate") ? reqstr("edate") :'' ;
                $get_where["stationid"]=reqstr("stationid")?reqstr("stationid") :false;
                $where_key=array("log1","log2","log3");//$Psys_PagerecordModel->where_log_config的 key组成的数组
                 //查询条件 end
                $adLogDayuv= $this->Psys_PagerecordModel->adLogDay($where_key,$get_where);
                header('Content-Type:application/json; charset=utf-8');
                $adLogDayuv= json_encode($adLogDayuv,true);
                echo $adLogDayuv;
         }
        
          /** 测试代码**/
         function testAction(){
                 $time_begin = reqstr("sdate") ? reqstr("sdate") :false;
                 $time_begin = reqstr("edate") ? reqstr("edate") :false;
                 $get_where["stationid"]=reqstr("stationid") ? reqstr("stationid") : '3';
                 $get_where["date"]= $time_begin;
                 $where_key=array("log1","log2","log3");//$Psys_PagerecordModel->where_log_config的 key组成的数组
                 //查询条件 end
                $alladuv= $this->Psys_PagerecordModel->adDetailLog( $where_key, $get_where);
                header('Content-Type:application/json; charset=utf-8');
                $alladuv= json_encode($alladuv,true);
                echo $alladuv;
 
        }   
        function test2Action(){
               $get_where['date'] = reqstr("edate") ? reqstr("edate") :'' ;
                $get_where["stationid"]=reqstr("stationid")?reqstr("stationid") :false;
                $where_key=array("log1","log2","log3");//$Psys_PagerecordModel->where_log_config的 key组成的数组
                 //查询条件 end
                $adLogDayuv= $this->Psys_PagerecordModel->adLogDay($where_key,$get_where);
             //  print_r($adLogDayuv);
              //exit();
                 $adLogDayuv= json_encode($adLogDayuv,true); 
                $this->smarty->assign('navpv',$adLogDayuv);//图表格式数据 
                $this->forward = 'test2';
        }
        
 }