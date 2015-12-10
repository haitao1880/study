<?php
/**
* Copyright(c) 2013
* 日    期:2013年11月20日
* 文 件 名:index.php
* 创建时间:下午5:20:05
* 字符编码:UTF-8
* 版本信息:$Id: index.php 74 2014-07-03 08:01:16Z tony_ren $
* 修改日期:$LastChangedDate: 2014-07-03 16:01:16 +0800 (周四, 03 七月 2014) $
* 最后版本:$LastChangedRevision: 74 $
* 修 改 者:$LastChangedBy: tony_ren $
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rockgame/trunk/code/gamewww/public/psys/index.php $
* 摘    要:引导页
*/
error_reporting(E_ALL);
date_default_timezone_set('PRC');

header("Content-Type:text/html; charset=utf-8");
$curdir = dirname(__FILE__).DIRECTORY_SEPARATOR;
require_once $curdir.'define.php';
require_once $curdir.'init.php';

require_once PSYS_PATH.'controller'.DIRECTORY_SEPARATOR."PSys_AbstractController.php";

XSession::Init();
XRun::run("psys");
