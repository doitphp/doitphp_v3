<?php
/**
 * 所要生成文件的内容数据存贮
 *
 * @author tommy
 * @copyright Copyright (C) www.doitphp.com 2020 All rights reserved.
 * @version $Id: dataStorageModel.php 1.0 2020-04-19 05:40:23Z tommy $
 * @package Model
 * @since 1.0
 */
namespace models;

use doitphp\library\File;

class dataStorageModel {

  /**
   * 错误信息
   *
   * @var string
   */
  protected $_errorInfo = null;

	/**
	 * 获取所要生成文件的信息数据
	 *
	 * @access public
	 * @return array
	 */
	public function getData() {

    //get file path
    $filePath = $this->_getStorageFilePath();
    if(!is_file($filePath)){
      return array();
    }

    $data = include $filePath;
    return $data;
	}

	/**
	 * 保存文件信息数据
	 *
	 * @access protected
	 *
	 * @param array $data 文件信息数据
	 *
	 * @return boolean
	 */
	protected function _saveData($data) {
    
    //parse params
    if(!is_array($data)){
      return false;
    }

    //get file path
    $filePath    = $this->_getStorageFilePath();
    $fileContent = "<?php\nif(!defined('IN_DOIT'))exit();\nreturn " . var_export($data, true) . ";";

    return File::writeFile($filePath, $fileContent);
	}

	/**
	 * 添加method信息
	 *
	 * @access public
	 *
	 * @param string $name method名称
	 * @param string $description method功能描述
	 * @param string $access 访问权限：public, protected, priviate
	 * @param string $dataType method返回的数据类型
	 *
	 * @return boolean
	 */
	public function addMethod($name, $description = null, $access = 'public', $dataType = 'void') {

    //parse params
    if(!$name){
      $this->_setErrorInfo('无效的参数调用！');
      return false;
    }

    //get data
    $data = $this->getData();

    if(!isset($data['methods']) || !$data['methods']){      
      $data['methods'] = array();
    }else{
      //check whether contain repeat method info
      foreach($data['methods'] as $lines){
        if($lines['name'] == $name){
          $this->_setErrorInfo('您所添加的类方法已存在！');
          return false;
        }
      }
    }

    //handle data
    $methodInfo = array(
      'name'        => $name,
      'description' => $description,
      'access'      => $access,
      'type'        => $dataType,
      'params'      => array(),
    );

    array_push($data['methods'], $methodInfo);

    //save data
    return $this->_saveData($data);
	}

	/**
	 * 编辑method信息
	 *
	 * @access public
	 *
	 * @param integer $id method排序ID
	 * @param string $name method名称
	 * @param string $description method功能描述
	 * @param string $access 访问权限：public, protected, priviate
	 * @param string $dataType method返回的数据类型
	 * 
	 * @return boolean
	 */
	public function editMethod($id = 0, $name, $description = null, $access = 'public', $dataType = 'void') {

    //parse params
    if(!$name){
      $this->_setErrorInfo('无效的参数调用！');
      return false;
    }
    
    //get data
    $data = $this->getData();

    //check whether exists method info
    if(!$data){      
      $this->_setErrorInfo('您所编辑的类方法不存在！');
      return false;
    }
    if(!isset($data['methods'][$id]) || !$data['methods'][$id]) {
      $this->_setErrorInfo('您所编辑的类方法不存在！');
      return false;
    }

    //check whether contain repeat method info
    foreach($data['methods'] as $key=>$lines){
      if($id == $key){
        continue;
      }
      if($lines['name'] == $name){
        $this->_setErrorInfo('您所编辑的类方法已存在！');
        return false;
      }
    }

    //handle data
    $data['methods'][$id] = array(
      'name'        => $name,
      'description' => $description,
      'access'      => $access,
      'type'        => $dataType,
      'params'      => $data['methods'][$id]['params'],
    );

    //save data
    return $this->_saveData($data);
	}

	/**
	 * 删除method信息
	 *
	 * @access public
	 *
	 * @param integer $id method排序ID
	 *
	 * @return boolean
	 */
	public function deleteMethod($id = 0) {

    //get data
    $data = $this->getData();

    //check whether exists method info
    if(!$data){      
      $this->_setErrorInfo('您所要删除的类方法不存在！');
      return false;
    }
    if(!isset($data['methods'][$id])) {
      $this->_setErrorInfo('您所要删除的类方法不存在！');
      return false;
    }

    //handle data
    unset($data['methods'][$id]);
    
    //save data
    return $this->_saveData($data);
	}

