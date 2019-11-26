<?php

/**
 * 【ctocode】      常用函数 - http相关处理
 * ============================================================================
 * @author       作者         ctocode-zhw
 * @version 	  版本	  v1.0.0.0.20170720
 * @copyright    版权所有   2015-2027，并保留所有权利。
 * @link         网站地址   http://www.ctocode.com
 * @contact      联系方式   QQ:343196936
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 */
/**
 * 发送post请求
 * @param string $url 请求地址
 * @param array $post_data post键值对数据
 * @return string
 */
function ctoHttpSendPost($url, $post_data)
{
	$postdata = http_build_query ( $post_data );
	$options = array(
		'http' => array(
			'method' => 'POST',
			'header' => 'Content-type:application/x-www-form-urlencoded',
			'content' => $postdata,
			'timeout' => 15 * 60
		)
	); // 超时时间（单位:s）

	$context = stream_context_create ( $options );
	$result = file_get_contents ( $url, false, $context );
	return $result;

	// $post_data = array(
	// 'username' => 'username',
	// 'password' => 'password'
	// );
	// send_post('http://api.cto.com/Copyright/inspect?demo=20170414', $post_data);
}
/**
 * POST方式得用下面的(需要开启PHP curl支持)。
 * @author ctocode-zhw
 * @version 2017-04-28
 */
function ctoHttpCurl($url = '', $data = null, $header = null)
{
	if(empty ( $url )){
		return '';
	}
	// 初始化curl模块,启动一个CURL会话
	$curl = curl_init ();
	// 请求的url地址
	curl_setopt ( $curl, CURLOPT_URL, $url );
	// 是否开启 显示返回的header头信息区域内容
	curl_setopt ( $curl, CURLOPT_HEADER, 0 );
	// 设置传递的头部信息
	$header_arr = array(
		'Content-Type: application/json; charset=utf-8'
	);
	// curl_setopt ( $curl, CURLOPT_HTTPHEADER,$header_arr );
	/*
	 * 是否对 认证证书来源的检查验证
	 * 0或者false | 1或者true
	 */
	curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, FALSE );
	/*
	 * 是否从 证书中检查SSL加密算法是否存在
	 * 0或者false | 1或者true
	 */
	curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, FALSE );
	if(! empty ( $data )){ // post方式提交.
		curl_setopt ( $curl, CURLOPT_POST, 1 );
		// post提交的数据包 ,http_build_query 传递多维数组
		curl_setopt ( $curl, CURLOPT_POSTFIELDS, http_build_query ( $data ) );
		// curl_setopt ( $curl, CURLOPT_CUSTOMREQUEST, "POST" );
	}
	/*
	 * 是否自动显示返回的信息
	 * 1表示将结果保存到字符串中
	 * 0还是输出到屏幕上
	 * 获取的信息以文件流的形式返回
	 */
	curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
	// 执行最大秒数
	// curl_setopt ( $curl, CURLOPT_TIMEOUT, 15 );
	// 设置超时限制防止死循环
	$timeout = 5;
	// curl_setopt ( $curl, CURLOPT_CONNECTTIMEOUT, $timeout );

	// 模拟用户使用的浏览器
	// curl_setopt ( $curl, CURLOPT_USERAGENT, "curl/7.12.1" );
	// curl_setopt ( $curl, CURLOPT_REFERER, "" );

	// 读取cookie
	// curl_setopt ( $curl, CURLOPT_COOKIE, $cookie_file );
	// 设置Cookie信息保存在指定的文件中
	// curl_setopt ( $curl, CURLOPT_COOKIEJAR, $cookie_file );
	// curl_setopt ( $curl, CURLOPT_COOKIEFILE, $cookie_file );
	// @ unlink ( $cookie );
	// 在需要用户检测的网页里需要增加下面两行
	// curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	// curl_setopt($curl, CURLOPT_USERPWD, US_NAME.":".US_PWD);

	// 执行cURL
	$output = curl_exec ( $curl );
	$is_errno = curl_errno ( $curl );
	if($is_errno){ // 捕抓异常
		return 'Errno' . $is_errno;
	}
	// 关闭CURL资源会话，并且释放系统资源
	curl_close ( $curl );
	// 返回数据
	return $output;

	// 使用
	// $cookie_file = tempnam ( './data', 'cookie' );

	// $curl_url = 'http://api.cto.com/';
	// $curl_post_data = array(
	// 'type' => $xxx_id,
	// 'staff_id' => $user_id,
	// 'staff_avatar' => $staff_avatar,
	// 'prevurl' => $_SERVER['HTTP_REFERER'],
	// 'token' => 'xsasadqwas123'
	// );
	// $result_json = ctoHttpCurl ( $curl_url, $curl_post_data );
	// $result_arr = json_decode ( $result_json, true );
}

