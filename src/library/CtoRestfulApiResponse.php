<?php

namespace ctocode\phpframe\library;

/** 
 * @class RESTful 响应输出类 ApiResponse   
 * @author ctocode-zhw
 * @version 2017-09-05
 * @remarks 
 * 根据接收到的Content-Type，将Request类返回的数组拼接成对应的格式，加上header后输出
 */
class CtoRestfulApiResponse extends CtoRestfulApiBase
{
	protected $httpVersion = "HTTP/1.1";
	// json格式
	protected function encodeJson($responseData = array())
	{
		return json_encode ( $responseData, true );
	}
	// xml格式
	protected function encodeXml($responseData = array())
	{ // 创建 SimpleXMLElement 对象
		/* '<?xml version="1.0"?><site></site>' */
		$xml = new SimpleXMLElement ( '<?xml version="1.0"?><rest></rest>' );
		foreach($responseData as $key=>$value){
			if(is_array ( $value )){
				foreach($value as $k=>$v){
					$xml->addChild ( $k, $v );
				}
			}else{
				$xml->addChild ( $key, $value );
			}
		}
		return $xml->asXML ();
	}
	// html格式
	protected function encodeHtml($responseData = array())
	{
		$html = "<table border='1'>";
		foreach($responseData as $key=>$value){
			$html .= "<tr>";
			if(is_array ( $value )){
				foreach($value as $k=>$v){
					$html .= "<td>" . $k . "</td><td>" . $v . "</td>";
				}
			}else{
				$html .= "<td>" . $key . "</td><td>" . $value . "</td>";
			}
			$html .= "</tr>";
		}
		$html .= "</table>";
		return $html;
	}

	// 文档风格
	protected function docApiStyle()
	{
		$style = '';
		$style .= '<style>';
		$style .= 'body{margin: 20px;font: 13px Helvetica Neue,Helvetica,PingFang SC,\5FAE\8F6F\96C5\9ED1,Tahoma,Arial,sans-serif;font-family: "Microsoft YaHei" ! important;}';
		$style .= 'p{margin:0;padding:0;margin-bottom:2px;letter-spacing:1px;} ';
		$htmls .= '</style>';
		return $style;
	}
}