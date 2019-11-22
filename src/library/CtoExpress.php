<?php

namespace ctocode\phpframe\library;

/**
 * 物流配送相关类
 * 
 * @author: ctocode-zhw
 * @version: 1.0.0
 * @date: 2018/12/01
 */
class CtoExpress
{
	public function getCompanysData()
	{
		$companysData = include __DIR__ . '/express_company.php';
		return $companysData;
	}
	public function getExpressInfo($searData = array())
	{
		// 测试物流公司编号：ems,测试单号：1179919218362
		// $order['shipping_expresscom'] = 'ems';
		// $order['shipping_expressnum'] = '1179919218362';
		$url = "http://www.kuaidi100.com/query?type={$searData['company']}&postid={$order['shipping_expressnum']}";
		$result = $this->httpGet ( $url );
		if(isset ( $result['message'] ) && $result['message'] == 'ok'){
			$deliverInfo = $result;
		}else{
			$deliverInfo = null;
		}
		// dd($deliverInfo);
		return $deliverInfo;
	}
	protected function httpGet($url)
	{
		$curl = curl_init ();
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $curl, CURLOPT_TIMEOUT, 500 );
		curl_setopt ( $curl, CURLOPT_URL, $url );
		$result = curl_exec ( $curl );
		curl_close ( $curl );

		return json_decode ( $result, 1 );
	}
}