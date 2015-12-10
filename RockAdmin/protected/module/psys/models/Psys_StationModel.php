<?php
class Psys_StationModel extends Psys_AbstractModel
{
	public function __construct()
	{
		parent::__construct();
	}
	
	private $path = '/home/upload/nginxlog/';
	private $LA1 = '/web1/www/appui_wonaonao_access.log';
	private $LA2 = '/web2/www/appui_wonaonao_access.log';
	private $LM1 = '/web1/www/m_wonaonao_access.log';
	private $LM2 = '/web2/www/m_wonaonao_access.log';
	
	private $RA = '/web*/www/appui_wonaonao_record.log';
	private $RM = '/web*/www/m_wonaonao_record_**.log';
	
	//前置流程统计
	function webCount($date,$station)
	{

		if($station == 1 || $station == 2){
			$dir = 'qdn/';
			$qr = $station == 1 ? ' | grep -v "222.43" ' : ' | grep "222.43" ';
			if ($station == 2) {
				return $this->webCount_2($date,$dir,$qr);
			}
		}else{
			$dir = 'jn/';
			$qr = '';
		}
		$time = $date ? date('Y_m_d',strtotime($date)) :date('Y_m_d',strtotime('-1 day'));
		$file_web = ' cat '.$this->path.$dir.$time.$this->LM1.' '.$this->path.$dir.$time.$this->LM2.$qr;
		$file_app = ' cat '.$this->path.$dir.$time.$this->LA1.' '.$this->path.$dir.$time.$this->LA2.$qr;
		$result = array();
		
		$sys = array('ios','android','andriod','else','wp');
		//首页
		$tp_index = $station == 1 ? '/index/index' : '/?';
		$tp_index2 = $station == 1 ? 'mac=.*&t' : 'usermac=.*&s';
		$sh = $file_web.'|  grep -i "get '.$tp_index.'" | grep " 200 " | grep -i "window" | grep -o "'.$tp_index2.'" | sort | uniq | grep "&" -c';
		exec($sh,$arr_index_e);
		$sh = $file_web.'|  grep -i "get '.$tp_index.'" | grep " 200 " | grep -i "mac " | grep -o "'.$tp_index2.'" | sort | uniq | grep "&" -c';
		exec($sh,$arr_index_i);
		$sh = $file_web.'|  grep -i "get '.$tp_index.'" | grep " 200 " | grep -i "android" | grep -o "'.$tp_index2.'" | sort | uniq | grep "&" -c';
		exec($sh,$arr_index_a);
		$index['ios'] = $arr_index_i[0];
		$index['android'] = $arr_index_a[0];
		$index['else'] = $arr_index_e[0];
		
		$array = array('ios'=>'0','android'=>'0','else'=>'0','name'=>'首页');
		$index += $array; 
		$result[] = $index;
		//注册
		$sh = $file_web.' | grep -i "post /member/register" | grep " 200 6" | grep -i -o "mac \|android\|window" |awk \'BEGIN{OFS="/"}{count[$0]++}END{for(name in count)print name,count[name]}\'';
	
		exec($sh,$arr_reg);
		foreach ($arr_reg as $m=>$n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode('/', $n);
			$tep['sys'] = strtolower(rtrim($tep['sys'])) == 'mac' ? 'ios' : $tep['sys'];
			$tep['sys'] = strtolower(rtrim($tep['sys'])) == 'window' ? 'else' : $tep['sys'];
			if(in_array(strtolower(rtrim($tep['sys'])),$sys)){
				$register[strtolower($tep['sys'])] += $tep['dtime'];
			}
			
		}
		$array = array('ios'=>'0','android'=>'0','else'=>'0','name'=>'注册');
		$register += $array;
		$result[] = $register;
		
		//欢迎页
		$sh = $file_web.' | grep -i "get /index/welcome"  | grep " 200 " | grep -i "window" | cut -d " " -f 7 | cut -d "?" -f 2 | grep -P -o "[0-9]{11}"  | sort | uniq | grep "1" -c';
		exec($sh,$arr_wel_e);
		$sh = $file_web.' | grep -i "get /index/welcome" | grep " 200 " | grep -i "mac " | cut -d " " -f 7 | cut -d "?" -f 2 | grep -P -o "[0-9]{11}"  | sort | uniq | grep "1" -c';
		exec($sh,$arr_wel_i);
		$sh = $file_web.' | grep -i "get /index/welcome" | grep " 200 " | grep -i "android" | cut -d " " -f 7 | cut -d "?" -f 2 | grep -P -o "[0-9]{11}"  | sort | uniq | grep "1" -c';
		exec($sh,$arr_wel_a);
		
		$welcome['ios'] = $arr_wel_i[0];
		$welcome['android'] = $arr_wel_a[0];
		$welcome['else'] = $arr_wel_e[0];
		$array = array('ios'=>'0','android'=>'0','else'=>'0','name'=>'全屏广告');
		$welcome += $array; 
		$result[] = $welcome;
		/*
		//弹出下载提示
		$sh = $file_web.'| grep " 200 " | grep -i "get /record.php?" | grep "/alert" | cut -d " " -f 7 | cut -d "/" -f 4 | cut -d "?" -f 2 | awk \'BEGIN{FS=OFS="/"}{count[$1]++}END{for(name in count)print name,count[name]}\'';
		exec($sh,$arr_alert);
		foreach ($arr_alert as $m=>$n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode('/', $n);
			if(in_array(strtolower($tep['sys']),$sys)){
				$alert[strtolower($tep['sys'])] = $tep['dtime'];
			}
		}
		$array = array('ios'=>'0','android'=>'0','else'=>'0','name'=>'弹出提示');
		$alert += $array; 
		$result[] = $alert;
		//下载人数
		$sh = $file_web.' | grep " 200 " | grep -i "get /record.php?downapp" | cut -d " " -f 7 | cut -d "/" -f 4 | cut -d "?" -f 2 | awk \'BEGIN{FS=OFS="/"}{count[$1]++}END{for(name in count)print name,count[name]}\'';
		exec($sh,$arr_down);
		foreach ($arr_down as $m=>$n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode('/', $n);
			if(in_array(strtolower($tep['sys']),$sys)){
				$down[strtolower($tep['sys'])] = $tep['dtime'];
			}
		}
		$array = array('ios'=>'0','android'=>'0','else'=>'0','name'=>'下载');
		$down += $array;
		$result[] = $down;
		*/
		//首次打开
		$sh = $file_app.'| grep " 200 " | grep -i "record.php?open/first" |cut -d " " -f 7 | cut -d "/" -f 4 | cut -d "?" -f 2 | awk \'BEGIN{FS=OFS="/"}{count[$1]++}END{for(name in count)print name,count[name]}\'';
		exec($sh,$arr_open);
		foreach ($arr_open as $m=>$n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode('/', $n);
			if(in_array(strtolower($tep['sys']),$sys)){
				$open[strtolower($tep['sys'])] = $tep['dtime'];
			}
		}
		$array = array('ios'=>'0','android'=>'0','else'=>'0','name'=>'首次打开');
		$open += $array;
		$result[] = $open;
		
		return $result;
	}
	//前置流程青岛北（或之类的）
	function webCount_2($date,$dir,$qr)
	{
		
		$time = $date ? date('Y_m_d',strtotime($date)) :date('Y_m_d',strtotime('-1 day'));
		$file_web = ' cat '.$this->path.$dir.$time.$this->LM1.' '.$this->path.$dir.$time.$this->LM2.$qr;
		$file_app = ' cat '.$this->path.$dir.$time.$this->LA1.' '.$this->path.$dir.$time.$this->LA2.$qr;
		$result = array();
		$sys = array('ios','android','else','wp');
		
		//首页
		$sh = $file_web.'|  grep -i "get /index/index" | grep " 200 " | grep -i "window" | grep -o "mac=.*&t" | sort | uniq | grep "&" -c';
		exec($sh,$arr_index_e);
		$sh = $file_web.'|  grep -i "get /index/index" | grep " 200 " | grep -i "mac " | grep -o "mac=.*&t" | sort | uniq | grep "&" -c';
		exec($sh,$arr_index_i);
		$sh = $file_web.'|  grep -i "get /index/index" | grep " 200 " | grep -i "android" | grep -o "mac=.*&t" | sort | uniq | grep "&" -c';
		exec($sh,$arr_index_a);
		$index['ios'] = $arr_index_i[0];
		$index['android'] = $arr_index_a[0];
		$index['else'] = $arr_index_e[0];
		$array = array('ios'=>'0','android'=>'0','else'=>'0','name'=>'首页');
		$index += $array;
		$result[] = $index;
		//注册
	
		$sh = $file_web.' | grep -i "post /member/register" | grep " 200 6" | grep -i -o "mac \|android\|window" |awk \'BEGIN{OFS="/"}{count[$0]++}END{for(name in count)print name,count[name]}\'';
	
		exec($sh,$arr_reg);
		foreach ($arr_reg as $m=>$n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode('/', $n);
			$tep['sys'] = strtolower(rtrim($tep['sys'])) == 'mac' ? 'ios' : $tep['sys'];
			$tep['sys'] = strtolower(rtrim($tep['sys'])) == 'window' ? 'else' : $tep['sys'];
			if(in_array(strtolower(rtrim($tep['sys'])),$sys)){
				
				$register[strtolower($tep['sys'])] += $tep['dtime'];
			}
			
		}
		$array = array('ios'=>'0','android'=>'0','else'=>'0','name'=>'注册');
		$register += $array;
		$result[] = $register;
		//欢迎页
		$sh = $file_web.' | grep -i "get /index/welcome" | grep "fromRegister" | grep " 200 " | grep -i "window" | cut -d " " -f 7 | cut -d "?" -f 2 | grep -P -o "[0-9].{11}"  | sort | uniq | grep "1" -c';
		exec($sh,$arr_wel_e);
		$sh = $file_web.' | grep -i "get /index/welcome" | grep "fromRegister" | grep " 200 " | grep -i "mac " | cut -d " " -f 7 | cut -d "?" -f 2 | grep -P -o "[0-9].{11}"  | sort | uniq | grep "1" -c';
		exec($sh,$arr_wel_i);
		$sh = $file_web.' | grep -i "get /index/welcome" | grep "fromRegister" | grep " 200 " | grep -i "android" | cut -d " " -f 7 | cut -d "?" -f 2 | grep -P -o "[0-9].{11}"  | sort | uniq | grep "1" -c';
		exec($sh,$arr_wel_a);
		
		$welcome['ios'] = $arr_wel_i[0];
		$welcome['android'] = $arr_wel_a[0];
		$welcome['else'] = $arr_wel_e[0];
		$array = array('ios'=>'0','android'=>'0','else'=>'0','name'=>'全屏广告');
		$welcome += $array;
		$result[] = $welcome;
		//弹出下载提示
		/*
		$sh = $file_web.'| grep " 200 " | grep -i "get /record.php?" | grep "/alert" | cut -d " " -f 7 | cut -d "/" -f 4 | cut -d "?" -f 2 | awk \'BEGIN{FS=OFS="/"}{count[$1]++}END{for(name in count)print name,count[name]}\'';
		exec($sh,$arr_alert);
		foreach ($arr_alert as $m=>$n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode('/', $n);
			if(in_array(strtolower($tep['sys']),$sys)){
				$alert[strtolower($tep['sys'])] = $tep['dtime'];
			}
		}
		$array = array('ios'=>'0','android'=>'0','else'=>'0','name'=>'弹出提示');
		$alert += $array;
		$result[] = $alert;
		//下载人数
		$sh = $file_web.' | grep " 200 " | grep -i "get /record.php?downapp" | cut -d " " -f 7 | cut -d "/" -f 4 | cut -d "?" -f 2 | awk \'BEGIN{FS=OFS="/"}{count[$1]++}END{for(name in count)print name,count[name]}\'';
		exec($sh,$arr_down);
		foreach ($arr_down as $m=>$n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode('/', $n);
			if(in_array(strtolower($tep['sys']),$sys)){
				$down[strtolower($tep['sys'])] = $tep['dtime'];
			}
		}
		$array = array('ios'=>'0','android'=>'0','else'=>'0','name'=>'下载');
		$down += $array;
		$result[] = $down;
		*/
		//首次打开
		$sh = $file_app.'| grep " 200 " | grep -i "record.php?open/first" |cut -d " " -f 7 | cut -d "/" -f 4 | cut -d "?" -f 2 | awk \'BEGIN{FS=OFS="/"}{count[$1]++}END{for(name in count)print name,count[name]}\'';
		exec($sh,$arr_open);
		foreach ($arr_open as $m=>$n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode('/', $n);
			if(in_array(strtolower($tep['sys']),$sys)){
				$open[strtolower($tep['sys'])] = $tep['dtime'];
			}
		}
		$array = array('ios'=>'0','android'=>'0','else'=>'0','name'=>'首次打开');
		$open += $array;
		$result[] = $open;
		return $result;
	}
	//新流程
	public function webCountNew($stationid,$page='1',$pagesize='15')
	{
		$db = new Psys_StationRule();
		$data = $db->webCountNew($stationid,$page,$pagesize);
		return $data;
		
	}

