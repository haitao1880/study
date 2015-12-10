<?php
/**
* Copyright(c) 2014
* 日    期:2014年11月05日
* 文 件 名:XPhpExcel.php
* 创建时间:15:03
* 字符编码:UTF-8
* 版本信息:v1.0
* 修改日期:none
* 最后版本:v1.0
* 创 建 者:Nick (nick@rockhippo.cn)
* 修 改 者:none
* 版本地址:none
* 摘    要:phpexcel封装(基础功能封装)
*/
  
/** PHPExcel */  
require_once(PUBLIB_PATH.'phpexcel/PHPExcel.php');

class XPhpExcel{
    
	private $font_size = '12px';//字号
    private $font_color = '#696969';//字体颜色
    private $objWriter;//对象实例
    
    /**
    *
    * @do 构造函数
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
	public function  __construct() {

        
        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized;
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
        
        //创建一个处理对象实例
        $this->objPHPExcel = new PHPExcel();     

	}
    
    /**
    *
    * @do 设置基本属性
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function setBasicAttr($setCreator = '',$setLastModifiedBy = '',$setTitle = '',$setSubject = '',$setDescription = '',$setKeywords = '',$setCategory = ''){
        
        //创建人
        $this->objPHPExcel->getProperties()->setCreator($setCreator);
        //最后修改人
        $this->objPHPExcel->getProperties()->setLastModifiedBy($setLastModifiedBy);
        //标题
        $this->objPHPExcel->getProperties()->setTitle($setTitle);
        //题目
        $this->objPHPExcel->getProperties()->setSubject($setSubject);
        //描述
        $this->objPHPExcel->getProperties()->setDescription($setDescription);
        //关键字
        $this->objPHPExcel->getProperties()->setKeywords($setKeywords);
        //种类
        $this->objPHPExcel->getProperties()->setCategory($setCategory);
        
    }
    
    /**
    *
    * @do 设置sheet
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function setSheet($Index = 0,$Title = 'sheet1'){
        
        if($Index == 0){

        }else{
            //创建一个新的工作空间(sheet)
            $this->objPHPExcel->createSheet();
        }
        
        //设置当前的sheet
        $this->objPHPExcel->setActiveSheetIndex($Index);
        //设置sheet的name
        $this->objPHPExcel->getActiveSheet()->setTitle($Title);
        
    }
    
    /**
    *
    * @do 设置单元格的值
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function setValue($key, $val){
        
        //设置单元格的值
        $this->objPHPExcel->getActiveSheet()->setCellValue($key, $val);
        
    }
    
    /**
    *
    * @do 设置单元格的宽度
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function setWidth($key, $val = '', $type = 1){
        
        //宽度设置
        if($type == 1){//1为自动  2为赋值
            $this->objPHPExcel->getActiveSheet()->getColumnDimension($key)->setAutoSize(true);
        }else{
            $this->objPHPExcel->getActiveSheet()->getColumnDimension($key)->setWidth($val);
        }
        
    }
    
    /**
    *
    * @do 导出
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function output($name = 'resume' ){
        
        //创建文件格式写入对象实例
        $this->objWriter = new PHPExcel_Writer_Excel2007($this->objPHPExcel);

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");;
        header('Content-Disposition:attachment;filename="'.$name.'.xlsx"');
        header("Content-Transfer-Encoding:binary");
        $this->objWriter->save('php://output');
        
    }
    
    /**
    *
    * @do 导出到文件
    *
    * @access public 
    * @author Nick
    * @copyright rockhippo
    * @param -
    * @return -
    *
    */
    public function outputFile($name = 'resume', $path){
        
        //创建文件格式写入对象实例
        $this->objWriter = new PHPExcel_Writer_Excel2007($this->objPHPExcel);
        
        $pathName = $path . $name . '.xlsx';
        $this->objWriter->save($pathName);
        
    }
    
    
}