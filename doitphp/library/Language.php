<?php
/**
 * 多语言管理
 *
 * @author tommy <tommy@doitphp.com>
 * @link http://www.doitphp.com
 * @copyright Copyright (C) 2015 www.doitphp.com All rights reserved.
 * @license New BSD License.{@link http://www.opensource.org/licenses/bsd-license.php}
 * @version $Id: Language.php 2.0 2012-12-22 19:41:54Z tommy <tommy@doitphp.com> $
 * @package core
 * @since 1.0
 */
namespace doitphp\library;

use doitphp\core\Response;

if (!defined('IN_DOIT')) {
    exit();
}

class Language {

    /**
     * 语言包的目录
     *
     * @var string
     */
    protected $_langPath = null;

    /**
     * 构造方法
     *
     * 用于初始化运行环境,或对基本变量进行赋值
     *
     * @access public
     * @return boolean
     */
    public function __construct() {

        //获取默认的语言包目录的路径
        $this->_langPath = BASE_PATH . '/language';

        return true;
    }

    /**
     * 获取语言包的基本路径
     *
     * @access public
     * @return string
     */
    public function getLanguagePath() {

        return $this->_langPath;
    }

    /**
     * 设置语言包的基本路径
     *
     * @access public
     *
     * @param string $path 语言包的基本路径
     *
     * @return boolean
     */
    public function setLanguagePath($path) {

        //参数分析
        if (!$path) {
            return false;
        }

        $this->_langPath = $path;

        return $this;
    }

    /**
     * 加载语言数据文件
     *
     * @access public
     *
     * @param string $langName 语言名称
     *
     * @return array
     */
    public function loadLanguage($langName = 'zh_cn') {

        //参数分析
        if (!$langName) {
            return false;
        }

        $langFilePath = $this->_langPath . DS . $langName . '.php';

        static $_langArray = array();
        if (!isset($_langArray[$langName])) {
            //分析语言文件是否存在
            if (!is_file($langFilePath)) {
                Response::halt("The Langueage File: {$langFilePath} is not found!");
            }

            //获取语言包内容
            $lang = array();
            include_once $langFilePath;
            $_langArray[$langName] = $lang;
        }

        return $_langArray[$langName];
    }

    /**
     * 获取语言包某键值的内容
     *
     * @access public
     *
     * @param string $key 键值
     * @param string $langName 语言名称
     *
     * @return mixed
     */
    public function get($key, $langName = 'zh_cn') {

        //参数分析
        if (!$key) {
            return null;
        }

        $langArray = $this->loadLanguage($langName);

        return isset($langArray[$key]) ? $langArray[$key] : null;
    }

}