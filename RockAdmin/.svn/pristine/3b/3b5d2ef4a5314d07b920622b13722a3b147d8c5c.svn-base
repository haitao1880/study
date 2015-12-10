<?php
/**
* Copyright(c) 2013
* 日    期:2013年11月20日
* 文 件 名:common.php
* 创建时间:下午5:30:41
* 字符编码:UTF-8
* 版本信息:$Id: common.php 626 2014-07-09 09:06:35Z tony_ren $
* 修改日期:$LastChangedDate: 2014-07-09 17:06:35 +0800 (周三, 09 七月 2014) $
* 最后版本:$LastChangedRevision: 626 $
* 修 改 者:$LastChangedBy: tony_ren $
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rocktrain/trunk/RockAdmin/protected/publib/comm/common.php $
* 摘    要:通用函数
*/

/**
 * 解析出URL地址，形如/psys/user/login
 * @param string $prjstr 子系统地址 psys,pweb
 * @param string $modstr 子系统模块
 * @param string $actstr 子系统模块Action
 */
function UrlParse(&$prjstr,&$modstr,&$actstr)
{

	//var_dump($_SERVER);
	//$t = reqarray('cnkey', array());
	//$t2 = reqstr('cntitle', '');
	//var_dump($_REQUEST);
	//exit;
	$result = XUrlParse($prjstr,$modstr,$actstr);
	if($result)return ;

	$uu = explode("?", $_SERVER['REQUEST_URI']);

	global $G_X;

	//$t = $_SERVER;

	if(defined("WEB_URL") && WEB_URL != '' && WEB_URL != '/')
	{
		$tt = trim(WEB_URL,'/');
		$uu[0] = str_ireplace($tt, '', $uu[0]);
	}
	$uu[0] = str_ireplace(array('.php',$prjstr), '', $uu[0]);
	$uu[0] = trim($uu[0],'/');

	$uu = explode("/", $uu[0]);

	if($prjstr != '')
	{
		$modstr = count($uu) > 0 ? $uu[0] : "";
		$actstr = count($uu) > 1 ? $uu[1] : "";
	}else{
		list($prjstr,$modstr,$actstr) = $uu;
		$prjstr = count($uu) > 0 ? $uu[0] : "";
		$modstr = count($uu) > 1 ? $uu[1] : "";
		$actstr = count($uu) > 2 ? $uu[2] : "";
	}

	$prjstr = $prjstr == '' ? 'pc' : trim($prjstr);
	$modstr = $modstr == '' ? 'index' : trim($modstr);
	$actstr = $actstr == '' ? 'index' : trim($actstr);
}

function XUrlParse(&$prjstr,&$modstr,&$actstr)
{
	$str = trim($_SERVER['QUERY_STRING']);
	if($str == '')
	{
		return false;
	}
	$uu = array();
	parse_str($str, $uu);
	if(array_key_exists('prj', $uu))
	{
		$prjstr = $uu['prj'];
	}else{
		return false;
	}
	if(array_key_exists('mod', $uu))
	{
		$modstr = $uu['mod'];
	}else{
		return false;
	}
	if(array_key_exists('act', $uu))
	{
		$actstr = $uu['act'];
	}
	
	// [REQUEST_URI] => /tickets/list/0?1&page=3
	

	$prjstr = $prjstr == '' ? 'pc' : trim($prjstr);
	$modstr = $modstr == '' ? 'index' : trim($modstr);
	$actstr = $actstr == '' ? 'index' : trim($actstr);

	return true;
}

function smarty_fix_include_callback($match){
	$smarty = getInstance('Smarty');
	$file = $smarty->template_dir . '/' . $match[1];
	return file_get_contents($file);
}

function smarty_fix_include($out_put, &$smarty){    // 找出所有的include标签,并找出file
 	return preg_replace_callback(
 			'/{include/s+.*?file=[\'"]*(.+)[\'"]*/}/i',
			'smarty_fix_include_callback', $out_put);
}


/**
 * 检查是否有访问的权限
 * @param array $qx 用户权限列表
 * @param string $prjstr 请求访问的项目，psys,ptech,...
 * @param string $modstr 请求访问的Controller user....
 * @param string $actstr 请求访问的ACTION login
 * @param string $allurl 所有权限的标识，默认为“all”
 * @return boolean true表示允许访问
 */
function ckAccess($qx,$prjstr,$modstr,$actstr,$allurl='all')
{
	$rtn = false;
	if(!is_array($qx) || count($qx) < 1)
	{
		if($qx == $allurl)$rtn = true;
		
		return $rtn;
	}

	$urlnow = '/'.$modstr.'/'.$actstr;
	if($prjstr != 'pc')
	{
		$urlnow = '/'.$prjstr.$urlnow;
	}

	foreach ($qx as $k=>$url)
	{
		//$url='/'.$prjstr.$url;
		if($url == $allurl || stripos($urlnow,$url) !== FALSE)
		{

			$rtn = true;
			break;
		}
	}

	return $rtn;
	//$result['qxlist']
}

/**
 * 获得登陆后的URL
 * @param array $qxurls 授权功能列表
 */
function GetLoginRedirect(array $qxurls,$url = "/psys")
{
	//如果有所有权限则返回系统管理
	if(in_array("all", $qxurls))
	{
		$url = "/psys";
	}else{
		foreach ($qxurls as $k=>$v)
		{
			$url = $v;
			break;
		}
	}
	return $url;
}

/**
 * 是否是机器人
 *
 * @return boolean
 */
function check_robot() {
	if(!defined('IS_ROBOT')) {
		$kw_spiders = 'Bot|Crawl|Spider|slurp|sohu-search|lycos|robozilla';
		$kw_browsers = 'MSIE|Netscape|Opera|Konqueror|Mozilla';
		if(!strexists($_SERVER['HTTP_USER_AGENT'], 'http://') && preg_match("/($kw_browsers)/i", $_SERVER['HTTP_USER_AGENT'])) {
			define('IS_ROBOT', FALSE);
		} elseif(preg_match("/($kw_spiders)/i", $_SERVER['HTTP_USER_AGENT'])) {
			define('IS_ROBOT', TRUE);
		} else {
			define('IS_ROBOT', FALSE);
		}
	}
	return IS_ROBOT;
}


/**
 * 返回客户端真实IP
 * @return 客户IPV4或unknown
 */
function real_ip() {
	if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
		$onlineip = getenv('HTTP_CLIENT_IP');
	} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
		$onlineip = getenv('HTTP_X_FORWARDED_FOR');
	} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
		$onlineip = getenv('REMOTE_ADDR');
	} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
		$onlineip = $_SERVER['REMOTE_ADDR'];
	}
	preg_match("/[\d\.]{7,15}/", $onlineip, $onlineipmatches);
	$onlineip = $onlineipmatches[0] ? $onlineipmatches[0] : 'unknown';
	return $onlineip;
}

