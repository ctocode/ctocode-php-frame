<?php

/**
 * 【ctocode】      核心文件
 * ============================================================================
 * @author       作者         ctocode-zhw
 * @version 	  版本	  v1.0.0.0.20170115
 * @copyright    版权所有   2015-2027，并保留所有权利。
 * @link         网站地址   http://www.ctocode.com
 * @contact      联系方式   QQ:343196936
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 */
namespace ctocode\phpframe;

// 1. 加载基础常量配置文件件
define ( '_CTOCODE_ROOT_', '20170526' );
/*
 * ============================================================================
 * 授权处理
 * ============================================================================
 */
$ctocode_frame = array(
	/* 软件名称 */
	'_CTOCODE_FRAME_NAME_' => 'ctocode-php-frame',
	'_CTOCODE_FRAME_VERSION_' => '1.0.0',
	/* 版本号等信息 */
	'_CTOCODE_FRAME_BUILD_' => '2017.07.28.1833',
	'_CTOCODE_FRAME_AUTHOR_' => 'ctocode member 343196936@qq.com',
	'_CTOCODE_FRAME_LINK_' => 'https://ctocode.com',
	/* 版权 */
	'_CTOCODE_COPYRIGHT_' => 'https://ctocode.com',
	/* 许可证 */
	'_CTOCODE_LICENSE_' => '20180906-v2',
	/* LICENSE ID */
	'_CTOCODE_LICENSE_ID' => 'V20170129',
	/* LICENSE KEY */
	'_CTOCODE_LICENSE_KEY' => 'ctocodeV20170129'
);
if(empty ( $ctocode_frame['_CTOCODE_LICENSE_'] ) || empty ( $ctocode_frame['_CTOCODE_COPYRIGHT_'] )){
	exit ( '【ctocode - error】许可证或者版权错误～' );
}
/*
 * ============================================================================
 * 其他
 * ============================================================================
 */
// ctocode 框架路径,定义一个常量,正则替换-获取当前文件的绝对目录,当前文件所在路径
define ( '_CTOCODE_PHPFRAME_', preg_replace ( '/(\/|\\\\){1,}/', '/', __DIR__ ) . '/' );
// ctocode 类库
define ( '_CTOCODE_LIB_', _CTOCODE_PHPFRAME_ . 'library/' );
// ctocode 核心
define ( '_CTOCODE_CORE_', _CTOCODE_PHPFRAME_ . 'core/' );
// ctocode 字体
define ( '_CTOCODE_FONTS_', dirname ( _CTOCODE_PHPFRAME_ ) . '/assets/fonts/' );
// 项目路径
define ( '_CTOCODE_PROJECT_', dirname ( _CTOCODE_PHPFRAME_ ) . '/' );
// 配置文件路径
is_dir ( _CTOCODE_PROJECT_ . 'config/' ) ? define ( '_CTOCODE_CONFIG_', _CTOCODE_PROJECT_ . 'config/' ) : exit ( '【ctocode - error】配置文件路径不存在～' );
// ctocode 组件开发
define ( '_CTOCODE_ADDONS_', _CTOCODE_PROJECT_ . 'addons/' );
// ctocode 自己扩展类库
define ( '_CTOCODE_EXTEND_', _CTOCODE_PROJECT_ . 'extend/' );
// 文件上传存放 文件资源路径
define ( '_CTOCODE_FILE_', _CTOCODE_PROJECT_ . 'public/upload/' );
// 程序运行产生的文件 ,ctocode 数据
define ( '_CTOCODE_RUNTIME_', _CTOCODE_PROJECT_ . 'runtime/' );
// composer扩展类库
define ( '_CTOCODE_VENDOR_', _CTOCODE_PROJECT_ . 'vendor/' );

// 2. 导入相关文件
/*
 * 加载 通用函数
 */
require _CTOCODE_PHPFRAME_ . 'function/check.php';
require _CTOCODE_PHPFRAME_ . 'function/file_do.php'; // 文件其他
require _CTOCODE_PHPFRAME_ . 'function/file.php'; // 文件操作
require _CTOCODE_PHPFRAME_ . 'function/html.php'; // html
require _CTOCODE_PHPFRAME_ . 'function/http.php'; // http
require _CTOCODE_PHPFRAME_ . 'function/img.php'; //
require _CTOCODE_PHPFRAME_ . 'function/input.php';
require _CTOCODE_PHPFRAME_ . 'function/ip.php'; // ip
require _CTOCODE_PHPFRAME_ . 'function/language.php'; // 语言
require _CTOCODE_PHPFRAME_ . 'function/load.php'; // load
require _CTOCODE_PHPFRAME_ . 'function/security.php'; // 安全处理
require _CTOCODE_PHPFRAME_ . 'function/session.php'; // session处理
require _CTOCODE_PHPFRAME_ . 'function/sql.php'; // sql语句,数据库
require _CTOCODE_PHPFRAME_ . 'function/str.php'; // 字符串
require _CTOCODE_PHPFRAME_ . 'function/time.php'; // 时间

// -重要
require _CTOCODE_PHPFRAME_ . 'function/common.php'; // 常用函数


/* ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
// 是否添加360网站安全处理脚本文件，注意文件路径
// if(is_file ( __DIR__ . '/360safe_webscan.php' ))
	// {
	// require (__DIR__ . '/360safe_webscan.php');
	// }
/* ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
// if(! file_exists ( _CTOCODE_CONFIG_ . 'install.lock' ))
	// { /* 未安装 */
	// 	// header ( 'Location:install' );
	// 	exit ( '未安装' );
	// }
/*
 * ============================================================================
 * ctocode之路，开始处理
 * ============================================================================
 */