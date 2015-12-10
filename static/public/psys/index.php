<?php
/**

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
