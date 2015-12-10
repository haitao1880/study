<?php
/**
* Copyright(c) 2014
* 日    期:2014年7月15日                                                 
* 作　  者:peter
* E-mail  :peter@rockhippo.net
* 文 件 名:Psys_AdsModel.php                                                
* 创建时间:下午3:21:47                                                
* 字符编码:UTF-8                                                   
* 脚本语言:PHP                                            
* 版本信息:$Id: Psys_AdsModel.php 681 2014-07-14 06:21:42Z jing $                                                 
* 修改日期:$LastChangedDate: 2014-07-14 14:21:42 +0800 (周一, 14 七月 2014) $                                     
* 最后版本:$LastChangedRevision: 681 $                                 
* 修 改 者:$LastChangedBy: jing $                                      
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rocktrain/trunk/RockAdmin/protected/module/psys/models/Psys_AdsModel.php $                                            
* 摘    要: 后台广告业务逻辑                                                      
*/

class Psys_AdsModel extends Psys_AbstractModel
{
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 
	 * 获取待同步数据
	 * @param array $where 查询条件数组
	 * @return array $data 查询数组
	 */
	
	public function getSyncList($servicer)
	{
		$rule = new Psys_AdsRule();
		$data = $rule->getSyncList($servicer);
		return $data;
	}
	
	/**
	 * 获取train数据库下数据
	 */
	public function GetOneAds($where,$field)
	{
		$rule = new Psys_AdsRule();
		$one = $rule->GetOneAds($where,$field);
		return $one;
	}
	/**
	 * 更新train下数据
	 */
	public function UpdateOneAds($data,$where)
	{
		$rule = new Psys_AdsRule();
		$updateR = $rule->UpdateOneAds($data,$where);
		return $updateR;
	}
	/**
	 * 新增train下一条数据
	 * 
	 * 
	 */
	public function AddOneAds($data)
	{
		$rule = new Psys_AdsRule();
		$insertR = $rule->AddOneAds($data);
		return $insertR;
	}
	
	/**
	 * 获取广告一名称字符串
	 * @param unknown $idstr
	 * @return string  */
	public function GetAdsOne($idstr){
		if($idstr){
			$rule = new Psys_AdsRule();
			$data = $rule->GetAdsOne($idstr);
			if($data){
				$ads = array();
				foreach ($data as $value){
					$ads[] = $value['adname'];
				}
				return implode(',', $ads);
			}else{
				return '';
			}	
		}else{
			return '';
		}
	}
	
	/**
	 * 获取车站的广告一信息
	 * @param unknown $appkey  */
	public function GetAdsTwo($idstr){
		if($idstr){
			$rule = new Psys_AdsRule();
			$data = $rule->GetAdsTwo($idstr);
			if($data){
				$ads = array();
				foreach ($data as $value){
					$ads[] = $value['adname'];
				}
				return implode(',', $ads);
			}else{
				return '';
			}	
		}else{
			return '';
		}
	}
	
	/**
	 * 增加车站全屏广告
	 * @param unknown $data
	 * @return Ambigous <成功则返回自增ID, boolean, unknown>  */
	public function addStationAds($data){
		$rule = new Psys_AdsRule();
		return $rule->addStationAds($data);
	}
	
	/**
	 * 通过appkey获取车站全屏广告
	 * @param string $appkey
	 * @return multitype:Ambigous <Ambigous, multitype:, boolean, number>  */
	public function getStationByAppkey($appkey=''){
		$rule = new Psys_AdsRule();
		$stations = $rule->getStationByAppkey($appkey);
		$data = array();
		foreach($stations as $key => $value){
			$data[$value['appkey']] = $value;
		}
		return $data;
	}
	
	/**
	 * 通过id获取车站全屏广告
	 * @param unknown $id  */
	public function getStationadsById($id){
		$rule = new Psys_AdsRule();
		return $rule->getStationadsById($id);
	}
}

?>