/**
 * 是否是邮箱
 * @param $email
 */
function is_email($email) {
	return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}
/**
 * 是否纯 汉字，字母，数字的组合
 * @param String $str
 * @return boolean
 */
function is_word($str){
	return strlen($str)>0&&ereg('['.chr(0xa1).'-'.chr(0xff).'0-9a-zA-Z]', $str);
}

/**
 * IE否
 */
function is_ie() {
	$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
	if((strpos($useragent, 'opera') !== false) || (strpos($useragent, 'konqueror') !== false)) return false;
	if(strpos($useragent, 'msie ') !== false) return true;
	return false;
}

/**
 * 是否是数字
 * @param unknown_type $obj
 * @param unknown_type $init
 * @return unknown
 */
function is_num($obj,$init=0)
{
	return isset($obj) && is_numeric($obj) ? $obj:$init;
}

/**
 * 是否是字符
 * @param unknown_type $obj
 * @param unknown_type $init
 * @return Ambigous <unknown, string>
 */
function is_str($obj,$init='')
{
	return isset($obj) ? trim($obj):$init;
}


/*
 *REQUEST
 */
function req($name)
{
	return isset($_REQUEST[$name]) ? $_REQUEST[$name] : '';
}

/*
 * REQUEST数字
 */
function reqnum($name,$init)
{
	return is_num(req($name),$init);
}

/*
 * REQUEST获取字符串
 */
function reqstr($name,$init='',$allowempty=FALSE)
{
	$rea=is_str(req($name),$init);

	if(!$allowempty && $rea==''){

		$rea=$init;
	}
	return $rea;
}
/**
 * REQUEST获得数组数据,如多选
 */
function reqarray($name,$init = array()){
	$array = req($name);
	if (!empty($array)){
		if(!is_array($array))settype($array, "array");
	}else{
		$array = $init;
	}
	return $array;
}
/**
 * 分页
 * @param unknown $sfilename 页面URL
 * @param unknown $CurrentPage 当前页
 * @param unknown $totalnumber 
 * @param unknown $maxperpage
 * @param string $ShowTotal
 * @param string $ShowAllPages
 * @param string $strUnit
 * @param number $ylnum
 * @return string
 */
function ShowPage($sfilename,$CurrentPage, $totalnumber, $maxperpage, $ShowTotal=true, $ShowAllPages=true, $strUnit="条",$ylnum=10)
{
	
	if ($CurrentPage < 1) $CurrentPage = 1;
	$n = ceil(intval($totalnumber) / intval($maxperpage));
	$strTemp=$strUrl='';

	if ($CurrentPage>$n) $CurrentPage=$n;

	$n = max($CurrentPage, $n);

	$strTemp = '<div class="pages">';
	if ($ShowTotal)
	{
		$strTemp .= "共 <FONT COLOR=red><b>" . $totalnumber . "</b></FONT> " . $strUnit . "&nbsp;&nbsp;";
	}
	$strUrl = $sfilename;//JoinChar(sfilename);
	if(stripos($strUrl,"?") === false)
	{
		$strUrl .= "?1";
	}

	if ($CurrentPage < 2)
	{
		//$strTemp .= "首页 上一页&nbsp;";
		// $strTemp .="<a href='" . $strUrl."&page=1' class=\"prev_page\">首页</a>";
		//第一页
		$strTemp .= '<a href="javascript:;" class="o "> </a>';
	}
	else
	{
		//上一页
		$strTemp .= "<a href='" . $strUrl."&page=".($CurrentPage - 1) . "' class=\"prev_page\"> </a>";
		//第一页
		$strTemp .="<a href='" . $strUrl."&page=1' class=\"page\"><span>1</span></a>";

	}
	if($CurrentPage > 5 )
	{
		$strTemp .='<a href="javascript:;" class="more_page">...</a>';
	}

	if ($CurrentPage >= 2){
		for($i=$CurrentPage-3;$i<$CurrentPage;$i++){
			if($i<2)$i=2;
			if($i>=$CurrentPage)break;
			$strTemp .= '<a href="'.$strUrl.'&page='.$i.'" class="page"><span>'.$i.'</span></a>';

		}
	}
	$strTemp .= '<a href="javascript:;" class="page current"><span>'.$CurrentPage.'</span></a>';
	$shownum=1;
	for ($i=$CurrentPage+1;$i<$CurrentPage+4;$i++){
		$shownum=$i;
		if($i>=$n-2)break;
		$strTemp .= '<a href="'.$strUrl.'&page='.$i.'" class="page"><span>'.$i.'</span></a>';

	}
	if($shownum<=$n){
		if($shownum<$n-1){
			$strTemp .='<a href="javascript:;" class="more_page">...</a>';
			$shownum=$n;
		}
		for($i=$shownum;$i<=$n;$i++){

			$strTemp .= '<a href="'.$strUrl.'&page='.$i.'" class="page"><span>'.$i.'</span></a>';
		}
		//$strTemp .= '<a href="'.$strUrl.'&page='.($n-1).'" class="page"><span>'.($n-1).'</span></a>';
		//$strTemp .= '<a href="'.$strUrl.'&page='.$n.'" class="page"><span>'.$n.'</span></a>';
	}
	/*
	 if ($ShowAllPages)
	 {
	 if($n < $ylnum)
	 {
	 $start = 1;$end = $n;
	 }else{
	 $end = ceil($CurrentPage / $ylnum);
	 if($CurrentPage % $ylnum == 0)$end++;
	 $start = max(($end - 1) * $ylnum,1);
	 $end = min($end * $ylnum,$n);
	 }
	 for(;$start <= $end;$start++)
	 {
	 if ($CurrentPage == $start)
	 {
	 $strTemp .= '<a href="javascript:;" class="page current"><span>'.$CurrentPage.'</span></a>';
	 }else{
	 $strTemp .= '<a href="'.$strUrl.'&page='.$start.'" class="page"><span>'.$start.'</span></a>';
	 }
	 }
	 }
	 */
	if ($n - $CurrentPage < 1)
	{
		//$strTemp .= "下一页 &nbsp;尾页";
		$strTemp .= '<a href="javascript:;" class="next_page"></a>';
	}
	else
	{
		$strTemp .= "<a href='" . $strUrl."&page=".($CurrentPage + 1). "' class=\"next_page\"></a>";
		//$strTemp .= "<a href='" .$strUrl."&page=". $n . "' class=\"next_page\">尾页</a>";
	}
	//$strTemp .= "&nbsp;页次：<strong><font color=red>" .$CurrentPage . "</font>/" . $n . "</strong>页 ";
	//$strTemp .= "&nbsp;<b>" .$maxperpage . "</b>" . $strUnit . "/页";
	$strTemp .= "<div class='jumpbox'>跳转<input type='text' class='pagenum' />页";
	$strTemp .="<input type='button' class='topage' value='GO' onclick='Number($(\".pagenum\").val())>0?location.href=\"".$strUrl."&page=\"+$(\".pagenum\").val():alert(\"请输入一个大于0数字！\")' /></div>";
	$strTemp .= "</div>";

	//如果总页数只有一页
	if ($n<=1) $strTemp="";
	
	return $strTemp;
}


