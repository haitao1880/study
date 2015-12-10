<?php
/**
* Copyright(c) 2015
* 日    期:2015年07月20日
* 文 件 名:{PSys_Registe}Model.php
* 创建时间:13:46
* 字符编码:UTF-8
* 版本信息:v1.0
* 修改日期:none
* 最后版本:v1.0
* 创 建 者:Nick (nick@rockhippo.cn)
* 修 改 者:none
* 版本地址:none
* 摘    要:注册统计模型层
*/
class PSys_RegisteModel extends PSys_AbstractModel{
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
    * @do 获取某周的数据 
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function getWeek($bdate,$edate,$group,$dbname,$distinguish,$stationChoice){
        
        $this->SetDb('rha_admin');
        
        $PSys_RegisteRule = new PSys_RegisteRule();
        return $PSys_RegisteRule->getWeek($bdate,$edate,$group,$dbname,$distinguish,$stationChoice);
        
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
        
        $PSys_RegisteRule = new PSys_RegisteRule();
        return $PSys_RegisteRule->getWeekChainDiagramDetail($id,$dbname);
        
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
    public function getWeekChainDiagram($data,$dbname,$distinguish,$stationChoice){
        
        $this->SetDb('rha_admin');
        
        $PSys_RegisteRule = new PSys_RegisteRule();
        return $PSys_RegisteRule->getWeekChainDiagram($data,$dbname,$distinguish,$stationChoice);
        
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
        
        $PSys_RegisteRule = new PSys_RegisteRule();
        return $PSys_RegisteRule->getOneMonthChainDiagramDetail($id,$dbname);
        
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
    public function getOneMonthChainDiagram($data,$dbname,$distinguish,$stationChoice){
        
        $this->SetDb('rha_admin');
        
        $PSys_RegisteRule = new PSys_RegisteRule();
        return $PSys_RegisteRule->getOneMonthChainDiagram($data,$dbname,$distinguish,$stationChoice);
        
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
        
        $PSys_RegisteRule = new PSys_RegisteRule();
        return $PSys_RegisteRule->getManyWeekChainDiagramDetail($id,$dbname);
        
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
    public function getManyWeekChainDiagram($data,$dbname,$distinguish,$stationChoice){
        
        $this->SetDb('rha_admin');
        
        $PSys_RegisteRule = new PSys_RegisteRule();
        return $PSys_RegisteRule->getManyWeekChainDiagram($data,$dbname,$distinguish,$stationChoice);
        
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
        
        $PSys_RegisteRule = new PSys_RegisteRule();
        return $PSys_RegisteRule->getManyMonthChainDiagramDetail($id,$dbname);
        
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
    public function getManyMonthChainDiagram($data,$dbname,$distinguish,$stationChoice){
        
        $this->SetDb('rha_admin');
        
        $PSys_RegisteRule = new PSys_RegisteRule();
        return $PSys_RegisteRule->getManyMonthChainDiagram($data,$dbname,$distinguish,$stationChoice);
        
    }
    
    
}