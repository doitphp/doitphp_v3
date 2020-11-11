<?php
/**
 * Memcached 缓存操作类
 *
 * @author tommy <tommy@doitphp.com>
 * @copyright Copyright (c) 2015 Tommy Software Studio
 * @link http://www.doitphp.com
 * @license New BSD License.{@link http://www.opensource.org/licenses/bsd-license.php}
 * @version $Id: Cache_Memcached.php 2.0 2015-04-01 15:00:01Z tommy $
 * @package cache
 * @since 1.0
 */
namespace doitphp\library;

use doitphp\core\Configure;
use doitphp\core\Response;
use \Memcached;

if (!defined('IN_DOIT')) {
    exit();
}

/**
 * 使用说明
 *
 * @author tommy<tommy@doitphp.com>
 *
 * @example
 *
 * 参数范例
 * $memOptions = array(
 *     'servers'=> array(
 *         array('host'=>'127.0.0.1', 'port'=>11211, 'persistent'=>true, 'weight'=>1, 'timeout'=>60),
 *         array('host'=>'192.168.0.101', 'port'=>11211, 'persistent'=>true, 'weight'=>2, 'timeout'=>60),
 *     ),
 *     'compressed'=>true,
 *     'expire' => 3600,
 *     'persistent' => true,
 * );
 *
 * 实例化
 *
 * 法一:
 * $memcached = new CacheMemcached($memOptions);
 *
 */

class CacheMemcached {

    /**
     * 单例模式实例化本类
     *
     * @var object
     */
    protected static $_instance = null;

    /**
     * Memcached实例
     *
     * @var object
     */
    private $_Memcached;

    /**
     * 默认的缓存服务器
     *
     * @var array
     */
    protected $_defaultServer = array(
        /**
         * 缓存服务器地址或主机名
         */
        'host' => '127.0.0.1',

        /**
         * 缓存服务器端口
         */
        'port' => '11211',
    );

    /**
     * 默认的缓存策略
     *
     * @var array
     */
    protected $_defaultOptions = array(

        /**
         * 缓存服务器配置,参看$_defaultServer
         * 允许多个缓存服务器
         */
        'servers' => array(),

        /**
         * 是否压缩缓存数据
         */
        'compressed' => false,

        /**
         * 缓存有效时间
         *
         * 如果设置为 0 表示缓存永不过期
         */
        'expire' => 900,

        /**
         * 是否使用持久连接
         */
        'persistent' => true,
    );

    /**
     * 构造方法
     *
     * @access public
     *
     * @param array $params 数据库连接参数,如主机名,数据库用户名,密码等
     *
     * @return boolean
     */
    public function __construct($options = null) {

        //分析memcached扩展模块的加载
        if (!extension_loaded('memcached')) {
            Response::halt('The memcached extension is not to be loaded before use!');
        }

        //获取Memcache服务器连接参数
        if (!$options || !is_array($options)) {
            $options = Configure::get('memcached');
        }

        if (is_array($options) && $options) {
            $this->_defaultOptions = $options + $this->_defaultOptions;
        }

        if (!$this->_defaultOptions['servers']) {
            $this->_defaultOptions['servers'][] = $this->_defaultServer;
        }

        $this->_Memcached = new Memcached();

        foreach ($this->_defaultOptions['servers'] as $server) {
            $server += array('host' => '127.0.0.1', 'port' => 11211, 'persistent' => true);
            $this->_Memcached->addServer($server['host'], $server['port'], $this->_defaultOptions['persistent']);
        }

        return true;
    }

    /**
     * 写入缓存
     *
     * @access public
     *
     * @param string $key 缓存Key
     * @param mixed $data 缓存内容
     * @param int $expire 缓存时间(秒)
     *
     * @return boolean
     */
    public function set($key, $data, $expire = null) {

        //参数分析
        if (!$key) {
            return false;
        }
        if (is_null($expire)) {
            $expire = $this->_defaultOptions['expire'];
        }

        return $this->_Memcached->set($key, $data, $expire);
    }

    /**
     * 读取缓存,失败或缓存撒失效时返回false
     *
     * @access public
     *
     * @param string $key 所要读取数据的key
     *
     * @return mixed
     */
    public function get($key) {

        //参数分析
        if (!$key) {
            return false;
        }

        return $this->_Memcached->get($key);
    }

    /**
     * 删除指定的缓存
     *
     * @access public
     *
     * @param string $key 所要删除数据的Key
     *
     * @return boolean
     */
    public function delete($key) {

        //参数分析
        if (!$key) {
            return false;
        }

        return $this->_Memcached->delete($key);
    }

    /**
     * 数据自增
     *
     * @access public
     *
     * @author ColaPHP
     * @param string $key 数据key
     * @param integer $value 自增数据值
     *
     * @return boolean
     */
    public function increment($key, $value = 1) {

        //参数分析
        if (!$key) {
            return false;
        }

        return $this->_Memcached->increment($key, $value);
    }

    /**
     * 数据自减
     *
     * @access public
     *
     * @param string $key 数据key
     * @param integer $value 自减数据值
     *
     * @return boolean
     */
    public function decrement($key, $value = 1) {

        //参数分析
        if (!$key) {
            return false;
        }

        return $this->_Memcached->decrement($key, $value);
    }

    /**
     * 清除所有的缓存数据
     *
     * @access public
     * @return boolean
     */
     public function clear() {

          return $this->_Memcached->flush();
     }

    /**
     * 返回memcached实例化对象
     *
     * @access public
     * @return object
     */
    public function getConnection() {

        return $this->_Memcached;
    }

     /**
      * 析构函数
      *
      * @access public
      * @return boolean
      */
     public function __destruct() {

         if ($this->_Memcached) {
             $this->_Memcached->quit();
         }

         return true;
     }

    /**
     * 单例模式
     *
     * 用于本类的单例模式(singleton)实例化
     *
     * @access public
     *
     * @param array $params 数据库连接参数
     *
     * @return object
     */
    public static function getInstance($params = null) {

        if (!self::$_instance) {
            self::$_instance = new self($params);
        }

        return self::$_instance;
    }
}