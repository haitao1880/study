<?php
/**
* Copyright(c) 2015
* 日    期:2015年07月08日
* 文 件 名:{PSys_Access}Model.php
* 创建时间:13:46
* 字符编码:UTF-8
* 版本信息:v1.0
* 修改日期:none
* 最后版本:v1.0
* 创 建 者:Nick (nick@rockhippo.cn)
* 修 改 者:none
* 版本地址:none
* 摘    要:访问统计模型层
*/
class PSys_AccessModel extends PSys_AbstractModel{
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
	}
    
    /**
    *
    * @do 获取某天的数据 rha_aclog_hour
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getDay($date,$group,$dbname,$stationChoice){
        
        $this->SetDb('rha_admin');
        
        $PSys_AccessRule = new PSys_AccessRule();
        return $PSys_AccessRule->getDay($date,$group,$dbname,$stationChoice);
        
    }
    
    /**
    *
    * @do 获取某周的数据 rha_aclog
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getWeek($bdate,$edate,$group,$dbname,$stationChoice){
        
        $this->SetDb('rha_admin');
        
        $PSys_AccessRule = new PSys_AccessRule();
        return $PSys_AccessRule->getWeek($bdate,$edate,$group,$dbname,$stationChoice);
        
    }
    
    /**
    *
    * @do 
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getDayChainDiagramDetail($id,$dbname){
        
        $this->SetDb('rha_admin');
        
        $PSys_AccessRule = new PSys_AccessRule();
        return $PSys_AccessRule->getDayChainDiagramDetail($id,$dbname);
        
    }
    
    /**
    *
    * @do 
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getDayChainDiagram($data,$dbname,$stationChoice){
        
        $this->SetDb('rha_admin');
        
        $PSys_AccessRule = new PSys_AccessRule();
        return $PSys_AccessRule->getDayChainDiagram($data,$dbname,$stationChoice);
        
    }
    
    
    /**
    *
    * @do 
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getWeekChainDiagramDetail($id,$dbname){
        
        $this->SetDb('rha_admin');
        
        $PSys_AccessRule = new PSys_AccessRule();
        return $PSys_AccessRule->getWeekChainDiagramDetail($id,$dbname);
        
    }
    
    /**
    *
    * @do 
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getWeekChainDiagram($data,$dbname,$stationChoice){
        
        $this->SetDb('rha_admin');
        
        $PSys_AccessRule = new PSys_AccessRule();
        return $PSys_AccessRule->getWeekChainDiagram($data,$dbname,$stationChoice);
        
    }
    
      
    /**
    *
    * @do 
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getOneMonthChainDiagramDetail($id,$dbname){
        
        $this->SetDb('rha_admin');
        
        $PSys_AccessRule = new PSys_AccessRule();
        return $PSys_AccessRule->getOneMonthChainDiagramDetail($id,$dbname);
        
    }
    
    /**
    *
    * @do 
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getOneMonthChainDiagram($data,$dbname,$stationChoice){
        
        $this->SetDb('rha_admin');
        
        $PSys_AccessRule = new PSys_AccessRule();
        return $PSys_AccessRule->getOneMonthChainDiagram($data,$dbname,$stationChoice);
        
    }        
    
        
    /**
    *
    * @do 
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getManyWeekChainDiagramDetail($id,$dbname){
        
        $this->SetDb('rha_admin');
        
        $PSys_AccessRule = new PSys_AccessRule();
        return $PSys_AccessRule->getManyWeekChainDiagramDetail($id,$dbname);
        
    }
    
    /**
    *
    * @do 
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getManyWeekChainDiagram($data,$dbname,$stationChoice){
        
        $this->SetDb('rha_admin');
        
        $PSys_AccessRule = new PSys_AccessRule();
        return $PSys_AccessRule->getManyWeekChainDiagram($data,$dbname,$stationChoice);
        
    }        
    
    /**
    *
    * @do 
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getManyMonthChainDiagramDetail($id,$dbname){
        
        $this->SetDb('rha_admin');
        
        $PSys_AccessRule = new PSys_AccessRule();
        return $PSys_AccessRule->getManyMonthChainDiagramDetail($id,$dbname);
        
    }
    
    /**
    *
    * @do 
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getManyMonthChainDiagram($data,$dbname,$stationChoice){
        
        $this->SetDb('rha_admin');
        
        $PSys_AccessRule = new PSys_AccessRule();
        return $PSys_AccessRule->getManyMonthChainDiagram($data,$dbname,$stationChoice);
        
    }
        
    /**
    *
    * @do 获取某周的客流数据 rha_traffic_daily
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getPIE($bdate,$edate,$group,$dbname,$stationChoice){
        
        $this->SetDb('rha_admin');
        
        $PSys_AccessRule = new PSys_AccessRule();
        return $PSys_AccessRule->getPIE($bdate,$edate,$group,$dbname,$stationChoice);
        
    }
    
    
}