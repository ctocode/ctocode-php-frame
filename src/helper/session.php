<?php

/**
 * 【ctocode】      常用函数 - session相关处理
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
 * @action 清楚session缓存
 * @author zhw
 * @version 20170129
 * @param  int $timeLong 时间戳
 * @return boolean
 */
function ctoSessionDel($timeLong = 86400)
{
	/* :::::::::::::::::::::::::::::: */
	/* 删除24小时 session 时间差（秒）：86400 */
	/* 删除12小时 session 时间差（秒）：43200 */
	$data_public_session_lock = _CTOCODE_CONFIG_ . 'public.session.lock';
	$current_time = time ();
	if(! file_exists ( $data_public_session_lock )){ /* 未安装 */
		$data_public_session_file_time = 0;
	}else{
		$data_public_session_file_time = filemtime ( $data_public_session_lock );
	}
	$chajutime = ($current_time - $data_public_session_file_time);
	if($chajutime > $timeLong){ /* 删除public——session */
		$data_public_dir = _CTOCODE_RUNTIME_ . "public/";
		// Open a known directory, and proceed to read its contents
		if(is_dir ( $data_public_dir )){
			if($dh = opendir ( $data_public_dir )){
				while(($data_public_file_name = readdir ( $dh )) !== false){
					if($data_public_file_name == "." || $data_public_file_name == ".."){
						continue;
					}
					// $is_user_session = strpos ( $data_public_file_name, 'user_' );
					$is_user_session = TRUE;
					if($is_user_session === FALSE){
						continue;
					}else{
						$data_public_file_path = $data_public_dir . $data_public_file_name;
						unlink ( $data_public_file_path );
					}
				}
				closedir ( $dh );
				fopen ( $data_public_session_lock, 'w' );
			}
		}
	}
}
function xxxxxxxxxxxxx()
{
	// // =================================设置====================
	// ini_set ( 'session.cookie_domain', '.bo.com' );
	// session_start ();
	// // $session_id = session_id ();
	// // setcookie ( "cookie_sn", $session_id, 100, "/", ".bo.com", 1 );
	// // echo $session_id;
	// $_SESSION['xxxxxx'] = array(
	// 0 => 'asdad',
	// 1 => '123123'
	// );
	// $_SESSION['xxx3'] = 'ctocode-解决php跨域session----11111111111111111111111';
	// echo '赋予的session:【 ' . $_SESSION['xxx3'] . ' 】<br>';
	// exit ();
	// // =================================获取====================
	// ini_set ( 'session.cookie_domain', '.bo.com' );
	// session_start ();
	// // $cookie = empty ( $_COOKIE['cookie_sn'] ) ? '' : $_COOKIE['cookie_sn'];
	// // session_id ( $_COOKIE['cookie_sn'] );
	// echo '获取的session:【 ' . $_SESSION['xxxxx'] . ' 】<br>';
	// exit ();
}

/**
 * 设置cookie
 */
function ctoCookieSet($name, $value, $kptime = 0, $path = '/')
{
	$extime = $kptime == 0 ? 0 : time () + $kptime;
	$safe = $_SERVER['SERVER_PORT'] == '443' ? 1 : 0;
	$domain = defined ( '_DOMAIN_PUBLIC_COOKIE_' ) ? _DOMAIN_PUBLIC_COOKIE_ : '';
	setcookie ( $name, $value, $extime, $path, $domain, $safe );
	setcookie ( $name . '__hash', substr ( md5 ( 'asdas' ), 0, 16 ), $extime, $path, $domain, $safe );
	// 原来是用ip来加密的，现用浏览器信息
	// setcookie ( $name . '__hash', substr ( md5 ( get_evnt ( 'browser' ) . $value . $_glb['sitekey'] ), 0, 16 ), $extime, $path, $domain, $safe );
}
/**
 * 清楚cookie
 */
function ctoCookieClear($name = '')
{
	$domain = defined ( '_DOMAIN_PUBLIC_COOKIE_' ) ? _DOMAIN_PUBLIC_COOKIE_ : '';
	$safe = $_SERVER['SERVER_PORT'] == '443' ? 1 : 0;
	setcookie ( $name, '', time () - 360000, "/", $domain, $safe );
	setcookie ( $name . '__hash', '', time () - 360000, "/", $domain, $safe );
}
/**
 * 获取cookie
 */
function ctoCookieGet($name = '', $sitekey)
{
	if(! isset ( $_COOKIE[$name] ) || ! isset ( $_COOKIE[$name . '__hash'] )){
		return '';
	}
	// if($_COOKIE[$name . '__hash'] != substr ( md5 ( 'asdas' . $_COOKIE[$name] ), 0, 16 ))
	if($_COOKIE[$name . '__hash'] != substr ( md5 ( get_evnt ( 'browser' ) . $_COOKIE[$name] . $sitekey ), 0, 16 )){
		return '';
	}
	return $_COOKIE[$name];
}
	