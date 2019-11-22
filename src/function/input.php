<?php
/**
 * 【ctocode】      常用函数 - input相关处理 , value相关处理
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
 * 类似 $_REQUEST，验证接收
 * 
 * @version 2017-06-22
 * @param  string $name   $key
 * @param  string $type   类型
 * @param  string $default 默认值
 * @return mixed
 */
function ctoRequestAll()
{
	return $_REQUEST;
}

/**
 * @action 获取输入参数 自动判断get或者post,支持过滤和默认值
 *  验证 接受表单的name 的值是否符合sql字段中的类型，
 *  自动赋予默认值
 *  重新封装tp I方法，并且完善
 * @使用方法:
 * <code>
 * 	checkRequest('form_name','string'); 获取id参数
 * 	checkRequest('form_name','int'); 获取id参数
 * 	checkRequest('name',''); 获取$_POST['name']
 * </code>
 * @version 2016-10-08
 * @author ctocode-zhw
 * @copyright ctocode
 * @link http://www.ctocode.com
 * @param string $name 字段表单name,变量的名称 支持指定类型
 * @param mixed $type 字段类型
 * @param mixed $default 你希望返回的默认的值
 * @return mixed
 */
function ctoRequest($name, $type = 'string', $default = '')
{
	$param = $_REQUEST;
	if(isset ( $param[$name] ) && is_array ( $param[$name] )){
		if(isset ( $param[$name] )){
			$return_val = $param[$name];
		}else{
			return null;
		}
	}else{
		$return_val = isset ( $param[$name] ) ? $_REQUEST[$name] : '';
	}
	if(! empty ( $type )){
		$return_val = ctoValueCheck ( $return_val, $type, $default );
	}
	// if(empty ( $return_val ) && ! isset ( $return_val )){
	if(empty ( $return_val )){
		$input = @file_get_contents ( 'php://input' );
		$input = json_decode ( $input, 1 );
		return isset ( $input[$name] ) ? $input[$name] : '';
	}
	return $return_val;
}
// 基础 值 验证函数 2016-10-10
function ctoValueCheck($value, $type = 'string', $default = null)
{
	switch($type)
	{
		case 'int':
			$data = ! empty ( $default ) ? $default : 0;
			$value = trim ( "{$value}" );
			$return = is_numeric ( $value ) ? $value : 0;
			if(empty ( $return ) && ! empty ( $default )){
				$return = $data;
			}
			break;
		case 'string':
			// addslashes 转义字符,默认开启
			// removeXss 过滤xss攻击，默认开启
			$data = ! empty ( $default ) ? $default : '';
			$value = trim ( "{$value}" );
			// 过滤转义字符
			$value = ctoSecurityAddslashes ( $value );
			// 过滤XSS
			$value = ctoSecurityRemoveXss ( $value );
			$return = ! empty ( $value ) ? $value : $data;
			break;
		case 'date':
			$data = isset ( $default ) ? $default : time ();
			$value = strtotime ( $value );
			$return = $value ? $value : $data;
			break;
		case 'float':
			$return = is_float ( $value ) ? $value : 0;
			break;
		case 'double':
			$return = is_double ( $value ) ? $value : 0;
			break;
		case 'arr':
		case 'array':
			$return = $value;
			if(is_array ( $value )){
				// 如果是数组的话，要递归处理
			}
			break;
		case 'null':
			$return = $value;
			break;
		default:
			$return = ctoSecurityAddslashes ( $value );
			break;
	}
	return $return;
}

//
function echoAjaxJsonData($arr = array())
{
	// TODO 目前强制为 json 返回
	header ( 'Content-Type: application/json; charset=utf-8' );
	exit ( json_encode ( $arr ) );
}
function echoJsonSuccess($data = '', $msg = '操作成功~')
{
	echoAjaxJsonData ( [
		'status' => 200,
		'data' => $data,
		'msg' => $msg
	] );
}
function echoJsonError($msg = '操作失败~', $status = 404)
{
	echoAjaxJsonData ( [
		'status' => $status,
		'msg' => $msg
	] );
}
function echoHtmlError($html_str = '')
{
	$html = '';
	$html .= "<h4> === 提示 === </h4>";
	$html .= "<p>{$html_str}</p>";
	echo $html;
	exit ();
}

