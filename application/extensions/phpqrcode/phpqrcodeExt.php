<?php
/**
 * PHP QrCode(二维码)扩展模块
 *
 * @author tommy
 * @copyright Copyright (C) www.doitphp.com 2020 All rights reserved.
 * @version $Id: phpqrcodeExt.php 1.0 2020-11-13 02:16:21Z tommy $
 * @package extension
 * @since 1.0
 */
namespace extensions;

use doitphp\core\Extension;

class phpqrcodeExt extends Extension {

	/**
	 * 构造方法
	 *
	 * @access public
	 * @return boolean
	 */
	public function __construct() {

		//load php qrcode file
		$qrcodeFilePath = $this->getExtPath() . '/phpqrcode.php';

		$this->import($qrcodeFilePath);

		return true;
	}

	/**
	 * 生成二维码图片。
	 * 图片格式为:png， 图片尺寸为：243px * 243px。
	 * 
	 * @access public
	 * 
	 * @param string $text 所要生成的二维码图片内容
	 * @param string $destFilePath 输出的二维码图片的路径。默认为false时，则直接显示图片。
	 * 
	 * @return boolean
	 */
	public function makeImage($text, $destFilePath = false) {

		return QRcode::png($text, $destFilePath, QR_ECLEVEL_L, 9, 1);
	}

}