<?php
/**
 * 模型（Model）基类
 *
 * 提供数据库操作常用的类方法
 *
 * @author tommy <tommy@doitphp.com>
 * @link http://www.doitphp.com
 * @copyright Copyright (C) 2015 www.doitphp.com All rights reserved.
 * @license New BSD License.{@link http://www.opensource.org/licenses/bsd-license.php}
 * @version $Id: Model.php 3.1 2020-05-01 23:30:00Z tommy <tommy@doitphp.com> $
 * @package core
 * @since 1.0
 */
namespace doitphp\core;

if (!defined('IN_DOIT')) {
    exit();
}

class Model {

    /**
     * 数据表名
     *
     * @var string
     */
    protected $_tableName = null;

    /**
     * 数据表字段信息
     *
     * @var array
     */
    protected $_tableFields = array();

    /**
     * 数据表的主键信息
     *
     * @var string
     */
    protected $_primaryKey = null;

    /**
     * model所对应的数据表名的前缀
     *
     * @var string
     */
    protected $_prefix = null;

    /**
     * 错误信息
     *
     * @var string
     */
    protected $_errorInfo = null;

    /**
     * 数据库连接参数
     *
     * @var array
     */
    protected $_config = array();

    /**
     * SQL语句容器，用于存放SQL语句，为SQL语句组装函数提供SQL语句片段的存放空间。
     *
     * @var array
     */
    protected $_parts = array();

     /**
     * 主数据库实例化对象
     *
     * @var object
     */
    protected $_master = null;

    /**
     * 从数据库实例化对象
     *
     * @var object
     */
    protected $_slave = null;

    /**
     * 数据库实例化是否为单例模式
     *
     * @var boolean
     */
    protected $_singleton = false;

    /**
     * 单例模式实现化对象
     *
     * @var object
     */
    protected static $_instance = null;

    /**
     * 构造方法（函数）
     *
     * 用于初始化程序运行环境，或对基本变量进行赋值
     *
     * @access public
     * @return boolean
     */
    public function __construct() {

        //分析数据库连接参数
        $this->_config = $this->_parseConfig();

        //执行前函数(类方法)
        $this->_init();

        return true;
    }

    /**
     * 获取当前数据库连接的实例化对象
     *
     * 使用本函数(类方法），可以实现对原生PDO所提供的函数的调用。
     *
     * @access public
     *
     * @param boolean $adapter 是否为主数据库。true:主数据库/false:从数据库
     *
     * @return object
     */
    public function getConnection($adapter = true) {

        if (!$adapter) {
            return $this->_slave();
        }

        return $this->_master();
    }

    /**
     * 实例化主数据库(Master MySQL Adapter)
     *
     * @access protected
     * @return object
     */
    protected function _master() {

        if ($this->_master) {
            return $this->_master;
        }

        $this->_master = new DbPdo($this->_config['master']);

        if ($this->_singleton) {
            $this->_slave = $this->_master;
        }

        return $this->_master;
    }

    /**
     * 实例化从数据库(Slave Adapter)
     *
     * @access public
     * @return object
     */
    protected function _slave() {

        if ($this->_slave) {
            return $this->_slave;
        }

        $this->_slave = new DbPdo($this->_config['slave']);

        if ($this->_singleton) {
            $this->_master = $this->_slave;
        }

        return $this->_slave;
    }

    /**
     * 获取当前模型(Model)文件所对应的数据表的名称
     *
     * 注:若数据表有前缀($prefix)时，系统将自动加上数据表前缀。
     *
     * @access public
     * @return string
     */
    public function getTableName() {

        if (!$this->_tableName) {
            //调用回调类方法，获取当前Model文件所绑定的数据表名
            $tableName = $this->_tableName();

            //当回调类方法未获取到数据表时，则默认为当前的Model类名
            if (!$tableName) {
                $tableName = substr(strtolower(get_class($this)), 7, -5);
                $tableName = str_replace('\\', '_', $tableName);
            }
            $tableName = trim($tableName);

            //分析数据表名，当有前缀时，加上前缀
            $this->_tableName = (!$this->_prefix) ? $tableName : $this->_prefix . $tableName;
        }

        return $this->_tableName;
    }

