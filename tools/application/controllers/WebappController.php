<?php
/**
 * 项目目录管理(WebApp管理)
 *
 * @author tommy
 * @copyright Copyright (C) www.doitphp.com 2020 All rights reserved.
 * @version $Id: WebappController.php 1.0 2020-04-24 14:21:56Z tommy $
 * @package Controller
 * @since 1.0
 */
namespace controllers;

class WebappController extends BaseController {

	/**
	 * 表单页：生成项目目录
	 *
	 * @access public
	 * @return void
	 */
	public function indexAction() {

		//parse webapp dir		
		$webappStatus = !is_dir($this->_webappPath . DS . 'application') ? false : true;
		if($webappStatus){
			$this->redirect($this->createUrl('files/index'));
		}

		//parse wethoer apache server
		$isApache = (strpos($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false) ? true : false;

		//assign params
		$this->assign(array(
		'pageTitle'    => 'WebApp管理',
		'webappStatus' => $webappStatus,
		'isApache'     => $isApache,
		'actionUrl'    => $this->getActionUrl('ajax_create_project'),
		));

		//display page
		$this->display();
	}

	/**
	 * Ajax调用：生成项目目录
	 *
	 * @access public
	 * @return void
	 */
	public function ajax_create_projectAction() {

		//get params
		$serverName = $this->post('webserver_name');
		$htaccess   = $this->post('rewrite_status', 0);

		$isApache    = ($serverName == 'apache') ? true : false;
		$hasHtaccess = ($htaccess) ? true : false;

		//instance model
		$webAppModel = $this->model('webApp');

		//create project directories and files
		$webAppModel->createProject();

		//create the file of apache rewrite rules
		if($isApache && $hasHtaccess) {
			$webAppModel->createApacheHtaccessFile();
		}

		$this->ajax(true, '项目目录创建成功！', array('targeturl'=>$this->createUrl('files/index')));
	}

}