	//流程导出
	public function webcountexport($stationid)
	{
		$db = new Psys_StationRule();
		$data = $db->webcountexport($stationid);
		return $data;
		
	}

	//流程统计汇总
	public function webCountAll($page='1',$pagesize='15')
	{
		$db = new Psys_StationRule();
		$data = $db->webCountAll($page,$pagesize);
		return $data;
	}
	/**
	 * 新版统计（record）<br>
	 * @author Neil Yang
	 * @param string $date
	 * @param int $station
	 * @desc description :<br>
	 * 			_wonaonao.com_record.log一共十个域，以[|cut|]分割=><br>
	 * 			1.ip,2.date,3.station,4.sid,5.method,6.url,7.referr,8.data,9.useragent,10.return;
	 */
	function NewLogCount($date,$station)
	{
		$s_conf = $this->GetOne(array('id'=>$station),'logfile,logip,ifconf,ap','station');
		
		$dir = $s_conf['logfile'].'/';
		$qr = $s_conf['logip']? ($s_conf['ifconf']? ' | grep "'.$s_conf['logip'].'" ' : ' | grep -v "'.$s_conf['logip'].'" ') : '';
		
		$time = $date ? date('Y_m_d',strtotime($date)) :date('Y_m_d',strtotime('-1 day'));
		$file_web = ' cat '.$this->path.$dir.$time.$this->RM.' '.$qr;
		$file_app = ' cat '.$this->path.$dir.$time.$this->RA.' '.$qr;
		
		switch ($s_conf['ap']){
			case 1 :
				$tp_mac = 'mac=';
				$mac_s = '4,12';
				break;
			case 2 :
				$tp_mac = 'usermac=';
				$mac_s = '8,16';
				break;
			default:
		}
		$sys = array('ios','android','else');
		
		
		/***** 4个页面的UV *****/
		//广告页（首页）
		$sh = $file_web.'| awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /index\/index/ ) print $8,"[|cut|]",$9}\' | 
					awk -F  "\[\|cut\|\]" \'{if(index($1,"'.$tp_mac.'")) $1=substr($1,index($1,"'.$tp_mac.'")+'.$mac_s.');else $1="-";print $1,"[|cut|]",$2}\' | 
					awk -F "\[\|cut\|\]" \'{if(tolower($2) ~ /window/) $2="else"; else if(tolower($2) ~ /mac /) $2="ios";else if(tolower($2) ~ /android/) $2="android"; else $2="-";print}\'| 
					sort | uniq | awk \'{count[$2]++}END{for(name in count)print name,count[name]}\' ';
		
		exec($sh , $arr_index);
		
		foreach ($arr_index as $m=>$n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode(' ', $n);
			if(in_array(strtolower($tep['sys']),$sys)){
				$index_uv[strtolower($tep['sys'])] = $tep['dtime'];
			}
		}
		//注册页
		$sh = $file_web. '| awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /index\/register/ ) print $7,"[|cut|]",$9}\' | 
					awk -F  "\[\|cut\|\]" \'{if(index($1,"'.$tp_mac.'")) $1=substr($1,index($1,"'.$tp_mac.'")+'.$mac_s.');else $1="-";print $1,"[|cut|]",$2}\' | 
					awk -F "\[\|cut\|\]" \'{if(tolower($2) ~ /window/) $2="else"; else if(tolower($2) ~ /mac /) $2="ios";else if(tolower($2) ~ /android/) $2="android"; else $2="-";print}\'| 
					sort | uniq | awk \'{count[$2]++}END{for(name in count)print name,count[name]}\' ';

		exec($sh , $arr_reg);
		
		foreach ($arr_reg as $m=>$n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode(' ', $n);
			if(in_array(strtolower($tep['sys']),$sys)){
				$reg_uv[strtolower($tep['sys'])] = $tep['dtime'];
			}
		}
		//欢迎页
		$sh = $file_web.'| awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /index\/welcome/ ) print $8,"[|cut|]",$9}\' |  
					grep -P  "\d{11}" | awk -F "\[\|cut\|\]" \'{if(tolower($2) ~ /window/) $2="else"; else if(tolower($2) ~ /mac /) $2="ios";else if(tolower($2) ~ /android/) $2="android"; else $2="-";print}\'| 
					sort | uniq | awk \'{count[$2]++}END{for(name in count)print name,count[name]}\' ';
		
		exec($sh,$arr_wel);
		
		foreach ($arr_wel as $m=>$n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode(' ', $n);
			if(in_array(strtolower($tep['sys']),$sys)){
				$wel_uv[strtolower($tep['sys'])] = $tep['dtime'];
			}
		}
		//sindex
		$sh = $file_web.'| awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /index\/sindex/ ) print $7,"[|cut|]",$9}\' |
					grep -P  "\d{11}" | awk -F "\[\|cut\|\]" \'{if(tolower($2) ~ /window/) $2="else"; else if(tolower($2) ~ /mac /) $2="ios";else if(tolower($2) ~ /android/) $2="android"; else $2="-";print}\'|
					sort | uniq | awk \'{count[$2]++}END{for(name in count)print name,count[name]}\' ';
		
		exec($sh,$arr_sindex);
		foreach ($arr_sindex as $m=>$n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode(' ', $n);
			if(in_array(strtolower($tep['sys']),$sys)){
				$sindex_uv[strtolower($tep['sys'])] = $tep['dtime'];
			}
		}
		/**** 入口页广告统计 ******/
		$sh = $file_web.'| awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /record.php/ &&  tolower($7)  ~ /index\/index/ && tolower($8)  ~ /ad\/show/)   print $8}\' | awk \'BEGIN{FS=OFS="/"}{count[$3"/"$4]++}END{for(name in count)print name,count[name]}\'';
		exec($sh,$arr_adshow);
		foreach ($arr_adshow as $m=>$n)
		{
			$tep = array();
			list($tep['detail'],$tep['sys'],$tep['dtime']) = explode(' ', $n);
			if(in_array(strtolower($tep['sys']),$sys)){
				$ad_show[strtolower($tep['sys'])] = $tep['dtime'];
			}
		}
		/*****验证码*****/
		$sh = $file_web . '| awk -F "\[\|cut\|\]" \'{if(tolower($5)  ~ /post/  && tolower($6) ~ /member\/againgetphonecode/)   print $10,"[|cut|]",$9}\' | 
						awk -F "\[\|cut\|\]" \'{$1=substr($1,8);if(tolower($2) ~ /window/) $2="else"; else if(tolower($2) ~ /mac /) $2="ios";else if(tolower($2) ~ /android/) $2="android"; else $2="-"; print}\'| 
						awk \'BEGIN{OFS="/"}{count[$1"/"$2]++}END{for(name in count)print name,count[name]}\'';
		exec($sh,$arr_code);
		foreach ($arr_code as $m=>$n)
		{
			$tep = array();
			list($tep['detail'],$tep['sys'],$tep['dtime']) = explode(' ', $n);
			if(in_array(strtolower($tep['sys']),$sys)){
				$code[strtolower($tep['sys'])] = $tep['dtime'];
			}
		}
		/*****注册******/
		$sh = $file_web . '| awk -F "\[\|cut\|\]" \'{if(tolower($5)  ~ /post/  && tolower($6) ~ /member\/register/)   print $10,"[|cut|]",$9}\' | 
						awk -F "\[\|cut\|\]" \'{if(tolower($2) ~ /window/) $2="else"; else if(tolower($2) ~ /mac /) $2="ios";else if(tolower($2) ~ /android/) $2="android"; else $2="-"; print}\'| 
						awk \'BEGIN{OFS="/"}{count[$1"/"$2]++}END{for(name in count)print name,count[name]}\'';
		
		exec($sh,$arr_register);
		
		foreach ($arr_register as $m=>$n)
		{
			$tep = array();
			list($tep['detail'],$tep['sys'],$tep['dtime']) = explode('/', $n);
			if(in_array(strtolower($tep['sys']),$sys)){
				$register[strtolower($tep['sys'])] = $tep['dtime'];
			}
		}
		/******sindex下 页面PV *****/
		$sh = $file_web.' | awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /station|game|movie|music/)  print substr($6,0,index($6,"/")),"[|cut|]",$9}\' |
						awk -F "\[\|cut\|\]" \'{if(tolower($2) ~ /window/) $2="else"; else if(tolower($2) ~ /mac /) $2="ios";else if(tolower($2) ~ /android/) $2="android"; else $2="-"; print}\' |
						awk \'BEGIN{OFS="/"}{count[$1"/"$2]++}END{for(name in count)print name,count[name]}\'';
		
		exec($sh,$arr_spv);
		
		foreach ($arr_spv as $m=>$n)
		{
			$tep = array();
			list($tep['detail'],$tep['sys'],$tep['dtime']) = explode('/', $n);
			if(in_array(strtolower($tep['sys']),$sys)){
				$spv[strtolower($tep['sys'])] = $tep['dtime'];
			}
		}
		/****Train下载弹窗*****/
		$sh = $file_web.'| awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /record.php/ && tolower($8) ~ /alertdown\/trainapp/)   print $8}\' | awk \'BEGIN{FS=OFS="/"}{count[$3"/"$4]++}END{for(name in count)print name,count[name]}\'';
		exec($sh,$arr_trainAlert);
		foreach ($arr_trainAlert as $m=>$n)
		{
			$tep = array();
			list($tep['detail'],$tep['sys'],$tep['dtime']) = explode(' ', $n);
			if(in_array(strtolower($tep['sys']),$sys)){
				$train_alert[strtolower($tep['sys'])] = $tep['dtime'];
			}
		}
		/****Train下载*****/
		$sh = $file_web.'| awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /record.php/ && tolower($8) ~ /downapp\/trainapp/)   print $8}\' | awk \'BEGIN{FS=OFS="/"}{count[$3"/"$4]++}END{for(name in count)print name,count[name]}\'';
		exec($sh,$arr_trainDown);
		foreach ($arr_trainDown as $m=>$n)
		{
			$tep = array();
			list($tep['detail'],$tep['sys'],$tep['dtime']) = explode(' ', $n);
			if(in_array(strtolower($tep['sys']),$sys)){
				$train_down[strtolower($tep['sys'])] = $tep['dtime'];
			}
		}
		/*****第三方APP下载弹窗****/
		$sh = $file_web.'| awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /record.php/ && tolower($8) ~ /alertdown/ && tolower($8) ~ /sindex/)   print $8}\' | awk \'BEGIN{FS=OFS="/"}{count[$2"/"$4]++}END{for(name in count)print name,count[name]}\'';
		exec($sh,$arr_appAlert);
		foreach ($arr_appAlert as $m=>$n)
		{
			$tep = array();
			list($tep['detail'],$tep['sys'],$tep['dtime']) = explode(' ', $n);
			if(in_array(strtolower($tep['sys']),$sys)){
				$app_apert[strtolower($tep['sys'])] = $tep['dtime'];
			}
		}
		/*****第三方APP下载****/
		$sh = $file_web.'| awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /record.php/ && tolower($8) ~ /downapp/ && tolower($8) ~ /sindex/)   print $8}\' | awk \'BEGIN{FS=OFS="/"}{count[$2"/"$4]++}END{for(name in count)print name,count[name]}\'';
		exec($sh,$arr_appDown);
		foreach ($arr_appDown as $m=>$n)
		{
			$tep = array();
			list($tep['detail'],$tep['sys'],$tep['dtime']) = explode(' ', $n);
			if(in_array(strtolower($tep['sys']),$sys)){
				$app_down[strtolower($tep['sys'])] = $tep['dtime'];
			}
		}
		/****时长*****/
		$sh = $file_web.'| awk -F "\[\|cut\|\]" \'{if(tolower($6) ~ /record.php/ && tolower($8) ~ /time\/web/)   print $8}\' | awk \'BEGIN{FS=OFS="/"}{sum[$2"/"$4]+=$3}END{for(name in sum)print name,sum[name]}\'';
		exec($sh,$arr_time);
		foreach ($arr_time as $m=>$n)
		{
			$tep = array();
			list($tep['detail'],$tep['sys'],$tep['dtime']) = explode(' ', $n);
			if(in_array(strtolower($tep['sys']),$sys)){
				$time[strtolower($tep['sys'])] = $tep['dtime'];
			}
		}
	}
	
