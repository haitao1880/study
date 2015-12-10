<?php 
/**
 * 数据库操作类
 * Terry
 */

$jiaoyundir = dirname(__file__).DIRECTORY_SEPARATOR;
require_once $jiaoyundir.'Jiaoyun_Config.php';

class Jiaoyun_Database{
	public static $ins;
	public static $prefix;
	protected static $db;
	protected static $host; 
	protected static $dbname;
	protected static $username;
	protected static $password;
	protected function __construct($config)
	{	
		self::$prefix = $config['tb_prefix'];	
		self::GetDb($config);	

	}

	protected function __clone()
	{

	}

	//获取实例
	public static function GetIns()
	{	

		global $G_X;
		if (!(self::$ins instanceof self)) {
			self::$ins = new self($G_X['bus_db']);
		}
		return self::$ins;
	}

	//获取数据库对象
	protected static function GetDb($config)
	{
		$options = array(
				PDO::ATTR_PERSISTENT => true,//设置长链接
				PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,//设置错误处理方式
				PDO::MYSQL_ATTR_INIT_COMMAND => 'set names utf8',//设置字符集
				PDO::ATTR_CASE => PDO::CASE_LOWER//指定列小写
		);
		$host = $config['host'];
		$dbname = $config['dbname'];
		$username = $config['username'];
		$password = $config['password'];		
		try{
			$db = new PDO("mysql:host=$host;dbname=$dbname","$username","$password",$options);			
			self::$db = $db;

		}catch (PDOException $e) {
            exit($e->getMessage());
        }
	}

	//获取所有行
	public function FetAll($sql)
	{
		try {
            $sth = self::$db->prepare($sql);
			$sth->execute();
			$result = $sth->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException  $e) {
            exit('SQL语句：'.$sql.'<br />错误信息：'.$e->getMessage());
        }
		return $result;
	}

	//获取一行数据
	public function FetRow($sql)
	{	
		try {
            $sth = self::$db->prepare($sql);
			$sth->execute();
			$result = $sth->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException  $e) {
            exit('SQL语句：'.$sql.'<br />错误信息：'.$e->getMessage());
        }
		return $result[0];
	}

	//插入数据
	public function Insert(Array $data,$tb)
	{
		$key = $var = '';
		foreach ($data as $kt=>$vt)
		{
			$key.= "`$kt`,";
			$var.= "?,";
		}
		$key = rtrim($key,',');
		$var = rtrim($var,',');
		$in_sql = "Insert Into $tb($key) Value ($var);";
		try{
			$sth = self::$db->prepare($in_sql);
			$sth->execute(array_values($data));
			$result = self::$db->lastInsertId();
		}catch (PDOException  $e) {
            exit('SQL语句：'.$in_sql.'<br />错误信息：'.$e->getMessage());
        }
        return $result;
		
	}

	//修改数据
	public function UpdateOne(Array $where, Array $update_data,$tb)
	{
		$_where = $_setData = '';
        foreach ($where as $_key=>$_value) {
        	$_where .= $_key.'='.$_value.' AND ';
        }
        $_where = ' WHERE '.substr($_where, 0, -4);


        foreach ($update_data as $k=>$v) {
            $_setData .= "$k='$v',";
        }
        $_setData = ' SET '.rtrim($_setData,',');
        $_sql = "UPDATE $tb $_setData $_where";
        return $this->db->execute($_sql)->rowCount();
	}

	 //执行SQL
    public function executesql($_sql) {
        try {
            $_stmt = self::$db->prepare($_sql);
            $_stmt->execute();
        } catch (PDOException  $e) {
            exit('SQL语句：'.$_sql.'<br />错误信息：'.$e->getMessage());
        }
        return $_stmt;
    }

     //判断是否存在 
    public function IsExists(Array $_param,$_table) {  
        $_where = '';
        foreach ($_param as $_key=>$_value) {  
            $_where .= "$_key='$_value' AND "; 
        }
        $_where = ' WHERE '.substr($_where, 0, -4);

        $_sql = "SELECT COUNT(*) as count FROM $_table $_where"; 
        $_stmt = $this->FetRow($_sql); 
        if (!$_stmt['count']) {
        	return false;
        }
        return true;
    } 
}

