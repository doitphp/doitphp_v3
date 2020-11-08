<?php
/**
 * 项目主配置文件
 *
 * @author tommy <tommy@doitphp.com>
 * @link http://www.doitphp.com
 * @copyright Copyright (C) Copyright (c) 2012 www.doitphp.com All rights reserved.
 * @license New BSD License.{@link http://www.opensource.org/licenses/bsd-license.php}
 * @version $Id: application.php 1.0 2013-01-11 21:53:32Z tommy <tommy@doitphp.com> $
 * @package config
 * @since 1.0
 */
use doitphp\core\Configure;

if (!defined('IN_DOIT')) {
    exit();
}

/**
 * 设置时区，默认时区为东八区(中国)时区(Asia/ShangHai)。
 */
//$config['application']['defaultTimeZone'] = 'Asia/ShangHai';

/**
 * 设置URL网址的格式。
 *  Configure::GET_FORMAT为:index.php?router=controller/action&params=value;
 *  Configure::PATH_FORMAT为:index.php/controller/action/params/value。
 * 默认为:Configure::PATH_FORMAT
 */
//$config['application']['urlFormat'] = Configure::PATH_FORMAT;

/**
 * 设置是否开启URL路由网址重写(Rewrite)功能。true:开启；false:关闭。默认:关闭。
 */
//$config['application']['rewrite'] = true;

/**
 * 设置是否开启Debug调用功能。true:开启；false:关闭。默认:关闭。
 */
//$config['application']['debug'] = true;

/**
 * 设置是否开启日志记录功能。true:开启；false:关闭。默认:关闭。
 */
//$config['application']['log'] = true;

/**
 * 自定义项目(application)目录路径的设置。注:结尾无需"/"，建议用绝对路径。
 */
//$config['application']['basePath'] = APP_ROOT . '/application';

/**
 * 自定义缓存(cache)目录路径的设置。注:结尾无需"/"，建议用绝对路径。
 */
//$config['application']['cachePath'] = APP_ROOT . '/cache';

/**
 * 自定义日志(log)目录路径的设置。注:结尾无需"/"，建议用绝对路径。
 */
//$config['application']['logPath'] = APP_ROOT . '/logs';

//设置登陆用户及密码
$config['adminLogin'] = array(
	'username'=>'doitphp',
	'password'=>123456,
);

//设置所要创建的应用(项目)的根目录, 注：结尾无需"/"。
$config['webappPath'] = substr(APP_ROOT, 0, -6);

/*$config['database'] = array(
   'dsn'      => 'mysql:host=localhost;dbname=yourDbname',
   'username' => 'yourUsername',
   'password' => 'yourPassword',
   'prefix'   => '',
   'charset'  => 'utf8',
);*/

/**
* 设置Cookie生存周期(默认：4小时)
*/
$config['cookie']['expire'] = 14400;

/**
* 设置Session生存周期(默认：1小时)
*/
$config['session']['expire'] = 3600;