/**
 * 执行JS分页
 * @param int $CurrentPage
 * @param int $totalnumber  总条数
 * @param int $maxperpage
 * @param bool $ShowTotal
 * @param bool $ShowAllPages
 * @param string $strUnit 显示单位，条
 * @param int $ylnum 显示条数
 * @return string
 */
function ShowPage_Ajax($CurrentPage, $totalnumber, $maxperpage, $ShowTotal=true, $ShowAllPages=true, $strUnit="条",$ylnum=10)
{
	//1 0 1 false true 条 10
	if ($CurrentPage < 1) $CurrentPage = 1;
	$n = ceil(intval($totalnumber) / intval($maxperpage));
	$strTemp=$strUrl='';

	$n = max($CurrentPage, $n);

	$strTemp = '<div class="pages">';
	if ($ShowTotal)
	{
		$strTemp .= "共 <FONT COLOR=red><b>" . $totalnumber . "</b></FONT> " . $strUnit . "&nbsp;&nbsp;";
	}
	if($totalnumber < 1)
	{
		$strTemp .= "</div>";
		return $strTemp;
	}

	if ($CurrentPage < 2)
	{
		//$strTemp .= "首页 上一页&nbsp;";
		$strTemp .= '<a href="javascript:;" class="prev_page"></a>';
	}
	else
	{
		//$strTemp .= "<a href='###' onclick='ajaxpage(1)'>首页</a>&nbsp;&nbsp;";
		$strTemp .= '<a href="javascript:;" class="prev_page" onclick="ajaxpage('.($CurrentPage - 1) . ')"></a>&nbsp;&nbsp;';
	}

	if ($ShowAllPages)
	{
		if($n < $ylnum)
		{
			$start = 1;$end = $n;
		}else{
			$end = ceil($CurrentPage / $ylnum);
			if($CurrentPage % $ylnum == 0)$end++;
			$start = max(($end - 1) * $ylnum,1);
			$end = min($end * $ylnum,$n);
		}
		for(;$start <= $end;$start++)
		{
			if ($CurrentPage == $start)
			{
				$strTemp .= '<a href="javascript:;" class="page current"><span>'.$CurrentPage.'</span></a>';
			}else{
				$strTemp .= '<a href="javascript:;" class="page" onclick="ajaxpage('.$start.')"><span>'.$start.'</span></a>';
			}
		}
	}

	if ($n - $CurrentPage < 1)
	//$strTemp .= "下一页 &nbsp;尾页";
	$strTemp .= '<a href="javascript:;" class="next_page"></a>';
	else
	{
		$strTemp .= '<a href="javascript:;" onclick="ajaxpage('.($CurrentPage + 1). ')" class="next_page"></a>';
		//$strTemp .= "<a href='###' onclick='ajaxpage(".($CurrentPage + 1). ")'>下一页</a>&nbsp;&nbsp;";
		//$strTemp .= "<a href='###' onclick='ajaxpage(". $n . ")'>尾页</a>";
	}
	//$strTemp .= "&nbsp;页次：<strong><font color=red>" .$CurrentPage . "</font>/" . $n . "</strong>页 ";
	//$strTemp .= "&nbsp;<b>" .$maxperpage . "</b>" . $strUnit . "/页";

	$strTemp .= "</div>";

	return $strTemp;
}


function arr2sql($data){
	if(!is_array($data)){return ' ';}
	$sql='';
	foreach($data as $k=>$v){
		if($v){
			if(strstr($k,'>')|| strstr($k,'<')||strstr($k,'=') ){
				$sql.= ' AND '.$k.addslashes($v);
			}else{
				$sql.= ' AND '.$k."='".addslashes($v)."'";
			}
		}

	}
	return $sql;
}

/**
 * 文本框内带有格式的输入替换为HTML代码
 * @param string $str 输入字符串
 * @return string
 */
function fmt_txt_html($str)
{
	$str2 = nl2br($str);
	$str_arr = explode('<br />', $str2);
	$dst = '';
	foreach ($str_arr as $k=>&$v)
	{
		//$v = trim(trim($v),'　');
		$v = trim($v);
		$v = preg_replace("/^(　)+|(　)+$/",'',$v);
		$v = '<p>'.$v.'</p>';
	}
	$dst = implode('', $str_arr);//<br /><br />

	return $dst;
}
/**
 * 带有格式的输入替换为文本框中的内容
 * @param string $str 输入字符串
 * @return string
 */
function fmt_html_txt($str)
{
	$str = str_ireplace("</p>", "\r\n", $str);
	$str = str_ireplace("<p>", "", $str);
	/*
	 $str2 = trim($str);
	 $searh = array('<br />','&nbsp;&nbsp;','&nbsp;');
	 $dest  = array("\r\n",' ',' ');
	 $str2 = str_ireplace($searh,$dest,$str2);
	 */
	return $str;
}

/**
 * @param string $val 要替换XSS的内容
 * @return string
 */
function RemoveXSS($val) {
	// remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
	// this prevents some character re-spacing such as <java\0script>
	// note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
	$val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
			 
	// straight replacements, the user should never need these since they're normal characters
	// this prevents like <IMG SRC=@avascript:alert('XSS')>
	$search = 'abcdefghijklmnopqrstuvwxyz';
   $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
   $search .= '1234567890!@#$%^&*()';
   $search .= '~`";:?+/={}[]-_|\'\\';
   for ($i = 0; $i < strlen($search); $i++) {
   		// ;? matches the ;, which is optional
		// 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

		// @ @ search for the hex values
		$val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
		// @ @ 0{0,7} matches '0' zero to seven times
		$val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
	}

	// now the only remaining whitespace attacks are \t, \n, and \r
	$ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
	$ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
   	$ra = array_merge($ra1, $ra2);

   	$found = true; // keep replacing as long as the previous round replaced something
	while ($found == true) {
		$val_before = $val;
		for ($i = 0; $i < sizeof($ra); $i++) {
			$pattern = '/';
			for ($j = 0; $j < strlen($ra[$i]); $j++) {
				if ($j > 0) {
					$pattern .= '(';
					$pattern .= '(&#[xX]0{0,8}([9ab]);)';
					$pattern .= '|';
					$pattern .= '|(&#0{0,8}([9|10|13]);)';
					$pattern .= ')*';
				}
				$pattern .= $ra[$i][$j];
			}
			$pattern .= '/i';
	        $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
	        $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
	        if ($val_before == $val) {
		       // no replacements were made, so exit the loop
		       $found = false;
			}
		}
	}
	return $val;
}

