<?php

namespace ctocode\library;

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
/**
 * 【ctocode】      常用函数 - debug 调试 相关处理
 * ============================================================================
 * @author       作者       ctocode-zhw
 * @version 	  版本	   v1.0.0.0.20170720
 * @copyright    版权所有   2015-2027，并保留所有权利。
 * @link         网站地址   https://www.10yun.com
 * @contact      联系方式   QQ:343196936
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 */
class CtoDebugErrorcode
{
	/**
	 * @action
	 * 根据9宫格，根据 板块+模块+控制器，转译str为数字
	 *  如 errorStr = 'pc/member/card' 对应9宫格数字为 726362372273
	 *
	 *  1       2[abc] 3[def]
	 *  4[ghi]  5[jkl] 6[mno]
	 *  7[pqrs] 8[tuv] 9[wxyz]
	 *
	 *  @author ctocode-zhw
	 *  @version 2016-11-16
	 */
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
		8001 => 'TOKEN_TYPE_UNDEFINED===#@#===syOpenAppsKey未定义',
		8002 => 'TOKEN_ACCESS_UNDEFINED===#@#===syOpenAppsToken未定义',

		/* 用户中心 */
		10001 => 'UCENTER_LOGIN_NOT===#@#===用户未登录',
		10002 => 'UCENTER_LOGIN_OVERDUE===#@#===用户登录过期',
		10003 => 'UCENTER_LOGIN_ERROR===#@#===用户登录失败',
		10013 => 'UCENTER_LOGIN_AGAIN===#@#===用户重新登录',
		//
		10018 => 'UCENTER_REGISTER_NOT===#@#===未注册绑定',
		10020 => 'UCENTER_MOBILE_UNKNOWN===#@#===手机号不存在',
		20001 => 'WEIXIN_PAY_FAIL===#@#===微信支付失败',
		/* 业务层面 */
		70000 => 'FINANCE_PAY_FAIL===#@#===财务微信支付失败'
	);
	protected static function getResponseResule($type = '', $str = '')
	{
		self::$_responseArr = self::$_RESPONSE_CODE_;
		$result = array();
		foreach (self::$_responseArr as $key => $val) {
			$item = array();
			$item = explode("===#@#===", $val);
			$result['code'] = $key;
			$result['flag'] = $item[0];
			$result['msg'] = $item[1];
			if ($type == 'flag_change_code') {
				if ($result['flag'] == $str) {
					break;
				}
			} elseif ($type == 'code_change_flag') {
				if ($result['code'] == $str) {
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
		if (is_string($str)) {
			return self::getResponseResule('flag_change_code', $str);
		} elseif (is_numeric($str)) {
			return self::getResponseResule('code_change_flag', $str);
		} else {
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
		return self::getResponseResule('code_change_flag', $code)['flag'];
	}
	/**
	 * 根据 标识 获取 状态码
	 * @param string $flag 标识
	 * @return mixed|array|string 状态码
	 */
	public static function getResponseCode($flag = '')
	{
		return self::getResponseResule('flag_change_code', $flag)['code'];
	}
	/**
	 * 调试
	 * @param array $data 要写入的数据
	 * @param string $tag 标识
	 * @param string $file_name 要写入的文件名
	 * @param number $type 写入数据的类型 1：字符串 2：数组
	 */
	function ctoLogDebug($data = array(), $tag = '', $file_name = 'newlogfile.txt', $type = 1)
	{
		// 判断路径是否存在
		$debug_log_dir = _CTOCODE_RUNTIME_ . '/debug_log/' . date('Y-m-d', time());
		if (!is_dir($debug_log_dir)) {
			mkdir($debug_log_dir, 0777, true);
		}
		$file_name = $debug_log_dir . '/' . $file_name;
		$time = date('Y-m-d H:i:s', time());
		$myfile = fopen($file_name, "a");
		if (!$myfile) {
			return FALSE;
		}
		fwrite($myfile, $tag . '==>' . $time . "\r\n");

		if ($type == 1) {
			fwrite($myfile, $data);
		} else {
			fwrite($myfile, var_export($data, TRUE));
		}
		fwrite($myfile, "\r\n\r\n");
		fclose($myfile);
		return TRUE;
	}

	/**
	 * @function 获取执行路径
	 * @version 2017-03-20
	 * @author ctocode-zhw
	 */
	function ctoDebugGetBackTrace()
	{
		$backtrace = debug_backtrace(NULL, 6);

		foreach ($backtrace as $key => $val) {
			$rtn = preg_match('/.*App.class.php$/', $val['file']);
			$rtn1 = preg_match('/.*dbrw.tp.php$/', $val['file']);
			$rtn2 = preg_match('/.*DB1_Read$/', $val['function']);
			$rtn3 = preg_match('/.*debug.func.php$/', $val['file']);
			if ($rtn || $rtn1 || $rtn2 || $rtn3) {
				unset($backtrace[$key]);
			}
		}
		$backtrace = array_reverse($backtrace);
		$trace = "\r\n\r\n";
		$trace .= date("Y-m-d H:i:s") . " ==================START==================\r\n";
		foreach ($backtrace as $key => $val) {
			$trace .= "class: " . $val['class'] . " ==> function: " . $val['function'];
			if (isset($val['file'])) {
				$trace .= "    file: " . $val['file'] . " ==> line: " . $val['line'] . "\r\n";
			} else {
				$trace .= "\r\n";
			}
		}
		return $trace;
	}

	/**
	 * @function 日志记录
	 * @version 2017-03-20
	 * @author ctocode-zhw
	 *
	 * @param int $bid 商户id
	 * @param string $string 要记录的字符串
	 * @param string $flags 标志位
	 */
	function ctoDebugLog($string = "", $flags = "", $dirName = "", $fileName = "")
	{
		// bs_staff_message AS umsg
		if ($rtn = preg_match('/.*bs_staff_message\s*AS\s*umsg.*/', $string)) {
			return;
		}
		$trace = ctoDebugGetBackTrace();
		if (!empty($flags)) {
			if (is_bool($flags)) {
				$flags = $flags === TRUE ? 'TRUE' : 'FALSE';
			}
			$trace .= "【FLAG】: " . $flags . "\r\n";
		}
		$trace .= $string;

		// 判断路径是否存在
		$debug_log_dir = 'data/mysql_log/' . date('Y-m-d', time()) . "/" . $dirName;
		if (!is_dir($debug_log_dir)) {
			mkdir($debug_log_dir, 0777, true);
		}

		$file_name = $debug_log_dir . '/' . $fileName;
		$fp = fopen($file_name, "a");
		if (!$fp) {
			return FALSE;
		}

		$content = $trace . "\r\n客户端ip地址：" . ctoIpGet() . "\r\n" . date("Y-m-d H:i:s") . "==================THE END==================\r\n";
		fwrite($fp, $content);
		fclose($fp);

		return TRUE;
	}
}
