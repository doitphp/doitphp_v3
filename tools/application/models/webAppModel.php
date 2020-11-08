<?php
/**
 * 创建项目目录文件操作
 *
 * @author tommy
 * @copyright Copyright (C) www.doitphp.com 2020 All rights reserved.
 * @version $Id: webAppModel.php 1.0 2020-04-18 12:44:01Z tommy $
 * @package Model
 * @since 1.0
 */
namespace models;

use doitphp\core\Configure;
use doitphp\library\File;

class webAppModel {

	/**
	 * 创建应用项目目录文件
	 *
	 * @access public
	 * @return boolean
	 */
	public function createProject() {

		//parse webapp path
		$appPath = $this->_getWebAppPath();

		//create project folders
		$webappDirArray = array(
			'application',
			'application/config',
			'application/controllers',
			'application/models',
			'application/views',
			'application/views/errors',
			'application/views/layouts',
			'application/views/widgets',
			'application/library',
			'application/widgets',
			'application/extensions',
			'application/language',
			'assets',
			'assets/css',
			'assets/images',
			'assets/js',
			'cache',
			'cache/temp',
			'cache/data',
			'logs',
		);

		$result = false;
		foreach($webappDirArray as $folderName){
			//make dir
			$dirPath = $appPath . DS . $folderName;

			if(!is_dir($dirPath)){
				$result  = File::makeDir($dirPath);
				if(!$result){
					break;
				}

				//create 403 access file
				$this->_createDenyFile($dirPath);
			}

			//create configure file
			if($folderName == 'application/config') {
				$this->_createConfigureFile($dirPath);
			}
		}

		if(!$result){
			return false;
		}

		//create entry file (index.php)
		$this->_createEntryFile($appPath);
		
		//create the file: robot.txt
		$this->_createRobotsFile($appPath);

		return true;
	}

	/**
	 * 生成项目的入口文件(引导文件:index.php)
	 *
	 * @access protected
	 *
	 * @param string $appPath 应用项目的根目录
	 *
	 * @return boolean
	 */
	protected function _createEntryFile($appPath) {

		$filePath   = $appPath . DS . 'index.php';
		if(is_file($filePath)){
			return true;
		}

		$fileContent = <<<EOT
<?php
/**
 * application index
 *
 * @author tommy <tommy@doitphp.com>
 * @copyright Copyright (C) 2009-2020 www.doitphp.com All rights reserved.
 * @version \$Id: index.php 3.2 2020-4-18 00:00:00Z tommy \$
 * @package application
 * @since 1.0
 */
use doitphp\App;

define('IN_DOIT', true);

/**
 * 定义项目所在路径(根目录):APP_ROOT
 */
define('APP_ROOT', dirname(__FILE__));

/**
 * 加载DoitPHP框架的初始化文件,如果必要可以修改文件路径
 */
require_once APP_ROOT . '/doitphp/App.php';

\$configFile = APP_ROOT . '/application/config/application.php';

/**
 * 启动应用程序(网站)进程
 */
App::run(\$configFile);
EOT;

		return File::writeFile($filePath, $fileContent);
	}

	/**
	 * 创建项目在Apache Web Server下的重写规则引导文件(.htaccess)
	 *
	 * @access public
	 * @return boolean
	 */
	public function createApacheHtaccessFile() {

		$appPath     = $this->_getWebAppPath();
		$filePath    = $appPath . DS . '.htaccess';
		$fileContent = <<<EOT
RewriteEngine on

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule !\\.(js|ico|txt|gif|jpg|png|css)\\\$ index.php [NC,L]
EOT;

		return File::writeFile($filePath, $fileContent);
	}

	/**
	 * 生成权限保护文件：403拒绝访问
	 *
	 * @access protected
	 *
	 * @param string $dirPath 目录路径
	 *
	 * @return string
	 */
	protected function _createDenyFile($dirPath) {

		$filePath = $dirPath . DS . 'index.html';
		if(is_file($filePath)){
			return true;
		}

		$fileContent = <<<EOT
<!DOCTYPE html><html><head><meta charset="utf-8"><title>403 Forbidden</title></head><body><h1>Forbidden</h1><p>Directory access is forbidden.</p></body></html>
EOT;

		return File::writeFile($filePath, $fileContent);
	}

