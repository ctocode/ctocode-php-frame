<?php

namespace ctocode\sdks\sms;

use think\facade\Db;

class SmsSdkCommon
{
	protected static $_instance = null;

	/**
	 * 获取当前的实例（单例）
	 * @access public
	 * @return static
	 */
	public static function getInstance($AutoCreate = false)
	{
		if($AutoCreate === true && ! self::$_instance){
			self::init ();
		}
		if(self::$_instance == null){
			self::init ();
		}
		return self::$_instance;
	}
	// 初始化当前实例
	public static function init()
	{
		return self::$_instance = new self ();
	}
	// 修改短信记录
	protected function changSmsLog($sys_id = 0)
	{
		$field = array();
		$field['sms_is_read'] = 1;
		Db::table ( 'addons_ctomsg_sms_log' )->where ( 'sys_id', $sys_id )->update ( $field );
	}
	// 记录短信记录
	protected function addSmsLog($requestData, $mobile = '', $code = '')
	{
		$field = array();
		$field['sms_type'] = $requestData['sms_send_type'];
		$field['sms_supplier'] = 0;
		$field['sms_from_ip'] = $requestData['log_ip'];
		$field['sms_from_client'] = $requestData['log_source'];
		$field['sms_sendtime'] = $requestData['log_time'] ?? time ();
		$field['business_id'] = $requestData['business_id'] ?? 0;
		$field['sms_mobile'] = $mobile;
		$field['sms_code'] = $code;
		$sql_result = Db::table ( 'addons_ctomsg_sms_log' )->save ( $field );
		if($sql_result === FALSE){
			// TODO 写入错误日志
		}
	}
	/**
	 * 手机号常规验证
	 * @version 2017-01-12
	 */
	protected function isCheckMobile($mobile = '')
	{
		if(empty ( $mobile )){
			return array(
				'status' => 404,
				'msg' => '手机不能为空',
				'data' => ''
			);
		}
		$mobile = trim ( $mobile );
		/**
		 * 手机号过滤，是否需要解码
		 */
		// $mobile = $this->isDecodeMobile ( $mobile );
		if(true !== ctoCheck ( 'mobile', $mobile )){
			return array(
				'status' => 404,
				'msg' => '手机号码格式错误'
			);
		}
		/**
		 * 是否风险检测
		 */
		// $safResult = $this->isCheckSaf() ;
		// if($safResult['status'] != 200){
		// return $safResult;
		// }
		/**
		 * 验证短信黑名单
		 */
		//
		$blackModel = loadRpcModelClass ( 'ctomsg', 'SmsBlack' );
		$blackData = $blackModel->getListData ( array(
			'black_flag' => $mobile
		) );
		if($blackData['total'] > 0){
			return array(
				'status' => 404,
				'msg' => '手机号black'
			);
		}
		return array(
			'status' => 200,
			'mobile' => $mobile
		);
	}

	/**
	 * 手机号解码
	 * @version 2018-05-05
	 */
	protected function isDecodeMobile($mobile = '')
	{
		$aesSett = array(
			'keyString' => 'ctocode20180505',
			'ivString' => 'ctocode6666'
		);
		$aes = new \ctocode\phpframe\library\CtoAes ( $aesSett );
		// base64_decode获取手机号
		return $aes->decrypt ( $mobile, 'base64' );
	}

	/**
	 * 手机号安全检测
	 * @version 2018-05-20
	 */
	protected static function isCheckSaf($smsSendParam = [])
	{
		/**
		 * 内部系统检测
		 * - 判断使用的设备标识
		 * - 是否在黑名单中
		 * - 是否被禁用
		 */
		$ucenterBlack = loadRpcModelClass ( 'comucenter', 'Black' );
		$isBlackCode = $ucenterBlack->getRowData ( [
			'ucenter_code' => $smsSendParam['log_code']
		] )['data'];
		if(! empty ( $isBlackCode )){
			// 存在于黑名单
			return array(
				'status' => 405,
				'msg' => '您违规操作已被禁用，请联系平台'
			);
		}

		/**
		 * 外部接口检测  - 阿里云saf
		 * - 注册风险识别
		 * - 短信风险识别，先获取短信风险值，然后与数据库的对比
		 */
		//
		$cmsSettingRpcModelObj = loadRpcModelClass ( 'adminbase', 'PlatSetting' );
		$safResult = $cmsSettingRpcModelObj->getRowData ( [
			'setts_key' => 'saf_register_open'
		] )['data'];
		if(! empty ( $safResult[0]['setts_value'] ) && $safResult[0]['setts_value'] == 'true'){
			$apiResultSmsData = ctoHttpCurl ( _URL_API_ . "saf/opt", array(
				'TokenType' => _TOOL_TOKEN_TYPES_,
				'saf_id' => \think\facade\Config::get ( 'ctocode._TOOL_SAF_SETT_ID_' ),
				'ip' => ctoIpGet (),
				'log_code' => $smsSendParam['log_code'],
				'log_type' => $smsSendParam['sms_send_type'],
				'log_souce' => $smsSendParam['sms_send_source'],
				'deviceType' => $smsSendParam['sms_device_type'],
				'deviceToken' => $smsSendParam['sms_device_token'],
				'mobile' => $smsSendParam['sms_send_mobile']
			) );
			$apiResultData = json_decode ( $apiResultSmsData, true );

			$settingScore = $cmsSettingRpcModelObj->getRowData ( [
				'setts_key' => 'saf_register_score'
			] )['data'];

			if($apiResultData['data']['Score'] > $settingScore[0]['setts_value']){
				return array(
					'status' => 405,
					'msg' => '该手机号存在风险，不予发送短信'
				);
			}
		}
		return array(
			'status' => 200
		);
	}
}