<?php
/**
* Copyright(c) 2013
* 日    期:2013年11月21日
* 文 件 名:abstractconst.php
* 创建时间:上午10:01:07
* 字符编码:UTF-8
* 版本信息:$Id: abstractconst.php 10 2014-06-13 07:03:39Z tony_ren $
* 修改日期:$LastChangedDate: 2014-06-13 15:03:39 +0800 (周五, 13 六月 2014) $
* 最后版本:$LastChangedRevision: 10 $
* 修 改 者:$LastChangedBy: tony_ren $
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rockgame/trunk/code/gamewww/protected/xconst/xconst/abstractconst.php $
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