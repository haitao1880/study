<?php
class Psys_MemberUserModel extends Psys_AbstractModel
{
	public function __construct()
	{
		parent::__construct();
	}
	//会员查询列表
	public function SearchList($username,$email,$regtime,$logintime,$page,$pagesize){
		if (!$username) {
			$username='';
		}else{
			$username = 'username=\''.$username.'\'';
		}

		if (!$email) {
			$email='';
		}else{
			if ($username) {
				$email = ' and email=\''.$email.'\'';
			} else {
				$email = ' email=\''.$email.'\'';
			}
		}

		if (!$regtime) {
			$regtime='';
		}else{
			if (!$username && !$email) {
				$regtime = ' from_unixtime(regtime,\'%Y-%m-%d\')=\''.$regtime.'\'';
			} else {
				$regtime = ' and from_unixtime(regtime,\'%Y-%m-%d\')=\''.$regtime.'\'';
			}
		}

		if (!$logintime) {
			$logintime='';
		}else{
			if (!$username && !$email && !$regtime) {
				$logintime = ' from_unixtime(logintime,\'%Y-%m-%d\')=\''.$logintime.'\'';
			} else {
				$logintime = ' and from_unixtime(logintime,\'%Y-%m-%d\')=\''.$logintime.'\'';
			}
		}
		$m = new Psys_MemberUserRule();
		$list = $m->SearchList($username,$email,$regtime,$logintime,$page,$pagesize);
		return $list;
	}
	
}

?>