/**
 * 去除数组中非数字的键值
 * @param array $arr
 */
function fmt_arr_num(array &$arr)
{
	foreach ($arr as $k=>$v)
	{
		if(trim($v) == '' || !is_numeric($v))unset($arr[$k]);
	}
}

/**
 * 去除数组中的空值
 * @param array $arr
 */
function fmt_arr_empty(array &$arr)
{
	foreach ($arr as $k=>$v)
	{
		if(trim($v) == '')unset($arr[$k]);
	}
}

/**
 * 添加addslashes
 * @param array|string $arr
 */
function fmt_arr_addslashes(&$arr)
{
	if(is_array($arr))
	{
		foreach ($arr as $k=>&$v)
		{
			$v = addslashes($v);
		}
	}else{
		$arr = addslashes($arr);
	}
}

/**
 * 过滤掉HTML代码
 * @param string $str
 * @return string
 */
function filterHtml($str)
{
	if(trim($str) == '')return '';

	/*$farr = array(
	 "/<!DOCTYPE([^>]*?)>|<\?xml([^>]*?)>/eis",
	 "/&nbsp;|&rdquo;|&ldquo;|&lsquo;|&hellip;|&le;|&rsquo;|&gt;|&amp;|&lt;|&perp;|&ang;|&deg;|&mdash;|&times;|&ge;/eis",
	 "/<(\/?)(html|body|v:shapetype|v:shapestyle|v:path|v:stroke|sub|em|st1:chmetcnv|st1:chsdate|h|table|strong|h1|h2|h3|h4|h5|head|link|i|div|sup|TABLEstyle|IMGstyle|img|u|tbody|tr|td|tablecellspacing|tablecellspacing|tdwidth|tdvalign|font|meta|a|b|span|p|base|input)([^>]*?)>/eis",
	 "/<(script|i?frame|style|title|form)(.*?)<\/\\1>/eis",
	 "/(<[^>]*?\s+)on[a-z]+\s*?=(\"|')([^\\2]*)\\2([^>]*?>)/isU",//过滤javascript的on事件
	 "/\s+/",//过滤多余的空白
	 "/[[:punct:]]/i",
	 "/[\x{2018}-\x{2026}\x{3000}-\x{301e}\x{fe50}-\x{ff1f}]/u"//中文字符,不过对￥不起作用,有待改进
	 );*/
	$farr = array(
          "/<!DOCTYPE([^>]*?)>|<\?xml([^>]*?)>/eis",
          "/&nbsp;|&rdquo;|&ldquo;|&lsquo;|&hellip;|&le;|&rsquo;|&gt;|&amp;|&lt;|&perp;|&ang;|&deg;|&mdash;|&times;|&ge;/eis",
          "/<(\/?)(html|body|v:shapetype|v:shapestyle|v:path|v:stroke|sub|em|st1:chmetcnv|st1:chsdate|h|table|strong|h1|h2|h3|h4|h5|head|link|i|div|sup|TABLEstyle|IMGstyle|img|u|tbody|tr|td|tablecellspacing|tablecellspacing|tdwidth|tdvalign|font|meta|a|b|span|p|base|input)([^>]*?)>/eis",
          "/<(script|i?frame|style|title|form)(.*?)<\/\\1>/eis",
          "/(<[^>]*?\s+)on[a-z]+\s*?=(\"|')([^\\2]*)\\2([^>]*?>)/isU",//过滤javascript的on事件
          "/\s+/",//过滤多余的空白
          "/[[:punct:]]/i"
          );
          $tarr = array(
          "",
          "",
          "",
          "\\1\\4",
          " ",
          "",
          "",
          ""
          );
          $str = preg_replace( $farr,$tarr,$str);
          return $str;
}

/**
 * Generate a random UUID
 *
 * @see http://www.ietf.org/rfc/rfc4122.txt
 * @return RFC 4122 UUID
 * @static
 * function create_guid() {
 $charid = strtoupper(md5(uniqid(mt_rand(), true)));
 $hyphen = chr(45);// "-"
 $uuid = chr(123)// "{"
 .substr($charid, 0, 8).$hyphen
 .substr($charid, 8, 4).$hyphen
 .substr($charid,12, 4).$hyphen
 .substr($charid,16, 4).$hyphen
 .substr($charid,20,12)
 .chr(125);// "}"
 return $uuid;
 }
 */
function pasguid()
{
	if (function_exists('com_create_guid') === true)
	{
		return strtolower(trim(com_create_guid(), '{}'));
	}
	//return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 65535),
	//mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479),
	//mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));

	$node = isset($_SERVER['SERVER_ADDR'])?$_SERVER['SERVER_ADDR']:0;
	$pid = null;

	if (strpos($node, ':') !== false) {
		if (substr_count($node, '::')) {
			$node = str_replace('::', str_repeat(':0000', 8 - substr_count($node, ':')) . ':', $node);
		}
		$node = explode(':', $node) ;
		$ipv6 = '' ;

		foreach ($node as $id) {
			$ipv6 .= str_pad(base_convert($id, 16, 2), 16, 0, STR_PAD_LEFT);
		}
		$node =  base_convert($ipv6, 2, 10);

		if (strlen($node) < 38) {
			$node = null;
		} else {
			$node = crc32($node);
		}
	} elseif (empty($node)) {
		$host = isset($_SERVER['HOSTNAME']) ? $_SERVER['HOSTNAME'] : '';


		if (empty($host)) {
			$host = isset($_SERVER['HOST']) ? $_SERVER['HOST'] : '';
		}

		if (!empty($host)) {
			$ip = gethostbyname($host);

			if ($ip === $host) {
				$node = crc32($host);
			} else {
				$node = ip2long($ip);
			}
		}
	} elseif ($node !== '127.0.0.1') {
		$node = ip2long($node);
	} else {
		$node = null;
	}

	if (empty($node)) {
		$node = crc32("DYhG93bdsa0qyJfIxdfs2guVoUubWwvniR2G0FgaC9mi");
	}

	if (function_exists('zend_thread_id')) {
		$pid = zend_thread_id();
	} else {
		$pid = getmypid();
	}

	if (!$pid || $pid > 65535) {
		$pid = mt_rand(0, 0xfff) | 0x4000;
	}

	list($timeMid, $timeLow) = explode(' ', microtime());
	$uuid = sprintf("%08x-%04x-%04x-%02x%02x-%04x%08x", (int)$timeLow, (int)substr($timeMid, 2) & 0xffff,
	mt_rand(0, 0xfff) | 0x4000, mt_rand(0, 0x3f) | 0x80, mt_rand(0, 0xff), $pid, $node);

	return $uuid;
}

