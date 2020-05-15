<?php

namespace ctocode\core;

trait CtoTraitModelParse
{
	protected $tablePrimary = '';
	/**
	 * @action SQL语句验证函数,防注入 防CC、至强抗DDoS 等
	 * @author ctocode-zhw
	 * @version 2017-03-14
	 */
	protected function sqlVerify($sql = null)
	{
		return $sql;
	}
	protected function parseSqlWhere()
	{
	}
	// 排序
	protected function parseSqlOrder($wsql = [], $zhubiaoPx = '')
	{
		if(empty ( $this->tablePrimary )){
			return '';
		}
		$order_by = ! empty ( $wsql['orderby'] ) ? strtolower ( trim ( $wsql['orderby'] ) ) : $this->tablePrimary;
		if($order_by == 'rand'){
			return " ORDER BY RAND() ";
		}
		$order_sort = ! empty ( $wsql['ordersort'] ) ? strtoupper ( trim ( $wsql['ordersort'] ) ) : 'DESC';
		$order_sort = in_array ( $order_sort, array(
			'DESC',
			'ASC'
		) ) ? $order_sort : 'DESC';
		return " ORDER BY {$zhubiaoPx}{$order_by} {$order_sort} ";
	}
	// 分页
	protected function parseSqlLimit($wsql = [])
	{
		$page = ! empty ( $wsql['page'] ) ? $wsql['page'] : 1;
		$page = is_numeric ( $page ) ? intval ( $page ) : 1;
		$pagesize = ! empty ( $wsql['pagesize'] ) ? $wsql['pagesize'] : 10;
		$pagesize = $pagesize == 'all' ? $pagesize : intval ( $pagesize );
		return $pagesize == 'all' ? ' ' : ' LIMIT ' . ($page - 1) * $pagesize . ",{$pagesize} ";
	}
	/**
	 * @action SQL语句解析函数
	 * @author ctocode-zhw
	 * @version 2017-03-14
	 */
	protected function parseInOrUp($table = '', $data = array(), $where_sql = '', $in_or_up = false)
	{
		if($in_or_up === true){
			return ctoSqlParse ( $table, $data, '', $in_or_up );
		}else{
			return ctoSqlParse ( $table, $data, $where_sql, $in_or_up );
		}
	}
}