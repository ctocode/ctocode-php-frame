<?php

namespace ctocode\sdks\pay;

require_once _CTOCODE_EXTEND_ . '/alipay-sdk-PHP-3.3.1/aop/AopClient.php';
require_once _CTOCODE_EXTEND_ . '/alipay-sdk-PHP-3.3.1/aop/request/AlipayTradeAppPayRequest.php';
/**
 * 支付宝支付
 * @author ctocode-Kinano
 * @version 2018/10/8 11:30
 */
class CtopaySdkAli
{

	/**
	 * @author: Kinano
	 * @param array $configData
	 * @param array $orderData
	 * @param string $payType
	 * @return string
	 */
	public static function paySend($configData = array(), $orderData = array(), $payType = 'pay')
	{
		$aop = new \AopClient ();
		$aop->gatewayUrl = $configData['pay_ali_gatewayUrl'];
		$aop->appId = $configData['ALIPAY_APPID'];
		$aop->rsaPrivateKey = trim ( $configData['ALIPAY_PRIVATE_KEY'] );
		$aop->format = "json";
		$aop->charset = "UTF-8";
		$aop->signType = "RSA2";
		$aop->alipayrsaPublicKey = trim ( $configData['ALIPAY_PUBLIC_KEY'] );
		// 支付操作
		if($payType == 'pay'){
			$request = new \AlipayTradeAppPayRequest ();
			$bizcontent = json_encode ( array(
				'body' => $orderData['body'],
				'subject' => $orderData['subject'],
				'out_trade_no' => $orderData['out_trade_no'],
				'timeout_express' => '30m',
				'total_amount' => $orderData['total_amount'],
				'product_code' => 'QUICK_MSECURITY_PAY'
			) );
			$request->setNotifyUrl ( $configData['pay_ali_return_url'] );
			$request->setBizContent ( $bizcontent );
			// 这里和普通的接口调用不同，使用的是sdkExecute
			$response = $aop->sdkExecute ( $request );
			// htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
			echo htmlspecialchars ( $response );
			exit (); // 就是orderString 可以直接给客户端请求，无需再做处理。

			return htmlspecialchars ( $response );
		}
	}
}