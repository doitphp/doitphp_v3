<?php
/**
 * DoitPHP自动加载引导类
 *
 * @author tommy <tommy@doitphp.com>
 * @link http://www.doitphp.com
 * @copyright Copyright (C) 2015 www.doitphp.com All rights reserved.
 * @license New BSD License.{@link http://www.opensource.org/licenses/bsd-license.php}
 * @version $Id: AutoLoad.php 3.1 2020-02-14 03:48:00Z tommy <tommy@doitphp.com> $
 * @package core
 * @since 1.0
 */
namespace doitphp\core;

use doitphp\App;

if (!defined('IN_DOIT')) {
    exit();
}

abstract class AutoLoad {

    /**
     * 项目文件的自动加载
     *
     * doitPHP系统自动加载核心类库文件(core目录内的文件)及运行所需的controller文件、model文件、widget文件等
     *
     * 注:并非程序初始化时将所有的controller,model等文件都统统加载完,再执行其它。
     * 理解本函数前一定要先理解AutoLoad的作用。
     * 当程序运行时发现所需的文件没有找到时,AutoLoad才会被激发,按照loadFile()的程序设计来完成对该文件的加载
     *
     * @access public
     *
     * @param string $className 所需要加载的类的名称,注:不含后缀名
     *
     * @return void
     */
    public static function loadFile($className) {

        //DoitPHP核心类文件的加载分析
        if (substr($className, 0, 8) == 'doitphp\\') {
            $className = substr($className, 8);
            $filePath  = DOIT_ROOT . DS . str_replace('\\', DS, $className) . '.php';
        } else {
            $filePath = BASE_PATH . DS . ((strpos($className, '\\') !== false) ? str_replace('\\', DS, $className) : $className) . '.php';
            if (!is_file($filePath)) {
                //根据配置文件设置,加载文件
                if (self::_loadImportConfigFile($className)) {
                    return true;
                }
                //当文件不存在时，提示错误信息
                Response::halt('The File: ' . $filePath . ' is not found !');
            }
        }

        App::loadFile($filePath);

        return true;
    }

    /**
     * 加载自定义配置文件所引导的文件
     *
     * @access private
     *
     * @param string $className 所需要加载的类的名称,注:不含后缀名
     *
     * @return void
     */
    private static function _loadImportConfigFile($className) {

        //定义自动加载状态。(true:已加载/false:未加载)
        $atuoLoadStatus = false;

        //分析配置文件import引导信息
        $importRules = Configure::get('import');

        //当配置文件引导信息合法时
        if ($importRules && is_array($importRules)) {
            foreach ($importRules as $rules) {
                if (!$rules) {
                    continue;
                }

                //当配置文件引导信息中含有*'时，将设置的规则中的*替换为所要加载的文件类名
                if (strpos($rules, '*') !== false) {
                    $filePath = str_replace('*', $className, $rules);
                } else {
                    $filePath = $rules . DS . $className . '.php';
                }

                //当自定义自动加载的文件存在时
                if (is_file($filePath)) {
                    //加载文件
                    App::loadFile($filePath);
                    $atuoLoadStatus = true;
                    break;
                }
            }
        }

        return $atuoLoadStatus;
    }
}