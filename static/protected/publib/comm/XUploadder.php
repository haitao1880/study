<?php
/**
* Copyright(c) 2013
* 日    期:2013年11月20日
* 文 件 名:XUploadder.php
* 创建时间:下午5:35:03
* 字符编码:UTF-8
* 版本信息:$Id: XUploadder.php 86 2014-07-09 09:58:48Z jing $
* 修改日期:$LastChangedDate: 2014-07-09 17:58:48 +0800 (周三, 09 七月 2014) $
* 最后版本:$LastChangedRevision: 86 $
* 修 改 者:$LastChangedBy: jing $
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rockgame/trunk/code/gamewww/protected/publib/comm/XUploadder.php $
* 摘    要:Uploadder封装类,可参考XImage
*/
class XUploadder
{
	protected $config = array(
	    //允许上传的类型);
		"pictype"=>array("image/jpg","image/jpeg","image/gif","image/bmp","image/pjpeg","image/png","image/x-png"),
		"pic_header" => "pditem_",
		"upload_url"=>"/pimg/",
		//"upload_path"=> DATA_PATH."pitem".DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR,
		"maxpicsize"=>20480 ,//上传图片大小限制100k
		"wrong_pic_type"=>"/static/default/images/wrong_pic_type.jpg",
		"maxsize_pic"=>"/static/default/images/maxsize_pic.jpg",
		"upload_fail_pic"=>"/static/default/images/upload_fail_pic.jpg",
		//"upload_log"=>PITEM_LOG_PATH."upload_pitem.txt",
	);
	public function __construct(array $conf)
	{
		$this->config = $conf;
	}
	
	/**
	 * 上传图片
	 * @param array $data 形如$_FILES
	 * @param unknown_type $type
	 * @return Ambigous <<type>, string, multitype:string number multitype:string  >
	 */
	public function Save(array $data,$type="item")
	{
		return $this->upload_process($data);
	}

	/**
     * 上传前进行处理
     * @param <type> $data
     * @return <type> 
     */
    protected function upload_process($data)
    {
   
         $filesize = intval($data['file']['size']/1024);
         //print_r($filesize);echo '<br>'.$this->config['maxpicsize'];exit;
         $filetype = $data['file']['type'];
 
         $istype = $this->isallowtype($filetype);
         if(!$istype)//判断是否是上传的几种类型，否则返回一张不是图片类型的图片
         {
            return $this->config['wrong_pic_type'];
         }else{
             if ($filesize>$this->config['maxpicsize']){//如果大于100k则提示图片过大,返回一张提示图片
                  return $this->config['maxsize_pic'];
             }else{
                  return $this->upload_pic($data);
             }
         }

    }
    
	/**
     * 上传图片
     * @param <type> $data
     * @return <type> 
     */
    protected function upload_pic($data){
    	$t = str_replace('.','',strstr($data["file"]["name"], '.'));
    	$t = explode(".", $data["file"]["name"]);
    	$t = array_pop($t);
    	$date = $this->get_pic_folder();
    	$upload_path = $this->config['upload_path'].implode(DIRECTORY_SEPARATOR, $date).DIRECTORY_SEPARATOR;

        do{
        	$filename   = $this->random(10).".".$t;
        	$uploadfile = $upload_path.$filename;
        }while(file_exists($uploadfile));
       // print_r($data['file']);exit;
        $flag = move_uploaded_file($data['file']['tmp_name'],$uploadfile);
        if ($flag){
			return $this->config['upload_url'].implode("/", $date)."/".$filename;
        }else{
            return "upload image wrong";
		}
    }
	
	/**
     * 是否为允许上传的类型
     * @param string $filetype 形如："image/jpg"
     * @return boolean 
     */
    public function isallowtype($filetype)
    {
        return in_array(strtolower($filetype),$this->config['pictype']);        
    }
    
	/**
     * 随机生成图片名称
     * @param num $length
     * @return string 
     */
    public function random($length)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
        $max = strlen($chars) - 1;
        $picname = "";
        mt_srand((double)microtime() * 1000000);
        for($i = 0; $i < $length; $i++)
        {
            $picname.= $chars[mt_rand(0, $max)];
        }
        return $this->config['pic_header'].$picname;
    }
    
	/**
     * 判断当前目录是否存在,不存在就新建目录，如今天是2011-05-26,则目录应该为/var/www/files/tusubjectfiles/2011/05/26/
     * @param <type> $upload_head
     * @return <type>
     */
    public function get_pic_folder()
    {
        $path = array(date("Y"),date("m"),date("d"));
        $upload_head = $this->config['upload_path'];
       
        foreach($path as $k => $v){
            $upload_head.= $v.DIRECTORY_SEPARATOR;
//             if (!is_dir($upload_head)){
//                 mkdir($upload_head);
//                 chmod($upload_head, 0777);
//             }
        }
        
        self::createFolder($upload_head);
        return $path;
    }
    /**
     * 递归生成文件
     * @param string $path
     */
    private function createFolder($path)
    {
    	if (!file_exists($path))
    	{
    		self::createFolder(dirname($path));
    		mkdir($path, 0777);
    	}
    }

    
 	/**
     *写入日志文件
     * @param <type> $str 
     */
    public function write_log($str){
        $f = fopen($this->config['upload_log'],"a");
        fwrite($f,$str);
        fclose($f);
    }
}
?>