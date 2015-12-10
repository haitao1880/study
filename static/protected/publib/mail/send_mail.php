<?php
/**
 * @author Kevin
 * @version $Id: send_mail.php 10 2014-06-13 07:03:39Z tony_ren $
 */

defined('SEND_MAIL_BY_TYPE_LANG_DEBUG') or define('SEND_MAIL_BY_TYPE_LANG_DEBUG', false);

require_once('class.phpmailer.php');

//$type 'active'
//$lang 'en_us', 'zh_cn'
function send_mail_by_type_lang($type='', $lang='en_us', $status='', array $data) {
	//print_r($data);
	//exit;
	global $_CFG;
	
	$user = isset($data['user']) ? $data['user'] : '';
	$mail = isset($data['mail']) ? $data['mail'] : '';
	$link = isset($data['link']) ? $data['link'] : '';
	$password = isset($data['password']) ? $data['password'] : '';
	
	$allow_type = $_CFG['mail']['allow_type'];
	$allow_lang = $_CFG['mail']['allow_lang'];
	
	$project_name = isset($_CFG['project']['name']) ? $_CFG['project']['name'] : 'XtraTV';
	
	$m_subject_org = ' -- ';
	$m_subject_org .= $project_name;
	
	$type = strtolower($type);
	if (! in_array($type, $allow_type)) {
		//echo "111111111";
		return false;
	}
	
	$lang = strtolower($lang);
	if (! in_array($lang, $allow_lang)) {
		//echo "222222222";
		return false;
	}
	
	$mail = strtolower($mail);
	if (!is_email($mail)) {
		//echo "333333333";
		return false;
	}
	
	$mail_subject = '';
	$mail_subject .= $_CFG['mail']['subject'][$type][$lang] . ' ';
	if ($status == 'succ') {
		$mail_subject .= ' '. $_CFG['mail']['subject']['succ'][$lang] . ' ';
	}
	$mail_subject .= $m_subject_org;
	
	if ($status == 'succ') {
		$mail_tpl = API_CUR_PATH . 'views/email/' . $type . '_succ_' . $lang . '.html';
	}else {
		$mail_tpl = API_CUR_PATH . 'views/email/' . $type . '_' . $lang . '.html';
	}
	//var_dump($mail_tpl);
	if (!file_exists($mail_tpl)) {
		//echo "444444444";
		return false;
	}
	
	if (!isset($_CFG['mail']['smtp']['hostname']) 
			|| !isset($_CFG['mail']['smtp']['auth']) 
			|| !isset($_CFG['mail']['smtp']['host'])
			|| !isset($_CFG['mail']['smtp']['port'])
			|| !isset($_CFG['mail']['smtp']['user'])
			|| !isset($_CFG['mail']['smtp']['pass'])
			|| !isset($_CFG['mail']['smtp']['mail'])
			|| !isset($_CFG['mail']['smtp']['sender'])) {
				//echo "555555555";
		return false;
	}
	
		//发邮件
	$phpmailer = new PHPMailer();
	$phpmailer->IsSMTP(); // telling the class to use SMTP
	$phpmailer->CharSet = 'UTF-8';
	$phpmailer->Hostname = $_CFG['mail']['smtp']['hostname'];
	if (SEND_MAIL_BY_TYPE_LANG_DEBUG) {
		$phpmailer->SMTPDebug  = 2;
	}
	$phpmailer->SMTPAuth = $_CFG['mail']['smtp']['auth'];
	$phpmailer->Host = $_CFG['mail']['smtp']['host'];
	$phpmailer->Port = $_CFG['mail']['smtp']['port'];
	$phpmailer->Username = $_CFG['mail']['smtp']['user'];
	$phpmailer->Password = $_CFG['mail']['smtp']['pass'];
	$phpmailer->AddReplyTo($_CFG['mail']['smtp']['mail'], $_CFG['mail']['smtp']['sender']);
	$phpmailer->SetFrom($_CFG['mail']['smtp']['mail'], $_CFG['mail']['smtp']['sender']);
	
	$phpmailer->AddAddress($mail, $user);
	$phpmailer->Subject = $mail_subject;
	$phpmailer->AltBody = 'To view the message, please use an HTML compatible email viewer!';
	
	//替换变量
	$support_url = isset($_CFG['home_url']) ? $_CFG['home_url'] : '';
	$msg_html = file_get_contents($mail_tpl);
	$msg_html = str_replace('<!--{user}-->', $user, $msg_html);
	$msg_html = str_replace('<!--{usermail}-->', $mail, $msg_html);
	$msg_html = str_replace('<!--{link}-->', $link, $msg_html);
	$msg_html = str_replace('<!--{password}-->', $password, $msg_html);
	$msg_html = str_replace('<!--{project}-->', $_CFG['mail']['smtp']['sender'], $msg_html);
	$msg_html = str_replace('<!--{support_mail}-->', $_CFG['mail']['smtp']['mail'], $msg_html);
	$msg_html = str_replace('<!--{support_url}-->', $support_url, $msg_html);
	$msg_html = str_replace('<!--{date}-->', date('Y-m-d'), $msg_html);
	$msg_html = str_replace('<!--{imgages_host}-->', $_CFG['mail']['images_dir'], $msg_html);
	
	$phpmailer->MsgHTML($msg_html);
	
//	$mail_images = array('gif-0081.gif', 'reg_line.jpg', 'reg_r2_c4.jpg', 'reg_r2_c21.jpg');
//	foreach ($mail_images as $val) {
//		$phpmailer->AddAttachment(ROOT_DIR . 'include/email/images/' . $val, 'images/'.$val);      // attachment
//	}
	//echo "aaaaaaaaaa";
	return $phpmailer->Send();
}