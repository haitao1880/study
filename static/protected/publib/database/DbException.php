<?php
/**
 *	数据异常类
 *	@author		yqren
 *	@copyright	2011-2012
 *	@version	2.0
 *	@package	PAS2
 *
 *	$Id: DbException.php 10 2014-06-13 07:03:39Z tony_ren $
 */
class DbException extends Exception
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