<?php
/**
* Copyright(c) 2014
* 日    期:2014年7月4日                                                 
* 作　  者:Tony_Ren
* E-mail  :Tony_Ren@rockhippo.net
* 文 件 名:index.php                                                
* 创建时间:下午2:57:54                                                
* 字符编码:UTF-8                                                   
* 脚本语言:PHP                                            
* 版本信息:$Id$                                                 
* 修改日期:$LastChangedDate$                                     
* 最后版本:$LastChangedRevision$                                 
* 修 改 者:$LastChangedBy$                                      
* 版本地址:$HeadURL$                                            
* 摘    要:引导页                                                       
*/

header("Content-Type:text/html; charset=utf-8");
error_reporting(E_ALL^E_NOTICE);

/*function gen() {
    $ret = (yield 'yield1');
    var_dump($ret);
    $ret = (yield 'yield2');
    var_dump($ret);
}
 
$gen = gen();
var_dump($gen->current());    // string(6) "yield1"
var_dump($gen->send('ret1')); // string(4) "ret1"   (the first var_dump in gen)
                              // string(6) "yield2" (the var_dump of the ->send() return value)
var_dump($gen->send('ret2'));


exit;*/
$curdir = dirname(__FILE__);
require_once $curdir.DIRECTORY_SEPARATOR.'define.php';
require_once $curdir.DIRECTORY_SEPARATOR.'init.php';

require_once PSYS_PATH.'controller'.DIRECTORY_SEPARATOR."Psys_AbstractController.php";
XSession::Init();
XRun::run("psys");

?>