    /**
     * 执行非查询SQL语句
     *
     * 注:本方法用于无需返回信息的操作。如:更改、删除、添加数据信息(即:用于执行非查询SQL语句)
     *
     * @access public
     *
     * @param string $sql 所要执行的SQL语句
     * @param array $params 待转义的数据。注:本参数支持字符串及数组，如果待转义的数据量在两个或两个以上请使用数组
     *
     * @return boolean
     */
    public function execute($sql, $params = null) {

        //参数分析
        if (!$sql) {
            return false;
        }

        if (!is_null($params) && !is_array($params)) {
            $params = func_get_args();
            array_shift($params);
        }

        //转义数据表前缀
        $sql = str_replace('#__', $this->_prefix, $sql);

        return $this->_master()->execute($sql, $params);
    }

    /**
     * 执行查询性的SQL语句
     *
     * 注:用于执行查询性的SQL语句（需要数据返回的情况）。
     *
     * @access public
     *
     * @param string $sql 所要执行的SQL语句
     * @param array $params 待转义的数据。注:本参数支持字符串及数组，如果待转义的数据量在两个或两个以上请使用数组
     *
     * @return boolean
     */
    public function query($sql, $params = null) {

        //参数分析
        if (!$sql) {
            return false;
        }

        if (!is_null($params) && !is_array($params)) {
            $params = func_get_args();
            array_shift($params);
        }

        //转义数据表前缀
        $sql = str_replace('#__', $this->_prefix, $sql);

        return $this->_slave()->query($sql, $params);
    }

    /**
     * 数据表写入操作
     *
     * @access public
     *
     * @param array $data 所要写入的数据内容。注:数据必须为数组
     * @param boolean $isReturnId 是否返回数据为:last insert id
     *
     * @return boolean|integer
     */
    public function insert($data, $isReturnId = false) {

        //参数分析
        if (!$data || !is_array($data)) {
            return false;
        }

        //获取当前的数据表名
        $tableName   = $this->getTableName();

        //数据过滤
        $insertArray = $this->_filterFields($data);
        if (!$insertArray) {
            return false;
        }

        //清空不必要的内存占用
        unset($data);

        return $this->_master()->insert($tableName, $insertArray, $isReturnId);
    }

    /**
     * 数据表更新操作
     *
     * @access public
     *
     * @param array $data 所要更改的数据内容
     *
     * @return boolean
     */
    public function update($data) {

        //参数分析
        if (!is_array($data) || !$data) {
            return false;
        }

        $condition   = $this->_parseCondition();

        //获取当前的数据表名
        $tableName   = $this->getTableName();

        //数据过滤
        $updateArray = $this->_filterFields($data);

        //清空不必要的内存占用
        unset($data);

        return $this->_master()->update($tableName, $updateArray, $condition['where'], $condition['value']);
    }

    /**
     * 数据表数据替换操作
     *
     * @access public
     *
     * @param array $data 所要替换的数据内容。注:数据必须为数组
     *
     * @return boolean
     */
    public function replace($data) {

        //参数分析
        if (!$data || !is_array($data)) {
            return false;
        }

        //获取当前的数据表名
        $tableName   = $this->getTableName();

        //数据过滤
        $replaceArray = $this->_filterFields($data);
        if (!$replaceArray) {
            return false;
        }

        //清空不必要的内存占用
        unset($data);

        return $this->_master()->replace($tableName, $replaceArray);
    }

    /**
     * 数据表删除操作
     *
     * @access public
     * @return boolean
     */
    public function delete() {

        $condition = $this->_parseCondition();

        //获取当前的数据表名
        $tableName = $this->getTableName();

        return $this->_master()->delete($tableName, $condition['where'], $condition['value']);
    }

