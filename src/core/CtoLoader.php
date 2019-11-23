<?php

namespace ctocode\phpframe\core;

class CtoLoader
{
}
// 注册自动加载类
function __autoloadCtocode($class)
{
	$class = str_replace ( 'CTOCODE_', '', $class );
	if(file_exists ( CTOCODE_ . $class . '.php' )){
		require (CTOCODE_ . $class . '.php');
		// require_once str_replace ( '//', '/', CTOCODE_ . '/' . $class );
	}
}
spl_autoload_register ( '__autoloadCtocode' );
