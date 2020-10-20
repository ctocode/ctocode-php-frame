<?php

namespace ctocode\sdks\sms;


# 参考文档 https://github.com/jpush/jsms-api-php-client

use JiGuang\JSMS;

/**
 * 极光短信
 * @author ctocode-zhw
 * @version 2016-11-15
 */
class SmsSdkJiguang extends SmsSdkCommon implements SmsSdkInterface
{
	public static function sdkSmsCheck($smsParam = [], $sdkOpt = [], $mobile = '', $code = '')
	{
		$code = trim($code);
		// 验证短信配置
		if (empty($code)) {
			return array(
				'status' => 404,
				'error' => 'Not Found',
				'msg' => '短信验证码不能为空'
			);
		}
		// 实例对象


		$smsObj = new \JiGuang\JSMS($sdkOpt['sms_appkey'], $sdkOpt['sms_appsecret'], [
			'ssl_verify' => false
		]);
		$sendResult = [];
		$sendResult = $smsObj->checkCode($smsParam['msg_id'], $code);
		$sendResult['result_json_decode'] = json_decode($sendResult['body'], true, 10);

		if ($sendResult['http_code'] === 200) {
			return array(
				'status' => 200,
				'error' => 'Not Found',
				'sms' => $sendResult['result_json_decode'],
				'msg' => '验证成功~'
			);
		}
		return array(
			'status' => 404,
			'error' => 'Not Found',
			'msg' => '验证失败:验证码出错或已过期~'
		);
	}
	public static function sdkSmsSend($smsParam = [], $sdkOpt = [], $mobile = '', $code = '', $type = '')
	{
		$checkResult = self::getInstance()->isCheckMobile($mobile);
		if ($checkResult['status'] != 200) {
			return $checkResult;
		}
		$mobile = $checkResult['mobile'];

		// 实例对象
		$smsObj = new \JiGuang\JSMS($sdkOpt['sms_appkey'], $sdkOpt['sms_appsecret'], [
			'ssl_verify' => false
		]);
		$sendResult = [];
		$sendResult = $smsObj->sendCode($mobile, $sdkOpt['sms_tplid']);
		$sendResult['result_json_decode'] = json_decode($sendResult['body'], true, 10);

		if ($sendResult['http_code'] == 200) {
			self::getInstance()->addSmsLog($smsParam, $mobile);
			$msg_id = $sendResult['result_json_decode']['msg_id'];
			return array(
				'status' => 200,
				'error' => 'Not Found',
				'msg' => '发送成功~',
				'sms_msg_id' => $msg_id ?? '',
				'sms' => $sendResult['result_json_decode']
			);
		}
	}
	/*
	 * ["http_code"]=> int(200)
	 * ["result"]=> array(1) {
	 * ["msg_id"]=> string(36) "feab13ee-ab35-490f-8c61-189a4b0da344"
	 * }
	 */
	/*
	 *
	 * ["http_code"]=> int(400)
	 * ["result"]=> array(1) {
	 * ["error"]=> array(2) {
	 * ["code"]=> int(50004)
	 * ["message"]=> string(14) "missing mobile"
	 * }
	 * }
	 */

	/*
	 * array(2) {
	 * ["http_code"]=> int(400)
	 * ["result"]=> array(2) {
	 * ["is_valid"]=> bool(false)
	 * ["error"]=> array(2) {
	 * ["code"]=> int(50011)
	 * ["message"]=> string(12) "expired code"
	 * }
	 * }
	 * }
	 */
	// 极光返回的状态码
	private $jiguangStatus = array(
		'50000' => '请求成功',
		'50001' => 'auth为空',
		'50002' => 'auth鉴权失败',
		'50003' => 'body为空',
		'50004' => 'mobile为空',
		'50005' => 'temp_id为空',
		'50006' => 'mobile无效',
		'50007' => 'body无效',
		'50008' => '没有短信验证权限',
		'50009' => '发送超频',
		'50010' => '验证码无效',
		'50011' => '验证码过期',
		'50012' => '验证码已验证过',
		'50013' => '无效temp_id',
		'50014' => '余额不足',
		'50015' => '验证码为空',
		'50016' => 'api不存在',
		'50017' => '媒体类型不支持',
		'50018' => '请求方法不支持',
		'50019' => '服务端异常',
		'50020' => '模板审核中',
		'50021' => '模板审核不通过',
		'50022' => '模板参数未全部替换',
		'50023' => '参数为空'
	);
}