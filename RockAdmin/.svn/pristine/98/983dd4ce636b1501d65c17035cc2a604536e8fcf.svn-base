<?php

// 调试模式开关
define( 'DEBUG_MODE', false );

if ( !function_exists('curl_init') ) {
    echo '您的服务器不支持 PHP 的 Curl 模块，请安装或与服务器管理员联系。';
    exit;
}

define( "WB_AKEY" , '3499045726' );
define( "WB_SKEY" , '4f714edc950bbb4cce7e8273eea8e874' );
define( "WB_CALLBACK_URL" , 'http://www.diyiyan.cc/oauth/wbcallback' );

if ( DEBUG_MODE ) {
    error_reporting(E_ALL);
    ini_set('display_errors', true);
}