// string(字符串) operation(DECODE-解密 其他-加密) key(混淆字符) expiry(过期时间) fixed(加密结果是否固定)
function ctoValueAuthcode($string, $operation = 'DECODE', $key = '', $expiry = 600, $fixed = false)
{
	$ckey_length = $fixed ? 0 : 4;
	$key = md5 ( $key ? $key : 'default_key' );
	$keya = md5 ( substr ( $key, 0, 16 ) );
	$keyb = md5 ( substr ( $key, 16, 16 ) );
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr ( $string, 0, $ckey_length ) : substr ( md5 ( microtime () ), - $ckey_length )) : '';

	$cryptkey = $keya . md5 ( $keya . $keyc );
	$key_length = strlen ( $cryptkey );

	$string = $operation == 'DECODE' ? base64_decode ( substr ( $string, $ckey_length ) ) : sprintf ( '%010d', $expiry ? $expiry + time () : 0 ) . substr ( md5 ( $string . $keyb ), 0, 16 ) . $string;
	$string_length = strlen ( $string );

	$result = '';
	$box = range ( 0, 255 );

	$rndkey = array();
	for($i = 0;$i <= 255;$i ++){
		$rndkey[$i] = ord ( $cryptkey[$i % $key_length] );
	}
	for($j = $i = 0;$i < 256;$i ++){
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}
	for($a = $j = $i = 0;$i < $string_length;$i ++){
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr ( ord ( $string[$i] ) ^ ($box[($box[$a] + $box[$j]) % 256]) );
	}
	if($operation == 'DECODE'){
		if((substr ( $result, 0, 10 ) == 0 || substr ( $result, 0, 10 ) - time () > 0) && substr ( $result, 10, 16 ) == substr ( md5 ( substr ( $result, 26 ) . $keyb ), 0, 16 )){
			return substr ( $result, 26 );
		}else{
			return '';
		}
	}else{
		return $keyc . str_replace ( '=', '', base64_encode ( $result ) );
	}
}
/**
 * @action 金额 小数 转换 int
 * @version 2016-08-08
 * @author http://www.ctocode.com
 */
function ctoValueMoneyEn($money = null)
{
	if(empty ( $money )){
		return 0;
	}
	if(is_numeric ( $money )){
		$int_money = ($money * 100);
		// $int_money = intval ( $int_money );
		$int_money = ( float ) ($int_money);
	}else{
		$int_money = 0;
	}
	return $int_money;
}
/**
 * @action int 转换 金额小数
 * @author zhw
 * @version 2016-08-08
 */
function ctoValueMoneyDe($money = null)
{
	if(empty ( $money )){
		return '0.00';
	}
	if(is_numeric ( $money )){
		$int_money = $money / 100;
		$int_money = sprintf ( "%.2f", $int_money );
	}else{
		$int_money = 0;
	}
	return $int_money;
}
/**
 * 数字(>0)加密变长
 * @author ctocode-zhw
 * @version 2015-07-12
 * @param number $int
 * @param string $type
 * @return number|string
 */
function ctoValueNumEncode($int = 0, $type = 'ENCODE')
{
	if($type == 'ENCODE'){
		$int = ( int ) $int;
		if($int == 0){
			return 0;
		}
		$len = strlen ( $int );
		$temp = (300000000 + $int - 19750806) * 2;
		return $temp . $len;
	}else{
		$num = ( int ) substr ( $int, 0, strlen ( $int ) - 1 );
		$len = ( int ) substr ( $int, - 1, 1 );
		if($num == 0 || $len == 0){
			return 0;
		}
		$new = ( int ) (($num / 2) + 19750806 - 300000000);
		if(strlen ( $new ) == $len){
			return $new;
		}
	}
	return 0;
}
function ctoValueNumDecode($int = 0, $type = 'ENCODE')
{
	return ctoValueNumEncode ( $int, 'DECODE' );
}