    /**
     * 主键查询:获取一行主键查询的数据
     *
     * 注:默认主键为数据表的物理主键
     *
     * @access public
     * @param integer $id 所要查询的主键值。注:本参数可以为数组。当为数组时，返回多行数据
     * @return array
     */
    public function find($id) {

        //参数分析
        if (!$id) {
            return false;
        }

        //分析字段信息
        $fields = $this->_parseFields();

        //获取当前数据表的名称及主键信息
        $tableName  = $this->getTableName();
        $primaryKey = $this->_getPrimaryKey();

        $sql = "SELECT {$fields} FROM {$tableName} WHERE {$primaryKey}";

        //当参数不为数组时
        if (!is_array($id)) {
            if (func_num_args() == 1) {
                $sql  .= " = " . $this->quoteInto($id);
                return $this->_slave()->getOne($sql);
            }
            $id = func_get_args();
        }

        $id    = array_map(array($this, 'quoteInto'), $id);
        $sql  .= " IN (" . implode(',', $id) . ")";
        return $this->_slave()->getAll($sql);
    }

    /**
     * 主键查询:获取数据表的全部数据信息
     *
     * 以主键为中心排序，获取数据表全部数据信息。注:如果数据表数据量较大时，慎用此函数（类方法），以免数据表数据量过大，造成数据库服务器内存溢出,甚至服务器宕机
     *
     * @access public
     * @return array
     */
    public function findAll() {

        //分析数据表字段
        $fields = $this->_parseFields();

        //获取当前 的数据表名及主键名
        $tableName  = $this->getTableName();

        //分析数据的排序
        $orderString = $this->_parseOrder();
        if (!$orderString) {
            $primaryKey =$this->_getPrimaryKey();
            $orderString = "{$primaryKey} ASC";
        }

        //组装SQL语句
        $sql = "SELECT {$fields} FROM {$tableName} ORDER BY {$orderString}";

        //分析LIMIT SQL语句
        $limitString = $this->_parseLimit();
        if($limitString) {
            $sql .= ' LIMIT ' . $limitString;
        }

        return $this->_slave()->getAll($sql);
    }

    /**
     * 获取查询数据的单选数据
     *
     * 根据一个查询条件，获取一行数据，返回数据为数组型，索引为数据表字段名
     *
     * @access public
     * @return array
     */
    public function getOne() {

        //分析查询条件
        $condition = $this->_parseCondition();

        //分析所要查询的字段
        $fields    = $this->_parseFields();

        //获取当前的数据表
        $tableName = $this->getTableName();

        $sql = "SELECT {$fields} FROM {$tableName}";
        if ($condition['where']) {
            $sql .= ' WHERE ' . $condition['where'];
        }

        //分析数据的排序
        $orderString = $this->_parseOrder();
        if ($orderString) {
            $sql .= ' ORDER BY ' . $orderString;
        }

        return $this->_slave()->getOne($sql, $condition['value']);
    }

    /**
     * 获取查询数据的全部数据
     *
     * 根据一个查询条件，获取多行数据。并且支持数据排序，及分页的内容显示
     *
     * @access public
     * @return array
     */
    public function getAll() {

        //分析查询条件
        $condition   = $this->_parseCondition();

        //获取当前的数据表
        $tableName   = $this->getTableName();

        //分析所要查询的字段
        $fields      = $this->_parseFields();

        //组装SQL语句
        $sql = "SELECT {$fields} FROM {$tableName}";
        if ($condition['where']) {
            $sql .= ' WHERE ' . $condition['where'];
        }

        //分析数据的排序
        $orderString = $this->_parseOrder();
        if ($orderString) {
            $sql .= ' ORDER BY ' . $orderString;
        }

        //分析数据的显示行数
        $limitString = $this->_parseLimit();
        if ($limitString) {
            $sql .= ' LIMIT ' . $limitString;
        }

        return $this->_slave()->getAll($sql, $condition['value']);
    }

