<?php
/**
 * 模型文件操作管理
 *
 * @author tommy
 * @copyright Copyright (C) www.doitphp.com 2020 All rights reserved.
 * @version $Id: ModsController.php 1.0 2020-04-18 11:53:11Z tommy $
 * @package Controller
 * @since 1.0
 */
namespace controllers;

use doitphp\core\Configure;
use doitphp\core\DbPdo;
use doitphp\library\File;
use library\fileCreator;

class ModsController extends BaseController {

	/**
	 * 新建模型文件数据提交页(索引页)
	 *
	 * @access public
	 * @return void
	 */
	public function indexAction() {

		//get storage file info
		$modsModel = $this->model('mods');
		$fileData  = $modsModel->getData();

		//获取数据表列表信息
		$tableList = array();
		if(Configure::get('database')){			
			$tableList = $this->_getTableLists();
		}

		//assign params
		$this->assign(array(
			'pageTitle'     => 'Model文件管理',
			'fileData'      => $fileData,
			'editMethodUrl' => $this->getActionUrl('edit_method'),
			'addParamsUrl'  => $this->getActionUrl('add_params'),
			'editParamsUrl' => $this->getActionUrl('edit_params'),
			'tableList'			=> $tableList,
		));

		//display page
		$this->display();		
	}

