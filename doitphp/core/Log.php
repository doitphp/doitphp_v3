<?php
/**
 * 日志内容的管理
 *
 * 日志的写入操作及日志内容的查询显示
 *
 * @author tommy <tommy@doitphp.com>
 * @link http://www.doitphp.com
 * @copyright Copyright (C) 2015 www.doitphp.com All rights reserved.
 * @license New BSD License.{@link http://www.opensource.org/licenses/bsd-license.php}
 * @version $Id: Log.php 2.0 2012-11-29 23:33:00Z tommy <tommy@doitphp.com> $
 * @package core
 * @since 1.0
 */
namespace doitphp\core;

use doitphp\App;

if (!defined('IN_DOIT')) {
    exit();
}

abstract class Log {

    /**
     * 写入日志
     *
     * @access public
     *
     * @param string $message     所要写入的日志内容
     * @param string $level       日志类型. 参数:Warning, Error, Notice
     * @param string $logFileName 日志文件名
     *
     * @return boolean
     */
    public static function write($message, $level = 'Error', $logFileName = null) {

        //参数分析
        if (!$message) {
            return false;
        }

        //当日志写入功能关闭时
        if(Configure::get('application.log') === false){
            return true;
        }

        $logFilePath = self::_getLogFilePath($logFileName);

        //分析日志文件存放目录
        $logDir = dirname($logFilePath);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        //分析记录日志的当前页面
        $controllerId = App::getControllerName();
        $actionId     = App::getActionName();

        //分析日志内容
        $message      = "[{$controllerId}][{$actionId}]:" . $message;
        $logIp        = Request::getClientIp();

        return error_log(date('[Y-m-d H:i:s]') . " {$level}: {$message} IP: {$logIp}\n", 3, $logFilePath);
    }

    /**
     * 显示日志内容
     *
     * 显示日志文件内容,以列表的形式显示.便于程序调用查看日志
     *
     * @access public
     *
     * @param string $logFileName 所要显示的日志文件内容,默认为null, 即当天的日志文件名.注:不带后缀名.log
     * @param string $dateType 输出日志内容的数据格式。html/json
     *
     * @return string
     */
    public static function show($logFileName = null, $dateType='html') {

        //参数分析
        $logFilePath    = self::_getLogFilePath($logFileName);

        $dateType       = (!$dateType) ? 'html' : strtolower($dateType);
        if(!in_array($dateType, array('html', 'json'))) {
            $dateType = 'html';
        }

        //读取日志内容
        $logContent     = is_file($logFilePath) ? file_get_contents($logFilePath) : '';
        $logArray       = explode("\n", $logContent);

        array_pop($logArray);
        $totalNumber    = count($logArray);

        //清除不必要内存占用
        unset($logContent);

        if($dateType == 'json') {
            $jsonArray = array('count'=>$totalNumber, 'data'=>$logArray);
            $logData   = json_encode($jsonArray);
        } else {
            $logData  = '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"><title>Log list</title><style>div{font-family:"Helvetica Neue",Helvetica,"PingFang SC","Hiragino Sans GB","Microsoft YaHei","微软雅黑",Arial,sans-serif;font-size:16px;line-height:36px;color:#303133}ul{margin:0;padding:0;list-style:none}li{text-align:left;text-indent:14px;background-color:#FFF}li:nth-of-type(odd){background-color:#d9ecff}</style></head><body>';

            $logData .= '<div><ul>';
            foreach ($logArray as $logValue) {
                $logData .= '<li>' . $logValue . '</li>';
            }
            $logData .= '</ul></div>';

            $logData .= '</body></html>';
        }

        header("Content-Type:text/html; charset=utf-8");
        echo $logData;
    }

    /**
     * 获取当前日志文件名
     *
     * @example
     *
     * $this->_getLogFilePath('sql');
     * 或
     * $this->_getLogFilePath('2012-11.2012-11-23');
     * 或
     * $this->_getLogFilePath('2012-11/2012-11-23');
     *
     * @access private
     *
     * @param string $logFileName 日志文件名
     *
     * @return string
     */
    private static function _getLogFilePath($logFileName = null) {

        //参数分析
        if ($logFileName && strpos($logFileName, '.') !== false) {
            $logFileName = str_replace('.', '/', $logFileName);
        }

        //组装日志文件路径
        $logFilePath = rtrim(Configure::get('application.logPath'), '/');
        if (!$logFileName) {
            $logFilePath .= DS . date('Y-m') . '/' . date('Y-m-d');
        } else {
            if (strpos($logFileName, '/') !== false) {
                $logFilePath .= DS . $logFileName;
            } else {
                $logFilePath .= DS . date('Y-m') . '/' . $logFileName;
            }
        }
        $logFilePath .= '.log';

        return $logFilePath;
    }
}