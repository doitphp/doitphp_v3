<?php
/**
 * 公共控制器
 *
 * @author tommy
 * @copyright Copyright (C) www.doitphp.com 2020 All rights reserved.
 * @version $Id: BaseController.php 1.0 2020-04-18 11:19:12Z tommy $
 * @package Controller
 * @since 1.0
 */
namespace controllers;

use doitphp\App;
use doitphp\core\Controller;
use doitphp\core\Configure;

class BaseController extends Controller {

	/*
	* 管理员登陆状态 cookie name
	*
	* @var string
	*/
	const LOGIN_COOKIE_NAME = 'doitphp_tools_loginstatus';

	/*
	* 返回网址 cookie name
	*
	* @var string
	*/
	const GOTOURL_COOKIE_NAME = 'doitphp_goto_url';

	/**
   * 项目根目录路径
   *
   * @var string
   */
  protected $_webappPath = null;

	/**
	 * 登陆状态分析
	 *
	 * @access protected
	 * @return boolean
	 */
	protected function _parseLogin() {

		//当未登陆或登陆时间超出有效期时,跳转至登陆页
		$loginStatus = $this->getCookie(self::LOGIN_COOKIE_NAME);
		if(!$loginStatus){
			//当为ajax调用页时
			if (substr(App::getActionName(), 0, 4) == 'ajax') {
				$this->ajax(false, '对不起，您没有登陆或登陆时间已超时，请重新登陆！');
			}

			//将当前网址存贮在cookie中，以备再次登陆时，跳转至当前页
			$this->setCookie(self::GOTOURL_COOKIE_NAME, $_SERVER['REQUEST_URI']);

			$this->redirect($this->createUrl('login/index'));
		}

		//重新set cookie, 防止访问同一个页面时间过长，造成登陆cookie失效
		$this->setCookie(self::LOGIN_COOKIE_NAME, true);

		return true;
	}

	/**
	 * 前函数(类方法)
	 *
	 * @access protected
	 * @return void
	 */
	protected function _init() {

		//判断登陆状态
		$this->_parseLogin();

		//set layouts view name
		$this->setLayout('main');

		//获取应用(项目)根目录
		$this->_webappPath = $this->_parseWebAppPath();

		//assign pulic params
		$this->assign(array(
			'baseUrl'      => $this->getBaseUrl(),
			'baseAssetUrl' => $this->getAssetUrl(),
			'selfUrl'      => $this->getSelfUrl(),
			'logoutUrl'    => $this->createUrl('login/logout'),
			'webappPath'   => $this->_webappPath,
		));

		return true;
	}

	/**
	 * 分析应用(项目)的根目录路径
	 *
	 * @access protected
	 * @return string
	 */
	protected function _parseWebAppPath() {

		$webappPath = Configure::get('webappPath');
		return str_replace('\\', '/', rtrim($webappPath, '/'));
	}

	/**
	 * 通过分析类名，用于子目录类文件的信息处理。
	 *
	 * @access protected
	 *
	 * @param string $className 类名称
	 *
	 * @return array
	 */
	protected function _parseClassInfo($className) {

		//所要生成文件的信息
		$fileInfo = array(
			'className' => $className,
			'nameSpace' => '',
			'subdir'    => '',
		);

		$pos = strpos($className, '_');
		if ($pos !== false) {
			$elementArray = explode('_', strtolower($className));

			$fileInfo['className'] = array_pop($elementArray);
			$fileInfo['subdir']    = implode(DS, $elementArray);
			$fileInfo['nameSpace'] = '\\' . implode('\\', $elementArray);
		}

		return $fileInfo;
	}

}