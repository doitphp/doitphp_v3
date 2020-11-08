<?php
/**
 * DoitPHP系统异常基类
 *
 * @author tommy <tommy@doitphp.com>
 * @link http://www.doitphp.com
 * @copyright Copyright(C) 2012-2020 www.doitphp.com All rights reserved.
 * @license New BSD License.{@link http://www.opensource.org/licenses/bsd-license.php}
 * @version $Id: DoitException.php 3.2 2020-04-30 16:37:00Z tommy <tommy@doitphp.com> $
 * @package core
 * @since 1.0
 */
namespace doitphp\core;

use \Exception;

class DoitException extends Exception {

    /**
     * 异常输出
     *
     * 注:当调试模式关闭时,异常提示信息将会写入日志
     *
     * @access public
     * @return string
     */
    public function __toString() {

        //分析获取异常信息
        $code         = $this->getCode();
        $exceptionMsg = $this->getMessage();
        $message      = ($code ? "Error Code:{$code}<br>" : '') . ($exceptionMsg ? "Error Message:{$exceptionMsg}" : '');

        $line = $this->getLine();
        $sourceFile = $this->getFile() . (!$line ? '' : "({$line})");

        $traceString = '';
        $traces = $this->getTrace();
        foreach ($traces as $key=>$trace) {
            $traceString .= "#{$key} {$trace['file']}({$trace['line']})<br>";
        }

        ob_start();
        //加载,分析,并输出excepiton文件内容
        include_once DOIT_ROOT . '/views/errors/exception.php';

        return ob_get_clean();
    }

}