<?php

namespace ctocode\sdks\sms;

/**
 * 阿里 - 大于 短信
 * @author ctocode-zhw
 * @version 2016-11-15
 */
class SmsSdkAlidayu extends SmsSdkCommon implements SmsSdkInterface
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
		// 验证 有效期内已经发送过
		$logModel = loadRpcModelClass('comsdks', 'SmsLog');
		$logData = $logModel->getListData(array(
			'page' => 1,
			'sms_mobile' => $mobile,
			'sms_code' => $code
		));
		if (!empty($logData['data'][0])) {
			$logRows = $logData['data'][0];
			$timeDiff = time() - $logRows['sms_sendtime'];
			if ($timeDiff < ($sdkOpt['sms_minute'] * 60)) {
				self::getInstance()->changSmsLog($logRows['sys_id']);
				return array(
					'status' => 200,
					'error' => 'Not Found',
					'sms' => 0,
					'msg' => '验证成功~'
				);
			}
		}
		return array(
			'status' => 404,
			'error' => 'Not Found',
			'msg' => '验证失败:验证码出错或已过期~'
		);
	}
	public static function sdkSmsSend($smsParam = [], $sdkOpt = [], $mobile = '', $code = '', $type = '')
	{
		// 处理手机号
		$checkResult = self::getInstance()->isCheckMobile($mobile);
		if ($checkResult['status'] != 200) {
			return $checkResult;
		}
		$mobile = $checkResult['mobile'];

		// 处理配置

		include_once _CTOCODE_EXTEND_ . '/aliyun-dysms-php-sdk-lite/sendSms.php';
		$code = '';
		if ($type != 'mass') {
			$code = ctoStrRandNum(6);
		}
		$sendResult = aliyunsendSms(array(
			'sms_appsign' => $sdkOpt['sms_appsign'],
			'sms_appkey' => $sdkOpt['sms_appkey'],
			'sms_appsecret' => $sdkOpt['sms_appsecret'],
			'sms_tplid' => $sdkOpt['sms_tplid']
		), $mobile, $code);
		$sendResult2 = (array) $sendResult;
		if ($sendResult2['Code'] == 'OK') {
			$sign =  ctoStrRandCode(5);
			self::getInstance()->addSmsLog($smsParam, $mobile, $code, $sign);
			$msg = $type == 'mass' ? '短信群发成功' : '短信验证码已发送';
			return array(
				'status' => 200,
				'error' => 'Not Found',
				'msg' => $msg,
				'data' => ['sign' => $sign]
			);
		}
	}
}