	/**
	 * @param 车站列表
	 * @return
	 */
	public function picDir($stationid)
	{
		$db = new Psys_StationRule();
		return $db->dirList($stationid);
	}
	/**
	 * 
	 * @param 车站列表
	 * @return 
	 */
	public function station()
	{
		$db = new Psys_StationRule();
		return $db->stationList();
	}
	public function downCount($date,$station)
	{
		
		$qr = $station == 1 ? ' grep "172.1" ' : ' grep "222.43" ';
		
		$c_data = function($time,$time2)
		{
			$grep_apps = 'cat '.$this->file.' | grep ".apk" | grep "'.$time.'" | grep "200 " | grep "/apps/" | '.$qr.' -c';
			$grep_game = 'cat '.$this->file.' | grep ".apk" | grep "'.$time.'" | grep "200 " | grep "/game/" | '.$qr.' -c';
			exec($grep_apps,$arr_a);
			exec($grep_game,$arr_g);
			$res = array('apps'=>$arr_a[0],'game'=>$arr_g[0],'time'=>$time2);
			return $res;
		};
		$return = array();
		if (!$date) {
			for ($i = 0 ; $i < 10 ;$i++)
			{
				$da = date('d/M/Y',time()-24*3600*$i);
				$showd = date('Y-m-d',time()-24*3600*$i);
				$temp_arr = $c_data($da,$showd);
				array_push($return, $temp_arr);
			}
		}else{
			$da = date('d/M/Y',strtotime($date));
			$showd = date('Y-m-d',strtotime($date));
			$temp_arr = $c_data($da,$showd);
			array_push($return, $temp_arr);
		}
		return $return;
	}
	
