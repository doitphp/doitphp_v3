<?php
/**
 * 对cookie数据的管理操作
 *
 * @author tommy <tommy@doitphp.com>
 * @link http://www.doitphp.com
 * @copyright Copyright (C) 2015 www.doitphp.com All rights reserved.
 * @license New BSD License.{@link http://www.opensource.org/licenses/bsd-license.php}
 * @version $Id: Cookie.php 2.0 2012-12-20 22:07:17Z tommy <tommy@doitphp.com> $
 * @package library
 * @since 1.0
 */
namespace doitphp\library;

use doitphp\core\Configure;

if (!defined('IN_DOIT')) {
    exit();
}

class Cookie {

    /**
     * Cookie存贮默认配置信息
     *
     * @var array
     */
    protected $_defaultOptions = array(
        'expire'    => 3600,
        'path'      => '/',
        'domain'    => null,
    );

    /**
     * Cookie的存贮设置选项
     *
     * @var array
     */
    protected $_options = null;

    /**
     * cookie值的加密算法
     *
     * @var string
     */
    protected $_cipher = null;

    /**
     * 构造函数
     *
     * @access public
     * @return boolean
     */
    public function __construct() {
        $options = Configure::get('cookie');

        $this->_options = ($options && is_array($options)) ? $options : array();
        $this->_options += $this->_defaultOptions;

        //当配置参数secretkey设置了有效的值(加密密钥)后，则开启cookie的加密功能。以下的AES-256-ECB加密算法才生效。
        $this->_cipher = 'AES-256-ECB';

        return true;
    }

    /**
     * 获取某cookie变量的值
     *
     * @access public
     *
     * @param string $cookieName cookie变量名
     * @param mixed $default 默认值
     *
     * @return mixed
     */
    public function get($cookieName, $default = null) {

        //参数分析
        if (!$cookieName) {
            return null;
        }
        if (!isset($_COOKIE[$cookieName])) {
            return $default;
        }

        $value = base64_decode($_COOKIE[$cookieName]);
        if ($this->_options['secretkey']) {
            $value = openssl_decrypt($value, $this->_cipher, $this->_options['secretkey']);
        }

        return unserialize($value);
    }

    /**
     * 设置某cookie变量的值
     *
     * @access public
     *
     * @param string $cookieName cookie的变量名
     * @param mixed $value cookie值
     * @param integer $expire cookie的生存周期
     *
     * @return boolean
     */
    public function set($cookieName, $value, $expire = null) {

        //参数分析
        if (!$cookieName) {
            return false;
        }

        $expire = is_null($expire) ? $this->_options['expire'] : $expire;
        $expire = $_SERVER['REQUEST_TIME'] + $expire;

        $value = serialize($value);
        if ($this->_options['secretkey']) {
            $value = openssl_encrypt($value, $this->_cipher, $this->_options['secretkey']);
        }
        $value = base64_encode($value);

        setcookie($cookieName, $value, $expire, $this->_options['path'], $this->_options['domain']);
        $_COOKIE[$cookieName] = $value;

        return true;
    }

    /**
     * 删除某个Cookie变量
     *
     * @access public
     *
     * @param string $name cookie的名称
     *
     * @return boolean
     */
    public function delete($name) {

        //参数分析
        if (!$name) {
            return false;
        }

        self::set($name, null, '-3600');
        unset($_COOKIE[$name]);

        return true;
    }

    /**
     * 清空cookie
     *
     * @access public
     * @return boolean
     */
    public function clear() {

        if (isset($_COOKIE)) {
            unset($_COOKIE);
        }

        return true;
    }

}