    /**
     * 组装SQL语句的WHERE语句
     *
     * 用于getOne()、getAll()等类方法的条件查询。
     *
     * @access public
     *
     * @param string|array $where Where的条件
     * @param string|array $value 待转义的数值
     *
     * @return object|boolean
     */
    public function where($where, $value = null) {

        //参数分析
        if (!$where) {
            return false;
        }

        //分析参数条件，当参数为数组时
        if (is_array($where)) {
            $where = implode(' AND ', $where);
        }

        $this->_parts['where'] = (isset($this->_parts['where']) && $this->_parts['where']) ? $this->_parts['where'] . ' AND ' . $where :  $where;

        //当$model->where('name=?', 'tommy');操作时,即:需要字符串转义
        if (!is_null($value)) {
            if (!is_array($value)) {
                $value = func_get_args();
                array_shift($value);
            }
            //当已执行过$this->where();语句时
            if(isset($this->_parts['whereValue']) && $this->_parts['whereValue']) {
                $this->_parts['whereValue'] = array_merge($this->_parts['whereValue'], $value);
            } else {
                $this->_parts['whereValue'] = $value;
            }
        }

        return $this;
    }

    /**
     * 组装SQL语句排序(ORDER BY)语句
     *
     * 用于getAll()的数据排行
     *
     * @access public
     *
     * @param string|array $orderDesc 排序条件。注:本参数支持数组
     *
     * @return object|boolean
     */
    public function order($orderDesc) {

        //参数分析
        if (!$orderDesc) {
            return false;
        }

        if (is_array($orderDesc)) {
            $orderDesc = implode(',', $orderDesc);
        }

        $this->_parts['order'] = (isset($this->_parts['order']) && $this->_parts['order']) ? $this->_parts['order'] . ', ' . $orderDesc : $orderDesc;

        return $this;
    }

    /**
     * 组装SQL语句的查询字段
     *
     * @access public
     *
     * @param string|array $fieldName 所要查询的数据表字段信息
     *
     * @return object|boolean
     */
    public function fields($fieldName) {

        //参数分析
        if (!$fieldName) {
            return false;
        }

        if (!is_array($fieldName)) {
            $fieldName = func_get_args();
        }

        $fieldName = implode(',', $fieldName);

        $this->_parts['fields'] = $fieldName;

        return $this;
    }

    /**
     * 组装SQL语句LIMIT语句
     *
     * limit(10,20)用于处理LIMIT 10, 20之类的SQL语句部分
     *
     * @access public
     *
     * @param integer $offset 启始id。注:参数为整形
     * @param integer $listCount 显示的行数
     *
     * @return object
     */
    public function limit($offset, $listCount = null) {

        //参数分析
        $offset    = (int)$offset;
        $listCount = (int)$listCount;

        $limitStr   = ($listCount) ? $offset . ', ' . $listCount : $offset;

        $this->_parts['limit'] = $limitStr;

        return $this;
    }

    /**
     * 组装SQL语句的LIMIT语句
     *
     * 注:本方法与$this-&gt;limit()功能相类，区别在于:本方法便于分页,参数不同
     *
     * @access public
     *
     * @param integer $page 当前的页数
     * @param integer $listCount 每页显示的数据行数
     *
     * @return object
     */
    public function pageLimit($page, $listCount) {

        //参数分析
        $page      = (int)$page;
        $listCount = (int)$listCount;

        if (!$listCount) {
            return false;
        }

        $page    = ($page < 1) ? 1 : $page;
        $offset = (int)$listCount * ($page - 1);

        return $this->limit($offset, $listCount);
    }

    /**
     * 获取数据表写入时的最新的Insert Id
     *
     * @access public
     * @return integer
     */
    public function getLastInsertId() {

        return $this->_master()->getLastInsertId();
    }

    /**
     * 字符串转义函数
     *
     * SQL语句指令安全过滤，用于字符转义
     *
     * @access public
     *
     * @param mixed $value 所要转义的字符或字符串。注:参数支持数组
     *
     * @return mixed
     */
    public function quoteInto($value = null) {

        return $this->_master()->escape($value);
    }