	/**
	 * app下载详情
	 * @param unknown $date
	 * @return Ambigous <multitype:, multitype:number Ambigous <> >
	 */
	public function downDetail($date,$station)
	{
		$qr = $station == 1 ? ' grep "172.1" ' : ' grep "222.43" ';
		
		$this->SetDb('db-rht_train');
		$time = $date ? date('Y_m_d',strtotime($date)):date('Y_m_d',strtotime('-1 day'));
		
		$file = $this->path.$time.$this->LA1.' '.$this->path.$time.$this->LA2;
		
		$grep = 'cat '.$file.' | grep "apk " | grep " 200 " | '.$qr.'  | cut -d " " -f 7 | cut -d "/" -f 4 | sort | uniq -c';
		exec($grep,$s_data);
		$tep = array();
		foreach ($s_data as $key=>$var)
		{
			$arra = explode(' ', trim($var,' '));
			if (!isset($arra[1]) ||  strchr($arra[1],'TrainApp')) {
				$arra[1] = 1;
			}
			if (is_numeric($arra[1])) {
				$d = $this->GetOne(array('appid'=>$arra[1]),'appname','rht_apps');
			}else{
				$d = $this->GetOne(array('appurl'=>$arra[1]),'appname','rht_apps');
			}
			if (!$d) {
				continue;
			}
			if (!isset($tep[$d['appname']])) {
				
				$tep += array($d['appname'] => $arra[0]);
			}else{
				
				$tep[$d['appname']] += $arra[0];
			}
			
		}
		arsort($tep);
		
		
		return $tep;
		
	}
	//查询记录总数
	public function totalrows()
	{
		$db = new Psys_StationRule();
		return $db->totalrows();
	}
	//新流程查询记录总数
	public function newtotalrows()
	{
		$db = new Psys_StationRule();
		return $db->newtotalrows();
	}
	/**
	 * 每日伙伴数据
	 * @param $date,$stationid
	 * @return $data
	 */
	public function everyday($date,$stationid)
	{
		$db = new Psys_StationRule();
		$lists = $db->Everyday($date,$stationid);
		$data = $lists['detail'];
		
		foreach ($data as &$v){
			if($v['model']=='index'){
				$v['action']='index';
			}
			unset($v['model']);
		}
		$list = array();
		foreach ($data as &$v){
			$v['sys']= strtolower($v['sys']);
			$list[$v['sys']][$v['action']]=$v['dtime'];
		}
		//未下载、转化率
		foreach ($list as &$v){
			$v['undown']=$v['alert']-$v['downapp'];

			$v['convert']=round($v['downapp']/$lists['link'],4)*100;
			$v['convert']=$v['convert']."%";
		}
		$info=array();
		$info['link']=$lists['link'];
		$info['detail']=$list;	
		return $info;
	}
	/**
	 * 注册统计
	 * @param $stationid
	 * @return $data
	 */
	public function regCount($page,$pagesize,$stationid)
	{
		$db = new Psys_StationRule();
		$data = $db->RegInfo($page,$pagesize,$stationid);
		$data = $data['allrow'];
		foreach ($data as $k=>&$v){
		
			$detail = explode(',', $v['postids']);
			foreach ($detail as $m=>$n)
			{
				$tep = explode('/', $n);
				if(isset($tep[1]))
				{
					$v[$tep[0]]=$tep[1];
				}
			}
			unset($v['postids']);
		}
		return $data;
	}
	/**
	 * 导航点击统计
	 * @param $stationid
	 * @return $data
	 */
	public function navhitCount($page,$pagesize,$stationid)
	{
		$db = new Psys_StationRule();
		$data = $db->NavhitInfo($page,$pagesize,$stationid);
		$data = $data['allrow'];
		foreach ($data as $k=>&$v){
	
			$detail = explode(',', $v['postids']);
			foreach ($detail as $m=>$n)
			{
				$tep = explode('/', $n);
				if(isset($tep[1]))
				{
					$v[$tep[0]]=$tep[1];
				}
			}
			unset($v['postids']);
		}
		return $data;
	}
	/**
	 * banner点击统计
	 * @param $stationid
	 * @return $data
	 */
	public function pagehitCount($page,$pagesize,$stationid)
	{
		$db = new Psys_StationRule();
		$data = $db->PagehitInfo($page,$pagesize,$stationid);
		$data = $data['allrow'];
		foreach ($data as $k=>&$v){
		
			$detail = explode(',', $v['postids']);
			foreach ($detail as $m=>$n)
			{
				$tep = explode('/', $n);
				if(isset($tep[1]))
				{
					$v[$tep[0]]=$tep[1];
				}
			}
			unset($v['postids']);
		}
		return $data;
	}
	/**
	 * banner-首页 详情统计
	 * @param $stationid $date
	 * @return $data
	 */
	public function BannerIndex($stationid,$date)
	{
		$db = new Psys_StationRule();
		return $db->BannerIndex($stationid,$date);
	}
	/**
	 * 影视统计
	 * @param $stationid
	 * @return $data
	 */
	public function movieCount($page,$pagesize,$stationid)
	{
		$db = new Psys_StationRule();
		$data = $db->MovieInfo($page,$pagesize,$stationid);
		foreach ($data['allrow'] as $k=>&$v){
			$detail = explode(',', $v['postids']);
			foreach ($detail as $m=>$n)
			{
				$tep = explode('/', $n);
				if(isset($tep[1]))
				{
					$v[$tep[0]]=$tep[1];
				}
			}
			unset($v['postids']);
		}
		return $data;
	}
	/**
	 * 影视详情统计
	 * @param $stationid
	 * @return $data
	 */
	public function moviePlay($date,$stationid)
	{
		$db = new Psys_StationRule();
		$data=$db->MovieDetail($date,$stationid);
		return $data;

	}
	/**
	 * 音乐统计
	 * @param $stationid
	 * @return $data
	 */
	public function musicCount($page,$pagesize,$stationid)
	{
		$db = new Psys_StationRule();
		$data = $db->MusicInfo($page,$pagesize,$stationid);
		$data = $data['allrow'];
		foreach ($data as $k=>&$v){
			$detail = explode(',', $v['postids']);
			foreach ($detail as $m=>$n)
			{
				$tep = explode('/', $n);
				if(isset($tep[1]))
				{
					$v[$tep[0]]=$tep[1];
				}	
			}
			unset($v['postids']);
		}
		return $data;
	}
	/**
	 * 音乐详情统计
	 * @param $stationid
	 * @return $data
	 */
	public function musicPlay($date,$stationid)
	{
		$db = new Psys_StationRule();
		$data=$db->MusicDetail($date,$stationid);
		return $data;

	}
	/**
	 * App统计
	 * @param $stationid
	 * @return $data
	 */
	public function appCount($page,$pagesize,$stationid)
	{
		$db = new Psys_StationRule();
		$data = $db->AppInfo($page,$pagesize,$stationid);
		$data = $data['allrow'];
		foreach ($data as $k=>&$v){
			$detail = explode(',', $v['postids']);
			foreach ($detail as $m=>$n)
			{
				$tep = explode('/', $n);
				$v[$tep[0]]=$tep[1];
			}
			unset($v['postids']);
		}
		return $data;
	}
	/**
	 * web App/game统计
	 * @param $stationid
	 * @return $data
	 */
	public function webAppCount($stationid,$select,$page,$pagesize)
	{
		$db = new Psys_StationRule();
		$select = $select == 1 ? 'game' : 'app';
		$data = $db->webAppInfo($stationid,$select,$page,$pagesize);
		
		foreach ($data['allrows'] as $k=>&$v){
			$detail = explode(',', $v['postids']);
			foreach ($detail as $m=>$n)
			{
				$tep = explode('/', $n);
				$v[$tep[0]]=$tep[1];
			}
			unset($v['postids']);
		}
		foreach ($data['allrows'] as &$v)
		{
			$v += array('date'=>'0','click_banner'=>'0','alert'=>'0','down'=>'0');
		}
		return $data;
	}
	/**
	 * web 游戏、应用详情统计
	 * @param $stationid
	 * @return $data
	 */
	public function webAppDetail($date,$stationid,$select)
	{
		$db = new Psys_StationRule();
		$select = $select == 1 ? 'game' : 'app';
		$data=$db->webAppDetail($date,$stationid,$select);
		foreach ($data['app'] as $k=>&$v){
			$v['detail'] = $this->GetAppName($v['detail']);
			$detail = explode(',', $v['postids']);
			foreach ($detail as $m=>$n)
			{
				$tep = explode('/', $n);
				$v[$tep[0]]=$tep[1];
			}
			unset($v['postids']);
		}
		foreach ($data['app'] as &$v)
		{
			$v += array('alert'=>'0','down'=>'0');
		}
		return $data;
	}
	/**
	 * 下载详情统计
	 * @param $stationid
	 * @return $data
	 */
	public function downapkCount($date,$stationid)
	{
		$db = new Psys_StationRule();
		$data=$db->DownInfo($date,$stationid);
		return $data;
	}
	
