<?php
/**
 * 控制器文件操作管理
 *
 * @author tommy
 * @copyright Copyright (C) www.doitphp.com 2020 All rights reserved.
 * @version $Id: CtlsController.php 1.0 2020-04-18 11:49:04Z tommy $
 * @package Controller
 * @since 1.0
 */
namespace controllers;

use doitphp\library\File;
use library\fileCreator;

class CtlsController extends BaseController {

	/**
	 * 索引页：生成controller文件 数据提交页
	 *
	 * @access public
	 * @return void
	 */
	public function indexAction() {

		//get storage file info
		$ctlModel = $this->model('ctls');
		$fileData = $ctlModel->getData();

		//assign params
		$this->assign(array(
			'pageTitle'     => 'Controller文件管理',
			'fileData'      => $fileData,

			'editActionUrl' => $this->getActionUrl('edit_action'),
			'editMethodUrl' => $this->getActionUrl('edit_method'),
			'addParamsUrl'  => $this->getActionUrl('add_params'),
			'editParamsUrl' => $this->getActionUrl('edit_params'),
		));

		//display page
		$this->display();
		
	}

	/**
	 * Ajax调用：生成新的controller文件
	 *
	 * @access public
	 * @return void
	 */
	public function ajax_create_fileAction() {

		//get params
		$controllerName = $this->post('controller_name');
		if (!$controllerName) {
			$this->ajax(false, '对不起，Controller 名称不能为空！');
		}
		if($controllerName){
			$controllerName = trim($controllerName, '_');
		}
		if (!$controllerName) {
			$this->ajax(false, '对不起，Controller名称格式错误！');
		}
		$controllerName   = strtolower($controllerName);

		$viewFolderStatus = $this->post('is_view_dir');
		$viewFileStatus   = $this->post('is_view_file');

		$description = $this->post('note_description');
		$author      = $this->post('note_author');
		$copyright   = $this->post('note_copyright');
		$license     = $this->post('note_license');
		$link        = $this->post('note_link');

		//controller文件子目录分析
		$fileInfo = $this->_parseClassInfo($controllerName);

		$controllerName = ucfirst($fileInfo['className']);
		//系统关键词分析过滤
		if(in_array($controllerName, array('Layouts', 'Widgets', 'Errors'))){
			$this->ajax(false, '对不起，您输入的Controller名称为系统关键词，请更换Controller名称!');
		}
		if($fileInfo['subdir']){
			$subDirs = explode(DS, $fileInfo['subdir']);
			if(in_array($subDirs[0], array('layouts', 'widgets', 'errors'))){
				$this->ajax(false, '对不起，您输入的Controller名称含有系统关键词，请更换Controller名称!');
			}
		}

		$webappPath = $this->_parseWebAppPath();
		$filePath   = $webappPath . DS . 'application/controllers';
		if($fileInfo['subdir']){
			$filePath .= DS . $fileInfo['subdir'];
		}
		
		$fileName  = $controllerName . 'Controller.php';
		$filePath .= DS . $fileName;

		//判断文件是否已存在
		if (is_file($filePath)) {
			$this->ajax(false, '对不起，所要创建的Controller文件已存在！');
		}

		//获取所要创建的controller文件内容
		$ctlModel = $this->model('ctls');
		$fileData = $ctlModel->getData();

		//分析并组装controller文件内容
		$fileContent  = "<?php\n";
		$fileContent .= fileCreator::fileNote($fileName, $description, $author, $copyright, 'controllers', $license, $link);
		$fileContent .= "namespace controllers{$fileInfo['nameSpace']};\n\nuse doitphp\core\Controller;\n\n";
		$fileContent .= fileCreator::classCodeStart($controllerName . 'Controller', 'Controller', false);

		//分析Controller文件的Action列表
		$actionLists = (!isset($fileData['actions']) || !$fileData['actions']) ? array() : $fileData['actions'];

		$actionArray = array();
		if($actionLists){
			foreach($actionLists as $lines){
				$fileContent .= fileCreator::methodNote('public', 'void', null, $lines['description']);
				$fileContent .= fileCreator::methodCode($lines['name'] . 'Action', 'public');

				$actionArray[] = strtolower($lines['name']);
			}
		}

		//分析默认Action
		if(!in_array('index', $actionArray)){			
			$fileContent .= fileCreator::methodNote('public', 'void', null, '索引页');
			$fileContent .= fileCreator::methodCode('indexAction', 'public');

			$actionArray[] = 'index';
		}

		//分析Controller文件的类方法(method)列表
		$methodLists = (!isset($fileData['methods']) || !$fileData['methods']) ? array() : $fileData['methods'];

		$methodArray = array();
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

				$methodArray[] = strtolower($lines['name']);
			}

