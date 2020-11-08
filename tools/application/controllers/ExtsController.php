<?php
/**
 * 扩展模块引导文件操作管理
 *
 * @author tommy
 * @copyright Copyright (C) www.doitphp.com 2020 All rights reserved.
 * @version $Id: ExtsController.php 1.0 2020-04-18 12:04:27Z tommy $
 * @package Controller
 * @since 1.0
 */
namespace controllers;

use doitphp\library\File;
use library\fileCreator;

class ExtsController extends BaseController {

	/**
	 * 索引页：生成扩展模块引导文件表单页
	 *
	 * @access public
	 * @return void
	 */
	public function indexAction() {

		//get storage file info
		$extsModel = $this->model('exts');
		$fileData  = $extsModel->getData();

		//assign params
		$this->assign(array(
			'pageTitle'     => '扩展模块管理',
			'fileData'      => $fileData,
			'editMethodUrl' => $this->getActionUrl('edit_method'),
			'addParamsUrl'  => $this->getActionUrl('add_params'),
			'editParamsUrl' => $this->getActionUrl('edit_params'),			
		));

		//display page
		$this->display();	
	}

	/**
	 * Ajax调用：生成扩展模块引导文件
	 *
	 * @access public
	 * @return void
	 */
	public function ajax_create_fileAction() {

		//get params
		$extName = $this->post('ext_name');
		if ($extName) {
				$extName = trim($extName, '_');
		}
		if (!$extName) {
				$this->ajax(false, '对不起，扩展名不能为空！');
		}
		if (strpos($extName, '_') !== false) {
				$this->ajax(false, '对不起，扩展模块命名不允许含有下划线！');
		}
		$extName = strtolower($extName);

		$description = $this->post('note_description');
		$author      = $this->post('note_author');
		$copyright   = $this->post('note_copyright');
		$license     = $this->post('note_license');
		$link        = $this->post('note_link');

		//parse file path
		$webappPath = $this->_parseWebAppPath();
		$filePath   = $webappPath . DS . 'application/extensions' . DS . $extName;

		$fileName  = $extName . 'Ext.php';
		$filePath .= DS . $fileName;

		//当所要创建的扩展引导文件存在时
		if (is_file($filePath)) {
			$this->ajax(false, '对不起，所要创建的扩展引导文件已存在！');
		}

		//获取所要创建的扩展引导文件内容
		$extsModel = $this->model('exts');
		$fileData  = $extsModel->getData();

		//分析并组装扩展引导文件内容
		$fileContent  = "<?php\n";
		$fileContent .= fileCreator::fileNote($fileName, $description, $author, $copyright, 'extension', $license, $link);
		$fileContent .= "namespace extensions;\n\n";
		$fileContent .= fileCreator::classCodeStart($extName . 'Ext', 'Extension', false);

		//构造函数(类方法)
		$fileContent .= fileCreator::methodNote('public', 'boolean', array(), '构造方法');
		$fileContent .= fileCreator::methodCode('__construct', 'public');

		//分析扩展引导文件的类方法(method)列表
		$methodLists = (!isset($fileData['methods']) || !$fileData['methods']) ? array() : $fileData['methods'];
		if($methodLists){
			foreach($methodLists as $lines){
				//类方法参数分析
				$methodNoteParams = array();
				$methodCodeParams = array();
				if($lines['params']){
					foreach($lines['params'] as $rows){
						$methodNoteParams[] = array($rows['name'], $rows['type'], $rows['description']);
						if (is_null($rows['default']) || $rows['default'] == '') {
							$methodCodeParams[] = $rows['name'];
						} else {
							$methodCodeParams[$rows['name']] = $rows['default'];
						}						
					}
				}
				$fileContent .= fileCreator::methodNote($lines['access'], $lines['type'], $methodNoteParams, $lines['description']);
				$fileContent .= fileCreator::methodCode($lines['name'], $lines['access'], $methodCodeParams);
			}
		}

		$fileContent .= fileCreator::classCodeEnd();

		//写入文件内容(生成文件)
		if(!File::writeFile($filePath, $fileContent)){
			$this->ajax(false, '对不起，创建扩展引导文件失败！请重新操作');
		}

		//清空扩展引导文件的内容信息存贮数据		
		$extsModel->clearData();

		//保存Controller文件的文件头代码注释信息, 以备下一个文件共用
		$notationModel = $this->model('notationStorage');
		$notationModel->setNotation($author, $copyright, $license, $link);

		$this->ajax(true, '扩展引导文件创建成功！', array('targeturl'=>'refresh'));
	}