	/**
	 * 创建配置文件
	 *
	 * @access protected
	 *
	 * @param string $dirPath 配置文件目录路径
	 *
	 * @return boolean
	 */
	protected function _createConfigureFile($dirPath) {
		
		$filePath   = $dirPath . DS . 'application.php';
		if(is_file($filePath)){
			return true;
		}
		$timeString = date('Y-m-d H:i:s');
		$fileContent = <<<EOT
<?php
/**
 * 项目主配置文件
 *
 * @author tommy <tommy@doitphp.com>
 * @link http://www.doitphp.com
 * @copyright Copyright (C) Copyright (c) 2020 www.doitphp.com All rights reserved.
 * @license New BSD License.{@link http://www.opensource.org/licenses/bsd-license.php}
 * @version \$Id: application.php 3.1 {$timeString} tommy <tommy@doitphp.com> \$
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
//\$config['application']['defaultTimeZone'] = 'Asia/ShangHai';

/**
 * 设置URL网址的格式。
 *  Configure::GET_FORMAT为:index.php?router=controller/action&params=value;
 *  Configure::PATH_FORMAT为:index.php/controller/action/params/value。
 * 默认为:Configure::PATH_FORMAT
 */
\$config['application']['urlFormat'] = Configure::PATH_FORMAT;

/**
 * 设置是否开启URL路由网址重写(Rewrite)功能。true:开启；false:关闭。默认:关闭。
 */
//\$config['application']['rewrite'] = true;

/**
 * 设置是否开启Debug调用功能。true:开启；false:关闭。默认:关闭。
 */
//\$config['application']['debug'] = true;

/**
 * 设置是否开启日志记录功能。true:开启；false:关闭。默认:关闭。
 */
//\$config['application']['log'] = true;

/**
 * 自定义项目(application)目录路径的设置。注:结尾无需"/"，建议用绝对路径。
 */
//\$config['application']['basePath'] = APP_ROOT . '/application';

/**
 * 自定义缓存(cache)目录路径的设置。注:结尾无需"/"，建议用绝对路径。
 */
//\$config['application']['cachePath'] = APP_ROOT . '/cache';

/**
 * 自定义日志(log)目录路径的设置。注:结尾无需"/"，建议用绝对路径。
 */
//\$config['application']['logPath'] = APP_ROOT . '/logs';

/**
 * 设置数据库(关系型数据库)的连接参数。 注:仅支持PDO连接。
 *
 * @example
 * 例一:单数据库
 * \$config['database'] = array(
 *    'dsn'      => 'mysql:host=localhost;dbname=doitphp',
 *    'username' => 'root',
 *    'password' => '123qwe',
 *    'prefix'   => 'do_',
 *    'charset'  => 'utf8',
 * );
 *
 * 例二:数据库主从分离
 * \$config['database'] = array(
 *     'master'  => array(
 *         'dsn'      => '...',
 *         'username' => '...',
 *         'password' => '...',
 *     ),
 *     'slave'   => array(
 *         'dsn'      => '...',
 *         'username' => '...',
 *         'password' => '...',
 *     ),
 *     'prefix'  => 'do_',
 *     'charset' => 'utf8',
 * );
 * 注:prefix为数据表前缀。当没有前缀时，此参数可以省略。charset为数库编码。默认值为:utf8。如编码为utf8时，此参数也可以省略。
 */
/*\$config['database'] = array(
   'dsn'      => 'mysql:host=localhost;dbname=yourDbname',
   'username' => 'yourUsername',
   'password' => 'yourPassword',
   'prefix'   => '',
   'charset'  => 'utf8',
);*/


/**
* 设置Cookie生存周期
*/
//\$config['cookie']['expire'] = 3600;

/**
* 设置Session生存周期
*/
//\$config['session']['expire'] = 3600;
EOT;

		return File::writeFile($filePath, $fileContent);
	}

	/**
	 * 创建项目搜索引擎网络爬虫引导文件(robots.txt)
	 *
	 * @access protected
	 *
	 * @param string $appPath 应用项目的根目录
	 *
	 * @return string
	 */
	protected function _createRobotsFile($appPath) {

		$filePath = $appPath . DS . 'robots.txt';
		$fileContent = <<<EOT
User-agent: *
Crawl-delay: 10
Disallow: /doitphp/
Disallow: /application/
Disallow: /cache/
Disallow: /logs/
EOT;

		return File::writeFile($filePath, $fileContent);
	}

		/**
	 * 分析并获取当前应用项目的根目录路径
	 *
	 * @access protected
	 * @return string
	 */
	protected function _getWebAppPath() {

		$appPath = rtrim(Configure::get('webappPath'), '/');
		if(!is_dir($appPath)) {
			File::makeDir($appPath);
		}
		
		return $appPath;
	}
}