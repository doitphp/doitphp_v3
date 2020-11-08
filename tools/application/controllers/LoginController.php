<?php
/**
 * 管理登陆
 *
 * @author tommy
 * @copyright Copyright (C) www.doitphp.com 2020 All rights reserved.
 * @version $Id: LoginController.php 1.0 2020-04-18 11:27:54Z tommy $
 * @package Controller
 * @since 1.0
 */
namespace controllers;

use doitphp\core\Controller;

class LoginController extends Controller {

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
	 * 登陆页(view)
	 *
	 * @access public
	 * @return void
	 */
	public function indexAction() {

		//当登陆状态为已登陆时，直接跳转网址
		$loginStatus = $this->_getLoginStatus();
		if($loginStatus){
			$targetUrl = $this->_getGotoUrl();
			$this->redirect($targetUrl);
		}

		//assign params
		$this->assign(array(
			'actionUrl' => $this->getActionUrl('ajax_login'),
		));

		//display page
		$this->display();
	}

	/**
	 * Ajax调用：登陆分析
	 *
	 * @access public
	 * @return void
	 */
	public function ajax_loginAction() {

		//获取参数
		$userName = $this->post('user_name');
		$passWord = $this->post('user_password');
		if (!$userName || !$passWord) {
			$this->ajax(false, '对不起，用户名或密码不能为空');
		}

		//分析判断登陆状态
		$loginModel = $this->model('login');

		if(!$loginModel->validateLogin($userName, $passWord)){
			$errorMsg = $loginModel->getErrorInfo();
			if(!$errorMsg){
				$errorMsg = '登陆操作失败，请重新操作!';
			}
			$this->ajax(false, $errorMsg);
		}

		//set cookie
		$this->setCookie(self::LOGIN_COOKIE_NAME, true);

		//get redirect url
		$targetUrl = $this->_getGotoUrl();

		$this->ajax(true, '登陆成功!', array('targeturl'=>$targetUrl));
	}

	/**
	 * 登出页
	 *
	 * @access public
	 * @return void
	 */
	public function logoutAction() {

		//set login status cookie
		$loginStatus = $this->_getLoginStatus();
		if($loginStatus){
			$this->setCookie(self::LOGIN_COOKIE_NAME, false);
		}

		$this->redirect($this->getActionUrl('index'));
	}

	/**
	 * 前函数(类方法)
	 *
	 * @access protected
	 * @return void
	 */
	protected function _init() {

		//assign params
		$this->assign(array(
			'baseUrl'      => $this->getBaseUrl(),
			'baseAssetUrl' => $this->getAssetUrl(),
		));
	
		return true;
	}

	/**
	 * 获取当前登陆状态
	 *
	 * @access protected
	 * @return boolean
	 */
	protected function _getLoginStatus() {

		$loginStatus = $this->getCookie(self::LOGIN_COOKIE_NAME);
		if(!$loginStatus){
			return false;
		}

		return true;
	}

	/**
	 * 分析跳转网址
	 *
	 * @access protected
	 * @return string
	 */
	protected function _getGotoUrl() {

		$gotoUrl = $this->getCookie(self::GOTOURL_COOKIE_NAME);
		$gotoUrl = (!$gotoUrl) ? $this->createUrl('index/index') : $gotoUrl;

		return $gotoUrl;
	}	
}