	/**
	 * 添加method信息
	 *
	 * @access public
	 * @return void
	 */
	public function add_methodAction() {

		//display page
		$this->render();
	}

	/**
	 * Ajax调用：添加method信息
	 *
	 * @access public
	 * @return void
	 */
	public function ajax_add_methodAction() {

		//get params
		$name   = $this->post('method_name');
		$desc   = $this->post('method_desc');
		$access = $this->post('method_access');
		$type   = $this->post('method_type');

		if (!$name) {
			$this->ajax(false, '对不起，参数不正确！');
		}
		if($name == '__construct'){
			$errorMsg = '您输入的类方法为构造函数，系统将自动创建，无需添加!';
			$this->ajax(false, $errorMsg);
		}

		//filter system keywords
		$keywords = $this->_getSystemKeyWords();
		if(in_array($name, $keywords)){
			$this->ajax(false, '您输入的Method名称为系统关键词！请更改输入');
		}

		//handle data
		$extsModel = $this->model('exts');
		if (!$extsModel->addMethod($name, $desc, $access, $type)) {
			$errorMsg = $extsModel->getErrorInfo();
			if(!$errorMsg){
				$errorMsg = '对不起，操作失败！请重新操作';
			}
			$this->ajax(false, $errorMsg);
		}

		$this->ajax(true, '恭喜！操作成功', array('targeturl'=>'refresh'));
	}

	/**
	 * 编辑method信息
	 *
	 * @access public
	 * @return void
	 */
	public function edit_methodAction() {

		//get params
		$methodId = (int)$this->get('id');

		//get storage file info
		$extsModel = $this->model('exts');
		$fileData  = $extsModel->getData();

		if(!isset($fileData['methods'][$methodId]) || !$fileData['methods'][$methodId]){
			exit('所要编辑的Method信息不存在');
		}

		//assign params
		$this->assign(array(
			'methodId'   => $methodId,
			'methodInfo' => $fileData['methods'][$methodId],
		));
		//display page
		$this->render();
	}

	/**
	 * Ajax调用：编辑method信息
	 *
	 * @access public
	 * @return void
	 */
	public function ajax_edit_methodAction() {

		//get params
		$id     = $this->post('method_id');
		$name   = $this->post('method_name');
		$desc   = $this->post('method_desc');
		$access = $this->post('method_access');
		$type   = $this->post('method_type');

		if (is_null($id) || !$name) {
			$this->ajax(false, '对不起，参数不正确！');
		}

		//filter system keywords
		$keywords = $this->_getSystemKeyWords();
		if(in_array($name, $keywords)){
			$this->ajax(false, '所要编辑的Method名称为系统关键词！请更改输入');
		}

		//handle data
		$extsModel = $this->model('exts');	
		if (!$extsModel->editMethod($id, $name, $desc, $access, $type)) {
			$errorMsg = $extsModel->getErrorInfo();
			if(!$errorMsg){
				$errorMsg = '对不起，操作失败！请重新操作';
			}
			$this->ajax(false, $errorMsg);
		}

		$this->ajax(true, '恭喜！操作成功', array('targeturl'=>'refresh'));
	}

	/**
	 * Ajax调用：删除method信息
	 *
	 * @access public
	 * @return void
	 */
	public function ajax_delete_methodAction() {

		//get params
		$id = $this->post('method_id');
		if (is_null($id)) {
			$this->ajax(false, '对不起，参数不正确！');
		}

		//handle data
		$extsModel = $this->model('exts');	
		if (!$extsModel->deleteMethod($id)) {
			$errorMsg = $extsModel->getErrorInfo();
			if(!$errorMsg){
				$errorMsg = '对不起，操作失败！请重新操作';
			}
			$this->ajax(false, $errorMsg);
		}

		$this->ajax(true, '恭喜！操作成功', array('targeturl'=>'refresh'));
	}

	/**
	 * 添加method参数信息
	 *
	 * @access public
	 * @return void
	 */
	public function add_paramsAction() {

		//get params
		$methodId = (int)$this->get('mid');

		//assign params
		$this->assign('methodId', $methodId);

		//display page
		$this->render();
	}

