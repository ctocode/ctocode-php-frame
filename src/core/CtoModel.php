<?php

namespace ctocode\phpframe\core;

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
	protected $tableType = 'mysql';
	protected $tableName = ''; // 当前模型.除开后缀的名字
	protected $tablePrimary = ''; // 当前模型.表单主键
	protected $table = ''; // 当前模型.表单名称

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
	protected function sqlRead($sql = '')
	{
		if(empty ( $sql )){
			return false;
		}
		$sql_result = ctoDbRead ( $sql );
		return $sql_result;
	}
	/**
	 * @action 执行 更新或者插入
	 * @execute
	 */
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
	 * @action 【验证函数】--价格验证
	 * @author ctocode-zhw
	 * @version 2017-03-14
	 */
	protected function checkPrice($value, $default = 0)
	{
		$data = ! empty ( $default ) ? $default : 0;
		$value = trim ( "{$value}" );
		$value = is_numeric ( $value ) ? $value : 0;
		$int_money = is_numeric ( $data );
		if(is_numeric ( $value )){
			$int_money = ($value * 100);
			// $int_money = intval ( $int_money );
			$int_money = ( float ) ($int_money);
		}
		return $int_money;
	}
	/**
	 * @action 【验证函数】--mysql【varchat】验证
	 * @author ctocode-zhw
	 * @version 2017-03-14
	 */
	protected function checkVarchar($value = '', $default = '')
	{
		if(! empty ( $value )){
			if(is_string ( $value )){
				return trim ( $value );
			}
			return $value;
		}else{
			return '';
		}
	}
	/**
	 * @action 【验证函数】--mysql【int】验证
	 * @author ctocode-zhw
	 * @version 2017-03-14
	 */
	protected function checkInt($value = 0, $default = 0)
	{
		$data = ! empty ( $default ) ? $default : 0;
		$value = trim ( "{$value}" );
		$return = is_numeric ( $value ) ? ( int ) ($value) : 0;
		if(empty ( $return ) && ! empty ( $default )){
			$return = $data;
		}
		return $return;
	}
	/**
	 * @action 【验证函数】--mysql【date转int】验证
	 * @author ctocode-zhw
	 * @version 2017-03-14
	 */
	protected function checkDate($value, $default = 0)
	{
		$data = ! empty ( $default ) ? $default : 0;
		$value = trim ( "{$value}" );
		$time = strtotime ( $value );
		$return = $time ? $time : 0;
		if(empty ( $return ) && ! empty ( $default )){
			$return = $data;
		}
		return $return;
	}
	/**
	 * @action SQL语句解析函数
	 * @author ctocode-zhw
	 * @version 2017-03-14
	 */
	protected function sqlParse($table = '', $data = array(), $where_sql = '', $in_or_up = false)
	{
		if($in_or_up === true){
			return ctoSqlParse ( $table, $data, '', $in_or_up );
		}else{
			return ctoSqlParse ( $table, $data, $where_sql, $in_or_up );
		}
	}

	/**
	 * @action SQL语句验证函数,防注入 防CC、至强抗DDoS 等
	 * @author ctocode-zhw
	 * @version 2017-03-14
	 */
	protected function sqlVerify($sql = null)
	{
		return $sql;
	}
	protected function checkStrIsComma($strs = null)
	{
		$str = str_replace ( "，", ",", $strs );
		$str = str_replace ( ",", ",", $str );

		if(strpos ( $str, ',' ) !== false){
			return false;
		}
		return true;

		// if (preg_match ("/，/", "Welcome to ，hi-docs.com.")) {
		// echo "A match was found.";
		// } else {
		// echo "A match was not found.";
		// }

		// if (strstr($str, '，')) {
		// echo 'exist comma!'; //含有逗号
		// } else {
		// echo 'not exist comma!'; //不含逗号
		// }
	}

	/**
	 * @action 获取数据表的字段
	 * @author ctocode-zhw
	 * @version 2017-07-19
	 * @param string $table_name
	 * @return string|string[][]
	 */
	function getTableColumn($table_name = '')
	{
		if(empty ( $table_name )){
			return '';
		}
		// 表单是否存在
		$is_table = ctoDbRead ( "SHOW TABLES LIKE '{$table_name}'; " );
		if(! empty ( $is_table[0] )){ // 字段是否存在
			$field_array = ctoDbRead ( "DESCRIBE {$table_name} ;" );
			$lists = array();
			foreach($field_array as $key=>$val){
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
}
