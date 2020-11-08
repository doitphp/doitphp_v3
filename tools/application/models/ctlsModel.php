<?php
/**
 * 模型：控制器文件信息数据管理
 *
 * @author tommy
 * @copyright Copyright (C) www.doitphp.com 2020 All rights reserved.
 * @version $Id: ctlsModel.php 1.0 2020-04-18 13:44:17Z tommy $
 * @package Model
 * @since 1.0
 */
namespace models;

class ctlsModel extends dataStorageModel {

	/**
	 * 添加Aciton
	 *
	 * @access public
	 *
	 * @param string $name Action名称
	 * @param string $description Acton功能描述
	 *
	 * @return boolean
	 */
	public function addAction($name, $description = null) {

    //parse params
    if(!$name){
      $this->_setErrorInfo('无效的参数调用！');
      return false;
    }

    //get data
		$data = $this->getData();

    if(!isset($data['actions']) || !$data['actions']){      
      $data['actions'] = array();
    }else{
      //check whether contain repeat method info
      foreach($data['actions'] as $lines){
        if($lines['name'] == $name){
          $this->_setErrorInfo('您所添加的类方法已存在！');
          return false;
        }
      }
    }

    //handle data
    $actionInfo = array(
      'name'        => $name,
      'description' => $description,
    );

		array_push($data['actions'], $actionInfo);
		
    //save data
    return $this->_saveData($data);
	}

	/**
	 * 编辑Action
	 *
	 * @access public
	 *
	 * @param integer $id Action的序号
	 * @param string $name Action名称
	 * @param string $description Acton功能描述
	 *
	 * @return boolean
	 */
	public function editAction($id = 0, $name, $description = null) {

    //parse params
    if(!$name){
      $this->_setErrorInfo('无效的参数调用！');
      return false;
    }

    //get data
    $data = $this->getData();

    //check whether exists action info
    if(!$data){      
      $this->_setErrorInfo('您所编辑的Action不存在！');
      return false;
    }
    if(!isset($data['actions'][$id]) || !$data['actions'][$id]) {
      $this->_setErrorInfo('您所编辑的Action不存在！');
      return false;
    }

    //check whether contain repeat action info
    foreach($data['actions'] as $key=>$lines){
      if($id == $key){
        continue;
      }
      if($lines['name'] == $name){
        $this->_setErrorInfo('您所编辑的Action已存在！');
        return false;
      }
    }

    //handle data
    $data['actions'][$id] = array(
      'name'        => $name,
      'description' => $description,
		);
		
    //save data
    return $this->_saveData($data);		
	}

	/**
	 * 删除Action
	 *
	 * @access public
	 *
	 * @param integer $id Action的序号
	 *
	 * @return boolean
	 */
	public function deleteAction($id = 0) {

    //get data
    $data = $this->getData();

    //check whether exists action info
    if(!$data){      
      $this->_setErrorInfo('您所要删除的Action不存在！');
      return false;
    }
    if(!isset($data['actions'][$id])) {
      $this->_setErrorInfo('您所要删除的Action不存在！');
      return false;
    }

    //handle data
    unset($data['actions'][$id]);
    
    //save data
    return $this->_saveData($data);
	}

  /**
	 * 回调方法: 获取数据存贮文件的名称
	 *
	 * @access protected
	 * @return boolean
	 */
	protected function _getStorageFileName() {

    return 'controllers_storage.data.php';
	}

}