/**
 * 操作结果标题
 * @param bool $isok false 出错了 true 操作结果
 */
function msg_title($isok = false)
{
	if($isok)
	{
		return '操作结果';
	}else{
		return '出错了';
	}
}

/**
 * 操作结果常用内容
 * @param string $url 跳转URL
 * @param int $isback 是否返回
 */
function msg_content($url='',$isback = 1)
{
	$url = $url == '' ? "history.back(1)":$url;
}

/**
 * 获取二维数组中的指定列，返回一个一位数组格式的结果
 * 默认为不导入相同数据并且不导入空数据
 * @param  array  $data         数据源
 * @param  string $Query_name   要查询的列的名字
 * @param  bool   $import_same  同值数据是否添加
 * @param  bool   $import_empty 为空时是否导入
 * @return array                形如array(0=>'ww',1=>'sss',2=>'zzz');
 */
function get_ceil_arr(array $data,$Query_name,$import_same=FALSE,$import_empty=FALSE){
	if(!$data){return FALSE;}
	$result = array();
	foreach ($data as $v){
		if(!$import_empty && !$v[$Query_name]){ continue;}
		if($import_same){//如果同名仍然添加
			$result[] = $v[$Query_name];
		}else{
			//否则就是同名则不添加
			if(!in_array($v[$Query_name], $result)){
				$result[] = $v[$Query_name];
			}
		}
	}
	return $result;
}

function FmtBrushInfo(&$list)
{
	$num1 = floor(count($list) * 0.10);

	if($num1 < 1)return;

	$num2 = $num1;
	$d1 = $d2 = 0;
	$tmp_arr = array();
	foreach ($list as $kk=>$vv)
	{
		//如果是最近30分钟发布，且brushscore值为0，则给初始值
		if($vv['brushscore'] == 0 && $vv['createtime'] < time() - 3600)
		{
			$vv['brushscore'] = 10000000 + (5 - $vv['star']);
		}
		$tmp_arr[$vv['id']] = $vv['brushscore'].str_pad($vv['id'], 10,'0');
	}
	arsort($tmp_arr);
	$arr_1 = array_slice($tmp_arr, 0, $num1,true);
	$arr_2 = array_slice($tmp_arr, $num1, $num2,true);
	foreach ($list as $kk2=>&$vv2)
	{
		if(array_key_exists($vv2['id'], $arr_1))
		{
			$vv2['brushstyle'] = 'brushstyle1';
		}
		elseif (array_key_exists($vv2['id'], $arr_2))
		{
			$vv2['brushstyle'] = 'brushstyle2';
		}
	}
	$tmp_arr = null;
	$arr_1 = null;
	$arr_2 = null;
}

/**
 * 格式化调试输出
 * @param all $var 输出内容
 * @param int $isbreak 是否中断
 */
function dump($var='',$isbreak = 1)
{
	echo "<pre>";
	var_dump($var);
	echo "</pre>";
	if($isbreak){
		exit;
	}
}

/**
 * 获得来源 页面地址
 * @access paslib
 * @author Jiss
 * @return string $ParentPageUrl 来源页面Url
 **/
function getHttpReferer()
{
	$ParentPageUrl = $_SERVER['HTTP_REFERER'];
	return $ParentPageUrl;
}

/**
 * 检查Url和来源URL是否相同
 * 这里的检查，是指来源URL中是否包含给出的$checkUrl
 *
 * @author Jiss
 * @param string $checkUrl 待检查的URL 必填
 * @return true or false;
 **/
function checkMyUrlIsHttpReferer($checkUrl)
{
	$ParentPageUrl = getHttpReferer();
	return strstr($ParentPageUrl,$checkUrl);
}


/**
 * 当前时间  含微秒
 * @return number
 */
function microtime_float()
{
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}


/**
 * 获得图片路径
 * @param number $number
 * @param string $size _48.jpg
 * @param string $prestr avata,img
 * @return string 存放的路径
 */
function GetImgPath($number,$prestr = 'avatar',$storeprepath='')
{
	$imgs = GetNumMod($number);
	if($prestr == 'avatar')
	{
		$prestr = 'avatar'.DIRECTORY_SEPARATOR;
	}else{
		$prestr = 'img'.DIRECTORY_SEPARATOR;
	}
	$prestr .= implode(DIRECTORY_SEPARATOR,$imgs).DIRECTORY_SEPARATOR;

	$uploadpath = $storeprepath.$prestr;

	if(!is_dir($uploadpath))
	{
		mkdir($uploadpath,0777,true);
	}
	//$uploadpath .= $number.$size;
	return $uploadpath;
}

/**
 * 获得用户头像URL
 * @param number $number
 * @param string $size _48.jpg
 * @param string $prestr avata,img
 * @return string
 */
function GetImgUrl($number,$prestr='avatar',$vpre='/upload/')
{
	$imgs = GetNumMod($number);
	if($prestr == 'avatar')
	{
		$prestr = 'avatar/';
	}else{
		$prestr = 'img/';
	}
	$url = $vpre.$prestr;
	$url .= implode('/',$imgs).'/';
	//$url .= $imgs[0].'/'.$imgs[1].'/';

	return $url;
}
/**
 * 取得头像上传路径
 * @param number $userid
 * @return string
 */
function GetAvataPath($userid,$storeprepath='')
{
	return GetImgPath($userid,'avatar',$storeprepath);
}
/**
 * 获各头像的上传URL
 * @param number $userid 用户ID
 * @param string $size eg 48.jpg
 * @return string
 */
function GetAvataUrl($userid,$vpre='/upload/')
{
	return GetImgUrl($userid,'avatar');
}

/**
 * 存放路径规则
 * 三级目录可以存放亿级图片
 * @param number $number
 * @return multitype:number
 */
