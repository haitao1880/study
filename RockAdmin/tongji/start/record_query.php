<?php
$curdir = dirname(__FILE__);
require_once $curdir . DIRECTORY_SEPARATOR . 'config.php';

/**
 * 增删改查常用SQL操作
 * @author leaf
 */
class queryModel
{
    private $prefix = ''; //表前缀
    private $tbname = ''; //表名，不带前缀
    private $default_tbname = 'log'; //默认表名
    private $host = '';
    private $username = '';
    private $password = '';
    private $dbname = '';

    /**
     * @param array $connect 多库操作时将新的连接传过来
     */
    public function __construct($connect = array())
    {
        if (empty($connect))
        {
            global $G_X;
            $this->host     = $G_X['db_tj']['host'];
            $this->username = $G_X['db_tj']['user'];
            $this->password = $G_X['db_tj']['pass'];
            $this->dbname   = $G_X['db_tj']['dbname'];
            $this->prefix   = $G_X['db_tj']['prefix'];
        }
        else
        {
            $this->host     = $connect['host'];
            $this->username = $connect['username'];
            $this->password = $connect['password'];
            $this->dbname   = $connect['dbname'];
            $this->prefix   = $connect['prefix'];
        }

        $this->setTable('');
    }

    /*public function __destruct()
    {
        $this->tbname = $this->default_tbname;
    }*/

    public function setTable($tbname)
    {
        $this->tbname = !empty($tbname) ? $tbname : $this->default_tbname;
    }

    public function getTable()
    {
        return $this->tbname;
    }


    /**
     * 执行一条SQL命令
     *
     * @param array $where 条件
     * @param str   $this  ->tbname    表名
     * @param str   $filed 字段
     *
     * @return array $result 结果
     */
    public function GetOne($where, $filed = '*', $order = 'id desc')
    {
        if (empty($where) or empty($this->tbname))
            return FALSE;

        if (is_array($where))
        {
            $_where = '1 ';

            foreach ($where as $k => $v)
            {
                $_where .= ' AND ';
                $_where .= $k . ' = "' . addslashes($v) . '"';
            }
        }
        else $_where = $where; //直接是写好了的where条件

        $_where .= ' ORDER BY ' . $order . ' LIMIT 0,1'; // 只显示一条

        $sql = 'SELECT ' . $filed . ' FROM ' . $this->prefix . $this->tbname . ' WHERE ' . ' ' . $_where;

        return $this->query($sql, 'one');
    }

    /**
     * 查询一堆数据
     *
     * @param array $where    条件
     * @param str   $this     ->tbname    表名
     * @param str   $filed    字段
     * @param str   $order    排序方式
     * @param int   $page     分页，当前页
     * @param int   $pagesize 分页，一页显示多少条
     *
     * @return array $result 结果
     */
    public function GetList($where, $filed = '*', $order = '', $page = 0, $pagesize = 20)
    {
        if (empty($where) or empty($this->tbname))
            return FALSE;

        $_where = is_array($where) ? $this->FormatWhere($where) : $where; //直接是写好了的where条件

        $sql = 'SELECT ' . $filed . ' FROM ' . $this->prefix . $this->tbname . ' WHERE ' . $_where;

        if (!empty($order))
            $sql .= ' ORDER BY ' . $order;

        $sql .= ' LIMIT ' . $page . ',' . $pagesize;

        $result = $this->query($sql, 'list');

        $result['totalnum'] = $this->CountNum($_where); // 统计满足条件的数据，不受分页影响

        return $result;
    }

    /**
     * 更新一条数据
     *
     * @param array $where 条件
     * @param str   $this  ->tbname    表名
     *
     * @return array $result 结果
     */
    public function UpdateOne($where, $data)
    {
        if (!is_array($where) or empty($where) or empty($this->tbname))
            return FALSE;

        $sql = 'UPDATE ' . $this->prefix . $this->tbname . ' SET ';

        $i = 1;
        foreach ($data as $k => $v)
        {
            $sql .= $k . ' = ' . '"' . $v . '"';
            if (count($data) != $i)
                $sql .= ','; //是否最后一条
            $i++;
        }

        $sql .= ' WHERE ' . $this->FormatWhere($where);

        return $this->query($sql);
    }


    /**
     * 删除一条数据
     *
     * @param array $where 条件
     * @param str   $this  ->tbname    表名
     *
     * @return array $result 结果
     */
    public function DeleteOne($where)
    {
        if (!is_array($where) or empty($where) or empty($this->tbname))
            return FALSE;

        $sql = 'DELETE FROM ' . $this->prefix . $this->tbname . ' WHERE ' . $this->FormatWhere($where);

        return $this->query($sql);
    }

