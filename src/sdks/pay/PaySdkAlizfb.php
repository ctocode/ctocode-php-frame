<?php

namespace app\api_sdks\extend;

use Alipay\EasySDK\Kernel\Factory;
use Alipay\EasySDK\Kernel\Util\ResponseChecker;
use Alipay\EasySDK\Kernel\Config;

/**
 * 支付宝支付
 * @author ctocode-Kinano
 * @version 2018/10/8 11:30
 */
/**
 * 
 * @version 2020-10-19
 * 手册 https://github.com/alipay/alipay-easysdk/tree/master/php
 * 官方：https://opendocs.alipay.com/open/00y8k9
 */
class PaySdkAlizfb
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

		//1. 设置参数（全局只需设置一次）
		Factory::setOptions(getOptions());

		try {
			//2. 发起API调用（以支付能力下的统一收单交易创建接口为例）
			$result = Factory::payment()->common()->create("iPhone6 16G", "20200326235526001", "88.88", "2088002656718920");
			$responseChecker = new ResponseChecker();
			//3. 处理响应或异常
			if ($responseChecker->success($result)) {
				echo "调用成功" . PHP_EOL;
			} else {
				echo "调用失败，原因：" . $result->msg . "，" . $result->subMsg . PHP_EOL;
			}
		} catch (Exception $e) {
			echo "调用失败，" . $e->getMessage() . PHP_EOL;;
		}
	}
	function getOptions()
	{
		$options = new Config();
		$options->protocol = 'https';
		$options->gatewayHost = 'openapi.alipay.com';
		$options->signType = 'RSA2';

		$options->appId = '<-- 请填写您的AppId，例如：2019022663440152 -->';

		// 为避免私钥随源码泄露，推荐从文件中读取私钥字符串而不是写入源码中
		$options->merchantPrivateKey = '<-- 请填写您的应用私钥，例如：MIIEvQIBADANB ... ... -->';

		$options->alipayCertPath = '<-- 请填写您的支付宝公钥证书文件路径，例如：/foo/alipayCertPublicKey_RSA2.crt -->';
		$options->alipayRootCertPath = '<-- 请填写您的支付宝根证书文件路径，例如：/foo/alipayRootCert.crt" -->';
		$options->merchantCertPath = '<-- 请填写您的应用公钥证书文件路径，例如：/foo/appCertPublicKey_2019051064521003.crt -->';

		//注：如果采用非证书模式，则无需赋值上面的三个证书路径，改为赋值如下的支付宝公钥字符串即可
		// $options->alipayPublicKey = '<-- 请填写您的支付宝公钥，例如：MIIBIjANBg... -->';

		//可设置异步通知接收服务地址（可选）
		$options->notifyUrl = "<-- 请填写您的支付类接口异步通知接收服务地址，例如：https://www.test.com/callback -->";

		//可设置AES密钥，调用AES加解密相关接口时需要（可选）
		$options->encryptKey = "<-- 请填写您的AES密钥，例如：aa4BtZ4tspm2wnXLb1ThQA== -->";



		return $options;
	}
	/**
	 * 旧版本支付
	 */
	private function oldPayxxxxx()
	{


		$aop = new \AopClient();
		$aop->gatewayUrl = $configData['pay_ali_gatewayUrl'];
		$aop->appId = $configData['ALIPAY_APPID'];
		$aop->rsaPrivateKey = trim($configData['ALIPAY_PRIVATE_KEY']);
		$aop->format = "json";
		$aop->charset = "UTF-8";
		$aop->signType = "RSA2";
		$aop->alipayrsaPublicKey = trim($configData['ALIPAY_PUBLIC_KEY']);
		// 支付操作
		if ($payType == 'pay') {
			$request = new \AlipayTradeAppPayRequest();
			$bizcontent = json_encode(array(
				'body' => $orderData['body'],
				'subject' => $orderData['subject'],
				'out_trade_no' => $orderData['out_trade_no'],
				'timeout_express' => '30m',
				'total_amount' => $orderData['total_amount'],
				'product_code' => 'QUICK_MSECURITY_PAY'
			));
			$request->setNotifyUrl($configData['pay_ali_return_url']);
			$request->setBizContent($bizcontent);
			// 这里和普通的接口调用不同，使用的是sdkExecute
			$response = $aop->sdkExecute($request);
			// htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
			echo htmlspecialchars($response);
			exit(); // 就是orderString 可以直接给客户端请求，无需再做处理。

			return htmlspecialchars($response);
		}
	}
	/**
	 * 
	 * 
	 * 
	 * 
	 * 
	 * 以下为yq
	 */

	// 应用ID
	const APPID = ALIPAY_APPID;
	// 请填写开发者私钥去头去尾去回车，一行字符串
	const RSA_PRIVATE_KEY = ALIPAY_PRIVATE_KEY;
	// 请填写支付宝公钥，一行字符串
	const ALIPAY_RSA_PUBLIC_KEY = ALIPAY_PUBLIC_KEY;
	// 支付宝服务器主动通知商户服务器里指定的页面
	private $callback = "";

	/**
	 *生成APP支付订单信息
	 * @param string $orderId 商品订单ID
	 * @param string $subject 支付商品的标题
	 * @param string $body 支付商品描述
	 * @param float $pre_price 商品总支付金额
	 * @param int $expire 支付交易时间
	 * @return bool|string 返回支付宝签名后订单信息，否则返回false
	 */
	public function unifiedorder($out_trade_no, $subject, $body, $total_amount, $timeout_express, $callback)
	{
		$this->callback = $callback;
		try {
			$aop = new \AopClient();
			$aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
			$aop->appId = self::APPID;
			$aop->rsaPrivateKey = self::RSA_PRIVATE_KEY;
			$aop->format = "json";
			$aop->charset = "UTF-8";
			$aop->signType = "RSA2";
			$aop->alipayrsaPublicKey = self::ALIPAY_RSA_PUBLIC_KEY;
			// 实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
			$request = new \AlipayTradeAppPayRequest();
			// SDK已经封装掉了公共参数，这里只需要传入业务参数
			$bizcontent = "{\"body\":\"{$body}\"," . // 支付商品描述
				"\"subject\":\"{$subject}\"," . // 支付商品的标题
				"\"out_trade_no\":\"{$out_trade_no}\"," . // 商户网站唯一订单号
				"\"timeout_express\":\"{$timeout_express}m\"," . // 该笔订单允许的最晚付款时间，逾期将关闭交易
				"\"total_amount\":\"{$total_amount}\"," . // 订单总金额，单位为元，精确到小数点后两位，取值范围[0.01,100000000]
				"\"product_code\":\"QUICK_MSECURITY_PAY\"" . "}";
			$request->setNotifyUrl($this->callback);
			$request->setBizContent($bizcontent);
			// 这里和普通的接口调用不同，使用的是sdkExecute
			$response = $aop->sdkExecute($request);
			// htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
			return htmlspecialchars($response); // 就是orderString 可以直接给客户端请求，无需再做处理。
		} catch (\Exception $e) {
			return false;
		}
	}
}
