<?php
/**
 * phpmailer扩展模块
 *
 * @author tommy
 * @copyright Copyright (C) www.doitphp.com 2020 All rights reserved.
 * @version $Id: phpmailerExt.php 1.0 2020-11-13 03:33:03Z tommy $
 * @package extension
 * @since 1.0
 */
namespace extensions;

//加载第三方库文件
include_once BASE_PATH . '/extensions/phpmailer/PHPMailer/class.phpmailer.php';

class phpmailerExt extends \PHPMailer {

	/**
	 * 构造方法
	 *
	 * @access public
	 * @return boolean
	 */
	public function __construct() {

		$this->exceptions = true;
		return true;
	}

	/**
	 * 设置smtp server 连接参数
	 *
	 * @access public
	 * @param array		$option	smtp服务器连接参数
	 * @return boolean
	 *
	 * @example
	 * $option = array (
	 *  'secure'   => 'TLS',
	 *	'host'     => 'smtp.tommycode.com',
	 *	'username' => 'tommy',
	 *	'password' => 'yourpassword',
	 *	'from'     =>'service@tommycode.com',
	 *	'fromname' =>'tommy support',
	 *	'reply'    =>'service@tommycode.com',
	 * );
	 *
	 * $mailerObject = $this->ext('phpmailer');
	 *
	 * $mailerObject->setSmtpConfig($option);
	 */
	public function setSmtpConfig($option) {

		//parse params
		if (empty($option) || !is_array($option)) {
			return false;
		}

		//设置SSL加密
		if (isset($option['secure']) && $option['secure']) {
			$this->SMTPSecure = $option['secure'];
		}

		$this->Host 	= $option['host'];
		$this->Username = $option['username'];
		$this->Password = $option['password'];

		$this->From 	= empty($option['from']) ? $option['username'] . '@' . str_replace('stmp.', '', $option['host']) : $option['from'];
		$this->FromName = empty($option['fromname']) ? $option['username'] : $option['fromname'];

		//设置smtp端口.
		$this->Port = empty($option['port']) ? 25 : $option['port'];

		if (empty($option['reply'])) {
			$this->AddReplyTo($this->From);
		} else {
			$this->AddReplyTo($option['reply']);
		}
		//$this->SMTPDebug  = 2;

		//clear unuseful memory
		unset($option);

		return true;
	}

	/**
	 * 发送邮件内容
	 *
	 * @access public
	 * @param string $to		所发送的邮件地址
	 * @param string $subject	邮件题目
	 * @param string $body		邮件内容, 支持html标签
	 * @return boolean
	 */
	public function sendMail($to, $subject, $body) {

		$this->IsSMTP();
		$this->SMTPAuth = true;

		$this->CharSet ="utf-8";
		$this->Encoding = "base64";

		$this->AddAddress($to);

		$this->Subject = $subject;
		$this->MsgHTML($body);
		$this->IsHTML(true);

		return $this->Send() ? true : false;
	}
}