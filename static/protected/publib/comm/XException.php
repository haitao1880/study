<?php
/**
* Copyright(c) 2013
* 日    期:2013年11月20日
* 文 件 名:XException.php
* 创建时间:下午5:32:14
* 字符编码:UTF-8
* 版本信息:$Id: XException.php 10 2014-06-13 07:03:39Z tony_ren $
* 修改日期:$LastChangedDate: 2014-06-13 15:03:39 +0800 (周五, 13 六月 2014) $
* 最后版本:$LastChangedRevision: 10 $
* 修 改 者:$LastChangedBy: tony_ren $
* 版本地址:$HeadURL: http://192.168.1.100:12000/svn/rockgame/trunk/code/gamewww/protected/publib/comm/XException.php $
* 摘    要:数据异常类
*/
class XException extends Exception
{
	/**
     * @var null|Exception
     */
    private $_previous = null;

    /**
     * Construct the exception
     *
     * @param  string $msg
     * @param  int $code
     * @param  Exception $previous
     * @return void
     */
    public function __construct($msg = '', $code = 0, Exception $previous = null)
    {
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            parent::__construct($msg, (int) $code);
            $this->_previous = $previous;
        } else {
            parent::__construct($msg, (int) $code, $previous);
        }
    }

    /**
     * Overloading
     *
     * For PHP < 5.3.0, provides access to the getPrevious() method.
     * 
     * @param  string $method 
     * @param  array $args 
     * @return mixed
     */
    public function __Call($method, array $args)
    {
        if ('getprevious' == strtolower($method)) {
            return $this->_GetPrevious();
        }
        return null;
    }

    /**
     * String representation of the exception
     *
     * @return string
     */
    public function __ToString()
    {
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            if (null !== ($e = $this->getPrevious())) {
                return $e->__ToString() 
                       . "\n\nNext " 
                       . parent::__ToString();
            }
        }
        return parent::__ToString();
    }

    /**
     * Returns previous Exception
     *
     * @return Exception|null
     */
    protected function _GetPrevious()
    {
        return $this->_previous;
    }
}