/**
 * @action 判断是否是https
 * @author ctocode-zwj
 * @version 2018-06-15
 */
function ctoIsHttps()
{
	if(! isset ( $_SERVER['HTTPS'] ))
		return FALSE;
	if($_SERVER['HTTPS'] === 1){ // Apache
		return TRUE;
	}elseif($_SERVER['HTTPS'] === 'on'){ // IIS
		return TRUE;
	}elseif($_SERVER['SERVER_PORT'] == 443){ // 其他
		return TRUE;
	}
	return FALSE;
}

/**
 * @action http转换为https  
 * @author ctocode-zwj
 * @version 2018-06-15
 */
function ctoHttpTrans($value = '')
{
	if(ctoIsHttps () && preg_match ( '/(http:\/\/)/i', $value )){
		return str_replace ( "http://", "https://", $value );
	}else{
		return $value;
	}
}

/**
 * POST方式得用下面的(需要开启PHP curl支持)。异步调用
 * @author ctocode-zwj
 * @version 2018-10-27
 */
function ctoHttpCurlAsync($nodes = '', $data = null, $json = false)
{
	// $nodes = array('http://hrhg.10.com/index/index1', 'http://hrhg.10.com/index/index2');
	$node_count = count ( $nodes );

	$curl_arr = array();
	$master = curl_multi_init (); // 1 创建批处理cURL句柄

	for($i = 0;$i < $node_count;$i ++){
		$url = $nodes[$i];
		$curl_arr[$i] = curl_init ( $url );
		if(! empty ( $data )){ // post方式提交.
			curl_setopt ( $curl_arr[$i], CURLOPT_POST, 1 );
			// post提交的数据包 ,http_build_query 传递多维数组
			if($json == true){
				$header_arr = array(
					'Content-Type: application/json; charset=utf-8'
				);
				curl_setopt ( $curl_arr[$i], CURLOPT_HTTPHEADER, $header_arr );
				curl_setopt ( $curl_arr[$i], CURLOPT_POSTFIELDS, $data ); // $data数据需要json_encode传过来
			}else{
				curl_setopt ( $curl_arr[$i], CURLOPT_POSTFIELDS, http_build_query ( $data ) );
			}
		}
		/*
		 * 是否对 认证证书来源的检查验证
		 * 0或者false | 1或者true
		 */
		curl_setopt ( $curl_arr[$i], CURLOPT_SSL_VERIFYPEER, FALSE );
		/*
		 * 是否从 证书中检查SSL加密算法是否存在
		 * 0或者false | 1或者true
		 */
		curl_setopt ( $curl_arr[$i], CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt ( $curl_arr[$i], CURLOPT_RETURNTRANSFER, 1 ); // return don't print
		curl_setopt ( $curl_arr[$i], CURLOPT_CONNECTTIMEOUT, 1 ); // 建立连接等待时间
		                                                          // curl_setopt($curl_arr[$i], CURLOPT_TIMEOUT_MS, 10);//响应超时时间

		curl_multi_add_handle ( $master, $curl_arr[$i] ); // 2 增加句柄
	}
	do{
		curl_multi_exec ( $master, $running ); // 3 执行批处理句柄
	}while($running > 0); // 4

	// echo "results: ";
	/*
	 * $data = array();
	 * for($i = 0; $i < $node_count; $i++) {
	 * $results = curl_multi_getcontent ( $curl_arr[$i] ); //5 获取句柄中的返回值
	 * $data[$i] = (curl_errno($curl_arr[$i]) == 0) ? $results : false;
	 * curl_multi_remove_handle($master, $curl_arr[$i]);//6 将$master中的句柄移除
	 * //echo( $i . "\n" . $results . "\n");
	 * }
	 */
	curl_multi_close ( $master );
	// return $data;
}

/**
 * @action 获取url内容的加速处理方法
 * @author: Kinano
 * @param string $durl url地址
 * @return string
 */
function ctoHttpCurl_file_get_contents($durl)
{
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_URL, $durl );
	curl_setopt ( $ch, CURLOPT_TIMEOUT, 2 );
	curl_setopt ( $ch, CURLOPT_USERAGENT, 10018 );
	curl_setopt ( $ch, CURLOPT_REFERER, 10016 );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	$r = curl_exec ( $ch );
	curl_close ( $ch );
	return $r;
}

/**
 * url 重定向
 * @author ctocode-zhw
 * @version
 */
