<?php
/**
 * 所要生成文件的文件注释存贮
 *
 * @author tommy
 * @copyright Copyright (C) www.doitphp.com 2020 All rights reserved.
 * @version $Id: noteStorageModel.php 1.0 2020-04-19 11:17:49Z tommy $
 * @package Model
 * @since 1.0
 */
namespace models;

use doitphp\library\File;

class notationStorageModel {

	/**
	 * 获取所要生成文件的信息数据
	 *
	 * @access public
	 * @return array
	 */
	public function getNotation() {

    //get file path
    $filePath = $this->_getStorageFilePath();
    if(!is_file($filePath)){
      return array();
    }

    $data = include $filePath;
    return $data;
	}

	/**
	 * 保存附加信息数据
	 *
	 * @access public
	 *
	 * @param string $author 文件作者
	 * @param string $copyright 文件版权
	 * @param string $lisence 发行协议
	 * @param string $link 文件的相关链接
	 *
	 * @return boolean
	 */
	public function setNotation($author = null, $copyright = null, $lisence = null, $link = null) {

    //get data
    $data = $this->getNotation();

		$data = array(
			'author'    => $author,
			'copyright' => $copyright,
			'lisence'   => $lisence,
			'link'      => $link,
		);

    //save data
    return $this->_saveData($data);
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

    return 'notation_storage.data.php';
  }  
}