	/**
	 * Ajax调用：生成模型文件
	 *
	 * @access public
	 * @return void
	 */
	public function ajax_create_fileAction() {

		//get params
		$modelName = $this->post('model_name');
		if (!$modelName) {
			$this->ajax(false, '对不起，Model名称不能为空！');
		}		
		if ($modelName) {
			$modelName = trim($modelName, '_');
		}
		if (!$modelName) {
			$this->ajax(false, '对不起，Model名称格式不正确！');
		}

		$tableStatus = $this->post('bind_table_status');
		$tableName   = $this->post('table_name');

		$author      = $this->post('note_author');
		$copyright   = $this->post('note_copyright');
		$license     = $this->post('note_license');
		$link        = $this->post('note_link');
		$description = $this->post('note_description');

		//分析Model名称
		$fileInfo  = $this->_parseClassInfo($modelName);
		$modelName = $fileInfo['className'] . 'Model';

		//获取数据表字段信息
		if ($tableStatus) {
			if (!$tableName) {
				$this->ajax(false, '对不起，所绑定的数据表名不能为空！');
			}

			$tableInfo = $this->_getTableInfo($tableName);
		}

		//parse file path
		$webappPath = $this->_parseWebAppPath();
		$filePath   = $webappPath . DS . 'application/models';
		if($fileInfo['subdir']){
			$filePath .= DS . $fileInfo['subdir'];
		}

		$fileName  = $modelName . '.php';
		$filePath .= DS . $fileName;

		//当所要创建的Model文件存在时
		if (is_file($filePath)) {
			$this->ajax(false, '对不起，所要创建的Model文件已存在！');
		}

		//获取所要创建的Model文件内容
		$modsModel = $this->model('mods');
		$fileData  = $modsModel->getData();

		//分析并组装model文件内容
		$fileContent  = "<?php\n";
		$fileContent .= fileCreator::fileNote($fileName, $description, $author, $copyright, 'Model', $license, $link);
		$fileContent .= "namespace models{$fileInfo['nameSpace']};\n\nuse doitphp\core\Model;\n\n";
		$fileContent .= fileCreator::classCodeStart($modelName, 'Model', false);

		//分析Model文件的类方法(method)列表
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

			//分析当前Model文件所绑定的数据表字段信息			
			if($tableStatus){
				//数据表名称
				$fileContent .= fileCreator::methodNote('protected', 'string', array(), '定义本Model Class所绑定数据表名称');
				$fileContent .= fileCreator::methodCode('_tableName', 'protected', array(), "\t\treturn '{$tableName}';");

				//数据表主键
				$fileContent .= fileCreator::methodNote('protected', 'array', array(), '定义数据表主键');
				$fileContent .= fileCreator::methodCode('_primaryKey', 'protected', array(), "\t\treturn '{$tableInfo['primaryKey'][0]}';");

				//数据表字段列表
				$fileContent .= fileCreator::methodNote('protected', 'array', array(), '定义数据表字段信息');
				$fileContent .= fileCreator::methodCode('_tableFields', 'protected', array(), "\t\treturn array('" . implode('\', \'', $tableInfo['fields']) . "');");
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
			$this->ajax(false, '对不起，创建Model文件失败！请重新操作');
		}

		//清空Model文件的内容信息存贮数据		
		$modsModel->clearData();

		//保存Controller文件的文件头代码注释信息, 以备下一个文件共用
		$notationModel = $this->model('notationStorage');
		$notationModel->setNotation($author, $copyright, $license, $link);

		$this->ajax(true, 'Model文件创建成功！', array('targeturl'=>'refresh'));
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
		$modsModel = $this->model('mods');	
		if (!$modsModel->addMethod($name, $desc, $access, $type)) {
			$errorMsg = $modsModel->getErrorInfo();
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
		$modsModel = $this->model('mods');
		$fileData  = $modsModel->getData();

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
			$this->ajax(false, '您输入的Method名称为系统关键词！请更改输入');
		}

		//handle data
		$modsModel = $this->model('mods');	
		if (!$modsModel->editMethod($id, $name, $desc, $access, $type)) {
			$errorMsg = $modsModel->getErrorInfo();
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
		$modsModel = $this->model('mods');	
		if (!$modsModel->deleteMethod($id)) {
			$errorMsg = $modsModel->getErrorInfo();
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
		$modsModel = $this->model('mods');
		if (!$modsModel->addParams($methodId, $name, $type, $desc, $default)) {
			$errorMsg = $modsModel->getErrorInfo();
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
		$modsModel = $this->model('mods');
		$fileData  = $modsModel->getData();

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
		$modsModel = $this->model('mods');	
		if (!$modsModel->editParams($methodId, $paramId, $name, $type, $desc, $default)) {
			$errorMsg = $modsModel->getErrorInfo();
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
		$modsModel = $this->model('mods');
		if (!$modsModel->deleteParams($methodId, $paramId)) {
			$errorMsg = $modsModel->getErrorInfo();
			if(!$errorMsg){
				$errorMsg = '对不起，操作失败！请重新操作';
			}
			$this->ajax(false, $errorMsg);
		}

		$this->ajax(true, '恭喜！操作成功', array('targeturl'=>'refresh'));
	}
	
	/**
	 * 获取数据表配置参数
	 *
	 * @access protected
	 * @return array
	 */
	protected function _getDbParams() {

		$dbParams = Configure::get('database');
		if (!$dbParams) {
			$this->showMsg('对不起，配置文件没有配置数据库连接参数！');
		}
		if (!isset($dbParams['charset'])) {
			$dbParams['charset'] = 'utf8';
		}

		return $dbParams;
	}	

	/**
	 * 获取数据表列表
	 *
	 * @access protected
	 * @return array
	 */
	protected function _getTableLists() {

		$dbParams = $this->_getDbParams();

		//数据库连接
		$dbObj     = DbPdo::getInstance($dbParams);
		$tableList = $dbObj->getTableList();

		//当使用数据表前缀时,将前缀名过滤掉
		if (isset($dbParams['prefix']) && $dbParams['prefix']) {
			$strLength = strlen($dbParams['prefix']);
			foreach ($tableList as $key=>$tableName) {
				if (substr($tableName, 0, $strLength) == $dbParams['prefix']) {
					$tableList[$key] = substr($tableName, $strLength);
				} else {
					unset($tableList[$key]);
				}
			}
		}

		return $tableList;
	}

	/**
	 * 获取数据表列表
	 *
	 * @access protected
	 * 
	 * @param string $tableName 数据表
	 * @return array
	 */
	protected function _getTableInfo($tableName) {

		$dbParams = $this->_getDbParams();
		if (isset($dbParams['prefix']) && $dbParams['prefix']) {
			$tableName = $dbParams['prefix'] . $tableName;
		}

		//数据库连接
		$dbObj      = DbPdo::getInstance($dbParams);

		$tableLists = $dbObj->getTableList();
		if(!in_array($tableName, $tableLists)){
			$this->ajax(false, '对不起，所绑定的数据表不存在！');
		}

		return $dbObj->getTableInfo($tableName);
	}

	/**
	 * Ajax调用：删除method参数信息
	 *
	 * @access protected
	 * @return array
	 */
	protected function _getSystemKeyWords() {

		return array(
			'__construct', 
			'__get',  
			'__set', 
			'__call', 
			'__destruct', 
			'_master', 
			'_slave', 
			'getInstance',
			'getConnection', 			
			'getTableName', 
			'execute', 
			'query', 
			'insert', 
			'update', 
			'replace', 
			'delete', 
			'find', 
			'findAll', 
			'getOne', 
			'getAll', 
			'where', 
			'order', 
			'fields', 
			'limit', 
			'pageLimit',
			'getLastInsertId', 
			'quoteInto', 
			'count', 
			'distinct', 
			'max', 
			'min', 
			'sum', 
			'avg', 
			'startTrans', 
			'commit', 
			'rollback', 
			'createSql', 
			'setTableName', 
			'dump', 
			'model', 
			'getConfig', 
			'getTablePrefix', 
			'setErrorInfo', 
			'getErrorInfo', 
			'_parseConfig', 
			'_filterFields', 
			'_parseFields', 
			'_parseCondition', 
			'_parseOrder', 
			'_parseLimit', 
			'_getValueByFunction', 
			'_getPrimaryKey', 
			'_getTableFields', 
		);
	}	
}