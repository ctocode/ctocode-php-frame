<?php

/**
 * 【ctocode】      常用函数 - check相关处理
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
 * 验证方法
 * @author ctocode-zhw
 * @version 2017-04-04
 * @link http://www.ctocode.com
 */
function ctoCheck($type = NULL, $str = NULL)
{
	if(empty ( $type ) || empty ( $str )){
		return false;
	}
	$regex = '';
	switch($type)
	{
		case 'num_letter':
			// 密码只能为数字和字母的组合
			$regex = "/^(w){4,20}$/";
			break;
		case '_letter':
			// 验证是否以字母开头
			$regex = "/^[a-za-z]{1}([a-za-z0-9]|[._]){3,19}$/";
			break;

		/*
		 * @action 验证手机号码
		 * @version 2016-10-17
		 * @author ctocode-zhw
		 * @link http://www.ctocode.com
		 */
		case 'mobile':
		case 'phone':
			// 验证手机号码
			// $regex = '#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,1,3,5,6,7,8]{1}\d{8}$|^18[\d]{9}$#';
			// $regex ="/^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|18[0|1|2|3|5|6|7|8|9])\d{8}$/"
			// $regex = "/^1[34578]{1}\d{9}$/";
			// $regex = '/^((0\d{2,3}-\d{7,8})|(\d{7,8})|(1[35847]\d{9}))$/';
			$regex = '/^1[3|4|5|6|7|8|9][0-9]\d{4,8}$/';
			if(strlen ( $str ) != 11){
				return false;
			}
			if(! is_numeric ( $str )){
				return false;
			}
			break;
		/*
		 * 验证数字
		 */
		case 'number':
			// 必须为不为0开头的纯数字,请重新填写
			$regex = "/^(0|[1-9][0-9]*)$/";
			break;
		/*
		 * 检测域名格式
		 */
		case 'url':
			$regex = "/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/";
			// $regex = "^http://[_a-zA-Z0-9-]+(.[_a-zA-Z0-9-]+)*$";
			// $regex = "/^(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/";
			break;
		/*
		 * @action 邮箱验证
		 * @author ctocode-zhw
		 * @version 2017-06-21
		 * @param string $type 邮箱
		 * @return boolean
		 */
		case 'email':
			// $regex = "/^[a-z0-9]+[.a-z0-9_-]*@[a-z0-9]+[.a-z0-9_-]*\.[a-z0-9]+$/i"
			// $regex = "/[a-za-z0-9]+@[a-za-z0-9]+.[a-z]{2,4}/"
			$regex = '/^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[-_a-z0-9][-_a-z0-9]*\.)*(?:[a-z0-9][-a-z0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})$/i';
			break;

		// 身份证验证
		case 'idcard':

			$regex = '/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/';
			break;
	}
	return preg_match ( $regex, $str ) ? true : false;
}
/**
 * 验证用户名是否以字母开头
 */
function ctoCheckUserName($str)
{
	if(preg_match ( "/^[a-za-z]{1}([a-za-z0-9]|[._]){3,19}$/", $str, $username )){
		return true;
	}else{
		return false;
	}
}
/**
 * 验证密码只能为数字和字母的组合
 */
function ctoCheckPassword($psd)
{
	if(preg_match ( "/^(w){4,20}$/", $psd, $password )){
		return true;
	}else{
		return false;
	}
}
/**
 * @action 判断 浏览器访问的内核,是否是手机访问
 * @author ctocode-zhw
 * @version 2017-12-20
 * @link http://www.ctocode.com
 * @return boolean
 */