    /**
     * 过虑数据表字段信息
     *
     * 用于insert()、update()里的字段信息进行过虑，删除掉非法的字段信息。
     *
     * @access protected
     *
     * @param array $data 待过滤的含字段信息的数据。注:本参数为数组
     *
     * @return array
     */
    protected function _filterFields($data) {

        //参数分析
        if (!$data || !is_array($data)) {
            return false;
        }

        //获取数据表字段
        $tableFields = $this->_getTableFields();

        $filteredArray  = array();
        foreach ($data as $key=>$value) {
            if(in_array($key, $tableFields)) {
                $filteredArray[$key] = $value;
            }
        }

        return $filteredArray;
    }

    /**
     * 分析数据表字段信息
     *
     * @access protected
     *
     * @param array $fields 数据表字段信息。本参数可为数组
     *
     * @return string
     */
    protected function _parseFields($fields = null) {

        //当参数为空时
        if (!$fields) {
            if (isset($this->_parts['fields']) && $this->_parts['fields']) {
                $fields = $this->_parts['fields'];
                unset($this->_parts['fields']);
            } else {
                $fields = '*';
            }

            return $fields;
        }

        //清除脏数据,避免影响当前数据
        if (isset($this->_parts['fields'])) {
            unset($this->_parts['fields']);
        }

        //当参数为数组时
        if (is_array($fields)) {
            $fields = implode(',', $fields);
        }

        return $fields;
    }

    /**
     * 分析SQL语句的条件语句
     *
     * @access protected
     *
     * @param array|string $where Where的条件
     * @param array $value 待转义的数值
     *
     * @return string
     */
    protected function _parseCondition($where = null, $value = null) {

        $conditionArray = array('where'=>null, 'value'=>null);

        //当条件为空时
        if (!$where) {
            if (isset($this->_parts['where']) && $this->_parts['where']) {
                $conditionArray['where'] = $this->_parts['where'];
                unset($this->_parts['where']);
            }

            if (isset($this->_parts['whereValue']) && $this->_parts['whereValue']) {
                $conditionArray['value'] = $this->_parts['whereValue'];
                unset($this->_parts['whereValue']);
            }

            return $conditionArray;
        }

        //为避免对当前的数据的影响，需要清掉脏数据
        if (isset($this->_parts['where'])) {
            unset($this->_parts['where']);
        }

        if (isset($this->_parts['whereValue'])) {
            unset($this->_parts['whereValue']);
        }

        //参数分析
        if (is_array($where)) {
            $where = implode(' AND ', $where);
        }
        $conditionArray['where'] = $where;

        if (!is_null($value)) {
            $conditionArray['value'] = $value;
        }

        return $conditionArray;
    }

    /**
     * 分析SQL语句的排序
     *
     * @access protected
     *
     * @param string|array $orderDesc Order by 排序
     *
     * @return string
     */
    protected function _parseOrder($orderDesc = null) {

        //参数分析
        if (!$orderDesc) {
            if (isset($this->_parts['order']) && $this->_parts['order']) {
                $orderDesc = $this->_parts['order'];
                unset($this->_parts['order']);
            }

            return $orderDesc;
        }

        //为避免对当前的数据的影响，需要清掉脏数据
        if (isset($this->_parts['order'])) {
            unset($this->_parts['order']);
        }

        if (is_array($orderDesc)) {
            $orderDesc = implode(',', $orderDesc);
        }

        return $orderDesc;
    }

    /**
     * 分析SQL语句的limit语句
     *
     * @access protected
     *
     * @param integer $offset 启始id。注:参数为整形
     * @param integer $listCount 显示的行数
     *
     * @return string
     */
    protected function _parseLimit($offset = null, $listCount = null) {

        $limitString = '';

        //参数分析
        if (is_null($offset)) {
            if (isset($this->_parts['limit']) && $this->_parts['limit']) {
                $limitString = $this->_parts['limit'];
                unset($this->_parts['limit']);
            }

            return $limitString;
        }

        //避免影响当前数据,清除脏数据
        if (isset($this->_parts['limit'])) {
            unset($this->_parts['limit']);
        }

        $limitString = ($listCount) ? "{$offset}, {$listCount}" : $offset;
        return $limitString;
    }

