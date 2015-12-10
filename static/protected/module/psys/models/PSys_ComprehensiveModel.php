<?php
/**
* Copyright(c) 2015
* 日    期:2015年07月08日
* 文 件 名:{PSys_Comprehensive}Model.php
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
class PSys_ComprehensiveModel extends PSys_AbstractModel{
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
    * @do 获取某天的数据 
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getDay($date,$group,$dbname,$step,$stationChoice){
        
        $this->SetDb('rha_admin');
        
        $PSys_ComprehensiveRule = new PSys_ComprehensiveRule();
        return $PSys_ComprehensiveRule->getDay($date,$group,$dbname,$step,$stationChoice);
        
    }
    
    
    /**
    *
    * @do 获取n天的数据 
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getDays($bdate,$edate,$group,$dbname,$step,$stationChoice){
        
        $this->SetDb('rha_admin');
        
        $PSys_ComprehensiveRule = new PSys_ComprehensiveRule();
        return $PSys_ComprehensiveRule->getDays($bdate,$edate,$group,$dbname,$step,$stationChoice);
        
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
        
        $PSys_ComprehensiveRule = new PSys_ComprehensiveRule();
        return $PSys_ComprehensiveRule->getWeek($bdate,$edate,$group,$dbname,$stationChoice);
        
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
        
        $PSys_ComprehensiveRule = new PSys_ComprehensiveRule();
        return $PSys_ComprehensiveRule->getDayChainDiagramDetail($id,$dbname);
        
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
        
        $PSys_ComprehensiveRule = new PSys_ComprehensiveRule();
        return $PSys_ComprehensiveRule->getDayChainDiagram($data,$dbname,$stationChoice);
        
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
        
        $PSys_ComprehensiveRule = new PSys_ComprehensiveRule();
        return $PSys_ComprehensiveRule->getWeekChainDiagramDetail($id,$dbname);
        
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
        
        $PSys_ComprehensiveRule = new PSys_ComprehensiveRule();
        return $PSys_ComprehensiveRule->getWeekChainDiagram($data,$dbname,$stationChoice);
        
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
        
        $PSys_ComprehensiveRule = new PSys_ComprehensiveRule();
        return $PSys_ComprehensiveRule->getOneMonthChainDiagramDetail($id,$dbname);
        
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
        
        $PSys_ComprehensiveRule = new PSys_ComprehensiveRule();
        return $PSys_ComprehensiveRule->getOneMonthChainDiagram($data,$dbname,$stationChoice);
        
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
        
        $PSys_ComprehensiveRule = new PSys_ComprehensiveRule();
        return $PSys_ComprehensiveRule->getManyWeekChainDiagramDetail($id,$dbname);
        
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
        
        $PSys_ComprehensiveRule = new PSys_ComprehensiveRule();
        return $PSys_ComprehensiveRule->getManyWeekChainDiagram($data,$dbname,$stationChoice);
        
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
        
        $PSys_ComprehensiveRule = new PSys_ComprehensiveRule();
        return $PSys_ComprehensiveRule->getManyMonthChainDiagramDetail($id,$dbname);
        
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
        
        $PSys_ComprehensiveRule = new PSys_ComprehensiveRule();
        return $PSys_ComprehensiveRule->getManyMonthChainDiagram($data,$dbname,$stationChoice);
        
    }
    
    
}