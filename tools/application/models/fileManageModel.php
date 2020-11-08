<?php
/**
 * 模型层：项目目录文件读取操作
 *
 * @author tommy
 * @copyright Copyright (C) www.doitphp.com 2020 All rights reserved.
 * @version $Id: fileManageModel.php 1.0 2020-04-18 12:56:06Z tommy $
 * @package Model
 * @since 1.0
 */
namespace models;

use \DirectoryIterator;
use doitphp\core\Configure;
use doitphp\library\File;

class fileManageModel {

	/**
	 * 读取项目目录文件列表
	 *
	 * @access public
	 *
	 * @param string $dirName 当前目录名称
	 *
	 * @return array
	 */
	public function getFileLists($dirName = null) {

		//parse params
		$dirPath = $this->_parseDirPath($dirName);

		//get file list
		$fileObject = new DirectoryIterator($dirPath);

		$fileArray = array();
		foreach ($fileObject as $lines) {

			//file filter
			if ($lines->isDot()) {
					continue;
			}

			//get file access
			$mod = '';
			if ($lines->isReadable()) {
				$mod .= 'r ';
			}
			if ($lines->isWritable()) {
				$mod .= 'w ';
			}
			if ($lines->isExecutable()) {
				$mod .= 'x ';
			}

			//get file extension name
			$ico = '';
			if($lines->isFile() == true) {
				$extension = strtolower(substr(strrchr($lines->getFilename(), '.'), 1));
				switch ($extension) {
					case 'php':
						$ico = 'php.gif';
						break;
					
					case 'html':
						$ico = 'htm.gif';
						break;
					
					case 'txt':
						$ico = 'txt.gif';
						break;
					
					case 'css':
						$ico = 'css.gif';
						break;
					
					case 'js':
						$ico = 'js.gif';
						break;
					
					case 'gif':
						$ico = 'gif.gif';
						break;
					
					case 'jpg':
					case 'jpeg':
						$ico = 'jpg.gif';
						break;
					
					case 'png':
						$ico = 'image.gif';
						break;
					
					default:$ico = '';
				}
			}

			//get folder role
			$fileName = $lines->getFilename();
			$isDir    = $lines->isDir();

			$fileArray[] = array(
				'name'	  => $fileName,
				'size'	  => File::formatBytes($lines->getSize()),
				'isdir'   => $isDir,
				'time'	  => date('Y-m-d H:i:s', $lines->getMTime()),
				'ico'     => $ico,
				'mod'			=> $mod,
				'ext'			=> $extension,
			);
		}

		return $fileArray;
	}

	/**
	 * 分析当前目录的有效路径
	 *
	 * @access protected
	 *
	 * @param string $dirName 当前目录名称
	 *
	 * @return string
	 */
	protected function _parseDirPath($dirName = null) {

		//parse params
		if(!$dirName) {
			$dirName = '';
		}

		$webappPath = Configure::get('webappPath');
		$dirPath    = $webappPath . DS . $dirName;

		return str_replace(array('\\', '//'), '/', $dirPath);
	}

	/**
	 * 分析当前目录是否为保护目录，注：doitphp框架核心文件及目录属于系统保护文件，不允许更改
	 *
	 * @access protected
	 *
	 * @param string $dirName 目录名称
	 *
	 * @return boolean
	 */
	protected function _isProtectFolder($dirName) {

		return  (substr($dirName, 0, 8) == '/doitphp' || substr($dirName, 0, 6) == '/tools') ? true : false;
	}

	/**
	 * 分析目录是否为Controller目录
	 *
	 * @access protected
	 *
	 * @param string $dirName 目录的名称
	 *
	 * @return boolean
	 */
	protected function _isControllerFolder($dirName) {

		//parse params
		if (!$dirName) {
			return false;
		}

		return (substr($dirName, -11) == 'controllers') ? true : false;
	}

	/**
	 * 分析目录是否为Model目录
	 *
	 * @access protected
	 *
	 * @param string $dirName 目录的名称
	 *
	 * @return boolean
	 */
	protected function _isModelFolder($dirName) {

		//parse params
		if (!$dirName) {
			return false;
		}
		//排除特殊情况：cache目录中的子目录:models
		if ($dirName == '/cache/models') {
			return false;
		}

		return (substr($dirName, -6) == 'models') ? true : false;
	}

	/**
	 * 分析目录是否为Widget目录
	 *
	 * @access protected
	 *
	 * @param string $dirName 目录的名称
	 *
	 * @return boolean
	 */
	protected function _isWidgetFolder($dirName) {

		//parse params
		if (!$dirName) {
			return false;
		}

		return (substr($dirName, -7) == 'widgets') ? true : false;
	}

	/**
	 * 分析目录是否为library目录
	 *
	 * @access protected
	 *
	 * @param string $dirName 目录的名称
	 *
	 * @return boolean
	 */
	protected function _isLibraryFolder($dirName) {

		//parse params
		if (!$dirName) {
			return false;
		}

		return (substr($dirName, -7) == 'library') ? true : false;
	}
	
	/**
	 * 分析目录是否为Extension目录
	 *
	 * @access protected
	 *
	 * @param string $dirName 目录的名称
	 *
	 * @return boolean
	 */
	protected function _isExtensionFolder($dirName) {

		//parse params
		if (!$dirName) {
			return false;
		}

		return (substr($dirName, -10) == 'extensions') ? true : false;
	}

	/**
	 * 分析上一级目录名称
	 *
	 * @access public
	 *
	 * @param string $dirName 目录的名称
	 *
	 * @return string
	 */
	public function getReturnUrl($dirName = null) {

		//parse params
		if (!$dirName) {
			return false;
		}

		$parentDir = str_replace('\\', '/', dirname($dirName));
		if(!$dirName || $dirName == '/'){
			return '';
		}

		return $parentDir;
	}

	/**
	 * 获取当前目录的目录角色
	 *
	 * @access public
	 *
	 * @param string $dirName 目录的名称
	 *
	 * @return string
	 */
	public function getPackageRoles($dirName = null) {

		//parse params
		if(!$dirName){
			return 'root';
		}

		//parse system folder
		if($this->_isProtectFolder($dirName)){
			return 'system';
		}

		//parse controllers folder
		if($this->_isControllerFolder($dirName)){
			return 'controller';
		}
		
		//parse models folder
		if($this->_isModelFolder($dirName)){
			return 'model';
		}

		//parse widgets folder
		if($this->_isWidgetFolder($dirName)){
			return 'widget';
		}

		//parse library folder
		if($this->_isLibraryFolder($dirName)){
			return 'libary';
		}

		//parse extension folder
		if($this->_isExtensionFolder($dirName)){
			return 'extension';
		}

		return 'other';
	}

}