    /**
     * 根据查询函数获取数据
     *
     * @access public
     *
     * @param string $funName 查询函数名称
     *
     * @return integer
     */
    protected function _getValueByFunction($funName) {

        //参数分析
        if (!$funName) {
            return false;
        }
        $funName = strtoupper($funName);

        //分析字段信息
        $fields = $this->_parseFields();
        $pos    = strpos($fields, ',');
        if ($pos !== false) {
            $fields = trim(substr($fields, 0, $pos));
        }
        //当字段信息为空时，默认为当前的主键
        if ($fields == '*') {
            $fields = $this->_getPrimaryKey();
        }

        //分析判断条件
        $condition  = $this->_parseCondition();

        //获取当前的数据表名
        $tableName  = $this->getTableName();

        $sql = "SELECT {$funName}({$fields}) AS valueName  FROM {$tableName}";
        if ($condition['where']) {
            $sql .= ' WHERE ' . $condition['where'];
        }

        $data = $this->_slave()->getOne($sql, $condition['value']);

        return (!$data) ? 0 : $data['valueName'];
    }

    /**
     * 获取查询信息的数据总行数
     *
     * @access public
     * @return integer
     */
    public function count() {

        return $this->_getValueByFunction('count');
    }

    /**
     * 获取查询信息的某数据表字段的唯一值的数据
     *
     * @access public
     * @return array
     */
    public function distinct() {

        //分析字段信息
        $fields = $this->_parseFields();
        $pos    = strpos($fields, ',');
        if ($pos !== false) {
            $fields = trim(substr($fields, 0, $pos));
        }
        //当字段信息为空时，默认为当前的主键
        if ($fields == '*') {
            $fields = $this->_getPrimaryKey();
        }

        $condition  = $this->_parseCondition();

        //获取当前的数据表名
        $tableName  = $this->getTableName();

        $sql = "SELECT DISTINCT {$fields} FROM {$tableName}";
        if ($condition['where']) {
            $sql .= ' WHERE ' . $condition['where'];
        }

        return $this->_slave()->getAll($sql, $condition['value']);
    }

    /**
     * 获取查询信息某数据表字段的最大值
     *
     * @access public
     * @return integer
     */
    public function max() {

        return $this->_getValueByFunction('max');
    }

    /**
     * 获取查询信息某数据表字段的最小值
     *
     * @access public
     * @return integer
     */
    public function min() {

        return $this->_getValueByFunction('min');
    }

    /**
     * 获取查询信息某数据表字段的数据和
     *
     * @access public
     * @return integer
     */
    public function sum() {

        return $this->_getValueByFunction('sum');
    }

    /**
     * 获取查询信息某数据表字段的数据的平均值
     *
     * @access public
     * @return integer
     */
    public function avg() {

        return $this->_getValueByFunction('avg');
    }

    /**
     * 事务处理:开启事务处理
     *
     * @access public
     * @return boolean
     */
    public function startTrans() {

        return $this->_master()->startTrans();
    }

    /**
     * 事务处理:提交事务处理
     *
     * @access public
     * @return boolean
     */
    public function commit() {

        return $this->_master()->commit();
    }

    /**
     * 事务处理:事务回滚
     *
     * @access public
     * @return boolean
     */
    public function rollback() {

        return $this->_master()->rollback();
    }

    /**
     * 创建SQL语句组装实例化对象
     *
     * @access public
     * @return object
     */
    public function createSql() {

        return DbCommand::getInstance($this->_prefix);
    }

    /**
     * 获取当前模型（Model）文件所对应的数据表主键
     *
     * 注:数据表的物理主键，真实存在的，不是虚拟的。
     *
     * @access protected
     * @return string
     */
    protected function _getPrimaryKey() {

        if (!$this->_primaryKey) {
            //从回调方法中获取数据表主键
            $primaryKey = $this->_primaryKey();
            if (!$primaryKey) {
                Response::halt('The table primary key not found! maybe the method _primaryKey() not defined in model class: ' . get_class($this));
            }
            $this->_primaryKey = trim($primaryKey);
        }

        return $this->_primaryKey;
    }

