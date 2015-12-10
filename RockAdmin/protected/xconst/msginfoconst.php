<?php
/**
* Copyright(c) 2014
* 日    期:2014年7月4日                                                 
* 作　  者:Tony_Ren
* E-mail  :Tony_Ren@rockhippo.net
* 文 件 名:msginfoconst.php                                                
* 创建时间:下午2:59:21                                                
* 字符编码:UTF-8                                                   
* 脚本语言:PHP                                            
* 版本信息:$Id: msginfoconst.php 821 2014-07-20 09:50:48Z tony_ren $                                                 
* 修改日期:$LastChangedDate: 2014-07-20 17:50:48 +0800 (周日, 20 七月 2014) $                                     
* 最后版本:$LastChangedRevision: 821 $                                 
* 修 改 者:$LastChangedBy: tony_ren $                                      
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rocktrain/trunk/RockAdmin/protected/xconst/msginfoconst.php $                                            
* 摘    要:操作信息提示                                                       
*/

// 1000~2000 网站前台常用提示
class MsgInfoConst extends AbstractConst {
	private static $msg_arr = array (
			// 1000 - 1500 user
			'1000' => '用户名或密码不能为空！',
			'1001' => '不存在该用户！',
			'1002' => '用户名与密码不匹配！',
			'1003' => '密码不能为空！',
			'1004' => '两次输入的密码不一致！',
			'1005' => '用户名已存在！',
			'1006' => '验证码不正确！',
			'1007' => '用户名不能为空！',
			'1008' => '卡号不能为空！',
			'1009' => '充值卡密码不能为空！',
			'1010' => '卡号不存在！',
			'1011' => '卡号不密码不匹配！',
			'1012' => '此卡已被使用过，如有疑问请联系客服！',
			'1013' => '更新用户火车币出错，如有疑问请联系客服！',
			'1014' => '余额不足！',
			
			'1030' => '车厢号必须填写！',
			'1031' => '车厢号只能是数字或字母！',
			'1032' => '座位号必须填写！',
			'1033' => '座位号只能是数字或字母！',
			'1034' => '订餐失败！',
			'1035' => '购物车内没有物品！',
			
			'1040' => '广告位不能为空！',
			'1041' => '图片地址不能为！',
			'1042' => '访问Action不能为空！',
			'1043' => '运营商已存在！',
			
			'1050' => '专辑名不能为空！',
			'1051' => '音乐名不能为空！',
			
			'2000' => '数据库出错！',
			'2001' => '参数出错！',
			'2002' => '数据保存成功！' 
	)
	;
	public  static $appkey_arr = array (
			'jn' => '济南站',
			'jnx' => '济南西站',
			'ta' => '泰安站',
			'qf' => '曲阜站',
			'tz' => '滕州站',
			'zz' => '枣庄站',
			'wf' => '潍坊站',
			'zb' => '淄博站',
			'yt' => '烟台站',
			'qdn' => '青岛站',
			'qdb' => '青岛北站',
			'sf-b' => '青岛汽车总站',
			'qdb-b' => '青岛汽车北站',
			'qdd-b' => '青岛汽车东站',
			'lxjs-b' => '青岛莱西姜山站' 
	);
	/***
	 * 得到站名
	 */
	public static function GetAppKey($num, &$err) {
		$err ['msgcode'] = $num;
		$err ['msg'] = @self::$appkey_arr [$num];
	}
	/**
	 * 获得提示信息
	 *
	 * @param int $num
	 *        	错误代码
	 */
	public static function GetMsg($num, &$err) {
		$err ['msgcode'] = $num;
		$err ['msg'] = @self::$msg_arr [$num];
	}
}
class UserFromConst {
	const APP = 1;
	const TRAIN = 2;
	const WEB = 3;
	const SDK = 4;
}

/**
 * 支付方式
 */
class DepositTypeConst {
	/**
	 * 条形码
	 */
	const BarCode = 10;
	const TransferFromAppToContent = 20;
	const TransferFromContentToApp = 30;
	const SMSApp = 40;
	const SMSContent = 50;
	const TransferFromAppToThirdParties = 60;
}
?>