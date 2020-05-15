<?php

namespace ctocode\core;

/**
 * 【ctocode】      核心文件 - 模型
 * ============================================================================
 * @author       作者         ctocode-zhw
 * @version 	  版本	  v1.0.0.0.20170720
 * @copyright    版权所有   2015-2027，并保留所有权利。
 * @link         网站地址   http://www.ctocode.com
 * @contact      联系方式   QQ:343196936
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 */
class CtoModel
{
	use CtoTraitModelCheck,CtoTraitModelParse;
	protected $tableType = 'mysql';
	protected $tableName = ''; // 当前模型.除开后缀的名字
	protected $tablePrimary = ''; // 当前模型.表单主键
	protected $table = ''; // 当前模型.表单名称

	// 是否自动解析
	protected $_autoAnalys = false;

	/* 数据库连接对象 */
	protected $_dbObj;
	/* */
	protected $dbprefix = ''; // 当前模型.表单前缀
	protected $time;
	// 当前模型的【表单字段】
	protected $_tableFields = array();
	// 【字段】表单提交的数据
	protected $_fieldFormData = array();
	// 【字段】根据数据表-和表单提交的数据，处理解析数据
	protected $_fieldParseData = array();
	// 当前表单的字段，类型，和验证方式
	/**
	 * @action 构造函数
	 */
	public function __construct($options = null)
	{
		$this->time = time ();
		$this->table = $this->dbprefix . $this->tableName;
		$this->_initialize ();
	}
	/**
	 * @action 初始化
	 */
	public function _initialize()
	{
	}
	// 处理读写分离
	/**
	 * @action 执行 查询
	 * @query
	 */
	protected function query($sql = '')
	{
		return $this->sqlRead ( $sql );
	}
	protected function analysField($readData, $tableFields)
	{
		$anaObj = new CtoModelAnalys ();
		return $anaObj->analysField ( $readData, $tableFields );
	}
	protected function sqlRead($sql = '')
	{
		if(empty ( $sql )){
			return false;
		}
		$sql_result = ctoDbRead ( $sql );
		if($this->_autoAnalys){
			return $this->analysField ( $sql_result, $this->_tableFields );
		}
		return $sql_result;
	}
	/**
	 * @action 执行 更新或者插入
	 * @execute
	 */
	protected function execute($sql = '')
	{
		return $this->sqlWrite ( $sql );
	}
	protected function sqlWrite($tableName = '', $fieldData = '', $wsql = '', $in_or_up = false, $type = 'xxx')
	{
		if($type == 'R'){
			$sql_result = ctoDbWrite ( $this->table, $fieldData, $wsql, $in_or_up );
		}else{
			$sql_result = ctoDbWrite ( $this->table, $fieldData, $wsql, $in_or_up );
		}
		return $sql_result;
	}
	/**
	 * @action 根据数据表模型解析form提交的数据
	 * @version 2018-08-12 01:22
	 * @author ctocode-zhw 
	 * @return array 
	 */
	public function parseRequestData($transmitData = null)
	{
		$requestData = array();
		if(! empty ( $transmitData )){
			$requestData = $transmitData;
		}else{
			$requestData = $_REQUEST;
		}
		// if($this->isFieldOverflow ( $requestData ) == false || $this->isFieldMust ( $requestData ) == false)
		// {
		// return false;
		// }
		$parseResult = array();
		$parseState = true;
		$parseMsg = '';
		foreach($this->_tableFields as $key=>$val){
			if(in_array ( $key, array_keys ( $requestData ) )){
				$field_check_func = 'check' . $val['type']; // 字段类型,验证函数
				$field_default = isset ( $val['default'] ) ? $val['default'] : null;
				$field_is_null = isset ( $val['null'] ) ? $val['null'] : null;
				// 当 未传递 $requestData[$key] 的时候
				if(! isset ( $requestData[$key] )){
					if(! empty ( $requestData[$this->tablePrimary] )){
						continue;
					}
					$form_val = '';
				}else{
					// null 为传递值未定义的字段
					if($requestData[$key] !== null){
						$form_val = $requestData[$key]; // 传递值
					}
				}
				// 验证方法
				if(method_exists ( $this, $field_check_func )){
					if(! empty ( $field_default )){
						$parseResult[$key] = $this->$field_check_func ( $form_val, $field_default );
					}else{
						$parseResult[$key] = $this->$field_check_func ( $form_val );
					}
				}else{ // 不符合的时候，默认值
					$parseResult[$key] = $form_val;
				}
				if((! empty ( $field_is_null ) && $field_is_null == 'no') && empty ( $form_val )){
					$parseState = false;
					$parseMsg = $val['comment'];
					break;
				}
			}
		}
		if($parseState === true){
			return array(
				'state' => 'success',
				'data' => $parseResult
			);
		}else{
			return array(
				'state' => 'error',
				'msg' => $parseMsg
			);
		}
	}
	/**
	 * @action 判断必须字段是否存在
	 * @version 2016-10-20
	 * @author ctocode-zhw
	 */
	protected function isFieldMust($arrs)
	{
		if(empty ( $arrs ) || empty ( $this->_fields )){
			return false;
		}
		foreach($this->_fields as $key=>$val){
			if(! array_key_exists ( $key, $arrs ) && $val['must'] == 1){ // 必须的字段不在传入的数组中
				return false;
			}
		}
		return true;
	}
	/**
	 * @action 判断字段是否超出
	 * @version 2016-10-20
	 * @author ctocode-zhw
	 */
	protected function isFieldOverflow($arrs)
	{
		if(empty ( $arrs ) || empty ( $this->_fields )){
			return false;
		}
		foreach($arrs as $key=>$val){
			if(! array_key_exists ( $key, $this->_fields )){ // 传入的数组中 超出定义的表字段
				return false;
			}
		}
		return true;
	}
	/**
	 * @action 获取数据表的字段
	 * @author ctocode-zhw
	 * @version 2017-07-19
	 * @param string $table_name
	 * @return string|string[][]
	 */
	public function getTableColumn($table_name = '')
	{
		if(empty ( $table_name )){
			return '';
		}
		// 表单是否存在
		$is_table = ctoDbRead ( "SHOW TABLES LIKE '{$table_name}'; " );
		if(! empty ( $is_table[0] )){ // 字段是否存在
			$field_array = ctoDbRead ( "DESCRIBE {$table_name} ;" );
			$lists = array();
			foreach($field_array as $val){
				$rows = array();
				$rows['field'] = $val['Field'];
				if(strpos ( $val['Type'], 'int' ) !== FALSE){
					$rows['type'] = 'int';
				}elseif(strpos ( $val['Type'], 'varchar' ) !== FALSE){
					$rows['type'] = 'varchar';
				}elseif(strpos ( $val['Type'], 'text' ) !== FALSE){
					$rows['type'] = 'text';
				}
				$lists[] = $rows;
			}
			return $lists;
		}
		return '';
	}
	/**
	 * 获取 sql 语句里面的 table 表名
	 * @param string $sqlString
	 * @return mixed|string
	 */
	public function getSqlTableName($sqlString = '')
	{
		$sqlString = str_replace ( '`', '', trim ( $sqlString ) ) . ' AND 1=1 ';
		$key = strtolower ( substr ( $sqlString, 0, 6 ) );
		if($key === 'select'){
			$tmp = explode ( 'where', strtolower ( trim ( $sqlString ) ) );
			$tmp = explode ( 'from', $tmp[0] );
			if(strpos ( $tmp[1], ',' ) !== false && ! stristr ( $tmp[1], 'select' )){
				$tmp = explode ( ',', $tmp[1] );
				foreach($tmp as $v){
					$v = trim ( $v );
					if(strpos ( $v, ' ' ) !== false){
						$tv = explode ( ' ', $v );
						$return[] = $tv[0];
					}
				}
				return $return;
			}else{
				$expression = '/((SELECT.+?FROM)|(LEFT\\s+JOIN|JOIN|LEFT))[\\s`]+?(\\w+)[\\s`]+?/is';
			}
		}else if($key === 'delete'){
			$expression = '/DELETE\\s+?FROM[\\s`]+?(\\w+)[\\s`]+?/is';
		}else if($key === 'insert'){
			$expression = '/INSERT\\s+?INTO[\\s`]+?(\\w+)[\\s`]+?/is';
		}else if($key === 'update'){
			$tmp = explode ( 'set', strtolower ( str_replace ( '`', '', trim ( $sqlString ) ) ) );
			$tmp = explode ( 'update', $tmp[0] );
			if(strpos ( $tmp[1], ',' ) !== false && ! stristr ( $tmp[1], 'update' )){
				$tmp = explode ( ',', $tmp[1] );
				foreach($tmp as $v){
					$v = trim ( $v );
					if(strpos ( $v, ' ' ) !== false){
						$tv = explode ( ' ', $v );
						$return[] = $tv[0];
					}
				}
				return $return;
			}else{
				$expression = '/UPDATE[\\s`]+?(\\w+)[\\s`]+?/is';
			}
		}
		$matches = [];
		preg_match_all ( $expression, $sqlString, $matches );
		return array_unique ( array_pop ( $matches ) );
	}
}
