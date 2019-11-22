<?php

namespace ctocode\phpframe\core;

class CtoLoader
{
}
// 注册自动加载类
function __autoloadCtocode($class)
{
	$class = str_replace ( 'CTOCODE_', '', $class );
	if(file_exists ( _CTOCODE_LIB_ . $class . '.php' )){
		require (_CTOCODE_LIB_ . $class . '.php');
		// require_once str_replace ( '//', '/', _CTOCODE_LIB_ . '/' . $class );
	}
}
spl_autoload_register ( '__autoloadCtocode' );
