<?php
/**
 * 模型：管理登陆操作
 *
 * @author tommy
 * @copyright Copyright (C) www.doitphp.com 2020 All rights reserved.
 * @version $Id: loginModel.php 1.0 2020-04-18 13:06:36Z tommy $
 * @package Model
 * @since 1.0
 */
namespace models;

use doitphp\core\Configure;

class loginModel {

	/**
   * 错误信息
   *
   * @var string
   */
	protected $_errorInfo = null;
	
	/**
	 * 分析管理员用户名及密码是否正确
	 *
	 * @access public
	 *
	 * @param string $username 管理员用户名
	 * @param string $password 登陆密码
	 *
	 * @return boolean
	 */
	public function validateLogin($username, $password) {

		//parse params
		if(!$username || !$password){
			return false;
		}

		//get configure of admin login
		$loginInfo = Configure::get('adminLogin');
		if($username != $loginInfo['username'] || $password != $loginInfo['password']){
			$this->_setErrorInfo('用户名或密码不正确！');
			return false;
		}

		return true;
	}

	  /**
   * 设置当前模型的错误信息
   *
   * @access protected
   *
   * @param string $message 所要设置的错误信息
   *
   * @return boolean
   */
  protected function _setErrorInfo($message) {

    //参数分析
    if (!$message) {
      return false;
    }

    //对信息进行转义
    $this->_errorInfo = $message;

    return true;
	}
	
  /**
   * 获取当前模型的错误信息
   *
   * @access public
   * @return string
   */
  public function getErrorInfo() {

    return $this->_errorInfo;
  }  	
}