	/**
	 * 订票统计
	 * @param $stationid
	 * @return $data
	 */
	public function orderCount($page,$pagesize,$condition)
	{
		$db = new Psys_StationRule();
		switch($condition['order_state'])
		{
			case '1':
				$condition['order_state'] = 'timeout';
				break;
			case '2':
				$condition['order_state'] = 'wait';
				break;
			case '3':
				$condition['order_state'] = 'pay';
				break;
			case '4':
				$condition['order_state'] = 'returned';
				break;
			case '5':
				$condition['order_state'] = 'success';
				break;
		}
		$data = $db->OrderInfo($page,$pagesize,$condition);
		foreach ($data as &$v1)
		{
			$v1['detail'] = json_decode($v1['detail'],true);
		}
		foreach ($data as &$v1)
		{
			switch($v1['state'])
			{
				case 'timeout':
					$v1['state'] = '超时未付';
					break;
				case 'wait':
					$v1['state'] = '待支付';
					break;
				case 'pay':
					$v1['state'] = '已支付';
					break;
				case 'returned':
					$v1['state'] = '已退款';
					break;
				case 'success':
					$v1['state'] = '已成交';
					break;
				case 'finish':
					$v1['state'] = '已成交';
					break;
			}
		}
		foreach ($data as $k1=>&$v1)
		{
			$v1['from'] = $v1['detail']['from_station_name'];
			$v1['to'] = $v1['detail']['to_station_name'];
			$v1['train_number'] = $v1['detail']['train_number'];
			$v1['seat_name'] = $v1['detail']['seat_name'];
			$v1['ticket_count'] = $v1['detail']['ticket_count'];
			unset($v1['detail']);
		}
		return $data;
	}
	
	public function uVisitor($date)
	{
		$do_grep = function($time,$time2)
		{
			$grep = 'cat '.$this->file.' | grep "'.$time.'" | grep "GET /.*/index " | cut -d " " -f 7,1| sort | uniq |cut -d " " -f 2 |  sort | uniq -c';
			exec($grep,$data);
			$res = array();
			$match = array('app'=>0,'game'=>0,'movie'=>0,'music'=>0);
			foreach ($data as $key=>$var)
			{
				$arr = explode(' ', trim($var));
				switch ($arr[1]){
					case '/app/index':
						$res += array('app'=>$arr[0]);
						break;
					case '/game/index':
						$res += array('game'=>$arr[0]);
						break;
					case '/movie/index':
						$res += array('movie'=>$arr[0]);
						break;
					case '/music/index':
						$res += array('music'=>$arr[0]);
						break;
					default:
				}
			}
	
	
			$res += array('time'=>$time2);
	
			$res += $match;
			return $res;
		};
	
		$return = array();
		if (!$date) {
			for ($i = 0 ; $i < 10 ;$i++)
			{
			$da = date('d/M/Y',time()-24*3600*$i);
			$showd = date('Y-m-d',time()-24*3600*$i);
			$temp_arr = $do_grep($da,$showd);
			array_push($return, $temp_arr);
			}
			}else{
			$da = date('d/M/Y',strtotime($date));
			$showd = date('Y-m-d',strtotime($date));
			$temp_arr = $do_grep($da,$showd);
			array_push($return, $temp_arr);
			}
			return $return;
			}
			//榜单点击
			public function albumHit($date)
			{
			$this->SetDb('db-rht_train');
	
		$time = $date?date('d/M/Y',strtotime($date)):'';
	
			$grep = 'cat '.$this->file.' | grep "'.$time.'" | grep "GET /music/albummusic/" | cut -d " " -f 7| sort | uniq -c';
		exec($grep,$data);
	
			$res = array();
	
			foreach ($data as $key=>$var)
			{
			$arr = explode(' ', trim($var));
			$con = explode('/', $arr[1]);
			$ser = array('id'=>$con[3]);
			$data = $this->GetOne($ser,'aname','rht_album');
					$res[$data['aname']]= $arr[0];
		}
			arsort($res);
	
	
			return $res;
			}
	
	//车站日期列表
	function AcList($page,$pagesize=10,$stationid =1)
	{
		$offset = $pagesize * ($page - 1);
		$obj = new Psys_StationRule();
		return $obj->AcList($offset,$pagesize,$stationid);
	}
	function AcTime($date,$stationid)
	{
		$obj = new Psys_StationRule();
		return $obj->AcTime($date,$stationid);
	}
	
	function ApLog($date,$stationid)
	{
		$obj = new Psys_StationRule();
		return $obj->ApLog($date,$stationid);
	}
	
	function ApDetail($date,$stationid)
	{
		$obj = new Psys_StationRule();
		return $obj->ApDetail($date,$stationid);
	}

	/**
	 * 获取监控日志概况
	 */
	public function CheckLogList(){
		$obj = new Psys_StationRule();
		return $obj->CheckLogList();
	}

	/**
	 * 用户连接走势
	 * @param int $stationid 车站id
	 * @param date $sdate    起始日期
	 * @param date $edate    结束日期
	 */
	public function UserConnect($stationid,$sdate,$edate){
		if (!$edate) {
			$where_date = " and date = '$sdate'";
		}else{
			$where_date = " and date BETWEEN '$sdate' AND '$edate'";
		}
		$obj = new Psys_StationRule();
		return $obj->UserConnect($stationid,$where_date);
	}

	/**
	 * 用户注册走势
	 * @param int $stationid 车站id
	 * @param date $sdate    起始日期
	 * @param date $edate    结束日期
	 */
	public function UserReg($stationid,$sdate,$edate){
		$sdate = date('Y_m_d',strtotime($sdate));

		if (!$edate) {
			$where_date = " and date = '$sdate'";
		}else{
			$edate = date('Y_m_d',strtotime($edate));
			$where_date = " and date BETWEEN '$sdate' AND '$edate'";
		}
		$obj = new Psys_StationRule();
		return $obj->UserReg($stationid,$where_date);
	}

	/**
	 * 用户下载走势
	 * @param int $stationid 车站id
	 * @param date $sdate    起始日期
	 * @param date $edate    结束日期
	 */
	public function UserDown($stationid,$sdate,$edate){
		$sdate = date('Y_m_d',strtotime($sdate));

		if (!$edate) {
			$where_date = " and date = '$sdate'";
		}else{
			$edate = date('Y_m_d',strtotime($edate));
			$where_date = " and date BETWEEN '$sdate' AND '$edate'";
		}
		$obj = new Psys_StationRule();
		return $obj->UserDown($stationid,$where_date);
	}

	/**
	 * 转化率走势
	 * @param int $stationid 车站id
	 * @param date $sdate    起始日期
	 * @param date $edate    结束日期
	 */
	public function UserRate($stationid,$sdate,$edate){
		$reg_sdate = date('Y_m_d',strtotime($sdate));

		if (!$edate) {
			$reg_where = " and date = '$reg_sdate'";
			$connect_where = " and date = '$sdate'";
		}else{
			$reg_edate = date('Y_m_d',strtotime($edate));
			$reg_where = " and date BETWEEN '$reg_sdate' AND '$reg_edate'";
			$connect_where = " and date BETWEEN '$sdate' AND '$edate'";
		}
		$obj = new Psys_StationRule();
		return $obj->UserRate($stationid,$reg_where,$connect_where);
	}

	/**
	 * 每日Ios流程步骤流失率
	 * @param int $stationid 车站id
	 * @param date $sdate    起始日期
	 * @param date $edate    结束日期
	 */
	public function UserRose($stationid,$sdate,$edate){
		$sdate = date('Y_m_d',strtotime($edate));
		$where_date = " and date = '$sdate'";
		
		$obj = new Psys_StationRule();
		return $obj->UserRose($stationid,$where_date);
	}