function GetNumMod($number)
{
	if($number < 3631)
	{
		$rtn = array(ceil(($number-$number%1000000)/1000000),$number-$number%1000);
	}else{

		$n_str = str_pad($number,11,'0',STR_PAD_LEFT);
		$rtn = str_split($n_str,3);
		array_pop($rtn);
		foreach($rtn as $k=>&$v)
		{
			$v = intval($v);
		}
	}
	return $rtn;
}

/**
 * 负责输出不同大小规格头像地址
 * @param string $imgpath  图片地址 /upload/img/0/0/102.jpg
 * @param string $size     图片大小规格字符串
 * @param bool   $print    true直接打印地址，false返回地址。默认为true
 * @return string $imgpath
 *
 */
function HeaderThumbnail($srcimgurl ,$sex='男', $size = 'middle' , $print = TRUE , $yiju = FALSE){
	global $G_X;
	$defaultFace = array(
	    'girl'=>$G_X['imgdomain']['staticimg'].'/style/default/images/sister.JPG',
	    'boy'=>$G_X['imgdomain']['staticimg'].'/style/default/images/brother.JPG'
	    );

	    if($yiju){
	    	$defaultFace['girl']=$G_X['imgdomain']['staticimg'].'/style/default/images/yiju/girl.png';
	    	$defaultFace['boy']=$G_X['imgdomain']['staticimg'].'/style/default/images/yiju/boy.png';
	    }
	    if(empty($srcimgurl)){
	    	if($sex=='女'){
	    		$srcimgurl= $defaultFace['girl'];
	    	}else{
	    		$srcimgurl =$defaultFace['boy'];
	    	}
	    }else{
	    	if(preg_match("/\/images\/(sister|brother)\.JPG/i",$srcimgurl,$sexR)){

	    		if($sexR[1]=='sister'){
	    			$srcimgurl= $defaultFace['girl'];
	    		}else{
	    			$srcimgurl =$defaultFace['boy'];
	    		}
	    	}else{
	    		if(substr($srcimgurl, 0,4)!="http")
	    		{
	    			$srcimgurl=$G_X['imgdomain']['uface'].$srcimgurl;
	    		}
	    	}

	    }

	    if(!$print)return $srcimgurl;
	    echo $srcimgurl;

}
/**
 * 负责输出不同大小规格图片高宽
 * @param string $size     图片大小规格字符串
 * @param string $width     原图宽
 * @param string $height     原图高
 * @param bool   $small    true返回缩略小图的高宽，false返回缩略图。默认为fasle
 *
 * @return array array('width'=>112;'height'=>112)
 *
 */
function ThumbW_H($size='middle',$width=null,$height=null,$small=false){
	global $G_X;
	$r=array('width'=>$width,'height'=>$height);
	if(!isset($G_X['imageThumb'][$size]))return $r;
	$param=array();
	if($small&&isset($G_X['imageThumb'][$size]['s'])){
		$param['thumbwidth']=$G_X['imageThumb'][$size]['s']['w'];
		$param['thumbheight']=$G_X['imageThumb'][$size]['s']['h'];
	}else{
		$param['thumbwidth']=$G_X['imageThumb'][$size]['w'];
		$param['thumbheight']=$G_X['imageThumb'][$size]['h'];
	}
	if(!empty($param['thumbwidth'])&&!empty($param['thumbheight'])){
		return $param;
	}
	if(!empty($param['thumbwidth'])){
		$imgratio=$param['thumbwidth']/$width;
		$r['width']=$param['thumbwidth'];
		$r['height']=ceil($height*$imgratio);
	}
	if(!empty($param['thumbheight'])){
		$imgratio=$param['thumbheight']/$height;
		$r['height']=$param['thumbheight'];
		$r['width']=ceil($width*$imgratio);
	}
	return $r;

}


/**
 * 为图片添加对应域名
 * @param String $ImgUrl
 * @return string
 */
function AddHostOnImg($ImgUrl,$Print=false){

	global $G_X;
	if("http://"==substr($ImgUrl, 0,7)){

	}else{
		if(strpos($ImgUrl,"/img/")){
			$ImgUrl= $G_X['imgdomain']['syimg'].$ImgUrl;
		}
		if(strpos($ImgUrl,"/avatar/")){
			$ImgUrl= $G_X['imgdomain']['uface'].$ImgUrl;
		}
	}
	if($Print)echo $ImgUrl;
	else return $ImgUrl;
}


/**
 * 负责输出不同大小规格图片地址
 * @param string $imgpath  图片地址 /upload/img/0/0/102.jpg
 * @param string $size     图片大小规格字符串
 * @param bool   $small    true返回缩略小兔，false返回缩略图。默认为fasle
 * @param bool   $print    true直接打印地址，false返回地址。默认为true

 * @return string $imgpath
 *
 */
function Thumbnail($srcimgurl , $size = 'middle' , $small=false,$print = TRUE){
	global $G_X;
	$srcimgurl=preg_replace("/\?.*/","",$srcimgurl);
	if(!empty($size)){
		$img_arr     = explode(".", $srcimgurl);
		$fileext     = end($img_arr);
		$Thumbstr    = '';
		if(isset($G_X['imageThumb'][$size]['suffix'])){
			$Thumbstr_thumb = '.'.$G_X['imageThumb'][$size]['suffix'];
			$thumbwidth_thumb = $G_X['imageThumb'][$size]['w'];
			$thumbheight_thumb = $G_X['imageThumb'][$size]['h'];
		}
		if($small){
			$Thumbstr_thumb_s = '.'.$G_X['imageThumb'][$size]['s']['suffix'];
			$thumbwidth_thumb_s = $G_X['imageThumb'][$size]['s']['w'];
			$thumbheight_thumb_s = $G_X['imageThumb'][$size]['s']['h'];

		}
		$newimgurl_thumb    = $img_arr[0].$Thumbstr_thumb.'.'.$fileext;
		$newimgurl_thumb_s = $img_arr[0].$Thumbstr_thumb_s.'.'.$fileext;

		$newimgpath_thumb    = str_ireplace('/', DIRECTORY_SEPARATOR, ltrim($newimgurl_thumb,'/upload/'));
		$newimgpath_thumb    = DATA_UPLOAD_PATH . $newimgpath_thumb;

		$newimgpath_thumb_s    = str_ireplace('/', DIRECTORY_SEPARATOR, ltrim($newimgurl_thumb_s,'/upload/'));
		$newimgpath_thumb_s    = DATA_UPLOAD_PATH . $newimgpath_thumb_s;

		$srcimgpath    = str_ireplace('/', DIRECTORY_SEPARATOR, ltrim($srcimgurl,'/upload/'));
		$srcimgpath    = DATA_UPLOAD_PATH . $srcimgpath;

		if(!file_exists($newimgpath_thumb) && file_exists($srcimgpath))
		{

			$image = new JdImage();
			$thumbtype = 2;
			if(empty($thumbwidth_thumb) || empty($thumbheight_thumb))$thumbtype = 1;
			$bicreat = $image->Thumb($srcimgpath, $newimgpath_thumb, $thumbwidth_thumb, $thumbheight_thumb,$thumbtype,0);

		}
		if($small&&(!file_exists($newimgpath_thumb_s) && file_exists($newimgpath_thumb)))
		{

			$image = new JdImage();
			$thumbtype = 2;
			if(empty($thumbwidth_thumb_s) || empty($thumbheight_thumb_s))$thumbtype = 1;
			$bicreat = $image->Thumb($newimgpath_thumb, $newimgpath_thumb_s, $thumbwidth_thumb_s, $thumbheight_thumb_s,$thumbtype,0);

		}

		$img_arr = null;
		$returnUrl=($small)?$newimgurl_thumb_s:$newimgurl_thumb;


	}else{
		$returnUrl =$srcimgurl;
	}
	$returnUrl=AddHostOnImg($returnUrl);
	if($print){
		echo $returnUrl;
	}else{
		return $returnUrl;
	}
}