			//默认类方法(method)分析
			if(!in_array('_init', $methodArray)){
				$fileContent .= fileCreator::methodNote('protected', 'boolean', array(), '回调类方法(前函数)：初始化运行环境');
				$fileContent .= fileCreator::methodCode('_init', 'protected');
			}
		}

		$fileContent .= fileCreator::classCodeEnd();

		//写入文件内容(生成文件)
		if(!File::writeFile($filePath, $fileContent)){
			$this->ajax(false, '对不起，创建Controller文件失败！请重新操作');
		}

		//处理视图目录及视图文件
		if($viewFolderStatus){
			//分析视图目录的路径
			$viewDirPath = $webappPath . DS . 'application/views';
			if($fileInfo['subdir']){
				$viewDirPath .= DS . $fileInfo['subdir'];
			}
			$viewDirPath .= DS . strtolower($controllerName);
			File::makeDir($viewDirPath);

			//生成视图文件
			if ($viewFileStatus) {
				foreach ($actionArray as $actionName) {
					$viewFilePath = $viewDirPath . DS . $actionName . '.php';
					File::writeFile($viewFilePath);
				}
			}
		}

		//清空Controller文件的内容信息存贮数据
		$ctlModel->clearData();

		//保存Controller文件的文件头代码注释信息, 以备下一个文件共用
		$notationModel = $this->model('notationStorage');
		$notationModel->setNotation($author, $copyright, $license, $link);

		$this->ajax(true, 'Controller文件创建成功！', array('targeturl'=>'refresh'));
	}

	/**
	 * 添加Action
	 *
	 * @access public
	 * @return void
	 */
	public function add_actionAction() {

		//display page
		$this->render();
	}

	/**
	 * Ajax调用：添加aciton数据
	 *
	 * @access public
	 * @return void
	 */
	public function ajax_add_actionAction() {

		//get params
		$name = $this->post('action_name');
		$desc = $this->post('action_desc');

		if (!$name) {
			$this->ajax(false, '对不起，参数不正确！');
		}
		//Action名称统一为小写字母
		$name = strtolower($name);

		$ctlModel = $this->model('ctls');	
		if (!$ctlModel->addAction($name, $desc)) {
			$errorMsg = $ctlModel->getErrorInfo();
			if(!$errorMsg){
				$errorMsg = '对不起，操作失败！请重新操作';
			}
			$this->ajax(false, $errorMsg);
		}

		$this->ajax(true, '恭喜！操作成功', array('targeturl'=>'refresh'));
	}

	/**
	 * 编辑action
	 *
	 * @access public
	 * @return void
	 */
	public function edit_actionAction() {

		//get params
		$actionId = (int)$this->get('id');

		//get storage file info
		$ctlModel = $this->model('ctls');
		$fileData = $ctlModel->getData();

		if(!isset($fileData['actions'][$actionId]) || !$fileData['actions'][$actionId]){
			exit('所要编辑的Aciton信息不存在');
		}

		//get action info
		$actionInfo = $fileData['actions'][$actionId];

		//assign params
		$this->assign(array(
			'actionId'   => $actionId,
			'actionInfo' => $actionInfo,
		));

		//display page
		$this->render();
	}

	/**
	 * Ajax调用：编辑action
	 *
	 * @access public
	 * @return void
	 */
	public function ajax_edit_actionAction() {

		//get params
		$id   = $this->post('action_id');
		$name = $this->post('action_name');
		$desc = $this->post('action_desc');

		if (is_null($id) || !$name) {
			$this->ajax(false, '对不起，参数不正确！');
		}
		//Action名称统一为小写字母
		$name = strtolower($name);

		$ctlModel = $this->model('ctls');	
		if (!$ctlModel->editAction($id, $name, $desc)) {
			$errorMsg = $ctlModel->getErrorInfo();
			if(!$errorMsg){
				$errorMsg = '对不起，操作失败！请重新操作';
			}
			$this->ajax(false, $errorMsg);
		}

		$this->ajax(true, '恭喜！操作成功', array('targeturl'=>'refresh'));
	}

	/**
	 * Ajax调用：删除action
	 *
	 * @access public
	 * @return void
	 */
	public function ajax_delete_actionAction() {

		//get params
		$id = $this->post('action_id');
		if (is_null($id)) {
			$this->ajax(false, '对不起，错误的参数调用！');
		}

		$ctlModel = $this->model('ctls');	
		if (!$ctlModel->deleteAction($id)) {
			$errorMsg = $ctlModel->getErrorInfo();
			if(!$errorMsg){
				$errorMsg = '对不起，操作失败！请重新操作';
			}
			$this->ajax(false, $errorMsg);
		}

		$this->ajax(true, '恭喜！操作成功', array('targeturl'=>'refresh'));
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

		//filter system keywords
		$keywords = $this->_getSystemKeyWords();
		if(in_array($name, $keywords)){
			$this->ajax(false, '您输入的Method名称为系统关键词！请更改输入');
		}

		//handle data
		$ctlModel = $this->model('ctls');	
		if (!$ctlModel->addMethod($name, $desc, $access, $type)) {
			$errorMsg = $ctlModel->getErrorInfo();
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
		$ctlModel = $this->model('ctls');
		$fileData = $ctlModel->getData();

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
			$this->ajax(false, '您所要编辑的Method名称为系统关键词！请更改输入');
		}

		//handle data
		$ctlModel = $this->model('ctls');	
		if (!$ctlModel->editMethod($id, $name, $desc, $access, $type)) {
			$errorMsg = $ctlModel->getErrorInfo();
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
		$id     = $this->post('method_id');

		if (is_null($id)) {
			$this->ajax(false, '对不起，参数不正确！');
		}

		//handle data
		$ctlModel = $this->model('ctls');	
		if (!$ctlModel->deleteMethod($id)) {
			$errorMsg = $ctlModel->getErrorInfo();
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
		$ctlModel = $this->model('ctls');	
		if (!$ctlModel->addParams($methodId, $name, $type, $desc, $default)) {
			$errorMsg = $ctlModel->getErrorInfo();
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
		$ctlModel = $this->model('ctls');
		$fileData = $ctlModel->getData();

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
		$ctlModel = $this->model('ctls');	
		if (!$ctlModel->editParams($methodId, $paramId, $name, $type, $desc, $default)) {
			$errorMsg = $ctlModel->getErrorInfo();
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
		$ctlModel = $this->model('ctls');	
		if (!$ctlModel->deleteParams($methodId, $paramId)) {
			$errorMsg = $ctlModel->getErrorInfo();
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
			'__construct', 
			'__get',
			'__set',
			'__call',
			'_stripSlashes',
			'_exception',
		);
	}
}