	/**
	 * Ajax调用：添加method参数信息
	 *
	 * @access public
	 * @return void
	 */
	public function ajax_add_paramsAction() {

		//get params
		$methodId = $this->post('method_id');
		$name     = $this->post('param_name');
		$desc     = $this->post('param_desc');
		$type     = $this->post('param_type');
		$default  = $this->post('param_default');

		if (is_null($methodId) || !$name) {
			$this->ajax(false, '对不起，参数不正确！');
		}

		//handle data
		$extsModel = $this->model('exts');
		if (!$extsModel->addParams($methodId, $name, $type, $desc, $default)) {
			$errorMsg = $extsModel->getErrorInfo();
			if(!$errorMsg){
				$errorMsg = '对不起，操作失败！请重新操作';
			}
			$this->ajax(false, $errorMsg);
		}

		$this->ajax(true, '恭喜！操作成功', array('targeturl'=>'refresh'));
	}

	/**
	 * 编辑method的参数信息
	 *
	 * @access public
	 * @return void
	 */
	public function edit_paramsAction() {

		//get params
		$methodId = (int)$this->get('mid');
		$paramId  = (int)$this->get('pid');

		//get storage file info
		$extsModel = $this->model('exts');
		$fileData  = $extsModel->getData();

		if(!isset($fileData['methods'][$methodId]) || !$fileData['methods'][$methodId]){
			exit('所要编辑的Method信息不存在');
		}
		if(!$fileData['methods'][$methodId]['params'][$paramId]){
			exit('所要编辑的Method信息不存在');
		}

		//assign params
		$this->assign(array(
			'methodId'  => $methodId,
			'paramId'   => $paramId,
			'paramInfo' => $fileData['methods'][$methodId]['params'][$paramId],
		));

		//display page
		$this->render();
	}

	/**
	 * Ajax调用：编辑method的参数信息
	 *
	 * @access public
	 * @return void
	 */
	public function ajax_edit_paramsAction() {

		//get params
		$methodId = $this->post('method_id');
		$paramId  = $this->post('param_id');
		$name     = $this->post('param_name');
		$type     = $this->post('param_type');
		$desc     = $this->post('param_desc');
		$default  = $this->post('param_default');

		if (is_null($methodId) || is_null($paramId) || !$name) {
			$this->ajax(false, '对不起，参数不正确！');
		}

		//handle data
		$extsModel = $this->model('exts');	
		if (!$extsModel->editParams($methodId, $paramId, $name, $type, $desc, $default)) {
			$errorMsg = $extsModel->getErrorInfo();
			if(!$errorMsg){
				$errorMsg = '对不起，操作失败！请重新操作';
			}
			$this->ajax(false, $errorMsg);
		}

		$this->ajax(true, '恭喜！操作成功', array('targeturl'=>'refresh'));
	}

	/**
	 * Ajax调用：删除method参数信息
	 *
	 * @access public
	 * @return void
	 */
	public function ajax_delete_paramsAction() {

		//get params
		$paramKey = $this->post('param_key');
		if (is_null($paramKey)) {
			$this->ajax(false, '对不起，参数不正确！');
		}

		$paramArray = explode('-', $paramKey);
		$methodId   = (int)$paramArray[0];
		$paramId    = (int)$paramArray[1];

		//handle data
		$extsModel = $this->model('exts');
		if (!$extsModel->deleteParams($methodId, $paramId)) {
			$errorMsg = $extsModel->getErrorInfo();
			if(!$errorMsg){
				$errorMsg = '对不起，操作失败！请重新操作';
			}
			$this->ajax(false, $errorMsg);
		}

		$this->ajax(true, '恭喜！操作成功', array('targeturl'=>'refresh'));
	}

	/**
	 * Ajax调用：删除method参数信息
	 *
	 * @access protected
	 * @return array
	 */
	protected function _getSystemKeyWords() {

		return array(
			'get', 
			'post', 
			'request', 
			'getCliParams', 
			'getCookie',  
			'setCookie', 
			'deleteCookie', 
			'getSession', 
			'setSession', 
			'deleteSession', 
			'showMsg', 
			'dump', 
			'redirect', 
			'import', 
			'getConfig', 
			'getBaseUrl', 
			'createUrl', 
			'getSelfUrl', 
			'getActionUrl', 
			'getAssetUrl', 
			'getServerName', 
			'getClientIp', 
			'instance', 
			'model', 
			'ext', 
			'ajax', 
			'setLayout', 
			'getView', 
			'assign', 
			'display',  
			'widget', 
			'render', 
			'loadExtensions', 
			'getExtName', 
			'getExtPath', 
			'_getExtRoot',
			'__construct', 
			'__get',
			'__set',
			'__call',
			'_stripSlashes',
			'_exception',
		);
	}	
}