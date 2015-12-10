<?php
/**
* Copyright(c) 2015
* 日    期:2015年07月08日
* 文 件 名:{PSys_Access}Rule.php
* 创建时间:13:55
* 字符编码:UTF-8
* 版本信息:v1.0
* 修改日期:none
* 最后版本:v1.0
* 创 建 者:Nick (nick@rockhippo.cn)
* 修 改 者:none
* 版本地址:none
* 摘    要:访问统计rule层
*/
class PSys_AccessRule extends PSys_DbAbstractRule{
    /**
    *
    * 继承构造函数
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
	public function __construct(){
		parent::__construct();
        $this->SetDb("rha_admin");
	}
    
    /**
    *
    * @do 获取某天的访问数据 rha_aclog_hour
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getDay($date,$group,$dbname,$stationChoice){

        $stationChoice = $stationChoice != '' ? " AND stationid in (".implode(',',$stationChoice).")" : "";
        
        $group = $group ? " GROUP BY $group" : "";
        
        $sql = "SELECT SUM(num) AS total,`hour` AS `date`,stationid,id FROM {$dbname} where date = '".$date."' AND type = 'uv' AND stationid != 0 {$stationChoice} {$group}";
        //echo $sql;
        
        return $this->Query($sql);

    }
    
    /**
    *
    * @do 获取某天的访问数据 rha_wifi_daily
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getWeek($bdate,$edate,$group,$dbname,$stationChoice){
        
        $stationChoice = $stationChoice != '' ? " AND station in (".implode(',',$stationChoice).")" : "";
        
        $group = $group != '' ? "GROUP BY $group" : "";
        
        $sql = "SELECT `date`,SUM(total) AS total,station,id FROM {$dbname} where date BETWEEN '".$bdate."' AND '".$edate."' {$stationChoice} {$group} ORDER BY `date`";
        //echo $sql;
        return $this->Query($sql);

    }
    
    /**
    *
    * @do 获取某天的访问数据 rha_aclog_hour 环比
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getDayChainDiagramDetail($id,$dbname){
        
        $sql = "SELECT `hour`,`date` FROM {$dbname} where id = '".$id."'";
        
        return $this->Query($sql);

    }
    
    /**
    *
    * @do 获取某天的访问数据 rha_aclog_hour 环比
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getDayChainDiagram($data,$dbname,$stationChoice){

        $stationChoice = $stationChoice != '' ? " AND stationid in (".implode(',',$stationChoice).")" : "";
        
        $sql = "SELECT num,stationid FROM {$dbname} where `date` = '".$data[0]['date']."' AND `hour` = '".$data[0]['hour']."' AND type = 'uv' AND stationid != 0 {$stationChoice}";
        //echo $sql;
        
        return $this->Query($sql);

    }
    
    
    /**
    *
    * @do 获取某周、月的访问数据 rha_wifi_daily 环比
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getWeekChainDiagramDetail($id,$dbname){
        
        $sql = "SELECT `date` FROM {$dbname} where id = '".$id."'";
        //echo $sql;
        
        return $this->Query($sql);

    }
    
    /**
    *
    * @do 获取某周、月的访问数据 rha_wifi_daily 环比
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getWeekChainDiagram($data,$dbname,$stationChoice){

        $stationChoice = $stationChoice != '' ? " AND station in (".implode(',',$stationChoice).")" : "";
        
        $sql = "SELECT total,station FROM {$dbname} where `date` = '".$data[0]['date']."' AND station != 0 {$stationChoice}";
        //echo $sql;
        
        return $this->Query($sql);

    }
    
    /**
    *
    * @do 获取单月的访问数据 rha_wifi_daily 环比
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getOneMonthChainDiagramDetail($id,$dbname){
        
        $result = $id;

        $data['date']=$result;
        return $data;

    }
    
    /**
    *
    * @do 获取单月的访问数据 rha_wifi_daily 环比
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getOneMonthChainDiagram($data,$dbname,$stationChoice){

        $stationChoice = $stationChoice != '' ? " AND station in (".implode(',',$stationChoice).")" : "";
        
        $sql = "SELECT SUM(total) AS total,station FROM {$dbname} where `date` = '".$data['date']."' AND station != 0 {$stationChoice} GROUP BY `station`";
        //echo $sql;
        return $this->Query($sql);

    }  
    
    /**
    *
    * @do 获取多周的访问数据 rha_wifi_daily 环比
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getManyWeekChainDiagramDetail($id,$dbname){
        
        $result = $id;

        $data['edate']=date('Y-m-d',strtotime("{$result} Sunday")); 
        $data['bdate']=date('Y-m-d',strtotime("{$data['edate']} -6 days"));
        return $data;

    }
    
    /**
    *
    * @do 获取多周的访问数据 rha_wifi_daily 环比
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getManyWeekChainDiagram($data,$dbname,$stationChoice){

        $stationChoice = $stationChoice != '' ? " AND station in (".implode(',',$stationChoice).")" : "";
        
        $sql = "SELECT SUM(total) AS total,station FROM {$dbname} where `date` BETWEEN '".$data['bdate']."' AND '".$data['edate']."' AND station != 0 {$stationChoice} GROUP BY `station`";

        return $this->Query($sql);

    }  
      
    /**
    *
    * @do 获取多月的访问数据 rha_wifi_daily 环比
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getManyMonthChainDiagramDetail($id,$dbname){
        
        $result = $id;

        $data['bdate']=date('Y-m-01',strtotime("{$result}")); 
        $data['edate']=date('Y-m-d',strtotime("{$data['bdate']} +1 month -1 day"));

        return $data;

    }
    
    /**
    *
    * @do 获取多月的访问数据 rha_wifi_daily 环比
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getManyMonthChainDiagram($data,$dbname,$stationChoice){

        $stationChoice = $stationChoice != '' ? " AND station in (".implode(',',$stationChoice).")" : "";
        
        $sql = "SELECT SUM(total) AS total,station FROM {$dbname} where `date` BETWEEN '".$data['bdate']."' AND '".$data['edate']."' AND station != 0 {$stationChoice} GROUP BY `station`";

        return $this->Query($sql);

    }
    
    
    /**
    *
    * @do 获取某天的访问数据 rha_wifi_week
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getMWeek($bdate,$edate,$group,$dbname){
        
        $group = $group != '' ? "GROUP BY $group" : "";
        
        $sql = "SELECT `sdate`,`edate`,SUM(totalnum) AS total,station,id FROM {$dbname} where sdate BETWEEN '".$bdate."' AND '".$edate."' {$group} ORDER BY `sdate`";
        //echo $sql;
        
        return $this->Query($sql);

    }
    
    /**
    *
    * @do 获取某月的访问数据 rha_wifi_daily
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getMonth($bdate,$edate,$group,$dbname){
        
        $group = $group != '' ? "GROUP BY `$group`" : "";
        
        $sql = "SELECT `date`,SUM(total) AS total,id FROM {$dbname} where date BETWEEN '".$bdate."' AND '".$edate."' {$group} ORDER BY `date`";
        
        return $this->Query($sql);

    }
    
        
    /**
    *
    * @do 获取某天的客流数据 rha_traffic_daily
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getPIE($bdate,$edate,$group,$dbname,$stationChoice){
        
        $stationChoice = $stationChoice != '' ? " AND stationid in (".implode(',',$stationChoice).")" : "";
        
        $group = $group != '' ? "GROUP BY $group" : "";
        
        $sql = "SELECT `date`,SUM(newuser) AS newuser,SUM(olduser) AS olduser,stationid,id FROM {$dbname} where date BETWEEN '".$bdate."' AND '".$edate."' {$stationChoice} {$group} ORDER BY `date`";
        //echo $sql;
        return $this->Query($sql);

    }
    
    
}