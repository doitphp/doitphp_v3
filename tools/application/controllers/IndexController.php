<?php
/**
 * Doit tools 首页
 *
 * @author tommy
 * @copyright Copyright (C) www.doitphp.com 2020 All rights reserved.
 * @version $Id: IndexController.php 1.0 2020-04-18 11:15:47Z tommy $
 * @package Controller
 * @since 1.0
 */
namespace controllers;

use doitphp\library\Client;

class IndexController extends BaseController {

	/**
	 * 引导页:项目的运行环境信息(主要)
	 *
	 * @access public
	 * @return void
	 */
	public function indexAction() {

		//检查$_SERVER变量
		$serverVars = array('SCRIPT_NAME', 'REQUEST_URI', 'HTTP_HOST', 'SERVER_PORT', 'HTTP_USER_AGENT', 'REQUEST_TIME', 'HTTP_ACCEPT_LANGUAGE', 'REMOTE_ADDR', 'HTTP_REFERER');

		$missArray = array();
		foreach ($serverVars as $value) {
			if (!isset($_SERVER[$value])) {
				$missArray[] = $value;
			}
		}

		//支持的数据库
		$databaseArray = array();
		if (function_exists('mysql_get_client_info') || extension_loaded('pdo_mysql')) {
			$databaseArray[] = 'MySql';
		}
		if (function_exists('mssql_connect') || extension_loaded('pdo_mssql')) {
			$databaseArray[] = 'MSSQL';
		}
		if (function_exists('pg_connect') || extension_loaded('pdo_pgsql')) {
			$databaseArray[] = 'PostgreSQL';
		}
		if (function_exists('oci_connect') || extension_loaded('pdo_oci8') || extension_loaded('pdo_oci')) {
			$databaseArray[] = 'Oracle';
		}
		if (extension_loaded('sqlite') || extension_loaded('pdo_sqlite')) {
			$databaseArray[] = 'Sqlite';
		}
		if (extension_loaded('mongo')) {
			$databaseArray[] = 'MongoDB';
		}

		//检查GD库
		if (extension_loaded('gd')) {
			$gdinfo=gd_info();
			$gdResult = (!$gdinfo['FreeType Support']) ? '<span class="text-danger">Not Support FreeType</span>' : 'Yes';
		} else {
			$gdResult = '<span class="text-danger">No</span>';
		}

		//assign params
		$this->assign(array(
		'pageTitle'     => '首页',
		'serverResult'  => ($missArray) ? '<span class="text-danger">$_SERVER不支持的变量为: ' . implode(', ', $missArray) . '</span>' : 'Yes',
		'databaseInfo'  => implode(',', $databaseArray),
		'gdResult'      => $gdResult,
		'operateSystem' => Client::getOs(),
		'phpinfoUrl'    => $this->getActionUrl('info'),
		));

		//display page
		$this->display();
	}

	/**
	 * 当前项目运行环境的详细信息：phpinfo
	 *
	 * @access public
	 * @return void
	 */
	public function infoAction() {

		phpinfo();
	}
}