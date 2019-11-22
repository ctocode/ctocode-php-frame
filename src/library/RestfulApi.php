<?php

/**
 * restful - api 接口风格基类
 * @author ctocode-zhw
 * @link
 */

/**
 * REST的全称是REpresentational State Transfer，表示表述性无状态传输，无需session，所以每次请求都得带上身份认证信息。
 * rest是基于http协议的，也是无状态的。只是一种架构方式，所以它的安全特性都需我们自己实现，没有现成的。
 * 建议所有的请求都通过https协议发送。REST ful web services 概念的核心就是“资源”。
 * 资源可以用 URI 来表示。
 * 客户端使用 HTTP 协议定义的方法来发送请求到这些 URIs，当然可能会导致这些被访问的”资源“状态的改变。HTTP请求对应关系如下：
 *
 */

// ========== ===================== ========================
// HTTP 方法 行为 示例
// ========== ===================== ========================

// GET 用来获取资源， http://xx.com/api/orders
// GET 获取某个特定资源的信息 http://xx.com/api/orders/123
// POST 用来新建资源（也可以用于更新资源）， http://xx.com/api/orders
// PUT 用来更新资源，http://xx.com/api/orders/123
// DELETE 用来删除资源。 http://xx.com/api/orders/123
//
// API的身份认证应该使用OAuth 2.0框架
// 服务器返回的数据格式，应该尽量使用JSON，避免使用XML
// 对于请求的数据一般用json或者xml形式来表示，推荐使用json。
class CTOCODE_RestfulApi extends \CTOCODE_RestfulApiResponse
{
	// 返回结果
	public function sendResponse($result_data = null, $send_type = 'json')
	{
		// $statusCode = $result_data['status'];
		// $data = $result_data['status'];
		// $statusMessage = $this->getHttpStatusMessage ( $statusCode );
		// 输出结果
		// header ( $this->httpVersion . " " . $statusCode . " " . $statusMessage );
		$requestContentType = isset ( $_SERVER['CONTENT_TYPE'] ) ? $_SERVER['CONTENT_TYPE'] : $_SERVER['HTTP_ACCEPT'];
		// TODO 目前强制为 json 返回
		$requestContentType = 'application/json';

		if(strpos ( $requestContentType, 'application/json' ) !== false){
			header ( 'Content-Type: application/json; charset=utf-8' );
			echo $this->encodeJson ( $result_data );
			exit ();
		}else if(strpos ( $requestContentType, 'application/' ) !== false){
			header ( "Content-Type: application/xml" );
			echo $this->encodeXml ( $data );
			exit ();
		}else{
			// header ( 'Content-type: text/html; charset=utf-8' );
			header ( "Content-Type: application/html" );
			echo $this->encodeHtml ( $data );
			exit ();
		}
	}
	/**
	 * 防止跨域,解决处理跨域问题
	 * @param array $MethodSett 允许访问的方式，默认get，post
	 * @param array $HeadersSett 允许传递的 header 参数
	 */
	public function doCrossDomain($AllowHeaders = array(), $RequestHeaders = array(), $Method = array(), $CacheOpt = array())
	{
		$this->doAllowHeaders ( $AllowHeaders );
		$this->doAllowMethods ( $Method );
		$this->doRequestHeaders ( $RequestHeaders );
		/* ========== 清空缓存 ========== */
		header ( "Cache-Control:no-cache" );
		// header ( 'Cache-Control: max-age=0' );
		header ( 'X-Accel-Buffering:no' ); // 关闭输出缓存
		header ( 'Pragma:no-cache' );
	}
	/**
	 * 准许跨域请求来源访问
	 * @param array $diyOpt
	 */
	public function doAllowOrigin($diyOpt = array())
	{
		$origin = isset ( $_SERVER['HTTP_ORIGIN'] ) ? $_SERVER['HTTP_ORIGIN'] : ''; // 跨域访问的时候才会存在此字段
		if(in_array ( $origin, $diyOpt )){
			header ( 'Access-Control-Allow-Origin:' . $origin );
		}else{
			header ( 'Access-Control-Allow-Origin: * ' );
		}
		header ( 'Access-Control-Allow-Credentials: true' );
	}
	/**
	 * 允许传递的 请求 参数
	 * @param array $diyOpt
	 */
	public function doRequestHeaders($diyOpt = array())
	{
		$allOpt = array_merge ( array(
			'Origin',
			'X-Requested-With',
			'Content-Type',
			'Accept'
		), $diyOpt );
		header ( 'Access-Control-Request-Headers:' . implode ( ',', $allOpt ) );
	}
	/**
	 * 允许传递的 header 参数
	 * @param array $diyOpt
	 */
	public function doAllowHeaders($diyOpt = array())
	{
		$allOpt = array_merge ( array(
			'Origin',
			'X-Requested-With',
			'Content-Type',
			'Accept',
			'Authorization'
		), $diyOpt );
		header ( 'Access-Control-Allow-Headers:' . implode ( ',', $allOpt ) );
	}
	/**
	 * 允许访问的方式
	 * @param array $diyOpt
	 */
	public function doAllowMethods($diyOpt = array())
	{
		$allOpt = array_merge ( array(
			'GET',
			'POST'
			// 'PUT',
			// 'DELETE',
			// 'OPTIONS'
		), $diyOpt );
		header ( 'Access-Control-Allow-Methods:' . implode ( ',', $allOpt ) );
	}
	public function doc($docApiBaseUrl = '', $docApiData = '')
	{
		$version = 'v1';
		$htmls = '';
		$htmls .= $this->docApiStyle ();

		$htmls .= "<p>接口：<b>{$docApiData['name']}</b></p>";
		$htmls .= "<p>URL：http://{$docApiBaseUrl}/{$docApiData['mod']}/{$docApiData['con']}</p>";
		$htmls .= '可选参数：';
		foreach($docApiData['api'] as $key=>$val){
			$htmls .= "<p>&nbsp;&nbsp;{$key}=（{$val['type']}） {$val['comment']}</p>";
		}
		$htmls .= '<p>&nbsp;</p>';
		return $htmls;
	}
	public function getHypermedia($baseUrl, $apiMenu)
	{
		$version = 'v1';
		$htmls = '';
		$htmls .= $this->docApiStyle ();

		foreach($apiMenu as $key=>$val){
			foreach($val['modules'] as $key2=>$val2){
				foreach($val2['controllers'] as $kye3=>$val3){
					if(empty ( $val3['api'] )){
						continue;
					}
					$api_menu = $val3['api'];
					$htmls .= '<p>接口：<b>' . "{$val['title']}_{$val2['name']}_{$val3['name']}" . '</b></p>';
					$htmls .= '<p>URL：' . "{$baseUrl}{$version}/{$val['apps']}/{$val2['mod']}/{$val3['con']}{$api_menu['link']}" . '</p>';
					if(! empty ( $api_menu['param'] )){
						$htmls .= "参数：";
						foreach($api_menu['param'] as $k=>$v){
							$htmls .= "<p>&nbsp;&nbsp;{$k}=({$v['type']})  {$v['remarks']}</p>";
						}
					}
					$htmls .= '<p>&nbsp;</p>';
				}
			}
		}
		return $htmls;
	}
	private function getDocLink($show_type = '')
	{
		$api_doc = $this->getHypermedia ();
		if($show_type == 'html'){
			$htmls = '';
			$htmls .= '<pre style="word-wrap: break-word; white-space: pre-wrap;">';
			$htmls .= '<p style="font-size: 16px;padding: 0;margin: 0;">提示1：可以点击url，查看更多详细api接口</p>';
			$htmls .= '<p style="font-size: 16px;padding: 0;margin: 0;">提示2：数据只返回status 和 data</p>';
			$htmls .= '<p>{</p>';
			foreach($api_doc as $key=>$val){
				$htmls .= '<p>';
				$htmls .= '   "' . $val['sign'] . '" : ';
				$htmls .= '"<a href="' . $val['link'] . '" target="_blank">';
				$htmls .= $val['link'] . '</a>",';
				$htmls .= ' /* 【' . $val['title'] . '】_' . $val['remarks'] . '*/';
				$htmls .= '</p>';
			}
			$htmls .= '}</pre>';
			exit ( $htmls );
		}else{
			header ( 'Content-Type:application/json; charset=utf-8' );
			exit ( json_encode ( $api_doc ) );
		}
	}
}