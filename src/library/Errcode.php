<?php
class CTOCODE_Errcode
{
	protected static $_responseArr = array();
	protected static $_responseFlagArr = array();
	protected static $_responseCodeArr = array();
	protected static function getResponseResule($type = '', $str = '')
	{
		global $_RESPONSE_CODE_;
		self::$_responseArr = $_RESPONSE_CODE_;
		$result = array();
		foreach(self::$_responseArr as $key=>$val){
			$item = array();
			$item = explode ( "===#@#===", $val );
			$result['code'] = $key;
			$result['flag'] = $item[0];
			$result['msg'] = $item[1];
			if($type == 'flag_change_code'){
				if($result['flag'] == $str){
					break;
				}
			}elseif($type == 'code_change_flag'){
				if($result['code'] == $str){
					break;
				}
			}
		}
		return $result;
	}
	/**
	 * 根据 状态码、或者状态标识 获取信息
	 * @param string $str
	 * @return mixed[]|array[]|string[]|string[]
	 */
	public static function getResponseInfo($str = '')
	{
		if(is_string ( $str )){
			return self::getResponseResule ( 'flag_change_code', $str );
		}elseif(is_numeric ( $str )){
			return self::getResponseResule ( 'code_change_flag', $str );
		}else{
			return array(
				'msg' => '未定义错误返回信息！'
			);
		}
	}
	/**
	 * 根据 状态码 获取 标识
	 * @param string $code 状态码
	 * @return mixed|array|string 标识
	 */
	public static function getResponseFlag($code = '')
	{
		return self::getResponseResule ( 'code_change_flag', $code )['flag'];
	}
	/**
	 * 根据 标识 获取 状态码
	 * @param string $flag 标识
	 * @return mixed|array|string 状态码
	 */
	public static function getResponseCode($flag = '')
	{
		return self::getResponseResule ( 'flag_change_code', $flag )['code'];
	}
}
