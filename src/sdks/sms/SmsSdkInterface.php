<?php

namespace ctocode\sdks\sms;

/**
 * 第三方短信封装sdk
 * 接口声明
 */
interface SmsSdkInterface
{
	/**
	 * 短信验证
	 * @param array $check  验证参数
	 * @param array $sdkOpt  短信配置
	 * @param string $mobile 手机号
	 * @param string $code   短信码
	 */
	public static function sdkSmsCheck($smsParam = [], $sdkOpt = [], $mobile = '', $code = '');
	/**
	 * 短信发送
	 * @param array $sendData  发送参数
	 * @param array $sdkOpt  短信配置
	 * @param string $mobile 手机号
	 * @param string $type   是否群发类型，mass为群发，置空单发
	 */
	public static function sdkSmsSend($smsParam = [], $sdkOpt = [], $mobile = '', $code = '', $type = '');
}