<?php
/**
 * 数据的加密,解密
 *
 * @author tommy <tommy@doitphp.com>
 * @copyright Copyright (c) 2010 Tommycode Studio, ColaPHP
 * @link http://www.doitphp.com
 * @license New BSD License.{@link http://www.opensource.org/licenses/bsd-license.php}
 * @version $Id: Encrypt.php 2.0 2012-12-29 16:20:14Z tommy $
 * @package library
 * @since 1.0
 */
namespace doitphp\library;

if (!defined('IN_DOIT')) {
    exit();
}

class Encrypt {

    /**
     * config data
     *
     * @var array
     */
    protected $_config = array();

    /**
     * Token生存时间周期,默认：两小时
     *
     * @var integer
     */
    protected static $_expire = 7200;

    /**
     * 加密字符串(密钥)
     *
     * @var string
     */
    private static $_key = 'your-secret-code';

    /**
     * 构造方法
     *
     * @access public
     * @return boolean
     */
    public function __construct() {

        //set config infomation
        $this->_config = array(
            'cipher'  => 'AES-256-ECB',
            'options' => OPENSSL_RAW_DATA,
        );

        return true;
    }

    /**
     * 加密
     *
     * @access public
     *
     * @param string $string 待加密的字符串
     * @param string $key 密钥
     *
     * @return string
     */
    public function encode($string, $key = null) {

        //参数分析
        if (!$string) {
            return false;
        }
        if (is_null($key)) {
            $key = self::$_key;
        }

        $string = openssl_encrypt($string, $this->_config['cipher'], $key, $this->_config['options']);

        return base64_encode($string);
    }

    /**
     * 解密
     *
     * @access public
     *
     * @param string $string 待解密的字符串
     * @param string $key 附加码
     *
     * @return string
     */
    public function decode($string, $key = null) {

        //参数分析
        if (!$string) {
            return false;
        }
        if (is_null($key)) {
            $key = self::$_key;
        }

        if (preg_match('/[^a-zA-Z0-9\/\+=]/', $string)) {
            return false;
        }

        $string = openssl_decrypt(base64_decode($string), $this->_config['cipher'], $key, $this->_config['options']);

        return $string;
    }

    /**
     * 生成令牌密码
     *
     * @access public
     *
     * @param string $string 所要加密的字符(也可以是随机的)
     * @param string $expire 令版密码的有效时间(单位:秒)
     * @param string $key 自定义密钥
     *
     * @return string
     */
    public function getToken($string, $expire = null, $key = null) {

        //参数分析
        if (!$string) {
            return false;
        }
        if (is_null($key)) {
            $key = self::$_key;
        }

        //设置token生存周期及附加加密码
        $expire = (!$expire) ? self::$_expire : $expire;
        $per    = ceil($_SERVER['REQUEST_TIME'] / $expire);

        return hash_hmac('md5', $per . $string, $key);
    }

    /**
     * 令牌密码验证
     *
     * @access public
     *
     * @param string $string 所要加密的字符(也可以是随机的)
     * @param string $tokenCode 所要验证的加密字符串
     * @param string $expire 令版密码的有效时间(单位:秒)
     * @param string $key 自定义密钥
     *
     * @return boolean
     */
    public function checkToken($string, $tokenCode, $expire = null, $key = null) {

        //参数分析
        if (!$string || !$tokenCode) {
            return false;
        }
        if (is_null($key)) {
            $key = self::$_key;
        }

        //设置token生存周期及附加加密码
        $expire = (!$expire) ? self::$_expire : $expire;
        $per    = ceil($_SERVER['REQUEST_TIME'] / $expire);

        //获取token值
        $sourceToken = hash_hmac('md5', $per . $string, $key);

        return ($sourceToken == $tokenCode) ? true : false;
    }

    /**
     * 生成随机码
     *
     * @access public
     *
     * @param integer $length 随机码长度 (0~32)
     *
     * @return string
     */
    public static function randCode($length = 5) {

        //参数分析
        $length = (int)$length;
        $length = ($length > 32) ? 32 : $length;

        $code  = md5(uniqid(mt_rand(), true));
        $start = mt_rand(0, 32 - $length);

        return substr($code, $start, $length);
    }
}