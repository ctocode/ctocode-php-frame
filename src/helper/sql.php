<?php

/**
 * 【ctocode】      常用函数 - sql相关处理
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
/**
 * @action 解析表单sql语句，更新update，或插入insert
 * 返回重新编写好的sql语句
 * @author Name zhw Email 343196936@qq.com Data 2016年3月28日
 * @param string $table 数据表名
 * @param array $data 更新或插入的数据
 * @param string $where_sql 查询条件
 * @param $in_or_up  存在即更新。必须还有唯一主键
 * @return string 返回重新编写好的sql语句
 */
function ctoSqlParse($table = '', $data = array(), $where_sql = '', $in_or_up = false)
{
	if($table == '' || ! is_array ( $data )){
		return '';
	}
	if($where_sql != ''){
		$field_update = '';
		foreach($data as $k=>$v){
			$field_update .= ($field_update == '' ? '' : ',') . "`$k`='$v'";
		}
		$sql = "UPDATE $table SET $field_update WHERE $where_sql;";
	}else{
		$field_key = $field_val = '';
		foreach($data as $k=>$v){
			$field_key .= ($field_key == '' ? '' : ',') . "`$k`";
			if(is_int ( $v )){
				$field_val .= ($field_val == '' ? '' : ',') . "$v";
			}else{
				$field_val .= ($field_val == '' ? '' : ',') . "'$v'";
			}
		}
		if($field_key != '' && $field_val != ''){
			$sql = "INSERT INTO $table ($field_key) VALUES ($field_val)";
		}
		
		if($in_or_up == true){
			$in_or_up_arr = array();
			foreach($data as $k=>$v){
				$in_or_up_arr[] = "`$k`='$v'";
			}
			$in_or_up_arr = join ( ",", $in_or_up_arr );
			$sql .= "   ON DUPLICATE KEY UPDATE " . $in_or_up_arr;
		}
	}
	return $sql;
}
// 查找表单字段是否存在
function ctoSqlDescribe()
{
	// 'Describe 表名 字段名'
}
/**
 * @action 获取数据表的字段
 * @author ctocode-zhw
 * @version 2017-07-19
 * @param string $table_name
 * @return string|string[][]
 */
function ctoSqlGetTableColumn($table_name = '')
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