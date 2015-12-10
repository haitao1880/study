<?php
/**
* Copyright(c) 2013
* 日    期:2013年11月20日
* 文 件 名:XMemcache.php
* 创建时间:下午5:33:38
* 字符编码:UTF-8
* 版本信息:$Id: XMemcache.php 53 2014-03-27 09:06:56Z tony_ren $
* 修改日期:$LastChangedDate: 2014-03-27 17:06:56 +0800 (周四, 27 三月 2014) $
* 最后版本:$LastChangedRevision: 53 $
* 修 改 者:$LastChangedBy: tony_ren $
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rocktrain/trunk/web/protected/publib/comm/XMemcache.php $
* 摘    要:MEMECACHED封装类
*/
class XMemCache
{
	private $mem_server = "127.0.0.1";
	private $mem_port = 11211;
	private $mem_timeout = 3;//3秒
	private $mem_key = "";
	
	protected static $_instance = null;
	protected $memobj = null;
	protected function __construct() 
	{
		global $G_X;
		$this->mem_server 	= $G_X['memcacheserver']['host'];
		$this->mem_port		= $G_X['memcacheserver']['port'];
		$this->mem_timeout	= $G_X['memcacheserver']['timeout'];
		$this->mem_key		= $G_X['memcacheserver']['prekey'];
	}
	
	public static function GetInstance()
    {
        if (null === self::$_instance) 
        {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
    
	
	public function getCacheKey($key,$info,$Rules=array(),$cacheTyle='one',$timeout=604800,$iszip=0)
	{
       
        $this->set($key, $info,$iszip,$timeout);
        foreach ($Rules as $Rule){
            if($ruleForKey=$this->get('Rule_'.$Rule.$cacheTyle)){
                $Rules+=$ruleForKey;
            }
            $this->Set('Rule_'.$Rule.$cacheTyle, array($key));
        }
       
	}
	/**
	 * 替换缓存值  若不存在则新增，若存在则相加
	 * @param unknown_type $key
	 * @param unknown_type $info
	 * @param unknown_type $timeout
	 * @param unknown_type $iszip
	 */
	public function replace($key,$info,$timeout=604800,$iszip=0){
	    if($r=$this->Get($key)){
	        $info=array_merge($info,$r);
	    }
	    $this->Set($key,$info,$timeout,$iszip);
	    
	    
	}
	
	public function unsetList($key){
	    
     }
	
	
	/**
	 * Enter 添加缓存
	 * @param <string> $key 键名
	 * @param <string> $info
	 * @param <int> $iszip 是否使用压缩，注间，要开启zlib
	 * @param <int> $timeout 0永久有效，604800 7天，最大不能超过30天
	 */
	public function Set($key,$info,$timeout=604800,$iszip=0){
	    $info=serialize($info);
		$key = $this->mem_key . $key;
	    return $this->GetObject()->set($key, $info,$iszip,$timeout);
	}
    
    /**
     * 根据KEY获得内容
     * @param unknown_type $key
     */
    public function Get($key)
    {
		$key = $this->mem_key . $key;
    	$mk = $key;
    	$r=$this->GetObject()->get($mk);
    	
    	return unserialize($r);
    }
    
	/**
	 * 删除缓存KEY
	 * @param unknown_type $key
	 */
	public function Del($key)
	{
		$mk = $key;
		return $this->GetObject()->Delete($mk);
	}
    
	/**
	 * 获得对象
	 */
    protected function GetObject()
    {
    	if(null == $this->memobj)
    	{
    		$this->memobj = new Memcache;
			$this->memobj->connect($this->mem_server,$this->mem_port,$this->mem_timeout);
    	}
    	
    	return $this->memobj;
    }
}

?>