/**
 * 字符串截取
 * @param string $srcstr 源字符串
 * @param number $len 汉字的长度
 * @param boolean $once true只取一次，否则取多次，以<br />分隔，即强制换行
 * @return string
 */
function msubstr($srcstr,$len,$once = false) {
	$str = strip_tags($srcstr);
	mb_internal_encoding("UTF-8");
	$srcbytelen = strlen($str);
	$bytelen = $len * 3;//UTF-8汉字占3个字节
	$tmpstr = '';
	for ($start=0;$start <= $srcbytelen;$start+=$bytelen)
	{
		$tmpstr .= mb_strcut($str,$start,$bytelen);
		if($once)break;
		$tmpstr .= '<br />';
	}

	return $tmpstr;
}

/**
 * 移除字符段落首尾空格
 * @param string $str    待移除空格的字符串
 */
function ParagTrim($str){
	$search1 = array("\r\n","\n");
	$dest1   = array('');
	$str = str_ireplace($search1,$dest1,$str);
	$search = array('<br>&nbsp;&nbsp;&nbsp;&nbsp;','<br />&nbsp;&nbsp;&nbsp;&nbsp;');
	$dest   = array('<br />','<br />');
	$str = str_ireplace($search,$dest,$str);
	return trim(trim($str),'&nbsp;');

}
/**
 * 显示开始时间 结束时间  及所用时间
 * @param unknown_type $timeEnd
 * @param unknown_type $timeBegin
 */
function showTimeRun($timeEnd,$timeBegin){
	echo "\n<!-- time begin:$timeBegin  end: $timeEnd  time: ".($timeEnd-$timeBegin)."  -->\n";

}

function tem_set_val($key,$value){
	if(isset($GLOBALS['AA'][$key])){
		if(!is_array($GLOBALS['AA'][$key]))
		$GLOBALS['AA'][$key]=array($GLOBALS['AA'][$key]);
		$GLOBALS['AA'][$key][]=$value;
	}else{
		$GLOBALS['AA'][$key]=$value;
	}


}
function tem_get_val($key){
	return ($GLOBALS['AA'][$key]);
}
function showScript(){

	$javascript =tem_get_val('script');

	if(is_array($javascript)){
		$javascript= array_unique($javascript);
		foreach ($javascript as $url){
			echo "<script src=\"$url\"></script>\n";
		}//type=\"text/javascript\" charset=\"UTF-8\" 
	}else{
		echo "<script src=\"$javascript\"></script>\n";
	}
}

/*
 获得字符串长度：中文、全角字母数字算1，半角字母数字算0.5
 */
function jdstrlen($str)
{
	$str = strip_tags($str);
	return (strlen($str) + mb_strlen($str,'UTF8')) / 4;
}


/**
 * 用于一句显示
 * 1-10.5个汉字，字号30px，行间距42px； 11-32.5个汉字，字号22px，行间距30px；33个汉字以上，字号14px，行间距20px；
 * @param string	 $string   字符串
 * @param int $FontSize 字号
 * @param int $LineSpacing 行距
 */
function  jdFontSizeFormat($string='',&$FontSize=14,&$LineSpacing=20){
	$strLength=jdstrlen($string);
	echo $strLength;
	if(1 <= $strLength && $strLength < 11){
		$FontSize=30;
		$LineSpacing=42;
	}elseif (11<=$strLength && $strLength<33){
		$FontSize=22;
		$LineSpacing=30;
	}elseif (33<=$strLength){
		$FontSize=14;
		$LineSpacing=20;
	}else{
		$FontSize=14;
		$LineSpacing=20;
	}

}

if ( ! function_exists('show_error'))
{
	function show_error($message, $status_code = 500, $heading = 'An Error Was Encountered')
	{
		$_error = new Exception($message, $status_code);//load_class('Exceptions', 'core');
		exit($heading.$message);
	}
}


/**
 +-----------------------------------------------------
 * Mcrypt 加密/解密
 * @param String $date 要加密和解密的数据
 * @param String $mode encode 默认为加密/decode 为解密
 * @return String
 * @author zxing@97md.net Mon Sep 14 22:59:28 CST 2009
 +-----------------------------------------------------
 * @example
 */
function JdCrypt($date,$mode = 'encode'){
	global $G_X;
	$key = md5($G_X['cpwd']['keycode']);//用MD5哈希生成一个密钥，注意加密和解密的密钥必须统一
	if ($mode == 'decode'){
		$date = base64_decode($date);
	}
	if (function_exists('mcrypt_create_iv')){
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	}
	if (isset($iv) && $mode == 'encode'){
		$passcrypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $date, MCRYPT_MODE_ECB, $iv);
	}elseif (isset($iv) && $mode == 'decode'){
		$passcrypt = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $date, MCRYPT_MODE_ECB, $iv);
	}
	if ($mode == 'encode'){
		$passcrypt = base64_encode($passcrypt);
	}
	return $passcrypt;
}


/**
 * 处理用户权限，根据url返回中文显示
 * @return array
 */
