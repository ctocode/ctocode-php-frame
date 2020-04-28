<?php
require_once (__DIR__ . '/alipay-sdk-PHP-20171023143822/AopSdk.php');
class Alipay
{
	/**
	 * 应用ID
	 */
	const APPID = ALIPAY_APPID;
	/**
	 *请填写开发者私钥去头去尾去回车，一行字符串
	 */
	const RSA_PRIVATE_KEY = ALIPAY_PRIVATE_KEY;
	/**
	 *请填写支付宝公钥，一行字符串
	 */
	const ALIPAY_RSA_PUBLIC_KEY = ALIPAY_PUBLIC_KEY;
	/**
	 * 支付宝服务器主动通知商户服务器里指定的页面
	 * @var string
	 */
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
		try{
			$aop = new \AopClient ();
			$aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
			$aop->appId = self::APPID;
			$aop->rsaPrivateKey = self::RSA_PRIVATE_KEY;
			$aop->format = "json";
			$aop->charset = "UTF-8";
			$aop->signType = "RSA2";
			$aop->alipayrsaPublicKey = self::ALIPAY_RSA_PUBLIC_KEY;
			// 实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
			$request = new \AlipayTradeAppPayRequest ();
			// SDK已经封装掉了公共参数，这里只需要传入业务参数
			$bizcontent = "{\"body\":\"{$body}\"," . // 支付商品描述
			"\"subject\":\"{$subject}\"," . // 支付商品的标题
			"\"out_trade_no\":\"{$out_trade_no}\"," . // 商户网站唯一订单号
			"\"timeout_express\":\"{$timeout_express}m\"," . // 该笔订单允许的最晚付款时间，逾期将关闭交易
			"\"total_amount\":\"{$total_amount}\"," . // 订单总金额，单位为元，精确到小数点后两位，取值范围[0.01,100000000]
			"\"product_code\":\"QUICK_MSECURITY_PAY\"" . "}";
			$request->setNotifyUrl ( $this->callback );
			$request->setBizContent ( $bizcontent );
			// 这里和普通的接口调用不同，使用的是sdkExecute
			$response = $aop->sdkExecute ( $request );
			// htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
			return htmlspecialchars ( $response ); // 就是orderString 可以直接给客户端请求，无需再做处理。
		}
		catch ( \Exception $e ){
			return false;
		}
	}
}