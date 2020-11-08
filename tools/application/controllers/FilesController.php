<?php
/**
 * 项目目录及文件管理
 *
 * @author tommy
 * @copyright Copyright (C) www.doitphp.com 2020 All rights reserved.
 * @version $Id: FilesController.php 1.0 2020-04-18 11:23:27Z tommy $
 * @package Controller
 * @since 1.0
 */
namespace controllers;

class FilesController extends BaseController {

	/**
	 * 显示项目目录文件列表(lists)
	 *
	 * @access public
	 * @return void
	 */
	public function indexAction() {

		//get params
		$dir = $this->get('path');
		$dir = (!$dir) ? $dir : str_replace('//', '/', $dir);

		//分析并获取应用(项目)目录的路径
		$this->_parseWebAppProject($this->_webappPath);
		
		$fileManageModel = $this->model('fileManage');

		//获取当前目录内的子目录及文件列表
		$fileLists = $fileManageModel->getFileLists($dir);

		//获取"返回上级"网址
		$returnUrl = $fileManageModel->getReturnUrl($dir);
		if($returnUrl){
			$returnUrl = '/?path=' . $returnUrl;
		}

		//获取当前目录的目录角色(root, system, controller, model, widget, libary, extension, other)
		$roleName = $fileManageModel->getPackageRoles($dir);

		//项目根目录的读写权限(是否具有可写权限)
		$isWritable = is_writable($this->_webappPath) ? true : false;

		//目录角色列表
		$roleLinks = array();
		switch($roleName){
			//controller
			case 'controller':
				$roleLinks = array(
					'text' => '创建Controller文件',
					'link' => $this->createUrl('ctls/index'),
				);
			break;

			//model
			case 'model':
				$roleLinks = array(
					'text' => '创建Model文件',
					'link' => $this->createUrl('mods/index'),
				);				
			break;

			//widget
			case 'widget':
				$roleLinks = array(
					'text' => '创建Widget文件',
					'link' => $this->createUrl('wids/index'),
				);				
			break;

			//library
			case 'libary':
				$roleLinks = array(
					'text' => '创建类文件',
					'link' => $this->createUrl('libs/index'),
				);				
			break;

			//extension
			case 'extension':
				$roleLinks = array(
					'text' => '创建扩展模块',
					'link' => $this->createUrl('exts/index'),
				);				
			break;
		}

		//assign params
		$this->assign(array(
			'pageTitle'  => '文件管理',
			'path'       => ($dir) ? $dir : '/',
			'isWritable' => $isWritable,
			'fileLists'  => $fileLists,
			'returnUrl'  => $returnUrl,
			'folderRole' => $roleName,
			'uploadUrl'  => $this->getActionUrl('ajax_upload_file'),
			'deleteUrl'  => $this->getActionUrl('ajax_delete_file'),
			'roleLinks'  => $roleLinks,
		));

		//dispaly page
		$this->display();
	}

	/**
	 * ajax调用：上传项目文件
	 *
	 * @access public
	 * @return void
	 */
	public function ajax_upload_fileAction() {

		//get params
		$dirName     = $this->post('upload_dirname');
		$uploadFile  = $_FILES['upload_file'];
		if (!$dirName) {
			$this->ajax(false, '对不起！上传文件参数错误');
		}

		$webappPath = $this->_parseWebAppPath();
		$uploadDirPath = $webappPath . $dirName;
		//判断所上传的目录是否存在
		if (!is_dir($uploadDirPath)) {
			$this->ajax(false, '对不起，所要上传文件的目录不存在！');
		}

		$newFile = $uploadDirPath . DS . $uploadFile['name'];

		//判断所要上传的文件是否存在
		if (is_file($newFile)) {
			$this->ajax(false, '对不起，所要上传的文件已经存在！');
		}

		$fileUploadObj = $this->instance('FileUpload');

		$result = $fileUploadObj->setLimitSize(1024*1024*8)->moveFile($uploadFile, $newFile);
		if(!$result){
			$erroMsg = $fileUploadObj->getErrorInfo();
			if(!$erroMsg){
				$erroMsg = '对不起！文件上传失败，请重新操作。';
			}
			$this->ajax(false, $erroMsg);
		}

		$this->ajax(true, '文件上传成功！', array('targeturl'=>'refresh'));
	}

	/**
	 * Ajax调用：删除所选的项目文件
	 *
	 * @access public
	 * @return void
	 */
	public function ajax_delete_fileAction() {

		//get params
		$dirName  = $this->post('dir_name', '/');
		$fileName = $this->post('file_name');
		if (!$fileName) {
			$this->ajax(false, '对不起，错误的参数调用');
		}

		$webappPath = $this->_parseWebAppPath();
		//parse file path
		$filePath = $webappPath . DS . $dirName . DS . $fileName;
		if (!is_file($filePath)) {
			$this->ajax(false, '对不起，所要删除的文件不存在！');
		}

		if (!unlink($filePath)) {
			$this->ajax(false, '对不起，文件删除操作失败！请重新操作');
		}

		$this->ajax(true, '文件删除成功！', array('targeturl'=>'refresh'));
	}

	/**
	 * 判断是否创建项目目录
	 *
	 * @access protected
	 * 
	 * @param string $webappPath 应用(项目)根目录路径
	 * 
	 * @return boolean
	 */
	protected function _parseWebAppProject($webappPath) {

		//跳转网址
		$targetUrl = $this->createUrl('webapp/index');

		//分析webapp目录是否存在
		if (!is_dir($webappPath)) {
			$errorMsg = "对不起！项目目录：{$webappPath} 不存在！请创建项目根目录";

			$this->showMsg($errorMsg, $targetUrl);
		}

		//分析应用目录
		if (!is_dir($webappPath . DS . 'application')) {
			$errorMsg = "对不起！您还没有创建WebApp目录。请进行如下操作:WebApp管理->创建WebApp目录";

			$this->showMsg($errorMsg, $targetUrl);
		}
	}
}