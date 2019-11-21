<?php

namespace ctocode\phpframe\library;

/**
 * AES算法类
 * 算法模式: CBC
 * 密钥长度: 128位
 * 补码方式: PKCS5Padding（加解密）/NOPadding（解密）
 * 密钥: ctocode-api
 * 密钥偏移量: api-token
 * 加密结果编码方式: base64字符串/十六进制字符串/二进制字符串流(还未处理)
 *
 * @author: ctocode-zwj
 * @version: 1.0.0
 * @date: 2018/12/01
 */
class CtoAes
{
	protected $keyString = 'ctocode-api';
	protected $ivString = 'api-token';
	// 密钥 须是16位
	protected $key;
	// 偏移量
	protected $iv;
	/**
	 * 解密字符串
	 * @param string $data 字符串
	 * @return string
	 */
	public function __construct($aesSett = array())
	{
		$this->keyString = ! empty ( $aesSett['keyString'] ) ? $aesSett['keyString'] : $this->keyString;
		$this->ivString = ! empty ( $aesSett['ivString'] ) ? $aesSett['ivString'] : $this->ivString;
		$this->key = $this->getMd516 ( $this->keyString );
		$this->iv = $this->getMd516 ( $this->ivString );
	}
	public function decode($str)
	{
		return openssl_decrypt ( base64_decode ( $str ), "AES-128-CBC", $this->key, OPENSSL_RAW_DATA, $this->iv );
	}
	/**
	 * 加密字符串
	 * @param string $data 字符串
	 * @return string
	 */
	public function encode($str)
	{
		return base64_encode ( openssl_encrypt ( $str, "AES-128-CBC", $this->key, OPENSSL_RAW_DATA, $this->iv ) );
	}
	private function getMd516($md5String = '')
	{
		return substr ( md5 ( $md5String ), 0, 16 );
	}

	// 16进制转为2进制
	private function _hex2bin($hex = false)
	{
		$ret = $hex !== false && preg_match ( '/^[0-9a-fA-F]+$/i', $hex ) ? pack ( "H*", $hex ) : false;
		return $ret;
	}

	/**
	 * 加密
	 *
	 * @param string 明文
	 * @return string 密文
	 */
	public function encrypt($data, $code = 'base64')
	{
		if(! is_string ( $data )){
			return false;
		}
		$data = openssl_encrypt ( $data, "AES-128-CBC", $this->key, OPENSSL_RAW_DATA, $this->iv );
		$data = $this->_encode ( $data, $code );
		return $data;
	}

	/**
	 * 解密
	 *
	 * @param string 密文
	 * @return string 明文
	 */
	public function decrypt($data, $code = 'base64')
	{
		if(! is_string ( $data )){
			return false;
		}
		$data = $this->_decode ( $data, $code );
		$data = openssl_decrypt ( $data, "AES-128-CBC", $this->key, OPENSSL_RAW_DATA, $this->iv );
		return $data;
	}
	private function _encode($data, $code)
	{
		switch(strtolower ( $code ))
		{
			case 'base64':
				$data = base64_encode ( $data );
				break;
			case 'hex':
				$data = bin2hex ( $data );
				break;
			case 'bin':
			default:
		}
		return $data;
	}
	private function _decode($data, $code = 'base64')
	{
		switch(strtolower ( $code ))
		{
			case 'base64':
				$data = base64_decode ( $data );
				break;
			case 'hex':
				$data = $this->_hex2bin ( $data );
				break;
			case 'bin':
			default:
		}
		return $data;
	}

	/**
	 * @param string 密文
	 * @todourl base64解码
	 * @author ctocode-zwj
	 */
	function urlsafe_b64decode($string)
	{
		$data = str_replace ( array(
			'-',
			'_'
		), array(
			'+',
			'/'
		), $string );
		$mod4 = strlen ( $data ) % 4;
		if($mod4){
			$data .= substr ( '====', $mod4 );
		}
		return base64_decode ( $data );
	}

	/**
	 * @param string 密文
	 * @todourl base64编码
	 * @author ctocode-zwj
	 */
	function urlsafe_b64encode($string)
	{
		$data = base64_encode ( $string );
		$data = str_replace ( array(
			'+',
			'/',
			'='
		), array(
			'-',
			'_',
			''
		), $data );
		return $data;
	}
}