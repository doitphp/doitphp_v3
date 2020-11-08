<?php
/**
 * 文件下载类
 *
 * @author tommy <tommy@doitphp.com>
 * @link http://www.doitphp.com
 * @copyright Copyright (C) 2015 www.doitphp.com All rights reserved.
 * @license New BSD License.{@link http://www.opensource.org/licenses/bsd-license.php}
 * @version $Id: FileDownload.php 2.0 2012-12-23 20:24:43Z tommy <tommy@doitphp.com> $
 * @package library
 * @since 1.0
 */
namespace doitphp\library;

use doitphp\core\Response;

if (!defined('IN_DOIT')) {
    exit();
}

class FileDownload {

    /**
     * http下载文件
     *
     * Reads a file and send a header to force download it.
     *
     * @access public
     *
     * @param string $filePath 文件路径
     * @param string $rename 文件重命名后的名称
     *
     * @return void
     */
    public static function getData($filePath, $rename = null) {

        //参数分析
        if(!$filePath) {
            return false;
        }

        if(headers_sent()) {
            return false;
        }

        //分析文件是否存在
        if (!is_file($filePath)) {
            Response::showMsg('Error 404:The file not found!');
        }

        //分析文件名
        $fileName = (!$rename) ? basename($filePath) : $rename;

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header("Content-Disposition: attachment; filename=\"{$fileName}\"");
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        ob_clean();
        flush();

        readfile($filePath);

        exit();
    }
}