	/**
	 * 每日流程数据概况
	 * @param int $stationid 车站id
	 * @param date $sdate    起始日期
	 * @param date $edate    结束日期
	 */
	public function DailyProcess($stationid,$sdate,$edate){
		$ad1_date = date('Y_m_d',strtotime($edate));
		$where_connect = " and date = '$edate'";
		$where_ad1 = " and date = '$ad1_date'";

		$obj = new Psys_StationRule();
		return $obj->DailyProcess($stationid,$where_connect,$where_ad1);
	}

	//上周用户连接数，注册数、下载数情况
	public function PrevWeekTotal($stationid,$sdate,$edate){
		$edate = date('Y-m-d',strtotime($sdate)-3600*24);
		$sdate = date('Y-m-d',strtotime($sdate)-3600*24*7);
		
		$reg_sdate = date('Y_m_d',strtotime($sdate));
		$reg_edate = date('Y_m_d',strtotime($edate));

		$reg_where = " and date BETWEEN '$reg_sdate' AND '$reg_edate'";
		$connect_where = " and date BETWEEN '$sdate' AND '$edate'";

		$obj = new Psys_StationRule();
		$data = $obj->PrevWeekTotal($stationid,$reg_where,$connect_where);
		//获取填充数据
		$fillarray = $this->fillarray($sdate,$edate,1);//2015-02-11
		$fillarray_ = $this->fillarray($sdate,$edate);//2015_02_11
		//格式化连接数组
		$data['connect'] = $this->arrayformat($data['connect'],'num');
		$data['connect'] += $fillarray;
		ksort($data['connect']);

		$data['reg'] = $this->arrayformat($data['reg'],'reg');
		$data['reg'] += $fillarray_;
		ksort($data['reg']);

		$data['down'] = $this->arrayformat($data['down'],'down');
		$data['down'] += $fillarray_;
		ksort($data['down']);
		
		return $data;
	}

	//本周用户连接数，注册数、下载数情况
	public function CurWeekTotal($stationid,$sdate,$edate){		
		$reg_sdate = date('Y_m_d',strtotime($sdate));
		$reg_edate = date('Y_m_d',strtotime($edate));
		
		$reg_where = " and date BETWEEN '$reg_sdate' AND '$reg_edate'";
		$connect_where = " and date BETWEEN '$sdate' AND '$edate'";
		
		$obj = new Psys_StationRule();
		$data = $obj->CurWeekTotal($stationid,$reg_where,$connect_where);

		//获取填充数据
		$fillarray = $this->fillarray($sdate,$edate,1);//2015-02-11
		$fillarray_ = $this->fillarray($sdate,$edate);//2015_02_11
		//格式化连接数组
		$data['connect'] = $this->arrayformat($data['connect'],'num');
		$data['connect'] += $fillarray;
		ksort($data['connect']);

		$data['reg'] = $this->arrayformat($data['reg'],'reg');
		$data['reg'] += $fillarray_;
		ksort($data['reg']);

		$data['down'] = $this->arrayformat($data['down'],'down');
		$data['down'] += $fillarray_;
		ksort($data['down']);

		return $data;
	}
	
	//上周注册、下载流失率
	public function PrevWeekRose($stationid,$sdate,$edate){		
		$edate = date('Y-m-d',strtotime($sdate)-3600*24);
		$sdate = date('Y-m-d',strtotime($sdate)-3600*24*7);
		
		$reg_sdate = date('Y_m_d',strtotime($sdate));
		$reg_edate = date('Y_m_d',strtotime($edate));

		$reg_where = " and date BETWEEN '$reg_sdate' AND '$reg_edate'";
		$connect_where = " and date BETWEEN '$sdate' AND '$edate'";
		
		$obj = new Psys_StationRule();
		$data = $obj->TwoWeekRose($stationid,$reg_where,$connect_where);

		//获取填充数据
		$fillarray = $this->fillarray($sdate,$edate);//2015_02_11
		//格式化连接数组
		$data['regrose'] = $this->arrayformat($data['regrose'],'rose');
		$data['regrose'] += $fillarray;
		ksort($data['regrose']);

		$data['downrose'] = $this->arrayformat($data['downrose'],'rose');
		$data['downrose'] += $fillarray;
		ksort($data['downrose']);
		return $data;
	}

	//本周注册、下载流失率
	public function CurWeekRose($stationid,$sdate,$edate){		
		$reg_sdate = date('Y_m_d',strtotime($sdate));
		$reg_edate = date('Y_m_d',strtotime($edate));
		
		$reg_where = " and date BETWEEN '$reg_sdate' AND '$reg_edate'";
		$connect_where = " and date BETWEEN '$sdate' AND '$edate'";
		
		$obj = new Psys_StationRule();
		$data = $obj->TwoWeekRose($stationid,$reg_where,$connect_where);

		//获取填充数据
		$fillarray = $this->fillarray($sdate,$edate);//2015_02_11
		//格式化连接数组
		$data['regrose'] = $this->arrayformat($data['regrose'],'rose');
		$data['regrose'] += $fillarray;
		ksort($data['regrose']);

		$data['downrose'] = $this->arrayformat($data['downrose'],'rose');
		$data['downrose'] += $fillarray;
		ksort($data['downrose']);
		return $data;
	}

	//按起始日期生成补位数组
	private function fillarray($sdate,$edate,$type=''){
		$fillarray = array();
		$sdate = strtotime($sdate);
		$edate = strtotime($edate);
		while ($sdate <= $edate) {
			if ($type == '') {
				$fillarray[date('Y_m_d',$sdate)] = 0; 
			}else{
				$fillarray[date('Y-m-d',$sdate)] = 0; 
			}
			
			$sdate = strtotime('+1 day',$sdate);
		}
		
		return $fillarray;
	}

	//数组转换如  array(array('date'=>'2015-02-11','num'=>234)) ——> array('2015-02-11'=>234);
	private function arrayformat($data,$key){
		$newdata = array();
		foreach($data as $v){
			$newdata[$v['date']] = $v[$key];
		}

		return $newdata;
	}


	//每日所有车站流程汇总数据
  	public function StationInfo($date,$isall=true){
  		$ad1_date = date('Y_m_d',strtotime($date));
		$where_connect = " date = '$date'";
		$where_process = " date = '$ad1_date'";
		// $where_connect = " date = '2015-02-05'";
		// $where_process = " date = '2015_02_05'";
		$obj = new Psys_StationRule();
		if ($isall) {
			return $obj->StationInfo($where_connect,$where_process);
		}else{
			return $obj -> SingleStationInfo($where_connect,$where_process);
		}
		
		
  	}