function handle_qxlist(){

	$userinfo = XSession::Get('Cur_X_User');
	if ($userinfo['id'] < 1)	return array();

	$priviactionModel = new PWeb_PriviActionModel();
	$qxlist = $priviactionModel->GetList('', '', 0, 0, 'cnname, url');

	foreach ($qxlist['allrow'] as $k=>$v)
	{
		$temp[$v['url']] = $v['cnname'];
	}


	foreach ($userinfo['qxlist'] as $k => $v)
	{
		$userinfo['qxlist'][$k] = array('url' =>$v, 'title' => $temp[$v]);
	}

	/*foreach ($userinfo['qxlist'] as $k1 => $v1) {
		foreach ($qxlist['allrow'] as $k2 => $v2) {
		if ($v1 == $v2['url']) {
		$userinfo['qxlist'][$k1] = array('url' => $v2['url'], 'title' => $v2['cnname'],);
		}
		}
		}*/


	return $userinfo;
}

/*
 * 判断是否手机客户端
 */

function jd_is_mobile() {
	static $is_mobile;

	if ( isset($is_mobile) )
	return $is_mobile;

	if ( empty($_SERVER['HTTP_USER_AGENT']) ) {
		$is_mobile = false;
	} elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false // many mobile devices (all iPhone, iPad, etc.)
	|| strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
	|| strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
	|| strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
	|| strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
	|| strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
	|| strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false ) {
		$is_mobile = true;
	} else {
		$is_mobile = false;
	}

	return $is_mobile;
}



function JumpToMobile(){
	global $G_X;
	//print_r($_SERVER);
	if(false==jd_is_mobile())return ;
	$isPc=JdCookie::Get('pc');
	if($isPc==1)return;

	$isExcept = false;
	//$G_X['ToMobile_except'] 里面的值表示，当访问这些地址时，不需要跳转到移动端
	foreach($G_X['ToMobile_except'] as $url)
	{
		if (stripos($_SERVER['REQUEST_URI'],$url) !== false)
		{
			$isExcept = true;
			break;
		}
	}
	if(!$isExcept)
	{
		foreach($G_X['ToMobile_path'] as $url){
			if(stripos($_SERVER['REQUEST_URI'],$url) !== false){
				header("Location:http://m".COOKIE_DOMAIN.$_SERVER['REQUEST_URI']);
				exit;
			}
		}
		header("Location:http://m".COOKIE_DOMAIN);
		exit;
	}

}

/**
 * 字符串半角和全角间相互转换
 * @param string $str  待转换的字符串
 * @param int    $type  TODBC:转换为半角；TOSBC，转换为全角
 * @return string  返回转换后的字符串
 */
function convertStrType($str, $type) {
	//全角
	$dbc = array(
                '０' , '１' , '２' , '３' , '４' ,  
                '５' , '６' , '７' , '８' , '９' ,
                'Ａ' , 'Ｂ' , 'Ｃ' , 'Ｄ' , 'Ｅ' ,  
                'Ｆ' , 'Ｇ' , 'Ｈ' , 'Ｉ' , 'Ｊ' ,
                'Ｋ' , 'Ｌ' , 'Ｍ' , 'Ｎ' , 'Ｏ' ,  
                'Ｐ' , 'Ｑ' , 'Ｒ' , 'Ｓ' , 'Ｔ' ,
                'Ｕ' , 'Ｖ' , 'Ｗ' , 'Ｘ' , 'Ｙ' ,  
                'Ｚ' , 'ａ' , 'ｂ' , 'ｃ' , 'ｄ' ,
                'ｅ' , 'ｆ' , 'ｇ' , 'ｈ' , 'ｉ' ,  
                'ｊ' , 'ｋ' , 'ｌ' , 'ｍ' , 'ｎ' ,
                'ｏ' , 'ｐ' , 'ｑ' , 'ｒ' , 'ｓ' ,  
                'ｔ' , 'ｕ' , 'ｖ' , 'ｗ' , 'ｘ' ,
                'ｙ' , 'ｚ' , '－' , '　'  , '：' ,
                '．' , '，' , '／' , '％' , '＃' ,
                '！' , '＠' , '＆' , '（' , '）' ,
                '＜' , '＞' , '＂' , '＇' , '？' ,
                '［' , '］' , '｛' , '｝' , '＼' ,
                '｜' , '＋' , '＝' , '＿' , '＾' ,
                '￥' , '￣' , '｀'
		);
	//半角
	$sbc = array( 
                '0', '1', '2', '3', '4',  
                '5', '6', '7', '8', '9',
                'A', 'B', 'C', 'D', 'E',  
                'F', 'G', 'H', 'I', 'J',
                'K', 'L', 'M', 'N', 'O',  
                'P', 'Q', 'R', 'S', 'T',
                'U', 'V', 'W', 'X', 'Y',  
                'Z', 'a', 'b', 'c', 'd',
                'e', 'f', 'g', 'h', 'i',  
                'j', 'k', 'l', 'm', 'n',
                'o', 'p', 'q', 'r', 's',  
                't', 'u', 'v', 'w', 'x',
                'y', 'z', '-', ' ', ':',
                '.', ',', '/', '%', ' #',
                '!', '@', '&', '(', ')',
                '<', '>', '"', '\'','?',
                '[', ']', '{', '}', '\\',
                '|', '+', '=', '_', '^',
                '￥','~', '`'
		);
	if($type == 'TODBC'){
    	return str_replace( $sbc, $dbc, $str );  //半角到全角
    }elseif($type == 'TOSBC'){
    	return str_replace( $dbc, $sbc, $str );  //全角到半角
    }else{
    	return $str;
    }
}

/**
 * 
 * 目录删除
 * @param $dir
 */
function delDir($dir)
{
	if(!is_dir($dir))
	{
		return;
	}
	$res = opendir($dir);	
	while($name = readdir($res))
	{
		if($name == '.' || $name == '..') continue;
		$path = $dir . '/' . $name;
		if(is_dir($path))
		{
			delDir($path);
		}
		else 
		{
			unlink($path);
		}
	}
	closedir($res);
	rmdir($dir);
}

/**
 *
 * @param string $url        	
 * @param array $data
 *        	key,value必须成对，不能含子array
 * @return @return array msgcode like 200,404
 *         msg return value
 */
function http_post_array($url, array $data) {
	$poststr = http_build_query ( $data );
	$ch = curl_init ();
	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); //添加HTTP版本信息
	curl_setopt ( $ch, CURLOPT_POST, 1 );
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $poststr );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt ( $ch, CURLOPT_TIMEOUT, 10 ); // 10s执行时间
	$return_content = curl_exec ( $ch );
	$return_code = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
	$return_errmsg = '';
	if (curl_errno ( $ch )) {
		$return_errmsg = curl_error ( $ch ); // 捕抓异常
	}
	curl_close ( $ch );
	
	return array (
			'msgcode' => $return_code,
			'msg' => json_decode($return_content,true),
			'errmsg' => $return_errmsg 
	);
}
	
	










?>