    /**
     * 获取当前模型（Model）文件所对应的数据表字段信息
     *
     * @access protected
     * @return array
     */
    protected function _getTableFields() {

        if (!$this->_tableFields) {
            //调用回调方法(获取数据表字段信息)
            $tableFields = $this->_tableFields();
            if (!$tableFields) {
                Response::halt('The table fields not found! maybe the method _tableFields() not defined in model class ' . get_class($this));
            }
            $this->_tableFields = $tableFields;
        }

        return $this->_tableFields;
    }

    /**
     * 设置当前模型（Model）文件所对应的数据表的名称
     *
     * 注:数据表名称不含数据表前缀（$prefix）
     *
     * @access public
     *
     * @param string $tableName 数据表名称
     *
     * @return object
     */
    public function setTableName($tableName) {

        //参数分析
        if (!$tableName) {
            return false;
        }

        $tableName = trim($tableName);
        $this->_tableName = (!$this->_prefix) ? $tableName : $this->_prefix . $tableName;

        return $this;
    }

    /**
     * 调试类方法:优雅输出print_r()函数所要输出的内容
     *
     * 注:详细信息参见Controller Class中的类方法dump()。
     *
     * @access public
     *
     * @param mixed $data 所要输出的数据
     * @param boolean $type 输出的信息是否含有数据类型信息。true:支持/false:不支持
     *
     * @return array
     */
    public function dump($data, $type = false) {

        return Controller::dump($data, $type);
    }

    /**
     * 实例化模型类
     *
     * 用于自定义业务逻辑时,实例化其它的模型类。
     *
     * @access public
     *
     * @param string $modelName 所要实例化的模型类的名称
     *
     * @return object
     */
    public function model($modelName) {

        //参数分析
        if (!$modelName) {
            return false;
        }

        return Controller::model($modelName);
    }

    /**
     * 获取配置文件的内容
     *
     * 注:此配置文件非数据库连接配置文件，而是其它用途的配置文件。详细信息参见Controller Class中的类方法getConfig()。
     *
     * @access public
     *
     * @param string $fileName 配置文件的名称。注:不含有“.php”后缀
     *
     * @return array
     */
    public function getConfig($fileName) {

        //参数分析
        if (!$fileName) {
            return false;
        }

        return Configure::getConfig($fileName);
    }

    /**
     * 获取当前模型（Model）文件所对应的数据表前缀
     *
     * @access public
     * @return string
     */
    public function getTablePrefix() {

        return $this->_prefix;
    }

    /**
     * 设置当前模型的错误信息
     *
     * @access public
     *
     * @param string $message 所要设置的错误信息
     *
     * @return boolean
     */
    public function setErrorInfo($message) {

        //参数分析
        if (!$message) {
            return false;
        }

        //对信息进行转义
        $this->_errorInfo = $message;

        return true;
    }

    /**
     * 获取当前模型的错误信息
     *
     * @access public
     * @return string
     */
    public function getErrorInfo() {

        return $this->_errorInfo;
    }

    /**
     * 回调类方法:自定义当前模型（Model）的数据库连接参数
     *
     * @access protected
     * @return array
     */
    protected function _setConfig() {

        return Configure::get('database');
    }