    /**
     * 添加一条数据
     *
     * @param array $data 内容
     * @param str   $this ->tbname     表
     *
     * @return array $result  结果
     */
    public function AddOne($data,$action = 1)
    {
        if (!is_array($data) or empty($data) or empty($this->tbname))
            return FALSE;

        $sql  = 'INSERT INTO `' . $this->prefix . $this->tbname . '` (';
        $sqlv = '';
        foreach ($data as $field => $value)
        {
            $sql .= '`' . $field . '`,';
            $sqlv .= "'" . addslashes($value) . "',";
        }

        $sql  = trim($sql, ",");
        $sqlv = trim($sqlv, ",");
        $sql .= ") VALUES (" . $sqlv . ")";

        return $this->query($sql,$action);
    }

    /**
     * 执行一条SQL命令
     *
     * @param str $sql    SQL命令
     * @param str $action 默认为1，1表示直接返回执行结果，用于删除，添加之类的不需要返回数据内容的
     * @return array $result 结果
     */
    public function query($sql, $action = 1)
    {
        if (empty($sql)) return FALSE;

        $con = mysql_connect($this->host, $this->username, $this->password) or die('数据库连接出错:' . mysql_error());
        mysql_select_db($this->dbname, $con) or die('选择数据表错误:' . mysql_error());

        mysql_query("set names 'utf8'", $con) or die('设置数据库编码出错:' . mysql_error());
        $temp = mysql_query($sql, $con) or die('数据库操作有误:' . mysql_error());

        //直接返回执行结果，用于删除，添加之类的不需要返回数据内容的
        if ($action == 1)
            $result = $temp;

        if ($action == 'add') //如果需要返回新加的数据的id
            return mysql_insert_id($con);

        // 查询一条
        if ($action == 'one' or $action == 'count')
            $result = mysql_fetch_assoc($temp);

        // 查询多条
        if ($action == 'list')
        {
            $result = array('allrow'=>array());

            while ($row = mysql_fetch_assoc($temp))
            {
                $result['allrow'][] = $row;
            }
        }

        mysql_close();

        return $result;
    }

    /**
     * 统计满足条件的数据个数
     *
     * @param str   $where 条件
     * @param str   $this  ->tbname    表名
     * @param str   $filed 字段
     * @param str   $count 要统计的字段
     * @param str   $group 分类统计
     */
    public function CountNum($where, $count = '*', $name = 'id', $group = '')
    {
        $sql = 'SELECT COUNT(' . $count . ') AS ' . $name;
        $sql .= ' FROM ' . $this->prefix . $this->tbname . ' WHERE ' . $where;

        if (!empty($group))
            $sql .= ' GROUP BY ' . $group;

        $result = $this->query($sql, 'count');

        if (isset($result[$name]))
            return $result[$name];
        else return 0;
    }

    /**
     * 格式化where条件
     *
     * @param array $where 条件
     *                     return str $_where  格式化以后的 where sql 语句
     */
    public function FormatWhere($where)
    {
        $_where = '1 ';
        foreach ($where as $k => $v)
        {
            $_where .= ' AND ';
            $len = strpos($k, '*'); // 查找是否带有参数

            if ($len > 0) // 带有 *
            {
                $key    = substr($k, 0, $len); // id_IN 中的id
                $canshu = substr($k, $len + 1); // id_IN 中的IN

                if (strtoupper($canshu) == 'IN' OR strtoupper($canshu) == 'NOT IN')
                {
                    $i = 1;
                    $_where .= $key . ' ' . $canshu . ' ('; // id IN (

                    foreach ($v as $k2 => $v2)
                    {
                        $_where .= '"' . addslashes($v2) . '"';
                        if (count($v) != $i)
                            $_where .= ','; // 是否最后一条
                        $i++;
                    }
                    $_where .= ')';
                }
                else // > >= <>
                {
                    $_where .= $key . ' ' . $canshu . ' "' . addslashes($v) . '"'; // id > 5
                }
            }
            else // 不带有 *
            {
                $_where .= $k . ' = "' . addslashes($v) . '"';
            }
        }

        return $_where;
    }

    //发送验证短信
    public function SendSms($mobile, $smscode = '')
    {
        if (empty($smscode))
            $smscode = substr(str_shuffle($mobile), 0, 6);

        $post = array(
            'SpCode'         => SMS_SPCODE,
            'LoginName'      => SMS_LOGINNAME,
            'Password'       => SMS_PASSWORD,
            'MessageContent' => iconv('UTF-8', 'GB2312', str_replace('{xxxxxxx}', '：' . $smscode, getSMSTemplate(SMS_REGISTER_TEMPLATE))),
            'UserNumber'     => $mobile,
            'f'              => SMS_F
        );

        $response = postProxy(SMS_HTTP_URL, $post);
        return strpos($response, 'result=0') !== FALSE ? $smscode : false;
    }
}

?>
