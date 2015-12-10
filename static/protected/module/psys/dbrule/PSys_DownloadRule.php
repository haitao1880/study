<?php
/**
* Copyright(c) 2015
* 日    期:2015年07月21日
* 文 件 名:{PSys_Download}Rule.php
* 创建时间:10:44
* 字符编码:UTF-8
* 版本信息:v1.0
* 修改日期:none
* 最后版本:v1.0
* 创 建 者:Nick (nick@rockhippo.cn)
* 修 改 者:none
* 版本地址:none
* 摘    要:下载统计rule层
*/
class PSys_DownloadRule extends PSys_DbAbstractRule{
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
    * @do 获取某天的访问数据 rha_wifi_daily
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getWeek($bdate,$edate,$group,$dbname,$distinguish,$stationChoice){
        
        $stationChoice = $stationChoice != '' ? " AND stationid in (".implode(',',$stationChoice).")" : "";
        
        //区分下载火伴
        switch($distinguish){
            
            case 1:
                
                $where = "AND action = 'trainDown' AND detail = 'uv'";
                break;
            
        }
        
        $group = $group != '' ? "GROUP BY $group" : "";
        
        $sql = "SELECT `date`,SUM(dtime) AS total,stationid,id FROM {$dbname} where `date` BETWEEN '".$bdate."' AND '".$edate."' {$where} {$stationChoice} {$group} ORDER BY `date`";
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
    public function getWeekChainDiagram($data,$dbname,$distinguish,$stationChoice){

        $stationChoice = $stationChoice != '' ? " AND stationid in (".implode(',',$stationChoice).")" : "";
        
        //区分下载火伴
        switch($distinguish){
            
            case 1:
                
                $where = "AND action = 'trainDown' AND detail = 'uv'";
                break;
            
        }
        
        $sql = "SELECT SUM(dtime) AS total,stationid FROM {$dbname} where `date` = '".$data[0]['date']."' AND stationid != 0 {$where} {$stationChoice} GROUP BY `stationid`";
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
    public function getOneMonthChainDiagram($data,$dbname,$distinguish,$stationChoice){

        $stationChoice = $stationChoice != '' ? " AND stationid in (".implode(',',$stationChoice).")" : "";
        
        //区分下载火伴
        switch($distinguish){
            
            case 1:
                
                $where = "AND action = 'trainDown' AND detail = 'uv'";
                break;
            
        }
        
        $sql = "SELECT SUM(dtime) AS total,stationid FROM {$dbname} where `date` = '".$data['date']."' AND stationid != 0 {$where} {$stationChoice} GROUP BY `stationid`";
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

        $data['edate']=date('Y_m_d',strtotime(str_replace("_","-",$result)." Sunday")); 
        $data['bdate']=date('Y_m_d',strtotime(str_replace("_","-",$data['edate'])." -6 days"));
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
    public function getManyWeekChainDiagram($data,$dbname,$distinguish,$stationChoice){

        $stationChoice = $stationChoice != '' ? " AND stationid in (".implode(',',$stationChoice).")" : "";
        
        //区分下载火伴
        switch($distinguish){
            
            case 1:
                
                $where = "AND action = 'trainDown' AND detail = 'uv'";
                break;
            
        }
        
        $sql = "SELECT SUM(dtime) AS total,stationid FROM {$dbname} where `date` BETWEEN '".$data['bdate']."' AND '".$data['edate']."' AND stationid != 0 {$where} {$stationChoice} GROUP BY `stationid`";

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

        $data['bdate']=date('Y_m_01',strtotime(str_replace("_","-",$result))); 
        $data['edate']=date('Y_m_d',strtotime(str_replace("_","-",$data['bdate'])." +1 month -1 day"));

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
    public function getManyMonthChainDiagram($data,$dbname,$distinguish,$stationChoice){

        $stationChoice = $stationChoice != '' ? " AND stationid in (".implode(',',$stationChoice).")" : "";
        
        //区分下载火伴
        switch($distinguish){
            
            case 1:
                
                $where = "AND action = 'trainDown' AND detail = 'uv'";
                break;
            
        }
        
        $sql = "SELECT SUM(dtime) AS total,stationid FROM {$dbname} where `date` BETWEEN '".$data['bdate']."' AND '".$data['edate']."' AND stationid != 0 {$where} {$stationChoice} GROUP BY `stationid`";

        return $this->Query($sql);

    }
    
}