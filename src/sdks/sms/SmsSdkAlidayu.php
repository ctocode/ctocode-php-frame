<?php

namespace ctocode\sdks\sms;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
// 处理配置
// Download：https://github.com/aliyun/openapi-sdk-php
// Usage：https://github.com/aliyun/openapi-sdk-php/blob/master/README.md
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

		$code = '';
		if ($type != 'mass') {
			$code = ctoStrRandNum(6);
		}
		$TemplateParam = array(
			"code" => $code
			// "product" => "阿里通信" ,新版本模板不支持中文
		);
		if (!empty($TemplateParam) && is_array($TemplateParam)) {
			$TemplateParam = json_encode($TemplateParam, JSON_UNESCAPED_UNICODE);
		}

		AlibabaCloud::accessKeyClient($sdkOpt['sms_appkey'], $sdkOpt['sms_appsecret'])
			->regionId('cn-hangzhou')
			->asDefaultClient();

		try {
			$result = AlibabaCloud::rpc()
				->product('Dysmsapi')
				// ->scheme('https') // https | http
				->version('2017-05-25')
				->action('SendSms')
				->method('POST')
				->host('dysmsapi.aliyuncs.com')
				->options([
					'query' => [
						'RegionId' => "cn-hangzhou",
						'PhoneNumbers' => $mobile,
						'SignName' => $sdkOpt['sms_appsign'],
						'TemplateCode' => $sdkOpt['sms_tplid'],
						'TemplateParam' => $TemplateParam
					],
				])
				->request();
			// $sendResult2 = (array) $sendResult;
			$sendResult2 = $result->toArray();
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
		} catch (ClientException $e) {
			echo $e->getErrorMessage() . PHP_EOL;
		} catch (ServerException $e) {
			echo $e->getErrorMessage() . PHP_EOL;
		}
	}
}
