<?php
/**
* Copyright(c) 2013
* 日    期:2013年11月21日
* 文 件 名:msginfoconst.php
* 创建时间:上午10:01:49
* 字符编码:UTF-8
* 版本信息:$Id: msginfoconst.php 10 2014-06-13 07:03:39Z tony_ren $
* 修改日期:$LastChangedDate: 2014-06-13 15:03:39 +0800 (周五, 13 六月 2014) $
* 最后版本:$LastChangedRevision: 10 $
* 修 改 者:$LastChangedBy: tony_ren $
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rockgame/trunk/code/gamewww/protected/xconst/msginfoconst.php $
* 摘    要:操作信息提示
*/

// 1000~2000 网站前台常用提示
class MsgInfoConst extends AbstractConst
{
	private static $msg_arr = array(
		'1000' =>array('en'=>'SUCCESS','zh'=>'成功'),
		'1001' =>array('en'=>'NOTALLPARAM','zh'=>'参数不全'),
		'1002' =>array('en'=>'FAIL','zh'=>'失败'),
		'1003' =>array('en'=>'','de'=>'','zh'=>''),
		'1004' =>array('en'=>'','de'=>'','zh'=>''),
	);
	
	/**
	 * 获得提示信息
	 * @param int $num 错误代码
	 * @param string $culture 语言集 en de，为空则为en
	 */
	public static function GetMsg($num,$culture = 'en')
	{
		if(empty($culture))
		{
			$culture = 'en';
		}
		
		return @self::$msg_arr[$num][$culture];
	}
}
?>