function ctoUrlRedirect($uri = '', $type = 'location', $http_response_code = 302)
{
	// if ( ! preg_match ( '#^https?://#i', $uri )) {
	// $uri = site_url ( $uri );
	// }
	switch($type)
	{
		case 'html':
			echo '<meta http-equiv="refresh" content="5; url=index.com">该页面不允许单独访问,5秒后跳转<br>';
			break;
		case 'js':
			echo "<script>" . "function redirect() {window.location.replace('$uri');}\n" . "setTimeout('redirect();', 0);\n" . "</script>";
			break;
		case 'refresh':
			header ( "Refresh:0;url=" . $uri );
			break;
		case '301':
			header ( 'HTTP/1.1 301 Moved Permanently' );
			header ( 'Location: ' . $uri );
			break;
		case '404':
			@header ( 'http/1.1 404 not found' );
			@header ( 'status: 404 not found' );
			break;
		case 'location':
			$replace = true;
			$http_response_code = 0;
			$string = str_replace ( array(
				"\r",
				"\n"
			), array(
				'',
				''
			), $uri );
			if(empty ( $http_response_code ) || PHP_VERSION < '4.3'){
				@header ( $string, $replace );
			}else{
				@header ( $string, $replace, $http_response_code );
			}
			if(preg_match ( '/^\s*location:/is', $string )){
				exit ();
			}
			break;
		default:
			header ( "Location: " . $uri, TRUE, $http_response_code );
			break;
	}
	exit ();
}
function ctoUrlBase()
{
	// // 获取域名或主机地址 localhost\192.168.0.1、127.0.0.1
	// echo $_SERVER['HTTP_HOST'] . "<br>"; //

	// // 获取网页地址 /blog/testurl.php
	// echo $_SERVER['PHP_SELF'] . "<br>"; //

	// // 获取网址参数 id=5
	// echo $_SERVER["QUERY_STRING"] . "<br>"; //

	// // 获取用户代理
	// echo $_SERVER['HTTP_REFERER'] . "<br>";

	// // 获取完整的url
	// echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "<br>";
	// echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] . "<br>";
	// // http://localhost/blog/testurl.php?id=5
	// // 包含端口号的完整url
	// echo 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"] . "<br>";
	// // http://localhost:80/blog/testurl.php?id=5
	// // 只取路径
	// $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"];
	// echo dirname ( $url );

	// $server_protocol = isset ( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
	// echo "<br>" . $server_protocol . "<br>";
	if(isset ( $_SERVER['HTTP_HOST'] ) && preg_match ( '/^((\[[0-9a-f:]+\])|(\d{1,3}(\.\d{1,3}){3})|[a-z0-9\-\.]+)(:\d+)?$/i', $_SERVER['HTTP_HOST'] )){
		$base_url = (ctoUrlIsHttps () ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . substr ( $_SERVER['SCRIPT_NAME'], 0, strpos ( $_SERVER['SCRIPT_NAME'], basename ( $_SERVER['SCRIPT_FILENAME'] ) ) );
		$base_url2 = substr ( $_SERVER['SCRIPT_NAME'], 0, strpos ( $_SERVER['SCRIPT_NAME'], basename ( $_SERVER['SCRIPT_FILENAME'] ) ) );
	}else{
		$base_url = 'http://localhost/';
	}
	return $base_url;
}
function ctoUrlIsHttps()
{
	if(! empty ( $_SERVER['HTTPS'] ) && strtolower ( $_SERVER['HTTPS'] ) !== 'off'){
		return true;
	}elseif(isset ( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && strtolower ( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) === 'https'){
		return true;
	}elseif(! empty ( $_SERVER['HTTP_FRONT_END_HTTPS'] ) && strtolower ( $_SERVER['HTTP_FRONT_END_HTTPS'] ) !== 'off'){
		return true;
	}
	return false;
}
/**
 * 获取域名，验证域名
 * @author ctocode-zhw
 * @param string $domain_host 需要获取二级域名的 域名，置空的话，获取当前域名
 * @param string $second_name 需要判断是否相同的二级域名名称
 * @return mixed
 */
function ctoUrlDomainSecond($domain_host = '', $second_name = '')
{
	$http_host = ! empty ( $domain_host ) ? $domain_host : $_SERVER['HTTP_HOST'];
	$matches = [];
	preg_match ( '/(.*\.)?\w+\.\w+$/', $http_host, $matches );
	$match = [];
	preg_match ( "#(.*?)\.#i", $http_host, $match );
	return array(
		'domain' => $matches[0],
		'erji' => ! empty ( $match[1] ) ? $match[1] : ''
	);
}