	/**
	 * 添加method参数
	 *
	 * @access public
	 *
	 * @param integer $methodId 所要添加参数的method排序ID
	 * @param string $name 参数名称
	 * @param string $type 参数的数据类型
	 * @param string $description 参数说明描述
	 * @param string $default 默认值
	 *
	 * @return boolean
	 */
	public function addParams($methodId = 0, $name, $type, $description = null, $default = null) {
    
    //parse params
    if(!$name){
      $this->_setErrorInfo('无效的参数调用！');
      return false;
    }

    //get data
    $data = $this->getData();

    //check whether exists method info
    if(!$data){      
      $this->_setErrorInfo('您所要添加参数的类方法不存在！');
      return false;
    }
    if(!isset($data['methods'][$methodId]) || !$data['methods'][$methodId]) {
      $this->_setErrorInfo('您所要添加参数的类方法不存在！');
      return false;
    }

    //check whether contain repeat method info
    if(isset($data['methods'][$methodId]['params']) && $data['methods'][$methodId]['params']){
      foreach($data['methods'][$methodId]['params'] as $lines){
        if($lines['name'] == $name){
          $this->_setErrorInfo('您所添加的参数名称已存在！');
          return false;
        }
      }
    }

    //handle data
    $paramsArray = array(
      'name'        => $name,
      'type'        => $type,
      'description' => $description,
      'default'     => $default,
    );

    array_push($data['methods'][$methodId]['params'], $paramsArray);

    //save data
    return $this->_saveData($data);
	}

	/**
	 * 编辑参数信息
	 *
	 * @access public
	 *
   * @param integer $methodId 所要添加参数的method排序ID
	 * @param integer $paramId 参数排序ID
	 * @param string $name 参数名称
	 * @param string $type 参数的数据类型
	 * @param string $description 参数说明描述
	 * @param string $default 默认值
	 * 
	 * @return boolean
	 */
	public function editParams($methodId = 0, $paramId = 0, $name, $type, $description = null, $default = null) {

    //parse params
    if(!$name){
      $this->_setErrorInfo('无效的参数调用！');
      return false;
    }

    //get data
    $data = $this->getData();

    //check whether exists method info
    if(!$data){      
      $this->_setErrorInfo('您所要编辑的参数不存在！');
      return false;
    }
    if(!isset($data['methods'][$methodId]['params'][$paramId]) || !$data['methods'][$methodId]['params'][$paramId]) {
      $this->_setErrorInfo('您所要编辑的参数不存在！');
      return false;
    }

    //check whether contain repeat method info
    foreach($data['methods'][$methodId]['params'] as $key=>$lines){
      if($key == $paramId){
        continue;
      }
      if($lines['name'] == $name){
        $this->_setErrorInfo('您所编辑的参数名称已存在！');
        return false;
      }
    }

    //handle data
    $data['methods'][$methodId]['params'][$paramId] = array(
      'name'        => $name,
      'type'        => $type,
      'description' => $description,
      'default'     => $default,
    );

    //save data
    return $this->_saveData($data);
	}

	/**
	 * 删除参数信息
	 *
	 * @access public
	 *
   * @param integer $methodId 所要添加参数的method排序ID
	 * @param integer $paramId 参数排序ID
	 *
	 * @return boolean
	 */
	public function deleteParams($methodId = 0, $paramId = 0) {

    //get data
    $data = $this->getData();

    //check whether exists method info
    if(!$data){      
      $this->_setErrorInfo('您所要删除的参数不存在！');
      return false;
    }
    if(!isset($data['methods'][$methodId]['params'][$paramId]) || !$data['methods'][$methodId]['params'][$paramId]) {
      $this->_setErrorInfo('您所要删除的参数不存在！');
      return false;
    }

    //handle data
    unset($data['methods'][$methodId]['params'][$paramId]);

    //save data
    return $this->_saveData($data);
  }

	/**
	 * 获取所要生成文件的信息数据
	 *
	 * @access public
	 * @return array
	 */
	public function clearData() {

    $data = array();
    
    //save data
    return $this->_saveData($data);
  }
    
	/**
	 * 获取数据存贮文件的路径
	 *
	 * @access protected
	 * @return boolean
	 */
	protected function _getStorageFilePath() {

    $fileName = $this->_getStorageFileName();

    return CACHE_PATH . DS . 'data' . DS . $fileName;
  }
  
  /**
	 * 回调方法: 获取数据存贮文件的名称
	 *
	 * @access protected
	 * @return boolean
	 */
	protected function _getStorageFileName() {

    return 'methods_storage.data.php';
  }
  
  /**
   * 设置当前模型的错误信息
   *
   * @access protected
   *
   * @param string $message 所要设置的错误信息
   *
   * @return boolean
   */
  protected function _setErrorInfo($message) {

    //参数分析
    if (!$message) {
      return false;
    }

    //对信息进行转义
    $this->_errorInfo = $message;

    return true;
  }

  /**
   * 获取当前模型的错误信息
   *
   * @access public
   * @return string
   */
  public function getErrorInfo() {

    return $this->_errorInfo;
  }  
}