<?php

namespace ctocode\phpframe\library;

// PHP Token(令牌)设计 设计目标: 避免重复提交数据.
// 检查来路,是否是外部提交 匹配要执行的动作(如果有多个逻辑在同一个页面实现,比如新增,删除,修改放到一个PHP文件里操作)
// 这里所说的token是在页面显示的时候,写到FORM的一个隐藏表单项(type=hidden). token不可明文,如果是明文,
// 那就太危险了,所以要采用一定的加密方式.密文要可逆.俺算法很白痴,所以采用了网上一个现成的方法.
class GConfig
{
}
class GSuperclass
{
}
class GSession
{
}

/**
 * 如何达到目的: 怎样避免重复提交? 
 * 在SESSION里要存一个数组,这个数组存放以经成功提交的token.
 * 在后台处理时,先判断这个token是否在这个数组里,如果存在,说明是重复提交. 
 * 如何检查来路?
 * 可选项,这个token在生成的时候,加入了当前的session_id.如果别人copy你的html(token一迸copy),
 * 在提交时,理论上token里包含的session_id不等于当前session_id,就可以判断这次提交是外部提交.
 * 如何匹配要执行的动作? 
 * 在token的时候,要把这个token的动作名称写进这个token里,这样,在处理的时候,把这个动作解出来进行比较就行了.
 * 我以前写的GToken不能达到上面所说的第二条,今天修改了一下,把功能2加上了.个人感觉还行.
 * 请大家看代码,感觉哪里有不合理的地方,还请赐教!谢谢. 加密我是找的网上的一个方法,稍作了一下修改. GEncrypt.inc.php:
 */
class GEncrypt extends GSuperclass
{
	protected static function keyED($txt, $encrypt_key)
	{
		$encrypt_key = md5 ( $encrypt_key );
		$ctr = 0;
		$tmp = "";
		for($i = 0;$i < strlen ( $txt );$i ++){
			if($ctr == strlen ( $encrypt_key ))
				$ctr = 0;
			$tmp .= substr ( $txt, $i, 1 ) ^ substr ( $encrypt_key, $ctr, 1 );
			$ctr ++;
		}
		return $tmp;
	}
	public static function encrypt($txt, $key)
	{
		// $encrypt_key = md5(rand(0,32000));
		$encrypt_key = md5 ( (( float ) date ( "YmdHis" ) + rand ( 10000000000000000, 99999999999999999 )) . rand ( 100000, 999999 ) );
		$ctr = 0;
		$tmp = "";
		for($i = 0;$i < strlen ( $txt );$i ++){
			if($ctr == strlen ( $encrypt_key ))
				$ctr = 0;
			$tmp .= substr ( $encrypt_key, $ctr, 1 ) . (substr ( $txt, $i, 1 ) ^ substr ( $encrypt_key, $ctr, 1 ));
			$ctr ++;
		}
		return base64_encode ( self::keyED ( $tmp, $key ) );
	}
	public static function decrypt($txt, $key)
	{
		$txt = self::keyED ( base64_decode ( $txt ), $key );
		$tmp = "";
		for($i = 0;$i < strlen ( $txt );$i ++){
			$md5 = substr ( $txt, $i, 1 );
			$i ++;
			$tmp .= (substr ( $txt, $i, 1 ) ^ $md5);
		}
		return $tmp;
	}
}
/*
 * GToken.inc.php 方法: a,granteToken 参数:formName,即动作名称,key是加密/解密 密钥. 返回一个字符串,形式是: 加密(formName:session_id) b,isToken
 * 参数:token 即granteToken产生的结果,formName,动作名称,fromCheck是否检查来路,如果为真,还要判断token里的session_id是否和当前的session_id一至.
 * c,dropToken,当成功执行一个动作后,调用这个函数,把这个token记入session里,
 */
/**
 * 原理：请求分配token的时候，想办法分配一个唯一的token, base64( time + rand + action)
 * 如果提交，将这个token记录，说明这个token以经使用，可以跟据它来避免重复提交。
 */
class CtoToken
{

	/**
	 * 得到当前所有的token
	 * @return array
	 */
	public static function getTokens()
	{
		$tokens = $_SESSION[GConfig::SESSION_KEY_TOKEN];
		if(empty ( $tokens ) && ! is_array ( $tokens )){
			$tokens = array();
		}
		return $tokens;
	}

	/**
	 * 产生一个新的Token
	 *
	 * @param string $formName        	
	 * @param string $key 加密密钥         	
	 * @return string
	 */
	public static function granteToken($formName, $key = GConfig::ENCRYPT_KEY)
	{
		$token = GEncrypt::encrypt ( $formName . ":" . session_id (), $key );
		return $token;
	}

	/**
	 * 删除token,实际是向session 的一个数组里加入一个元素，说明这个token以经使用过，以避免数据重复提交。
	 *
	 * @param string $token        	
	 */
	public static function dropToken($token)
	{
		$tokens = self::getTokens ();
		$tokens[] = $token;
		GSession::set ( GConfig::SESSION_KEY_TOKEN, $tokens );
	}

	/**
	 * 检查是否为指定的Token
	 * @param string $token 要检查的token值
	 * @param string $formName        	
	 * @param boolean $fromCheck 是否检查来路,如果为true,会判断token中附加的session_id是否和当前session_id一至.
	 * @param string $key 加密密钥
	 * @return boolean
	 */
	public static function isToken($token, $formName, $fromCheck = false, $key = GConfig::ENCRYPT_KEY)
	{
		$tokens = self::getTokens ();

		if(in_array ( $token, $tokens )) // 如果存在，说明是以使用过的token
			return false;

		$source = split ( ":", GEncrypt::decrypt ( $token, $key ) );

		if($fromCheck)
			return $source[1] == session_id () && $source[0] == $formName;
		else
			return $source[0] == $formName;
	}
}
// 示例:

// 首先从$_POST里取出token,用isToken判断.

//  这一切看着似乎是没有问题了.
// 如果想判断是否是执行的匹配动作,可以把isToken里的formName改一下,运行,很好,没有匹配上.证明这个成功.

// 是否能避免重复提交,我没有验证,太简单的逻辑了.

// 余下的就是判断 来路检查 是否正常工作了.
// 把上面的示例产生的html copy到本地的一个网页内(以达到不同的域的目的),运行,检查来路不明,没有执行动作(需要把isToken的第三个参数设为true).
// 把isToken的第三个参数设置为false,提交,指定的动作执行了!

// 好了,到此为止,不知道哪个地方是否还存在BUG,这就要在长期运用中慢慢调试修改了!