  	/**
  	 * 第二个版本的前置流程统计
  	 * @param date $date    日期
  	 * @param int $station 车站
  	 */
  	public function OldWebCount($date,$station)
	{	

		if($station == 1 || $station == 2){
			$dir = 'qdn/';
			$qr = $station == 1 ? ' | grep -v "222.43" ' : ' | grep "222.43" ';
			if ($station == 2) {
				return $this->OldWebCount_2($date,$dir,$qr);
			}
		}else{
			$dir = 'jn/';
			$qr = '';
		}
		$time = $date ? date('Y_m_d',strtotime($date)) :date('Y_m_d',strtotime('-1 day'));
		$file_web = ' cat '.$this->path.$dir.$time.$this->LM1.' '.$this->path.$dir.$time.$this->LM2.$qr;
		$file_app = ' cat '.$this->path.$dir.$time.$this->LA1.' '.$this->path.$dir.$time.$this->LA2.$qr;
		$result = array();
		
		$sys = array('ios','android','andriod','else','wp');
		//首页
		$tp_index = $station == 1 ? '/index/index' : '/?';
		$tp_index2 = $station == 1 ? 'mac=.*&t' : 'usermac=.*&s';
		$sh = $file_web.'|  grep -i "get '.$tp_index.'" | grep " 200 " | grep -i "window" | grep -o "'.$tp_index2.'" | sort | uniq | grep "&" -c';
		exec($sh,$arr_index_e);

		$sh = $file_web.'|  grep -i "get '.$tp_index.'" | grep " 200 " | grep -i "mac " | grep -o "'.$tp_index2.'" | sort | uniq | grep "&" -c';
		exec($sh,$arr_index_i);
		$sh = $file_web.'|  grep -i "get '.$tp_index.'" | grep " 200 " | grep -i "android" | grep -o "'.$tp_index2.'" | sort | uniq | grep "&" -c';
		exec($sh,$arr_index_a);
		$index['ios'] = $arr_index_i[0];
		$index['android'] = $arr_index_a[0];
		$index['else'] = $arr_index_e[0];
		
		$array = array('ios'=>'0','android'=>'0','else'=>'0','name'=>'首页');
		$index += $array; 
		$result[] = $index;
		//注册
		$sh = $file_web.' | grep -i "post /member/register" | grep " 200 8" | grep -i -o "mac \|android\|window" |awk \'BEGIN{OFS="/"}{count[$0]++}END{for(name in count)print name,count[name]}\'';
	
		exec($sh,$arr_reg);

		foreach ($arr_reg as $m=>$n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode('/', $n);
			$tep['sys'] = strtolower(rtrim($tep['sys'])) == 'mac' ? 'ios' : $tep['sys'];
			$tep['sys'] = strtolower(rtrim($tep['sys'])) == 'window' ? 'else' : $tep['sys'];
			if(in_array(strtolower(rtrim($tep['sys'])),$sys)){
				$register[strtolower($tep['sys'])] += $tep['dtime'];
			}
			
		}
		var_dump($register);
		exit;
		$array = array('ios'=>'0','android'=>'0','else'=>'0','name'=>'注册');
		$register += $array;
		
		$result[] = $register;
		
		//欢迎页
		$sh = $file_web.' | grep -i "get /index/welcome" | grep "fromRegister" | grep " 200 " | grep -i "window" | cut -d " " -f 7 | cut -d "?" -f 2 | grep -P -o "[0-9].{11}"  | sort | uniq | grep "1" -c';
		exec($sh,$arr_wel_e);
		$sh = $file_web.' | grep -i "get /index/welcome" | grep "fromRegister" | grep " 200 " | grep -i "mac " | cut -d " " -f 7 | cut -d "?" -f 2 | grep -P -o "[0-9].{11}"  | sort | uniq | grep "1" -c';
		exec($sh,$arr_wel_i);
		$sh = $file_web.' | grep -i "get /index/welcome" | grep "fromRegister" | grep " 200 " | grep -i "android" | cut -d " " -f 7 | cut -d "?" -f 2 | grep -P -o "[0-9].{11}"  | sort | uniq | grep "1" -c';
		exec($sh,$arr_wel_a);
		
		$welcome['ios'] = $arr_wel_i[0];
		$welcome['android'] = $arr_wel_a[0];
		$welcome['else'] = $arr_wel_e[0];
		$array = array('ios'=>'0','android'=>'0','else'=>'0','name'=>'全屏广告');
		$welcome += $array; 
		$result[] = $welcome;
		
		//弹出下载提示
		$sh = $file_web.'| grep " 200 " | grep -i "get /record.php?" | grep "/alert" | cut -d " " -f 7 | cut -d "/" -f 4 | cut -d "?" -f 2 | awk \'BEGIN{FS=OFS="/"}{count[$1]++}END{for(name in count)print name,count[name]}\'';
		exec($sh,$arr_alert);
		foreach ($arr_alert as $m=>$n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode('/', $n);
			if(in_array(strtolower($tep['sys']),$sys)){
				$alert[strtolower($tep['sys'])] = $tep['dtime'];
			}
		}
		$array = array('ios'=>'0','android'=>'0','else'=>'0','name'=>'弹出提示');
		$alert += $array; 
		$result[] = $alert;
		//下载人数
		$sh = $file_web.' | grep " 200 " | grep -i "get /record.php?downapp" | cut -d " " -f 7 | cut -d "/" -f 4 | cut -d "?" -f 2 | awk \'BEGIN{FS=OFS="/"}{count[$1]++}END{for(name in count)print name,count[name]}\'';
		exec($sh,$arr_down);
		foreach ($arr_down as $m=>$n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode('/', $n);
			if(in_array(strtolower($tep['sys']),$sys)){
				$down[strtolower($tep['sys'])] = $tep['dtime'];
			}
		}
		$array = array('ios'=>'0','android'=>'0','else'=>'0','name'=>'下载');
		$down += $array;
		$result[] = $down;
		//首次打开
		$sh = $file_app.'| grep " 200 " | grep -i "record.php?open/first" |cut -d " " -f 7 | cut -d "/" -f 4 | cut -d "?" -f 2 | awk \'BEGIN{FS=OFS="/"}{count[$1]++}END{for(name in count)print name,count[name]}\'';
		exec($sh,$arr_open);
		foreach ($arr_open as $m=>$n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode('/', $n);
			if(in_array(strtolower($tep['sys']),$sys)){
				$open[strtolower($tep['sys'])] = $tep['dtime'];
			}
		}
		$array = array('ios'=>'0','android'=>'0','else'=>'0','name'=>'首次打开');
		$open += $array;
		$result[] = $open;
		
		return $result;
	}

	//前置流程青岛北（或之类的）
	private function OldWebCount_2($date,$dir,$qr)
	{
		
		$time = $date ? date('Y_m_d',strtotime($date)) :date('Y_m_d',strtotime('-1 day'));
		$file_web = ' cat '.$this->path.$dir.$time.$this->LM1.' '.$this->path.$dir.$time.$this->LM2.$qr;
		$file_app = ' cat '.$this->path.$dir.$time.$this->LA1.' '.$this->path.$dir.$time.$this->LA2.$qr;
		$result = array();
		$sys = array('ios','android','else','wp');
		
		//首页
		$sh = $file_web.'|  grep -i "get /index/index" | grep " 200 " | grep -i "window" | grep -o "mac=.*&t" | sort | uniq | grep "&" -c';
		exec($sh,$arr_index_e);
		$sh = $file_web.'|  grep -i "get /index/index" | grep " 200 " | grep -i "mac " | grep -o "mac=.*&t" | sort | uniq | grep "&" -c';
		exec($sh,$arr_index_i);
		$sh = $file_web.'|  grep -i "get /index/index" | grep " 200 " | grep -i "android" | grep -o "mac=.*&t" | sort | uniq | grep "&" -c';
		exec($sh,$arr_index_a);
		$index['ios'] = $arr_index_i[0];
		$index['android'] = $arr_index_a[0];
		$index['else'] = $arr_index_e[0];
		$array = array('ios'=>'0','android'=>'0','else'=>'0','name'=>'首页');
		$index += $array;
		$result[] = $index;
		//注册
	
		$sh = $file_web.' | grep -i "post /member/register" | grep " 200 8" | grep -i -o "mac \|android\|window" |awk \'BEGIN{OFS="/"}{count[$0]++}END{for(name in count)print name,count[name]}\'';
	
		exec($sh,$arr_reg);
		foreach ($arr_reg as $m=>$n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode('/', $n);
			$tep['sys'] = strtolower(rtrim($tep['sys'])) == 'mac' ? 'ios' : $tep['sys'];
			$tep['sys'] = strtolower(rtrim($tep['sys'])) == 'window' ? 'else' : $tep['sys'];
			if(in_array(strtolower(rtrim($tep['sys'])),$sys)){
				
				$register[strtolower($tep['sys'])] += $tep['dtime'];
			}
			
		}
		$array = array('ios'=>'0','android'=>'0','else'=>'0','name'=>'注册');
		$register += $array;
		$result[] = $register;
		//欢迎页
		$sh = $file_web.' | grep -i "get /index/welcome" | grep "fromRegister" | grep " 200 " | grep -i "window" | cut -d " " -f 7 | cut -d "?" -f 2 | grep -P -o "[0-9].{11}"  | sort | uniq | grep "1" -c';
		exec($sh,$arr_wel_e);
		$sh = $file_web.' | grep -i "get /index/welcome" | grep "fromRegister" | grep " 200 " | grep -i "mac " | cut -d " " -f 7 | cut -d "?" -f 2 | grep -P -o "[0-9].{11}"  | sort | uniq | grep "1" -c';
		exec($sh,$arr_wel_i);
		$sh = $file_web.' | grep -i "get /index/welcome" | grep "fromRegister" | grep " 200 " | grep -i "android" | cut -d " " -f 7 | cut -d "?" -f 2 | grep -P -o "[0-9].{11}"  | sort | uniq | grep "1" -c';
		exec($sh,$arr_wel_a);
		
		$welcome['ios'] = $arr_wel_i[0];
		$welcome['android'] = $arr_wel_a[0];
		$welcome['else'] = $arr_wel_e[0];
		$array = array('ios'=>'0','android'=>'0','else'=>'0','name'=>'全屏广告');
		$welcome += $array;
		$result[] = $welcome;
		//弹出下载提示
		$sh = $file_web.'| grep " 200 " | grep -i "get /record.php?" | grep "/alert" | cut -d " " -f 7 | cut -d "/" -f 4 | cut -d "?" -f 2 | awk \'BEGIN{FS=OFS="/"}{count[$1]++}END{for(name in count)print name,count[name]}\'';
		exec($sh,$arr_alert);
		foreach ($arr_alert as $m=>$n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode('/', $n);
			if(in_array(strtolower($tep['sys']),$sys)){
				$alert[strtolower($tep['sys'])] = $tep['dtime'];
			}
		}
		$array = array('ios'=>'0','android'=>'0','else'=>'0','name'=>'弹出提示');
		$alert += $array;
		$result[] = $alert;
		//下载人数
		$sh = $file_web.' | grep " 200 " | grep -i "get /record.php?downapp" | cut -d " " -f 7 | cut -d "/" -f 4 | cut -d "?" -f 2 | awk \'BEGIN{FS=OFS="/"}{count[$1]++}END{for(name in count)print name,count[name]}\'';
		exec($sh,$arr_down);
		foreach ($arr_down as $m=>$n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode('/', $n);
			if(in_array(strtolower($tep['sys']),$sys)){
				$down[strtolower($tep['sys'])] = $tep['dtime'];
			}
		}
		$array = array('ios'=>'0','android'=>'0','else'=>'0','name'=>'下载');
		$down += $array;
		$result[] = $down;
		//首次打开
		$sh = $file_app.'| grep " 200 " | grep -i "record.php?open/first" |cut -d " " -f 7 | cut -d "/" -f 4 | cut -d "?" -f 2 | awk \'BEGIN{FS=OFS="/"}{count[$1]++}END{for(name in count)print name,count[name]}\'';
		exec($sh,$arr_open);
		foreach ($arr_open as $m=>$n)
		{
			$tep = array();
			list($tep['sys'],$tep['dtime']) = explode('/', $n);
			if(in_array(strtolower($tep['sys']),$sys)){
				$open[strtolower($tep['sys'])] = $tep['dtime'];
			}
		}
		$array = array('ios'=>'0','android'=>'0','else'=>'0','name'=>'首次打开');
		$open += $array;
		$result[] = $open;
		return $result;
	}

