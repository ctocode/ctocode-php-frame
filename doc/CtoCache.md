```
/* 初始化设置cache的配置信息什么的 */
CTOCODE_Cache::setCachePrefix ( 'core' ); // 设置缓存文件前缀
CTOCODE_Cache::setCacheDir ( './cache' ); // 设置存放缓存文件夹路径

/*
 * 模式1 缓存存储方式
 * a:3:{s:8:"contents";a:7:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:34;i:4;i:5;i:5;i:6;i:6;i:6;}s:6:"expire";i:0;s:5:"mtime";i:1318218422;}
 * 模式2 缓存存储方式
 * <?php
 * // mktime: 1318224645
 * return array (
 * 'contents' => array (
 * 0 => 1,
 * 1 => 2,
 * 2 => 3,
 * 3 => 34,
 * 4 => 5,
 * 5 => 6,
 * 6 => 6 ),
 * 'expire' => 0,
 * 'mtime' => 1318224645 ) ?>
 */

CTOCODE_Cache::setCacheMode ( '1' );

if(! $row = CTOCODE_Cache::get ( 'zj2sad' )){
	$array = array(
		1,
		2,
		3,
		34,
		5,
		6,
		612312
	);
	$row = CTOCODE_Cache::set ( 'zj2sad', $array );
}
// cache::flush(); 清空所有缓存

print_r ( $row );

// 省略参数即采用缺省设置, $cache = new Cache($cachedir);

/*
 * 1.根据需求,查询 数据库(mysql)-取查询结果ID
 * 2.根据 ID ,查询缓存
 * ->【存在】直接获取缓存结果
 * ->【不存在 】 1.查询数据库.取出所有需要的数据 2.结果 ,新建文件 缓存
 */
/**
 $param = array(
 'file_dir' => NULL, // 存放目录
 'file_sign' => 'sign', // 文件夹分类标识
 'time' => 0, // 有效期,默认0永久(单位为秒)
 'file_name' => NULL, // 缓存文件名
 'extension' => NULL, // 扩展名
 'isvalid' => NULL, // 有效
 'other' => NULL
 );
 ctoFileCacheAdd ( array(
 'file_dir' => _CTOCODE_RUNTIME_ . '/xxxx/xxx/',
 'file_sign' => 123
 ), array(
 'name' => 'asdasd',
 'asdasd' => 'asd'
 ) );
 $cacheData = ctoFileCacheGet ( array(
 'file_dir' => _CTOCODE_RUNTIME_ . '/xxxx/xxx/',
 'file_sign' => 123
 ) );
 */
 ```