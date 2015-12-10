<?php
/**
 * 分离济南、平度、即墨ac日志
 */

class Cut_Ac_File
{
	protected $old_log_dir = '/home/upload/aclog/sd/jn/';
	protected $old_log_file; //被分割ac日志
	protected $temp_log_file; //临时日志
	protected $bak_log_file;

	protected $new_log_dir = '/home/upload/aclog/sd/fenge/';
	protected $new_log_file; //分割后要追加的日志


	public function __construct()
	{

	}

	/**
	 * 设置文件路径
	 */
	protected function SetDir($is_all_day = 0)
	{
		if (!$is_all_day) {
			$date = date('Y-m-d-H',strtotime("-1 hour"));			
		}else{
			$date = date('Y-m-d');
		}

		$this->old_log_file = $this->old_log_dir.'aclog'.$date.'.txt';
		$this->new_log_file = $this->new_log_dir.'aclog'.$date.'.txt';
		$this->temp_log_file = $this->old_log_dir.'temp'.$date.'.txt';
		$this->bak_log_file = $this->old_log_file.'.bak';
	}

	/**
	 * 分割 
	 */
	public function condition($acip,$is_all_day=0)
	{
		$this->SetDir($is_all_day);
		$acip = str_replace(array('.',','),array('\.','\|'),$acip);
		$sh1 = "cat $this->old_log_file | grep \"$acip\" >> $this->new_log_file";
		$sh2 = "cat $this->old_log_file | grep -v \"$acip\" >> $this->temp_log_file";
		$sh3 = "mv $this->old_log_file $this->bak_log_file"; //备份
		$sh4 = "mv $this->temp_log_file $this->old_log_file"; 
		passthru($sh1);
		passthru($sh2);
		passthru($sh3);
		passthru($sh4);
	}
}

//平度、即墨
$acip = '10.0.56.,10.0.57.,10.0.58.,10.0.59.,10.0.60.,10.0.61.,10.0.62.,10.0.63.,10.0.40.,10.0.41.,10.0.42.,10.0.43.,10.0.44.,10.0.45.,10.0.46.,10.0.47.';

//$cur_hour = date('H');
$cur_hour = '06';

$obj = new Cut_Ac_File();
//每天06点的时候需要分割大日志
if ($cur_hour == '06') {
	$obj->condition($acip,1);
}
$obj->condition($acip);