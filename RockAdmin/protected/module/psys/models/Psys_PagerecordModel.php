<?php
class Psys_PagerecordModel extends Psys_AbstractModel
    {

    public function __construct()
        {
        parent::__construct();
        }
       //第二套 统计的 
 private $where_data_config = array(
        "1" => array("pageurl" => "/index/index", "eventurl" => "/index/index", "pagestate" => "1", "name" => "广告1打开"), //index/index 广告1打开
        "2" => array("pageurl" => "/index/register", "eventurl" => "/index/register", "pagestate" => "1", "name" => "注册页"), //index/register   注册页 UV
        "3" => array("pageurl" => "/index/register", "eventurl" => "/member/register", "pagestate" => "2", "name" => "注册页成功"), //i注册页成功 
        "4" => array("pageurl" => "/index/welcome", "eventurl" => "index/welcome", "pagestate" => "1", "name" => "广告页2"), //注册页成功跳转到的 广告页2/uv
        "5" => array("pageurl" => "index/sindex", "eventurl" => "index/sindex", "pagestate" => "1", "name" => "sindex "), //sindex/uv 
        "6" => array("eventurl" => "downApp", "pagestate" => "1", "name" => "下载APP统计"), // 下载APP统计 
        "phonecodeclick" => array("pageurl" => "/index/register", "eventurl" => "/member/againgetphonecode", "pagestate" => "1", "name" => "获取验证码"), //获取验证码 uv
        "phonecodeok" => array("pageurl" => "/index/register", "eventurl" => "/member/againgetphonecode", "pagestate" => "2", "name" => "发送验证码"), //sindex/uv  发送验证码 成功  
        "phonecodeerror" => array("pageurl" => "/index/register", "eventurl" => "/member/againgetphonecode", "pagestate" => "3", "name" => "发送验证码失败"), //sindex/uv  发送验证码 失败   
        "1adclick61" => array("pageurl" => "/index/sindex", "eventurl" => "adclick", "adid" => "61", "name" => "开心消消sindex_click"), //      
        "1adclick49" => array("pageurl" => "/index/sindex", "eventurl" => "adclick", "adid" => "49", "name" => "消灭星星2015sindex_click"), //消灭星星2015    
        "1adclick40" => array("pageurl" => "/index/sindex", "eventurl" => "adclick", "adid" => "40", "name" => "去吧皮卡丘sindex_click"), // 去吧皮卡丘
        "1adclick25" => array("pageurl" => "/index/sindex", "eventurl" => "adclick", "adid" => "25", "name" => "三重镇sindex_click"), //三重镇
        "1adclick21" => array("pageurl" => "/index/sindex", "eventurl" => "adclick", "adid" => "21", "name" => "索尼克冲刺sindex_click"), //索尼克冲刺
        "1adclick61" => array("pageurl" => "/index/sindex", "eventurl" => "adclick", "adid" => "61", "name" => "开心消消sindex_click"), //开心消消
        "1adclick54" => array("pageurl" => "/index/sindex", "eventurl" => "adclick", "adid" => "54", "name" => "秦时明月2sindex_click"), //秦时明月2
        "1adclick40" => array("pageurl" => "/index/sindex", "eventurl" => "adclick", "adid" => "40", "name" => "去吧皮卡丘sindex_click"), //去吧皮卡丘
        "2click54" => array("pageurl" => "/game/detail", "other" => "click", "eventurl" => "61", "name" => "开心消消game/detail_click"), //开心消消      
        "2click49" => array("pageurl" => "/game/detail", "other" => "click", "eventurl" => "49", "name" => "消灭星星2015  game/detail_click"), //消灭星星2015    
        "2click40" => array("pageurl" => "/game/detail", "other" => "click", "eventurl" => "40", "name" => "去吧皮卡丘game/detail_click"), // 去吧皮卡丘
        "2click25" => array("pageurl" => "/game/detail", "other" => "click", "eventurl" => "25", "name" => "三重镇game/detail_click"), //三重镇
        "2click21" => array("pageurl" => "/game/detail", "other" => "click", "eventurl" => "21", "name" => "索尼克冲刺game/detail_click"), //索尼克冲刺
        "2click61" => array("pageurl" => "/game/detail", "other" => "click", "eventurl" => "61", "name" => "开心消消game/detail_click"), //开心消消
        "2click54" => array("pageurl" => "/game/detail", "other" => "click", "eventurl" => "54", "name" => "秦时明月2game/detail_click"), //秦时明月2     
        "1down61" => array("pageurl" => "/game/detail", "other" => "down", "eventurl" => "61", "name" => "开心消消game/detail_down"), //开心消消      
        "1down49" => array("pageurl" => "/game/detail", "other" => "down", "eventurl" => "49", "name" => "消灭星星2015game/detail_down"), //消灭星星2015    
        "1down40" => array("pageurl" => "/game/detail", "other" => "down", "eventurl" => "40", "name" => "去吧皮卡丘game/detail_down"), // 去吧皮卡丘
        "1down25" => array("pageurl" => "/game/detail", "other" => "down", "eventurl" => "25", "name" => "三重镇game/detail_down"), //三重镇
        "1down21" => array("pageurl" => "/game/detail", "other" => "down", "eventurl" => "21", "name" => "索尼克冲刺game/detail_down"), //索尼克冲刺
        "1down61" => array("pageurl" => "/game/detail", "other" => "down", "eventurl" => "61", "name" => "开心消消game/detail_down"), //开心消消
        "1down54" => array("pageurl" => "/game/detail", "other" => "down", "eventurl" => "54", "name" => "秦时明月2game/detail_down"), //秦时明月2 
    );
 //日志板块 需要查询的 数组 配置文件 
     private    $where_log_config=array(
              "log1"=>array("model"=>"sindex","action"=>"game_detail","name"=>"首页点击数"),
              "log2"=>array("model"=>"sindex","action"=>"game_click","name"=>"进入详情页数"),
              
              "log3"=>array("model"=>"sindex","action"=>"game_down","name"=>"下载数"),
           );
     //手机配置文件 
    private $mobile_os_config = array("1" => "Android", "2" => "iPhone", "3" => "else", "88" => "total");
      //站点 配置文件 只有个别 方法 在用  有时间 再修正 完善数据库 配置 用数据库的 
    public $site_appkey = array(
        "qdn" => "青岛南", //青岛南
        "qdb" => "青岛北", //青岛北
        "jn" => "济南", //济南
        "jnx" => "济南西", //济南西
        "ta" => "泰安", //泰安
        "qf" => "曲阜", //曲阜
        "tz" => "滕州", //滕州
        "zz" => "枣庄", //枣庄
        "wf" => "潍坊", //潍坊
        "zb" => "淄博", //淄博
    );

    function webCount()
        {
        $Psys_PagerecordRule = new Psys_PagerecordRule();
        $max = $Psys_PagerecordRule->get_max_date();
        $allnum = $this->get_allnum();
        }

    /** 获取 日期列表数据 
     * 需要传入 $stationid 为字符串 如 qdn
     * 
     * * */
    function datelist($stationid, $page = '1', $pagesize = '0')
        {
         $appkey_where=$stationid?" and appkey ='{$stationid}'":"";  
        $Psys_PagerecordRule = new Psys_PagerecordRule();
        $temp_max_date = $Psys_PagerecordRule->get_max_date(); //获取最大天数 date("Y-m-d",(strtotime($temp_max_date)))
        $max_date = reqstr("date") ? date("Y-m-d", (strtotime(reqstr("date")) + 86400)) : date("Y-m-d", (strtotime($temp_max_date) - ($page - 1) * $pagesize * 86400));

        $min_date = reqstr("date") ? reqstr("date") : date("Y-m-d", (strtotime($temp_max_date) - $page * $pagesize * 86400));
        
        $sql = "select * from rha_pagerecord where date>='{$min_date}' and date <='{$max_date}' {$appkey_where} order by date desc;";

        $query_data = $Psys_PagerecordRule->list_query($sql); //数据库 只执行一次简单查询就获取所有数据 
        $td_column = array();
        foreach ($this->where_data_config as $where_key => $where_val) {
            $pageurl = isset($where_val['pageurl']) ? $where_val['pageurl'] : '';
            $eventurl = isset($where_val['eventurl']) ? $where_val['eventurl'] : '';
            $pagestate = isset($where_val['pagestate']) ? $where_val['pagestate'] : '';
            $other = isset($where_val['other']) ? $where_val['other'] : '';
            $adid = isset($where_val['adid']) ? $where_val['adid'] : ''; //广告 id
            $name = isset($where_val['name']) ? $where_val['name'] : ''; //名字 

            $key = $pageurl . "_" . $eventurl . '_' . $pagestate . '_' . $other . '_' . $adid;
            $td_column[$key]["key"] = $where_key;
            $td_column[$key]["name"] = $name;
        }
        $data = array();
        foreach ($query_data as $value) {
            $key = $value["pageurl"] . "_" . $value["eventurl"] . '_' . $value["pagestate"] . '_' . $value["other"] . '_' . $value["adid"];
            $first_key = $value['mos'] == '88' ? 'all' : "detail";
            $data[$first_key][$value["date"]][$td_column[$key]['key']]['data'][$value['mos']] = array("num" => $value["num"]);
            $data[$first_key][$value["date"]][$td_column[$key]['key']]['name'] = $td_column[$key]['name'];
            $data[$first_key][$value["date"]][$td_column[$key]['key']]['appkey'] = $value['appkey'];
        }
        return $data;
        }

        
     /**  传入类别 PV/uv 某个日期时间段内 某个站点  某个类别 下 的总设备和细分设备访问数据 ---生成图表数据  * */
    function getSiteTotal($key_num="1",$pv_uv="2")
        {
        $pv_uv_name=$pv_uv=="2"?'uv':"pv";
        $tmp_data = $this->querySiteTotal($this->where_data_config[$key_num],$pv_uv);
        $data = array(); //下面为组合成 图表格式数据
        foreach ($tmp_data as $v) {
            $mosname = $this->mobile_os_config[$v["mos"]];
            if (!in_array($mosname, $data["legend"]['data'])) {
                $data["legend"]['data'][] = $mosname;  //legend 数据生成 图表  顶上标题 
            }
            if (!in_array($v['date'], $data["xaxis"]['data'])) {
                $data["xaxis"]['data'][] = $v['date'];  // xaxis 数据 生成   横坐标标题
            }
            $data["title"]['text']=$this->where_data_config[$key_num]['name'].$pv_uv_name;//设置主标题 
          
            $data["title"]['subtext']=$this->site_appkey[$v['appkey']];//设置附标题 
            
            $series_tmp[$mosname]['smooth']=true; //series 为曲线数据 
            $series_tmp[$mosname]['name'] = $mosname;
            $series_tmp[$mosname]['type']="line";
            $series_tmp[$mosname]['data'][] = $v['num'];
          }
          //处理series数据  (为了删除$series_temp数组KEY 再传给$data["series"]  )
          foreach($series_tmp as $v){  //删除 不必要的 key（因为 图表格式不支持 )
               $data["series"][]=$v;
          }
       return $data;
        }
        
    /* 传入  日期 pv或者 uv 获取 所有站点的 pv 或者 uv数据(不包含细分数据：如 Android iPhone els设备访问数据)  --生成图表格式 */
    function getTotalAppkeyBig($where_key,$date,$pv_uv="2"){
         $pv_uv_name=$pv_uv=="2"?'uv':"pv";
          foreach($where_key as $v){ //$v 值为 $this->where_data_config的 key
             $temp_data[$v]= $this->queryBig($v,$date,$pv_uv);
          }
         $data=array();
         $series_tmp=array();
         $legend_tmp=array();  //下面为组合成 图表格式数据
         $data["title"]['text']="统计对比".$pv_uv_name;//设置主标题 
         foreach($temp_data as $k=>$v){
               $data["xaxis"]['data'][]=$this->where_data_config[$k]["name"];
               foreach($v as $v2){ //series 为曲线数据 
                  $legend_tmp[$v2["appkey"]]=$this->site_appkey[$v2["appkey"]]; 
                  $series_tmp[$v2["appkey"]]['smooth']=true;
                  $series_tmp[$v2["appkey"]]['name'] =$this->site_appkey[$v2["appkey"]];
                  $series_tmp[$v2["appkey"]]['type']="line";
                  $series_tmp[$v2["appkey"]]['data'][] =$v2['num'];
               }
        }
          foreach($series_tmp as $v){ //删除 不必要的 key（因为 图表格式不支持 )
                   $data["series"][]=$v; //series 为曲线数据 
          }
          foreach($legend_tmp as $v2){ //删除 不必要的 key
                     $data["legend"]['data'][]=$v2;
          }
          return $data;
     }
     
    /* 传入  日期 pv或者 uv 获取 所有站点的 pv 或者 uv数据(不包含细分数据：如 Android iPhone els设备访问数据) 排列以时间   --生成图表格式 */
    function getTotalPartTime($where_key,$pv_uv="2"){ //$where_key 为 $this->where_data_config的 key值 组成的数组 1为pv  2为uv 
         $pv_uv_name=$pv_uv=="2"?'uv':"pv";
         $temp_data=array();
         foreach($where_key as $v){ //$v 值为 $this->where_data_config的 key
             $temp_data[$v]= $this->querySiteBig($v,$pv_uv);
          }
         $series_tmp=array();
         $legend_tmp=array();  
         $xaxis_tmp=array(); //下面为组合成 图表格式数据
         $data["title"]['text']=$pv_uv_name;//设置图表主标题 
         foreach($temp_data as $k=>$v){    //$k 为 $this->where_data_config的 key值
               $legend_tmp[$k]=$this->where_data_config[$k]['name']; 
               foreach($v as $v2){
                  $xaxis_tmp[$v2["date"]]=$v2["date"];    //xaxis 为横坐标 
                  $series_tmp[$k]['smooth']=true; //$k 为 $this->where_data_config的 key值
                  $series_tmp[$k]['name'] =$this->where_data_config[$k]['name'];
                  $data["title"]['subtext']=$v2['appkey'];//设置附标题 
                  $series_tmp[$k]['type']="line";
                  $series_tmp[$k]['data'][] =$v2['num'];
               }
        } //删除不必要的 数组 key （因为 图表格式不支持 )
       foreach($series_tmp as $v){
                   $data["series"][]=$v;//series 为曲线数据 
          }
        foreach($legend_tmp as $v2){
                     $data["legend"]['data'][]=$v2;
          }
        foreach ($xaxis_tmp as $v3){
                     $data["xaxis"]['data'][]=$v3;
          }
          return $data;
     }

    /**  获取表的 最大日期 和最小日期 (用于分页)-- 公共方法  **/
    function get_allnum()
        {
        $Psys_PagerecordRule = new Psys_PagerecordRule();
        $max_date = $Psys_PagerecordRule->get_max_date(); //获取最大天数
        $min_date = $Psys_PagerecordRule->get_min_date(); //获取最小天数
        $date1 = strtotime($max_date);
        $date2 = strtotime($min_date);
        $allnum = ceil(abs($date1 - $date2) / 86400); //计算出总天数 
        return $allnum;
        }
        
      function getAllSiteBig($where_key,$pv_uv="2"){
      $pv_uv_name=$pv_uv=="2"?'uv':"pv";
      $tmp_data=array();
      foreach($where_key as $v){ //$v 值为 $this->where_data_config的 key
             $tmp_data[$v]= $this->queryAllSiteBig($v,$pv_uv);
       }
      
       $data=array();
       $series_tmp=array(); // 曲线数据
       $xaxis_tmp=array();//横坐标
       //下面为组合成 图表格式数据
      foreach($tmp_data as $k=>$v){
           
           $data["legend"]['data'][]=$this->where_data_config[$k]['name'];// 曲线标题
          foreach($v as $v2){ 
                  $xaxis_tmp[$v2['date']]=$v2['date'];
                   $series_tmp[$k][$v2['date']]['num']+=$v2['num'];
                }
             }
             //xaxis 为曲线 横坐标
           foreach( $xaxis_tmp as $v){
                $data['xaxis']['data'][]=$v;
              
            }
            //series 为曲线数据 
           foreach($series_tmp as $k=>$v){
                  $tmp=array();
                  $tmp[$k]['smooth']=true;
                  $tmp[$k]['name'] =$this->where_data_config[$k]['name'];
                  $tmp[$k]['type']="line";
                 foreach($v as $k2=>$v2){
                     $tmp[$k]['data'][]=$v2['num'];
                   }
                  $data["series"][]=$tmp[$k];
             }
           return  $data;
    }    
        
 /** 查询 所有站点 所有设备 总数据**/
   function queryAllSiteBig($where_key,$pvuv="2"){
        $where_arr= $this->where_data_config[$where_key];
        $Psys_PagerecordRule = new Psys_PagerecordRule();
        $time_begin = reqstr("sdate") ? reqstr("sdate") : date("Y-m-d", (time() - 10 * 86400));
        $time_end = reqstr("edate") ? reqstr("edate") : date("Y-m-d", (time()));
       $where = "date >='{$time_begin}' and  date <='{$time_end}' and pvuv={$pvuv} and mos=88"; //固定的条件 
        while (list($key, $val) = each($where_arr)) {  //动态传入的条件
            $key != 'name' && $where .=" and {$key}= '{$val}' ";
        }
        $sql = "SELECT num,appkey,date FROM  rha_pagerecord WHERE  {$where}";  //最终 sql 查询 语句 
        $query_data = $Psys_PagerecordRule->list_query($sql);
        return $query_data;
   }     
   
   /** 查询 某个日期时间段内 某个站点  某个类别 下 的总设备和细分设备访问数据-- 公共方法  **/
    function querySiteTotal($where_arr,$pvuv="2")//pv_uv="1" 为 uv 2为
        {
        $Psys_PagerecordRule = new Psys_PagerecordRule();
        $time_begin = reqstr("sdate") ? reqstr("sdate") : date("Y-m-d", (time() -  7* 86400));
        $time_end = reqstr("edate") ? reqstr("edate") : date("Y-m-d", (time()));
        $appkey = reqstr("appkey") ? reqstr("appkey") : "wf";
        $where = "appkey='{$appkey}' and date >='{$time_begin}' and  date <='{$time_end}' and pvuv={$pvuv}"; //固定的条件 
        while (list($key, $val) = each($where_arr)) {  //动态传入的条件
            $key != 'name' && $where .=" and {$key}= '{$val}' ";
        }
        $sql = "SELECT * FROM  rha_pagerecord WHERE  {$where} order by date asc";  //最终 sql 查询 语句 
        $query_data = $Psys_PagerecordRule->list_query($sql);
        return $query_data;
        }   
        
        
    /**  查询 某个日期时间段内 某个站点  某个类别 下 的总设备访问数据(不含细分)-- 公共方法 * */
    function querySiteBig($where_key,$pvuv="2")//pv_uv="1" 为 uv 2为
        {
        $where_arr= $this->where_data_config[$where_key];
        $Psys_PagerecordRule = new Psys_PagerecordRule();
        $time_begin = reqstr("sdate") ? reqstr("sdate") : date("Y-m-d", (time() - 7 * 86400));
        $time_end = reqstr("edate") ? reqstr("edate") : date("Y-m-d", (time()));
        $appkey = reqstr("appkey") ? reqstr("appkey") : "wf";
        $where = "appkey='{$appkey}' and date >='{$time_begin}' and  date <='{$time_end}' and pvuv={$pvuv} and mos=88"; //固定的条件 
        while (list($key, $val) = each($where_arr)) {  //动态传入的条件
            $key != 'name' && $where .=" and {$key}= '{$val}' ";
        }
        $sql = "SELECT * FROM  rha_pagerecord WHERE  {$where}";  //最终 sql 查询 语句 
        $query_data = $Psys_PagerecordRule->list_query($sql);
        return $query_data;
        } 
        
     /** 查询 所有站点 站点的 pv 或者 uv数据 （不含细分数据）-- 公共方法     **/     
    function queryBig($num,$date='',$pv_uv="2"){ //pv_uv="1" 为 uv 2为
        $Psys_PagerecordRule = new Psys_PagerecordRule();
        $date= empty($date)? reqstr('date',''):$date;
        empty($date)&& $date=date("Y-m-d", (time() - 2*86400));
        $where_arr=$this->where_data_config[$num];
        while (list($key, $val) = each($where_arr)) {  //动态传入的条件
                $key != 'name' && $where .=" and {$key}= '{$val}' ";
            }
        $sql = "select * from rha_pagerecord where date='{$date}'  {$where} and mos=88 and pvuv={$pv_uv} ";
        $query_data = $Psys_PagerecordRule->list_query($sql);
        return $query_data;
    }
    
      /** 转换为流失率 * */
      function lossRate($data){
          $data["title"]['text'].="流失率";
          foreach($data['series'] as $k=>$v){
              $series_data=array();
              $len=count($v['data']);
              for($i=0;$i<$len;$i++){
                   $nextkey=($i+1)>=$len?$len:($i+1);
                   $series_data[]=ceil((   $v['data'][$nextkey]-$v['data'][$i]     )/$v['data'][$i]*100);
                }
               $data['series'][$k]['data']=$series_data;
          }
          return $data;
      }
      
      function adDetailLog($where_key,$get_where){
         $temp_data=array();
         foreach ($where_key as $v){
             $temp_data[$v]= $this->queryCount($v,$get_where);
          }
         //处理成 统计图 格式 数据 begin
         $series_tmp=array();
         $legend_tmp=array();  
         $xaxis_tmp=array(); 
           
         $title=  $this-> findAppkeyStr($get_where["stationid"],'idtotitle');//设置图表主标题 
         $data["title"]['text']=$title?$title:"全部站点";//设置图表主标题 
         $data["title"]['subtext']="所有游戏";//设置附标题 
        
         foreach($temp_data as $k=>$v){    //$k 为 $this->where_data_config的 key值
               $legend_tmp[$k]=$this->where_log_config[$k]['name']; 
               foreach($v as $v2){
                  $xaxis_tmp[$v2["date"]]=$v2["date"];    //xaxis 为横坐标 
                  $series_tmp[$k][$v2['date']]['dtime']+=$v2['dtime'];
                }
                 if(!$v){
                   $first=current($temp_data);
                    foreach($first as $k2=>$v2){
                    $xaxis_tmp[$v2["date"]]=$v2["date"];    //xaxis 为横坐标 
                    $series_tmp[$k][$v2['date']]['dtime']=0; //series 为曲线数据 
                   }
               }
        } 
      
      //删除不必要的 数组 key （因为 图表格式不支持 )
       foreach($legend_tmp as $v2){
                     $data["legend"]['data'][]=$v2;
          }
        //xaxis 为曲线 横坐标
        foreach ($xaxis_tmp as $v){
                     $data["xaxis"]['data'][]=$v;
                     //补全 数据不全 ，为空设置为0 -建议在生成数据的时候 没有就设置为0 就可以去掉该代码
                      foreach($series_tmp as $k2=>$v2){
                          if(!array_key_exists($v,$v2) ){
                            
                          $series_tmp[$k2][$v]['dtime']=0;
                          }
                      }
          }
       //series 为曲线数据 
        foreach($series_tmp as $k=>$v){
          
                  $tmp=array();
                  $tmp[$k]['smooth']=true;
                  $tmp[$k]['name'] =$this->where_log_config[$k]['name'];
                  $tmp[$k]['type']="line";
                  $tmp[$k]['itemStyle']=array("normal"=>array("label"=>array("show"=>true,"position"=>"top"))); //打开 线条上数字显示 
                  $tmp[$k]['large']=true;
                 //   $tmp[$k]['ribbonType']=true;
                  
                 foreach($xaxis_tmp as $k2=>$v2){
                     $tmp[$k]['data'][]=$series_tmp[$k][$v2]['dtime'];
                   }
                  $data["series"][]=$tmp[$k];
             }
           //处理成 统计图 格式数据 end
          return $data;
      }
      
      //详情
       function adLogDay($where_key,$get_where){
           $temp_data=array();
         foreach ($where_key as $v){
             $temp_data[$v]= $this->adLogDaycom($v,$get_where);
          }
        
         $series_tmp=array();
         $legend_tmp=array();  
         $xaxis_tmp=array(); //下面为组合成 图表格式数据
         
         if( $get_where["stationid"]){
               $data["title"]['text']=$this-> findAppkeyStr($get_where["stationid"],'idtotitle');
               $data["title"]['subtext']= $get_where['date']."详细";//设置附标题
         }else{
                $data["title"]['text']="所有站";//设置图表主标题 
                $data["title"]['subtext']=$get_where['date']."总数";//设置附标题
         }
        
        foreach($temp_data as $k=>$v){   
              //$k 为 $this->where_data_config的 key值
               $xaxis_tmp [$k]=$this->where_log_config[$k]['name']; //xaxis 为横坐标 
               foreach($v as $v2){
                 $appkeyname=$this->findAdStr($v2['detail'],'idtotitle');
                 $legend_tmp[$v2['detail']]=$v2['detail'];     
                 $series_tmp[$v2['detail']][$k]['dtime']+=$v2['dtime'];
               }
               if($v==0){
                 
                     $series_tmp[$v2['detail']][$k]['dtime']=0;  //series 为曲线数据
               }
           } //以下 删除不必要的 数组 key （因为 图表格式不支持 )
       
         foreach($legend_tmp as $v){
                     $data["legend"]['data'][]=$this->findAdStr($v,'idtotitle');
          }
            //xaxis 为曲线 横坐标
        foreach ($xaxis_tmp as $v){
                     $data["xaxis"]['data'][]=$v;
                     //数组不全 补全
                      foreach($series_tmp as $k2=>$v2){
                          if(!array_key_exists($v,$v2) ){
                            $series_tmp[$k2][$v]['dtime']=0;
                          }
                      }
                       
          }
          //series 为曲线数据   
        foreach($series_tmp as $k=>$v){
                  $tmp=array();
                 foreach($v as $k2=>$v2){
                      $tmp['smooth']=true;
                      $tmp['type']="line";
                      $tmp['itemStyle']=array("normal"=>array("label"=>array("show"=>true,"position"=>"top"))); //打开 线条上数字显示 
                      $tmp['name'] =$this->findAdStr($k,'idtotitle');
                      $tmp['data'][]=$v2['dtime'];
                   }
                  $data["series"][]=$tmp;
             }
       return $data;
      }
      //
      function adLogDaycom($where_key,$get_where){
        $where=  $this->where_log_config[$where_key];
        unset($where['name']);
        $where=array_merge($where,$get_where);
        $sql = "SELECT * FROM  rha_count WHERE ".$this->whereToSql($where);
        $Psys_PagerecordRule = new Psys_PagerecordRule();
        $query_data = $Psys_PagerecordRule->list_query($sql);
        return $query_data; 
       }
      
      
      //日志统计板块的 查询 rha_coynt 数据
      function queryCount($where_key,$get_where){
        $where=  $this->where_log_config[$where_key];
        unset($where['name']);
        $where=array_merge($where,$get_where);
        $sql = "SELECT * FROM  rha_count WHERE ".$this->whereToSql($where);
        $Psys_PagerecordRule = new Psys_PagerecordRule();
        $query_data = $Psys_PagerecordRule->list_query($sql);
        return $query_data;
     }
     
     
    /** $where            ------------公共函数
     * $where["username"]="xiaowang";
       $timearr=array(  //如果 以数组形似 为下面格式 如果是以一维数组  默认为等于转换mysql where后 符号
            array("date",">=",$time_begin),
            array("date","<=",$time_end),
           );
       $where=array_merge($timearr, $where);
     */  
   function whereToSql($where){
         $sql_where="";
        //数组转换为条件语句
           foreach($where as $k=> $v){
             $and=empty($sql_where)?" ":" and ";
             if(is_array($v)){ 
                foreach($v as $v2){
                    if(trim($v2[1])==''){
                          $sql_where.= ($v2[2]!=false)? " ".$v2[0].' '.$v2[1]." {$v2[2]}":'';
                        }else{
                           $sql_where.= ($v2[2]!=false)? $and.$v2[0].' '.$v2[1]." '{$v2[2]}'":'';   
                        }
                }
               }else{
                   $sql_where.= $v!=false? $and.$k."='{$v}'":""; 
              }
          }
          return $sql_where;
    } 
    
    /** 通过 广告 id 找 广告 字符串 或者 字符 找 id**/
    function findAdStr($id,$what="idtotitle"){
       static $addata=array();
       if(empty($addata)){
         $sql = "SELECT id,appname FROM rha_apps";
            $Psys_PagerecordRule = new Psys_PagerecordRule();
            $query_data = $Psys_PagerecordRule->list_query($sql);
            foreach( $query_data as $v){
                $addata["idtotitle"][$v['id']]=$v['appname'];
                $addata["titletoid"][$v['appname']]=$v['id'];
             }
       }
        return $addata[$what][$id];
    }
    
    
    
    /**  获取所有站点名称 ID 等 **/
    function getAllAppkey($what='nametotitle'){
            $appkeyData=array();
            $sql = "SELECT id,stationname,acfile FROM rha_station";
            $Psys_PagerecordRule = new Psys_PagerecordRule();
            $query_data = $Psys_PagerecordRule->list_query($sql);
            foreach( $query_data as $v){
              //   $appkeyData["id"][]=$v["id"];
                 $appkeyData["nametotitle"][$v["id"]]=$v["stationname"];
               //  $appkeyData["title"][]=$v["stationname"];       
           }
          return  $appkeyData[$what];
    }
    
    /**日志板块的  通过站字符串名 找 id 或者 通过 站id找 站字符串 **/
    function findAppkeyStr($num_or_name,$to=''){
        static $appkeyData=array();
        if(empty($appkeyData)){
            $Psys_PagerecordRule = new Psys_PagerecordRule();
            $sql = "SELECT id,stationname,acfile FROM rha_station";
            $query_data = $Psys_PagerecordRule->list_query($sql);
            foreach( $query_data as $v){
                 $appkeyData["nametoid"][$v["acfile"]]=$v["id"];
                 $appkeyData["idtoname"][$v["id"]]=$v["acfile"];
                 $appkeyData["idtotitle"][$v["id"]]=$v["stationname"];
                 $appkeyData["nametotitle"][$v["acfile"]]=$v["stationname"];
              }
         }if(empty($to)){
                if(is_numeric($num_or_name)){
                    return  $appkeyData["idtoname"][$num_or_name];
               }else{
                    return  $appkeyData["nametoid"][$num_or_name];
               }
         }else{
              return  $appkeyData[$to][$num_or_name];
         }
    }

     /** 分页公式 * */
    function openpage($baseurl = '', $num = 12, $allnum = 200, $thepage, $other = '', $addpage = 3)
        {
        //分页计算公式
        $allpage = intval(($allnum + $num - 1) / $num);
        $indexpage = 1;
        $thepage = $thepage > $allpage ? $allpage : ($thepage < 0 ? 1 : $thepage);   //当前页 

        $uppage = ($thepage - 1) <= 1 ? false : ($thepage - 1);                      //上一页 

        $addfirstpage = ($thepage - $addpage) < 1 ? 1 : ($thepage - $addpage); //循环第一页

        $addendpage = ($thepage + $addpage) > $allpage ? $allpage : ($thepage + $addpage); //循环最后一页

        $nextpage = ($thepage + 1) >= $allpage ? false : ($thepage + 1);   //下一页

        $endpage = ($indexpage == $allpage) ? false : $allpage;
        $show = "";
        //生成首页  
        if ($indexpage) {
            $show.="<li class='previous'><a href='{$baseurl}?page={$uppage}{$other}'>&laquo;First</a></li> ";
        }
        //生成上一页  
        if ($uppage) {
            $show.=" <li class='previous'><a href='{$baseurl}?page={$uppage}{$other}'>&laquo;Previous</a></li>";
        }
        for ($i = $addfirstpage; $i <= $addendpage; $i++) {
            $b = "<li><a href='{$baseurl}?page={$i}{$other}'>{$i}</a></li> ";  //数字页 
            if ($i == $thepage) {
                $b = " <li class='active'>{$i}</li>"; //本页 
            }
            if ($i == $indexpage || $i == $allpage) {
                
            } else {
                $show.= $b;
            }
        }
        //生成下一页
        if ($nextpage) {
            $show.=" <li class='next'> <a href='{$baseurl}?page={$nextpage}{$other}'>Next &raquo;</a> </li>";
        }
        //生成尾页
        if ($endpage) {
            $show.=" <a href='{$baseurl}?page={$endpage}'>Last &raquo;</a></li>";
        }
        //生成总页码
        $show.=" <li class='next-off'> {$thepage}/{$allpage} Page</li>";
        return $show;
        }

    }
