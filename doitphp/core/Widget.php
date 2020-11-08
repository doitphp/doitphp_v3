<?php
/**
 * 挂件(widget)基类
 *
 * @author tommy <tommy@doitphp.com>
 * @link http://www.doitphp.com
 * @copyright Copyright (C) 2015 www.doitphp.com All rights reserved.
 * @license New BSD License.{@link http://www.opensource.org/licenses/bsd-license.php}
 * @version $Id: Widget.php 2.0 2012-12-16 21:45:57Z tommy <tommy@doitphp.com> $
 * @package core
 * @since 1.0
 */
namespace doitphp\core;

if (!defined('IN_DOIT')) {
    exit();
}

abstract class Widget extends Controller {

    /**
     * 视图变量数组容器
     *
     * @var array
     */
    protected $_options = array();

    /**
     * 视图目录路径
     *
     * @var string
     */
    protected $_viewPath = null;

    /**
     * Widget的前函数(类方法)
     *
     * @access protected
     * @return boolean
     */
    protected function _init() {

        //获取当前视图的目录(当视图文件的格式为PHP时)
        $this->_viewPath = BASE_PATH . '/views/widgets';

        return true;
    }

    /**
     * 设置视图文件布局结构视图的文件名(layout)
     *
     * 挂件(Widget)的视图机制不支持layout视图
     *
     * @access public
     *
     * @param string $layoutName 所要设置的layout名称。默认值为:null，即:不使用layout视图
     *
     * @return boolean
     */
    public function setLayout($layoutName = null) {

        return false;
    }

    /**
     * 显示当前页面的视图内容
     *
     * 注:挂件(Widget)的视图机制不支持Layout视图
     *
     * @access public
     *
     * @param string $fileName 视图名称
     *
     * @return string
     */
    public function display($fileName = null) {

        //模板变量赋值
        if ($this->_options) {
            extract($this->_options, EXTR_PREFIX_SAME, 'widget');
            //清空不必要的内存占用
            $this->_options = array();
        }

        //当视图格式为PHP时
        $viewFile = $this->_parseViewFile($fileName);

        ob_start();
        include $viewFile;
        $widgetContent = ob_get_clean();

        echo $widgetContent;
    }

    /**
     * 视图变量赋值操作
     *
     * @access public
     *
     * @param mixed $keys 视图变量名
     * @param mixed $value 视图变量值
     *
     * @return void
     */
    public function assign($keys, $value = null) {

        //参数分析
        if (!$keys) {
            return false;
        }

        if (!is_array($keys)) {
            $this->_options[$keys] = $value;
            return true;
        }

        foreach ($keys as $handle=>$lines) {
            $this->_options[$handle] = $lines;
        }

        return true;
    }

    /**
     * 加载并显示视图片段文件内容
     *
     * 相当于include 代码片段，当$return为:true时返回代码代码片段内容,反之则显示代码片段内容
     *
     * @access public
     *
     * @param string $fileName 视图片段文件名称
     * @param array $data 视图模板变量，注:数组型
     * @param boolean $return 视图内容是否为返回，当为true时为返回，为false时则为显示。 默认为:false
     *
     * @return string
     */
    public function render($fileName = null, $data = array(), $return = false) {

        //分析视图文件路径
        $viewFile = $this->_parseViewFile($fileName);

        //模板变量赋值
        if ($data && is_array($data)) {
            extract($data, EXTR_PREFIX_SAME, 'widget');
            unset($data);
        } else {
            if (!$fileName && $this->_options) {
                extract($this->_options, EXTR_PREFIX_SAME, 'widget');
                $this->_options = array();
            }
        }

        //获取当前视图的页面内容(视图片段)
        ob_start();
        include $viewFile;
        $widgetContent = ob_get_clean();

        //返回信息
        if (!$return) {
            echo $widgetContent;
        } else {
            return $widgetContent;
        }
    }

    /**
     * 分析挂件(widget)的视图文件路径
     *
     * 注:这里的视图指的是挂件的视图文件(PHP格式)
     *
     * @access protected
     *
     * @param string $fileName 视图文件名。注:不含文件后缀
     *
     * @return string
     */
    protected function _parseViewFile($fileName = null) {

        $fileName = (!$fileName) ? $this->_getWidgetName() : $fileName;
        $viewPath = $this->_viewPath . DS . $fileName . VIEW_EXT;

        //检查视图文件路径是否正确
        if (!is_file($viewPath)) {
            Response::halt("The widget view file: {$viewPath} is not found!");
        }

        return $viewPath;
    }

    /**
     * 获取当前的视图目录的路径
     *
     * @access public
     * @return string
     */
    public function getViewPath() {

        return $this->_viewPath;
    }

    /**
     * 设置当前的视图目录路径
     *
     * @access public
     *
     * @param string $viewPath 视图目录的路径
     *
     * @return boolean
     */
    public function setViewPath($viewPath) {

        //参数分析
        if (!$viewPath) {
            return false;
        }

        $this->_viewPath = $viewPath;

        return true;
    }

    /**
     * 返回视图的实例化对象
     *
     * @access public
     * @return object
     */
    public static function getView() {

        return self::$_viewObject;
    }

    /**
     * 获取当前Widget的名称
     *
     * @access protected
     * @return string
     */
    protected function _getWidgetName() {

        return substr(get_class($this), 8, -6);
    }

    /**
     * 运行挂件
     *
     * @access public
     *
     * @param array $params 参数. 如array('id'=>23)
     *
     * @return void
     */
    public function renderContent($params = null){

        return true;
    }
}