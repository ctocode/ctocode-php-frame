<?php

namespace ctocode\phpframe\library;

/**
 * 【 调用方式参考 】
 *
 * ===== 用户未登录案例 ====
 * $responseInfo = \CTOCODE_Errcode::getResponseInfo ( 'UCENTER_LOGIN_NOT' );
 * echoJsonError ( $responseInfo['msg'], $responseInfo['code'] );
 *
 * ===== 返回
 *
 * @author ctocode-zhw
 * @version 2017-12-13
 */
class CtoErrorcode
{
	protected static $_responseArr = array();
	protected static $_responseFlagArr = array();
	protected static $_responseCodeArr = array();
	protected static $_RESPONSE_CODE_ = array(
		200 => 'SYSTEM_SUCCESS===#@#===请求成功',
		404 => 'SYSTEM_ERROR===#@#===系统异常', // 系统发生错误
		502 => 'SYSTEM_BUG===#@#===数据库异常',
		505 => 'SYSTEM_BUG===#@#===系统bug',

		1049 => 'SYSTEM_WRONG_POSITION===#@#===错误位置!',
		1050 => 'SYSTEM_WAS_WRONG===#@#===抱歉,出错啦!',
		1051 => 'SYSTEM_JUMP_CUE===#@#===跳转提示',
		1052 => 'SYSTEM_JUMP_IMMEDIATELY===#@#===立即跳转',
		1053 => 'SYSTEM_JUMP_STOP===#@#===停止跳转',
		/* token */
		8001 => 'TOKEN_TYPE_UNDEFINED===#@#===token-type未定义',
		8002 => 'TOKEN_ACCESS_UNDEFINED===#@#===token-access未定义',
		
		/* 用户中心 */
		10001 => 'UCENTER_LOGIN_NOT===#@#===用户未登录',
		10002 => 'UCENTER_LOGIN_OVERDUE===#@#===用户登录过期',
		10003 => 'UCENTER_LOGIN_ERROR===#@#===用户登录失败',
		10013 => 'UCENTER_LOGIN_AGAIN===#@#===用户重新登录',
		//
		10018 => 'UCENTER_REGISTER_NOT===#@#===未注册绑定',
		10020 => 'UCENTER_MOBILE_UNKNOWN===#@#===手机号不存在',
		20001 => 'WEIXIN_PAY_FAIL===#@#===微信支付失败' ,
		/* 业务层面 */
		70000 => 'FINANCE_PAY_FAIL===#@#===财务微信支付失败'
	);
	protected static function getResponseResule($type = '', $str = '')
	{
		self::$_responseArr = self::$_RESPONSE_CODE_;
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
