<?php
/**
 * Copyright(c) ${year}
 * 日    期:${date}
 * 文 件 名:${file}
 * 创建时间:${time}
 * 字符编码:UTF-8
 * 版本信息:$$Id: config_sys.php 119 2014-07-29 09:13:05Z jing $$
 * 修改日期:$$LastChangedDate: 2014-07-29 17:13:05 +0800 (周二, 29 七月 2014) $$
 * 最后版本:$$LastChangedRevision: 119 $$
 * 修 改 者:$$LastChangedBy: jing $$
 * 版本地址:$$HeadURL: http://192.168.1.100:12000/svn/rockgame/trunk/code/gamewww/protected/configs/config_sys.php $$
 * 摘    要:后台配置
 */
$curdir = dirname(__FILE__).DIRECTORY_SEPARATOR;
include_once $curdir.'config.php';
include_once $curdir.'actionlist_sys.php';

$G_X['upload'] = array(

		'unique_salt' 	=> 'Nr!2y[ad',
		'targetFolder' 	=> '/upload',
		'maxSize'		=> 1024 * 1024 * 48,
		'picture_type'	=> array('jpg','jpeg','png','JPG','JPEG','PNG'),

);

//栏目类型
$G_X['category'] = array(

        1   =>  'Page',
        2   =>  'Article',
        100 =>  'Link',

);

//字段前端类型
$G_X['fieldtype'] = array(
	
		1	=>	'input[text]',
		2	=>	'input[radio]',
		3	=>	'input[checkbox]',
		4   =>	'select',
        5   =>  'input[file]',
        6   =>  'input[color]',
        7   =>  'input[date]',
        8   =>  'textarea',
        9   =>  'editor',
		
		99	=>	'input[type=hidden]',

);

//字段file控件上传类型
$G_X['fieldfilevalue'] = array(
	
		1	=>	'*.jpg',
		2	=>	'*.png',
		3   =>	'*.gif',
		4	=>	'*.rar',
		5   =>	'*.zip',
		6	=>	'*.xlsx',
		7	=>	'*.docx',
		8	=>	'*.pptx',

);

//字段正则类型
$G_X['regular'] = array(

    0   =>   "-None-(无正则)",
    1   =>   "/^/d+$/(非负整数)",
    2   =>   "/^[0-9]*[1-9][0-9]*$/(正整数)",
    3   =>   "/^((-/d+)|(0+))$/(非正整数)",
    4   =>   "/^-[0-9]*[1-9][0-9]*$/(负整数)",
    5   =>   "/^-?/d+$/(整数)",
    6   =>   "/^/d+(/./d+)?$/(非负浮点数)",
    7   =>   "/^(([0-9]+/.[0-9]*[1-9][0-9]*)|([0-9]*[1-9][0-9]*/.[0-9]+)|([0-9]*[1-9][0-9]*))$/(正浮点数)",
    8   =>   "/^((-/d+(/./d+)?)|(0+(/.0+)?))$/(非正浮点数)",
    9   =>   "/^(-(([0-9]+/.[0-9]*[1-9][0-9]*)|([0-9]*[1-9][0-9]*/.[0-9]+)|([0-9]*[1-9][0-9]*)))$/(负浮点数)",
    10   =>   "/^(-?/d+)(/./d+)?$/(浮点数)",
    11   =>   "/^/d+(/.{1}/d+)?$/(数字)",
    12   =>   "/^[A-Za-z]+$/(由26个英文字母组成的字符串)",
    13   =>   "/^[A-Z]+$/(由26个英文大写字母组成的字符串)",
    14   =>   "/^[a-z]+$/(由26个英文小写字母组成的字符串)",
    15   =>   "/^[A-Za-z0-9]+$/(由数字和26个英文字母组成的字符串)",
    16   =>   "/^/w+$/(由数字、26个英文字母或者下划线组成的字符串)",
    17   =>   "/^[/x00-/xff]+$/(匹配所有单字节长度的字符组成的字符串)",
    18   =>   "/^[^/x00-/xff]+$/(匹配所有双字节长度的字符组成的字符串)",
    19   =>   "/[^/x00-/xff]+/(字符串是否含有双字节字)",
    20   =>   "/^[/w-]+(/.[/w-]+)*@[/w-]+(/.[/w-]+)+$/(Email地址)",
    21   =>   "/[http|https]://([w-]+.)+[w-]+(/[w- ./?%&=]*)?/(匹配中文字符的正则)",
    22   =>   "/(d+).(d+).(d+).(d+)/(匹配IP地址)",

);

//字段宽度类型
$G_X['fieldwidth'] = array(

    "span11"   =>   "span11",
    "span10"   =>   "span10",
    "span9"   =>   "span9",
    "span8"   =>   "span8",
    "span7"   =>   "span7",
    "span6"   =>   "span6",
    "span5"   =>   "span5",
    "span4"   =>   "span4",
    "span3"   =>   "span3",
    "span2"   =>   "span2",
    "span1"   =>   "span1",
    "spar"   =>   "spar",

);

//字段时间类型
$G_X['fieldtime'] = array(

    1   =>  array(0=>'yyyy-MM-dd',1=>'%Y-%m-%d'),
    2   =>  array(0=>'yyyy-MM-dd HH:mm:ss',1=>'%Y-%m-%d %H:%M:%S'),
    3   =>  array(0=>'dd/MM/yyyy',1=>'%d/%m/%Y'),

);

//字段对齐方式
$G_X['fieldalign'] = array(

    0   =>  'center',
    1   =>  'left',
    2   =>  'right',

);

//字段加密
$G_X['vkey'] = '%';
$G_X['vcode'] = 'ducmi0xZ';
$G_X['vlen'] = 14;
$G_X['vsafetime'] = 15 * 60;//900秒

//SET FILE UNLOCK
$G_X['unlock_setfile'] = 'fc440b210f517618cd2af3c6850bdda1';


