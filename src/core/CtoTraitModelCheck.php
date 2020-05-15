<?php

namespace ctocode\core;

trait CtoTraitModelCheck
{

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
		if(is_numeric ( $value )){
			$time = $value;
		}else{
			$value = trim ( "{$value}" );
			$time = strtotime ( $value );
		}
		$return = $time ? $time : 0;
		if(empty ( $return ) && ! empty ( $default )){
			$return = $data;
		}
		return $return;
	}
	protected function checkTime($value, $default = 0)
	{
		return $this->checkDate ( $value, $default );
	}
	//
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
}