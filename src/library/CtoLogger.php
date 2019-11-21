<?php

namespace ctocode\phpframe\library;

/**
 * @author ctocode-lww
 * @version 2018-10-17
 */
class CtoLogger
{
	/**
	 * 记录数据信息
	 * @param string $content 日志内容
	 * @param string $file_name 文件名
	 */
	public static function info($content, $file_name = 'info.log')
	{
		self::writeLog ( $content, $file_name );
	}

	/**
	 * 记录错误信息
	 * @param string $content 日志内容
	 * @param string $file_name 文件名
	 */
	public static function error($content, $file_name = 'error.log')
	{
		self::writeLog ( $content, $file_name );
	}

	/**
	 * 记录调试信息
	 * @param string $content 日志内容
	 * @param string $file_name 文件名
	 */
	public static function debug($content, $file_name = 'debug.log')
	{
		self::writeLog ( $content, $file_name );
	}
	/**
	 * 写入日志
	 * @param string $content 日志内容
	 * @param string $file_name 文件名
	 */
	public static function writeLog($content, $file_name)
	{
		if(defined ( "_CTOCODE_RUNTIME_" )){
			$file_name = _CTOCODE_RUNTIME_ . 'ctocode_log/' . $file_name;
		}else{
			$file_name = dirname ( __DIR__, 5 ) . '/runtime/sctocode_log/' . $file_name;
		}
		if(! file_exists ( dirname ( $file_name ) )){
			mkdir ( dirname ( $file_name ), 0777, true );
		}
		$content = (is_array ( $content ) || is_object ( $content )) ? print_r ( $content, 1 ) : $content;
		@file_put_contents ( $file_name, PHP_EOL . date ( 'Y-m-d H:i:s' ) . "============================" . PHP_EOL . $content, FILE_APPEND );
	}
}