function ctoCheckBrowser()
{
	// 下列几个数组，用^^^分割，减少代码行数

	/* os */
	$mobile_os_str = '';
	$mobile_os_str .= 'Google Wireless Transcoder^^^Windows CE^^^WindowsCE^^^Symbian^^^Android';
	$mobile_os_str .= '^^^armv6l^^^armv5^^^Mobile^^^CentOS^^^mowser^^^AvantGo^^^Opera Mobi';
	$mobile_os_str .= '^^^J2ME/MIDP^^^Smartphone^^^Go.Web^^^Palm^^^iPAQ';
	$mobile_os_arr = explode ( "^^^", $mobile_os_str );
	/* token */
	$mobile_token_str = '';
	$mobile_token_str .= 'Profile/MIDP^^^Configuration/CLDC-';
	$mobile_token_str .= '^^^160×160^^^176×220^^^240×240^^^240×320^^^320×240';
	$mobile_token_str .= '^^^UP.Browser^^^UP.Link^^^SymbianOS^^^PalmOS^^^PocketPC^^^SonyEricsson^^^Nokia';
	$mobile_token_str .= '^^^BlackBerry^^^Vodafone^^^BenQ^^^Novarra-Vision^^^Iris';
	$mobile_token_str .= '^^^NetFront^^^HTC_^^^Xda_^^^SAMSUNG-SGH^^^Wapaka^^^DoCoMo^^^iPhone^^^iPod';
	$mobile_token_arr = explode ( "^^^", $mobile_token_str );
	/* agents */
	$mobile_agents_str = '';
	$mobile_agents_str .= '240x320';
	$mobile_agents_str .= '^^^acer^^^acoon^^^acs-^^^abacho^^^airness^^^alcatel^^^amoi^^^android^^^anywhereyougo.com';
	$mobile_agents_str .= '^^^applewebkit/525^^^applewebkit/532^^^asus^^^audio^^^au-mic^^^avantogo';
	$mobile_agents_str .= '^^^becker^^^benq^^^bilbo^^^bird^^^blackberry^^^blazer^^^bleu';
	$mobile_agents_str .= '^^^cdm-^^^compal^^^coolpad^^^danger^^^dbtel^^^dopod';
	$mobile_agents_str .= '^^^elaine^^^eric^^^etouch^^^fly ^^^fly_^^^fly-';
	$mobile_agents_str .= '^^^go.web^^^goodaccess^^^gradiente^^^grundig^^^haier^^^hedy^^^hitachi^^^htc';
	$mobile_agents_str .= '^^^huawei^^^hutchison^^^inno^^^ipad^^^ipaq^^^ipod^^^jbrowser^^^kddi^^^kgt^^^kwc';
	$mobile_agents_str .= '^^^lenovo^^^lg ^^^lg2^^^lg3^^^lg4^^^lg5^^^lg7^^^lg8^^^lg9^^^lg-^^^lge-^^^lge9^^^longcos';
	$mobile_agents_str .= '^^^maemo^^^mercator^^^meridian^^^micromax^^^midp^^^mini^^^mitsu^^^mmm^^^mmp^^^mobi^^^mot-^^^moto';
	$mobile_agents_str .= '^^^nec-^^^netfront^^^newgen^^^nexian^^^nf-browser^^^nintendo^^^nitro^^^nokia^^^nook^^^novarra';
	$mobile_agents_str .= '^^^obigo^^^palm^^^panasonic^^^pantech^^^philips^^^phone^^^pg-^^^playstation^^^pocket^^^pt-';
	$mobile_agents_str .= '^^^qc-^^^qtek^^^rover^^^sagem^^^sama^^^samu^^^sanyo^^^samsung^^^sch-^^^scooter^^^sec-^^^sendo';
	$mobile_agents_str .= '^^^sgh-^^^sharp^^^siemens^^^sie-^^^softbank^^^sony^^^spice^^^sprint^^^spv^^^symbian';
	$mobile_agents_str .= '^^^tablet^^^talkabout^^^tcl-^^^teleca^^^telit^^^tianyu^^^tim-^^^toshiba^^^tsm';
	$mobile_agents_str .= '^^^up.browser^^^utec^^^utstar^^^verykool^^^virgin^^^vk-^^^voda^^^voxtel^^^vx';
	$mobile_agents_str .= '^^^wap^^^wellco^^^wig browser^^^wii^^^windows ce^^^wireless^^^xda^^^xde^^^zte';
	$mobile_agents_arr = explode ( "^^^", $mobile_agents_str );

	$user_agent = isset ( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';

	$browser_type = 'pc';
	$is_mobile = false;
	foreach($mobile_agents_arr as $device){
		if(false !== stristr ( $user_agent, $device )){
			$is_mobile = true;
			break;
		}
	}
	/* 获取浏览器内核 */
	// $browser_kernel = preg_match ( '|\(.*?\)|', $user_agent, $matches_kernel ) > 0 ? $matches[0] : '';
	/* 获取版本号-内核 */
	// $browser_version = preg_match ( '/.*?(MicroMessenger\/([0-9.]+))\s*/', $user_agent, $matches_version );
	if($is_mobile == true){
		if(false !== strpos ( $user_agent, "MicroMessenger" )){ /* 微信浏览器 */
			$browser_type = 'weixin';
		}else{
			$browser_type = 'mobile';
		}
	}
	return $browser_type;

	// 方法2
	$user_agent_commentsblock = preg_match ( '|\(.*?\)|', $user_agent, $matches ) > 0 ? $matches[0] : '';
	$found_mobile = ctoCheckSubstrs ( $mobile_os_arr, $user_agent_commentsblock ) || ctoCheckSubstrs ( $mobile_token_arr, $user_agent );
	if($found_mobile){
		return true;
	}else{
		return false;
	}
}
function ctoCheckSubstrs($substrs, $text)
{
	foreach($substrs as $substr){
		if(false !== strpos ( $text, $substr )){
			return true;
		}
	}
	return false;
}
/*
 * @action 车牌号码匹配
 * @author: ctocode-lcg
 * @version: 2018/10/29
 * @param string
 * @return bool
 */
function ctoCheckCarLicense($license)
{
	if(empty ( $license )){
		return false;
	}
	$regular = "/[京津冀晋蒙辽吉黑沪苏浙皖闽赣鲁豫鄂湘粤桂琼川贵云渝藏陕甘青宁新使]{1}[A-Z]{1}[0-9a-zA-Z]{5}$/u";
	preg_match ( $regular, $license, $match );
	if(isset ( $match[0] )){
		return true;
	}
	$regular = '/[京津冀晋蒙辽吉黑沪苏浙皖闽赣鲁豫鄂湘粤桂琼川贵云渝藏陕甘青宁新]{1}[A-Z]{1}[0-9a-zA-Z]{4}[挂警学领港澳]{1}$/u';
	preg_match ( $regular, $license, $match );
	if(isset ( $match[0] )){
		return true;
	}
	$regular = '/^WJ[京津冀晋蒙辽吉黑沪苏浙皖闽赣鲁豫鄂湘粤桂琼川贵云渝藏陕甘青宁新]?[0-9a-zA-Z]{5}$/ui';
	preg_match ( $regular, $license, $match );
	if(isset ( $match[0] )){
		return true;
	}
	$regular = "/[A-Z]{2}[0-9]{5}$/";
	preg_match ( $regular, $license, $match );
	if(isset ( $match[0] )){
		return true;
	}
	$regular = "/[京津冀晋蒙辽吉黑沪苏浙皖闽赣鲁豫鄂湘粤桂琼川贵云渝藏陕甘青宁新]{1}[A-Z]{1}[DF]{1}[0-9a-zA-Z]{5}$/u";
	preg_match ( $regular, $license, $match );
	if(isset ( $match[0] )){
		return true;
	}
	$regular = "/[京津冀晋蒙辽吉黑沪苏浙皖闽赣鲁豫鄂湘粤桂琼川贵云渝藏陕甘青宁新]{1}[A-Z]{1}[0-9a-zA-Z]{5}[DF]{1}$/u";
	preg_match ( $regular, $license, $match );
	if(isset ( $match[0] )){
		return true;
	}
	return false;
}