    /**
     * 分析配置文件中数据库连接的相关内容
     *
     * 对数据库配置文件进行分析,以明确主从分离信息
     *
     * @access protected
     * @return array
     */
    protected function _parseConfig() {

        //获取数据库连接参数信息
        $params = $this->_setConfig();

        if (!$params || !is_array($params)) {
            Response::halt('The configuration of database connect incorrect!');
        }

        //获取数据表前缀，默认为空
        $this->_prefix     = (isset($params['prefix']) && $params['prefix']) ? trim($params['prefix']) : '';

        //分析默认参数，默认编码为:utf-8
        $params['charset'] = (isset($params['charset']) && $params['charset']) ? trim($params['charset']) : 'utf8';

        //分析主数据库连接参数
        $configuration = array();
        if (isset($params['master']) && $params['master']) {
            $configuration['master']            = $params['master'];
            $configuration['master']['charset'] = $params['charset'];
        } else {
            $configuration['master']            = $params;
        }

        //分析从数据库连接参数
        if (isset($params['slave']) && $params['slave']) {
            //当从数据库只有一组数据时(Only One)。
            if (isset($params['slave']['dsn'])) {
                $configuration['slave'] = $params['slave'];
            } else {
                //当从数据库有多组时，随机选择一组进行连接
                $randIndex              = array_rand($params['slave']);
                $configuration['slave'] = $params['slave'][$randIndex];
            }
            $configuration['slave']['charset'] = $params['charset'];
        } else {
            $this->_singleton     = true;
            $configuration['slave'] = $configuration['master'];
        }

        //将数据库的用户名及密码及时从内存中注销，提高程序安全性
        unset($params);

        return $configuration;
    }

    /**
     * 回调类方法:自定义数据表名
     *
     * 在继承类中重载本方法可以定义所对应的数据表的名称
     *
     * @access protected
     * @return string
     */
    protected function _tableName() {

        return null;
    }

    /**
     * 回调类方法:自定义数据表主键
     *
     * 在继承类中重载本方法可以定义所对应的数据表的主键。
     *
     * @access protected
     * @return string
     */
    protected function _primaryKey() {

        return null;
    }

    /**
     * 回调类方法:自定义数据表字段信息
     *
     * 在继承类中重载本方法可以定义所对应的数据表的字段信息。
     *
     * @access protected
     * @return array
     */
    protected function _tableFields() {

        return array();
    }

    /**
     * 回调类方法:前函数(类方法)
     *
     * 用于自定义实例化当前模型时所执行的程序
     *
     * @access protected
     * @return boolean
     */
    protected function _init() {

        return true;
    }

    /**
     * 自动变量设置
     *
     * 程序运行时自动完成类中作用域为protected及private的变量的赋值 。
     *
     * @access public
     *
     * @param string $name 属性名
     * @param string $value 属性值
     *
     * @return boolean
     */
    public function __set($name, $value) {

        //设置当前的数据表
        if ($name == 'tableName') {
            $tableName = $this->quoteInto($value);
            $this->setTableName($tableName);
        }
    }

    /**
     * 自动变量获取
     *
     * 程序运行时自动完成类中作用域为protected及private的变量的获取。
     *
     * @access public
     *
     * @param string $name 属性名
     *
     * @return boolean|object
     */
    public function __get($name) {

        //过滤类中已有的变量
        $protectedParams = array(
            '_tableName',
            '_tableFields',
            '_primaryKey',
            '_prefix',
            '_errorInfo',
            '_config',
            '_parts',
            '_master',
            '_slave',
            '_singleton',
            '_instance',
        );
        if (in_array($name, $protectedParams)) {
            return null;
        }
    }

    /**
     * 类方法自动调用引导
     *
     * 用于处理类外调用本类不存在的方法时的信息提示
     *
     * @access public
     *
     * @param string $method 类方法名称
     * @param array $args 所调用类方法的参数
     *
     * @return array
     */
    public function __call($method, $args) {

        Response::halt('The method: ' . $method . '() is not found in ' . get_class($this) . ' class!');
    }

    /**
     * 析构方法（函数）
     *
     * 当本类程序运行结束后，用于"打扫战场"，如:清空无效的内存占用等
     *
     * @access public
     * @return boolean
     */
    public function __destruct() {

        $this->_master = null;

        $this->_slave  = null;

        $this->_parts  = array();
    }

    /**
     * 单例模式实例化当前模型类
     *
     * @access public
     * @return object
     */
    public static function getInstance() {

        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}