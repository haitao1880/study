<?php
/**
* Copyright(c) 2014
* 日    期:2014年7月20日                                                 
* 作　  者:Tony_Ren
* E-mail  :Tony_Ren@rockhippo.net
* 文 件 名:Psys_TrainModel.php                                                
* 创建时间:下午3:11:37                                                
* 字符编码:UTF-8                                                   
* 脚本语言:PHP                                            
* 版本信息:$Id: Psys_TrainModel.php 3174 2014-08-20 01:49:06Z terry $                                                 
* 修改日期:$LastChangedDate: 2014-08-20 09:49:06 +0800 (周三, 20 八月 2014) $                                     
* 最后版本:$LastChangedRevision: 3174 $                                 
* 修 改 者:$LastChangedBy: terry $                                      
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rocktrain/trunk/RockAdmin/protected/module/psys/models/Psys_TrainModel.php $                                            
* 摘    要:  列车管理                                                     
*/

class Psys_TrainModel extends Psys_AbstractModel
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public $traintype = array(
			'K'	=> '快速',
			'Z'	=> '直达特快',
			''	=> '其他',
			'T'	=> '空调特快',
			'D'	=> '动车组',
			'G'	=> '高速动车',
		);
	
	/**
	 * 更新车次经过的站点列表
	 * @param number $trainid
	 */
	public function UpdateTrainStationList($trainid)
	{
		$w = array('tid'=>$trainid);
		$list = $this->GetList($w,'orderid ASC',-1,-1,'stationid','rhi_trainnodetail');
		$cityids = ",";
		foreach ($list['allrow'] as $k1=>$v1)
		{
			$cityids .= $v1['stationid'].",";
		}
		$updata = array('stationlist'=>$cityids);
		$w = array('id'=>$trainid);
		return $this->UpdateOne($updata,$w,'rhi_trainno');
	}
	
	/**
	 * @param string $staion 站名
	 * @param boolean $bl_insert 如果站名不存在，是否添加
	 * @return int 站名ID
	 */
	public function GetTrainCityId($station,$bl_insert = true)
	{
		$w = array('cityname'=>$station);
		$one = $this->GetOne($w,'id','rhi_trainstation');
		
		$id = 0;
		if(!$one && $bl_insert)
		{
			$data = array('cityname'=>$station);		
			require_once COMMON_PATH."Xpinyin.php";
			$py = new Xpinyin();
			
			$data['pinyin'] = $py->getAllPY($station);
			$data['szm']	= $py->getFirstPY($station);
			$data['flag']	= 1;
			
			$id = $this->AddOne($data,'rhi_trainstation');
			
		}else{
			$id = $one['id'];
		}
		
		return $id;
	}
}

?>