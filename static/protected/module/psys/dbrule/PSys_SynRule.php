<?php
/**
* Copyright(c) 2015
* 日    期:2015年07月14日
* 文 件 名:{PSys_Menu}Rule.php
* 创建时间:13:55
* 字符编码:UTF-8
* 版本信息:v1.0
* 修改日期:none
* 最后版本:v1.0
* 创 建 者:daniel (daniel@rockhippo.cn)
* 修 改 者:none
* 版本地址:none
* 摘    要:访问统计rule层
*/
class PSys_SynRule extends PSys_DbAbstractRule{
    /**
    *
    * 继承构造函数
    *
    * @Member public 
    * @author daniel
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
	public function __construct(){
		parent::__construct();
        $this->SetDb('db-rht_train');
        $this->SetTable("rht_cites");
	}  
	//获取指定SQL列表
	public function GetsqlList($where){
		$this->SetTable('rht_updatesql');
		$list = $this->GetList($where);
		$this->SetTable("rht_cites");
		return $list;
	}
	//获取SQL语句中表名
	public function getDBname(){
		$this->SetTable('rht_updatesql');
		$str = 'select updatetype from rht_updatesql group by updatetype';
		$list = $this->Query($str);
		return $list;
	}
	//获取站点名
	public function GetsiteName($where){
		$this->SetTable('rht_cites');
		$list = $this->GetList($where);
		return $list['allrow'];
	}
	
	//获取单条sql详情
	public function getsqlDetails($where){
		$this->SetTable('rht_updatesql');
		$sql = $this->GetOne('*',$where);
		return $sql;
	}
	//按条件获取sql列表
	public function getcitesql($id,$databaseName){
		$this->SetTable('rht_updatesql');
		if(!$databaseName){
			$sql = "select * from rht_updatesql where not find_in_set('".$id."',siteid)";
		}else{
			$sql = "select * from rht_updatesql where not find_in_set('".$id."',siteid) and updatetype = '".$databaseName."'";
		}
		$list = $this->Query($sql);
		return $list;
	}
	//按照ID获取一条SQL记录
	public function GetOnesql($where){
		$this->SetTable('rht_updatesql');
		$sql = $this->GetOne('*', $where);
		return $sql;
	}
}
