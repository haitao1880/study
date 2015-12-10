<?php
/**
 * 根据手机号区分操作系统
 */

class Subsystem{

	//手机号
	protected $phones = array();

	//手机号文件路径
	protected $file;

	//解析的日志所在时间,默认是前一天('2015-03-02');
	protected $date;

	//匹配的文件
	protected $access = 'index_welcome.txt';

	//目标文件路径
	protected $log_path = '/home/upload/nginxlog/';
	protected $web = '/web*/www/';
	protected $station;

	//安卓数量
	protected $android;
	//ios数量
	protected $ios;

	/**
	 * 构造函数
	 * @param string $file    手机号码文件路径
	 * @param date   $date    要匹配的日期
	 * @param string $station 车站 如 'jn'
	 */
	public function __construct($file,$date,$station){
		if (!$date) {
			$date = date('Y-m-d',strtotime('-1 day'));
		}
		$this->file = $file;
		$this->date = $date;
		$this->station = $station;
	}


	public function Resolve(){
		$this->getphones($this->file);
		$this->SystemRegNumber($this->phones);
		return $this->getregnum();

	}

	/**
	 * 从文件中获取手机号
	 * @param  string $file 文件路径
	 */
	protected function getphones($file){
		$phones = file_get_contents($file);
		$this->phones = explode(PHP_EOL, $phones);
	}

	/**
	 * 按手机号解析每个系统的注册人数
	 */
	protected function SystemRegNumber($phones){

		if (!is_array($phones) || empty($phones)) {
			return;
		}

		//定义所要匹配文件的路径
		$date = date('Y_m_d',strtotime($this->date));
		$filedir = $this->log_path.$this->station.'/'.$date.$this->web.$this->access;

		//分系统循环匹配手机号所在的操作系统
		foreach($phones as $k=>$v){
			$s = substr($v,0,11);
			$conn = 'sudo -u root -S cat '.$filedir.' | awk \'{if($7 ~ /index\/welcome\?'.$s.'/) exit; }END{print }\' | grep -v -i "window" | grep -i -o "android \| mac "';
			//$conn = "cat $filedir | awk '{if($7 ~ /index\/welcome\?$v/) exit; }END{print }' | grep -v -i 'window' | grep -i 'mac ' -c";
			//
			//$s = substr($v,0,11);
			//$conn = "sudo -u root -S cat $filedir | grep -i 'get /index/welcome?$s' | grep -i -v 'window' | grep -i -o 'mac \| android' |sort|uniq";
			/*echo $conn ;
			exit;*/
			// echo $v.PHP_EOL;
			// $system = array();
			$system = shell_exec($conn);
			//exec($conn,$system);
			//file_put_contents('./rrrr.txt',$conn.PHP_EOL,FILE_APPEND);
			
			// exit;
			echo $system;
			if (preg_match('/android/i',$system)) {
				$this->android += 1;
			}

			if (preg_match('/mac/i',$system)) {
				$this->ios += 1;
			}
			echo $this->android.'----------'.$this->ios.PHP_EOL;
		}
	}

	/**
	 * 获取每个系统的注册人数
	 */
	protected function getregnum(){
		$reg['android'] = $this->android;
		$reg['ios'] = $this->ios;
		return $reg;
	}

}

$file = './3.2/zb_phone.txt';
$m= new Subsystem($file,'2015-03-02','jn');
$data = $m -> Resolve();
file_put_contents('./03-02-zb.txt','android:'.$data['android'].'   ios:'.$data['ios']);


?>