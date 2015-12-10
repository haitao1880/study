<?php
/**
* Copyright(c) 2014
* 日    期:2014年7月4日                                                 
* 作　  者:Tony_Ren
* E-mail  :Tony_Ren@rockhippo.net
* 文 件 名:abstractconst.php                                                
* 创建时间:下午2:59:00                                                
* 字符编码:UTF-8                                                   
* 脚本语言:PHP                                            
* 版本信息:$Id$                                                 
* 修改日期:$LastChangedDate$                                     
* 最后版本:$LastChangedRevision$                                 
* 修 改 者:$LastChangedBy$                                      
* 版本地址:$HeadURL$                                            
* 摘    要:常量值抽象类                                                       
*/
class AbstractConst
{

	/**
	 * 获得常量值的中文
	 * @param int $status
	 * @param string $clsname
	 */
	protected static function GetConstMsg($status,$clsname,$pre = '')
	{
		$rc = new ReflectionClass($clsname);
		$oo = $rc->getConstants();
		foreach ($oo as $k=>$v)
		{
			if($pre != '')
			{
				if(substr($k,0,strlen($pre)) == $pre && $v == $status)
				{
					$kk = $clsname."::".$k."_MSG";
					return @constant($kk);
				}
			}elseif($v == $status){
				$kk = $clsname."::".$k."_MSG";
				return @constant($kk);
			}
		}
	}
	
	/**
	 * 获得静态常量列表
	 * @param string $clsname 类名
	 * @param string $pre 要获取常量前缀，为空则获取所有的
	 * @return array:key常量值  value常量的中文
	 */
	protected static function GetConstList($clsname,$pre='')
	{
		$arr = array();
		
		$rc = new ReflectionClass($clsname);
		$oo = $rc->getConstants();
		foreach ($oo as $k=>$v)
		{
			if($pre != '')
			{
				if(substr($k,0,strlen($pre)) == $pre && substr($k, -3) != "MSG")
				{
					$arr[$v] = @constant($clsname."::".$k."_MSG");
				}
			}elseif(substr($k, -3) != "MSG"){
				$arr[$v] = @constant($clsname."::".$k."_MSG");
			}
		}
		$rc = null;
		$oo = null;
		
		return $arr;
	}
}