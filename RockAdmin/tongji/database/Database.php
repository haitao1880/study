<?php
/**
 * 数据库操作类
 * Terry
 */
class Database{
	public static $ins;
	protected $db;
	protected $dbname = 'rht_admin';
	// protected $host = '192.168.28.201';
	protected $host = 'localhost';
	protected $username = 'root';
	protected $passwd = 'password';

	protected function __construct()
	{
		/*$options = array(
				PDO::ATTR_PERSISTENT => true,//设置长链接
				PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,//设置错误处理方式
				PDO::MYSQL_ATTR_INIT_COMMAND => 'set names utf8',//设置字符集
				PDO::ATTR_CASE => PDO::CASE_LOWER//指定列小写
		);*/
		try{
			$db = new PDO("mysql:host=$this->host;dbname=$this->dbname","$this->username","$this->passwd");
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$db->exec('set names utf8');
			$this->db = $db;
		}catch (PDOException $e) {
            exit($e->getMessage());
        }

	}

	protected function __clone()
	{

	}

	//获取实例
	public static function GetIns()
	{
		if (!(self::$ins instanceof self)) {
			self::$ins = new self();
		}
		return self::$ins;
	}

	//获取所有行
	public function FetAll($sql)
	{
		try {
            $sth = $this->db->prepare($sql);
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
            $sth = $this->db->prepare($sql);
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
		$sth = $this->db->prepare($in_sql);
		$sth->execute(array_values($data));
		return $this->db->lastInsertId();
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
        return $this->execute($_sql)->rowCount();
	}

	 //执行SQL
    public function executesql($_sql) {
        try {
            $_stmt = $this->db->prepare($_sql);
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