	/**
	 * wifi每日连接详情数据ajax
	 * @param date $date      日期
	 * @param string $sortname  排序字段
	 * @param string $sortorder 排序方式
	 * @param int $page      当前页
	 * @param int $pagesize  每页条数
	 */
	public function WifiDailyInfo($date,$sortname,$sortorder,$page,$pagesize){
		$obj = new Psys_StationRule();
  		return $obj -> WifiDailyInfo($date,$sortname,$sortorder,$page,$pagesize);
	}

	/**
	 * wifi每7日连接详情数据ajax
	 * @param string $sortname  排序字段
	 * @param string $sortorder 排序方式
	 * @param int $page      当前页
	 * @param int $pagesize  每页条数
	 */
	public function WifiWeekInfo($sortname,$sortorder,$page,$pagesize){
		$obj = new Psys_StationRule();
  		return $obj -> WifiWeekInfo($sortname,$sortorder,$page,$pagesize);
	}

	/**
	 * 广告1与广告2展示的pv详情
	 * @param int $page          当前页数
	 * @param int $pagesize  
	 * @param date $date      日期
	 * @param str $sortname  排序字段
	 * @param str $sortorder 排序方式
	 */
	public function AdShowPvInfo($page,$pagesize,$date,$sortname,$sortorder){
		$obj = new Psys_StationRule();
  		return $obj -> AdShowPvInfo($page,$pagesize,$date,$sortname,$sortorder);
	}

	/**
	 * 广告pv统计图
	 * @param date $date      
	 * @param int $show_type 广告位
	 */
	public function GraphAdPv($date,$show_type){
		$obj = new Psys_StationRule();
  		$data = $obj -> GraphAdPv($date,$show_type);
  		$result['x_cat'][] = '广告';
  		$i = 0;
  		foreach($data as $k=>$v){
  			//$result['x_cat'][$i] = $v['ad_name'];
			//拼接分类
			$datas[$i]['name']= $v['ad_name'];			
			$datas[$i]['data'][]=(int)$v['total'];
			$i++;
		}
		$result['y_data'] = $datas;
		return $result;
	}

	/**
	 * 根据游戏应用id获取游戏名
	 */
	public function GetAppName($appid){
		$name = $this->GetOne(array('id'=>$appid),'appname','rha_apps');
		return $name['appname'];
	}

	/**
	 * 获取首页导航点击PV
	 * @param  $sdate     起始时间
	 * @param  $edate     结束时间
	 * @param  $stationid 车站id
	 * @param  $page      当前页数
	 * @param  $pagesize  每页条数
	 * @param  $sortname  排序字段
	 * @param  $sortorder 排序方式
	 */
	public function NavigatorPv($sdate,$edate,$stationid,$page=1,$pagesize=100,$sortname='date',$sortorder="asc"){
		$obj = new Psys_StationRule();
  		return $obj -> NavigatorPv($sdate,$edate,$stationid,$page,$pagesize,$sortname,$sortorder);
	}

	/**
	 * wifi连接数据
	 * @param  $sdate  起始时间
	 * @param  $edate  截止时间
	 * @param  $station 车站
	 * @return         
	 */
	public function wifidata($sdate,$edate,$station,$page,$pagesize,$is_graph=0){
		$obj = new Psys_StationRule();
  		return $obj -> wifidata($sdate,$edate,$station,$page,$pagesize,$is_graph);
	}

	/**
	 * 查询当日wifi每小时链接人数
	 * @param  $date    
	 * @param  $station 
	 */
	public function WifiHourNum($date,$station){
		$obj = new Psys_StationRule();
  		return $obj -> WifiHourNum($date,$station);
	}

	/**
	 * 查询当日每个ap访问人数
	 * @param  $date    
	 * @param  $station 
	 */
	public function ApQueryNum($date,$station){
		$obj = new Psys_StationRule();
  		return $obj -> ApQueryNum($date,$station);
	}

	/**
	 * 页面停留时间统计
	 * @param  $sdate  起始时间
	 * @param  $edate  截止时间
	 * @param  $station 车站
	 * @return         
	 */
	public function StayTimeInfo($sdate,$edate,$station,$page,$pagesize){
		$obj = new Psys_StationRule();
  		return $obj -> StayTimeInfo($sdate,$edate,$station,$page,$pagesize);
	}

	/**
	 * 拼接统计图数据格式
	 * @param int  $index   下标
	 * @param str  $content name值
	 * @param int  $data    值
	 * @param boolean $is_show 默认是否显示
	 */
	public function GraphFormat($index,$content,$data,$is_show=true){
		$datas[$index]['name'] = $content;
		$datas[$index]['data'][] = $data;
		$datas[$index]['visible'] = $is_show;
		return $datas;
	}

	/**
	 * 广告PV展示
	 * @param  $sdate    
	 * @param  $edate    
	 * @param  $station  
	 * @param  $adtype   广告位置
	 * @param  $page     
	 * @param  $pagesize 
	 */
	public function ShowAdPvInfo($sdate,$edate,$station,$adtype,$page,$pagesize,$is_graph=0){
		$obj = new Psys_StationRule();
  		return $obj -> ShowAdPvInfo($sdate,$edate,$station,$adtype,$page,$pagesize,$is_graph);
	}


	/**
	 * 根据关键字或者id获取广告名称
	 * @param         $key 关键字或者id
	 */
	public function GetAdName($key){
		if (is_numeric($key)) {
			$where = array('id'=>$key);
		}else{
			$where = array('imgurl'=>$key);
		}

		return $this->GetOne($where,'id,adname','rha_ads');
	}

	/**
	 * 每7天注册数据
	 * @param [type] $sdate   
	 * @param [type] $edate   
	 * @param [type] $station 
	 */
	public function RegisterWeekData($sdate,$edate,$station){
		$obj = new Psys_StationRule();
  		return $obj -> RegisterWeekData($sdate,$edate,$station);
	}

	/**
	 * 每日注册统计图数据
	 * @param date $sdate  
	 * @param date $edate  
	 * @param int $station 
	 */
	public function RegData($sdate,$edate,$station){
		$sdate = str_replace('-','_',$sdate);
		$edate = str_replace('-','_',$edate);
		$obj = new Psys_StationRule();
  		return $obj -> RegData($sdate,$edate,$station);
	}

	/**
	 * 获取活跃用户信息
	 */
	public function UserActiveInfo($station){
		$obj = new Psys_StationRule();
  		return $obj -> UserActiveInfo($station);
	}

	/**
	 * 所选时间段范围内每7天的wifi连接数与注册人数
	 * @param  [type] $station [description]
	 * @param  [type] $sdate   [description]
	 * @param  [type] $edate   [description]
	 * @return [type]          [description]
	 */
	public function wifidataweek($station,$sdate,$edate){
		$obj = new Psys_StationRule();
  		return $obj -> wifidataweek($station,$sdate,$edate);
	}
	
	
}
