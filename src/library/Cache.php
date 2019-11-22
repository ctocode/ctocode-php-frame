<?php
class CTOCODE_Cache
{
	private static $_instance = null;
	protected $_options = array(
		'cache_dir' => "./",
		'file_name_prefix' => 'cache',
		'mode' => '1'
	); // mode 1 为serialize model 2为保存为可执行文件

	/**
	 * 得到本类实例
	 * @return object Ambiguous
	 */
	public static function getInstance()
	{
		if(self::$_instance === null){
			self::$_instance = new self ();
		}
		return self::$_instance;
	}

	/**
	 * 得到缓存信息
	 * @param string $id
	 * @return boolean|array
	 */
	public static function get($id)
	{
		$instance = self::getInstance ();
		// 缓存文件不存在
		if(! $instance->has ( $id )){
			return FALSE;
		}
		$file = $instance->_file ( $id );
		$data = $instance->_fileGetContents ( $file );
		if($data['expire'] == 0 || time () < $data['expire']){
			return $data['contents'];
		}
		return FALSE;
	}
	/**
	 * 设置一个缓存
	 * @param string $id 缓存id
	 * @param array $data 缓存内容
	 * @param int $cacheLife 缓存生命 默认为0无限生命
	 */
	public static function set($id, $data, $cacheLife = 0)
	{
		$instance = self::getInstance ();
		$time = time ();
		$cache = array();
		$cache['contents'] = $data;
		$cache['expire'] = $cacheLife === 0 ? 0 : $time + $cacheLife;
		$cache['mtime'] = $time;
		$file = $instance->_file ( $id );
		return $instance->_filePutContents ( $file, $cache );
	}

	/**
	 * 清除一条缓存
	 * @param string cache id
	 * @return void
	 */
	public static function delete($id)
	{
		$instance = self::getInstance ();

		if(! $instance->has ( $id )){
			return FALSE;
		}
		$file = $instance->_file ( $id );
		// 删除该缓存
		return unlink ( $file );
	}

	/**
	 * 判断缓存是否存在
	 * @param string $id cache_id
	 * @return boolean true 缓存存在 FALSE 缓存不存在
	 */
	public static function has($id)
	{
		$instance = self::getInstance ();
		$file = $instance->_file ( $id );

		if(! is_file ( $file )){
			return FALSE;
		}
		return true;
	}

	/**
	 * 通过缓存id得到缓存信息路径
	 * @param string $id
	 * @return string 缓存文件路径
	 */
	protected function _file($id)
	{
		$instance = self::getInstance ();
		$fileNmae = $instance->_idToFileName ( $id );
		return $instance->_options['cache_dir'] . $fileNmae;
	}

	/**
	 * 通过id得到缓存信息存储文件名
	 * @param $id
	 * @return string 缓存文件名
	 */
	protected function _idToFileName($id)
	{
		$instance = self::getInstance ();
		$prefix = $instance->_options['file_name_prefix'];
		return $prefix . '---' . $id;
	}

	/**
	 * 通过filename得到缓存id
	 * @param $id
	 * @return string 缓存id
	 */
	protected function _fileNameToId($fileName)
	{
		$instance = self::getInstance ();
		$prefix = $instance->_options['file_name_prefix'];
		return preg_replace ( '/^' . $prefix . '---(.*)$/', '$1', $fileName );
	}
	/**
	 * 把数据写入文件
	 * @param string $file 文件名称
	 * @param array $contents 数据内容
	 * @return bool
	 */
	protected function _filePutContents($file, $contents)
	{
		if($this->_options['mode'] == 1){
			$contents = serialize ( $contents );
		}else{
			$time = time ();
			$contents = "<?php\n" . " // mktime: " . $time . "\n" . " return " . var_export ( $contents, true ) . "\n?>";
		}
		$result = FALSE;
		$f = @fopen ( $file, 'w' );
		if($f){
			@flock ( $f, LOCK_EX );
			fseek ( $f, 0 );
			ftruncate ( $f, 0 );
			$tmp = @fwrite ( $f, $contents );
			if(! ($tmp === FALSE)){
				$result = true;
			}
			@fclose ( $f );
		}
		@chmod ( $file, 0777 );
		return $result;
	}

	/**
	 * 从文件得到数据
	 * @param string $file
	 * @return boolean|array
	 */
	protected function _fileGetContents($file)
	{
		if(! is_file ( $file )){
			return FALSE;
		}
		if($this->_options['mode'] == 1){
			$f = @fopen ( $file, 'r' );
			@$data = fread ( $f, filesize ( $file ) );
			@fclose ( $f );
			return unserialize ( $data );
		}else{
			return include $file;
		}
	}
	/**
	 * 构造函数
	 */
	protected function __construct()
	{
	}

	/**
	 * 设置缓存路径
	 * @param string $path
	 * @return self
	 */
	public static function setCacheDir($path)
	{
		$instance = self::getInstance ();
		if(! is_dir ( $path )){
			exit ( 'file_cache: ' . $path . ' 不是一个有效路径 ' );
		}
		if(! is_writable ( $path )){
			exit ( 'file_cache: 路径 "' . $path . '" 不可写' );
		}

		$path = rtrim ( $path, '/' ) . '/';
		$instance->_options['cache_dir'] = $path;

		return $instance;
	}

	/**
	 * 设置缓存文件前缀
	 * @param string $prefix
	 * @return self
	 */
	public static function setCachePrefix($prefix)
	{
		$instance = self::getInstance ();
		$instance->_options['file_name_prefix'] = $prefix;
		return $instance;
	}

	/**
	 * 设置缓存存储类型
	 * @param int $mode
	 * @return self
	 */
	public static function setCacheMode($mode = 1)
	{
		$instance = self::getInstance ();
		if($mode == 1){
			$instance->_options['mode'] = 1;
		}else{
			$instance->_options['mode'] = 2;
		}
		return $instance;
	}
	/**
	 * 删除所有缓存
	 * @return boolean
	 */
	public static function flush()
	{
		$instance = self::getInstance ();
		$glob = @glob ( $instance->_options['cache_dir'] . $instance->_options['file_name_prefix'] . '--*' );

		if(empty ( $glob )){
			return FALSE;
		}

		foreach($glob as $v){
			$fileName = basename ( $v );
			$id = $instance->_fileNameToId ( $fileName );
			$instance->delete ( $id );
		}
		return true;
	}
}

/**
 * @action ===设置文件缓存,添加缓存数据
 * @param string $path 路径
 * @param string $key 唯一键
 * @param string $val 值
 * @param string
 * @param string $slize
 */
function ctoFileCacheAdd($param = array(), $content, $val = FALSE, $slize = FALSE)
{
	$file_dir = $param['file_dir'];
	if(empty ( $param['file_sign'] )){
		return FALSE;
	}
	$file_md5 = md5 ( $param['file_sign'] );
	$file_md5_name = '/' . substr ( $file_md5, 0, 2 ) . '/' . substr ( $file_md5, 2, 2 );
	$file_name = substr ( $file_md5, 4, 28 ) . ($slize ? '.inc' : '.php');
	$file_path = $file_dir . $file_md5_name . '/' . $file_name;
	ctoFilePutPhp ( $file_path, $content );
}
/**
 * @action ==获取文件缓存, 获取缓存数据
 * @param string $path
 * @param string $key
 * @param string $time
 * @param string $slize
 * @return  <boolean, unknown>
 */
function ctoFileCacheGet($param = array(), $time = - 1, $slize = FALSE, $is_update = NULL)
{
	$file_dir = $param['file_dir'];
	if(empty ( $param['file_sign'] )){
		return FALSE;
	}
	$file_md5 = md5 ( $param['file_sign'] );
	$file_md5_name = '/' . substr ( $file_md5, 0, 2 ) . '/' . substr ( $file_md5, 2, 2 );
	$file_name = substr ( $file_md5, 4, 28 ) . ($slize ? '.inc' : '.php');
	$file_path = $file_dir . $file_md5_name . '/' . $file_name;

	if(! is_file ( $file_path )){
		return false;
	}

	if($time == - 1){
		$rs = ctoFileCacheParse ( $file_path, $slize );
		return $rs;
	}
	$time = intval ( $time );
	$date = @filemtime ( $file_path ); // 本函数返回文件中的数据块上次被写入的时间，也就是说，文件的内容上次被修改的时间。
	if(($date + $time) < time ()){ // 根据判断,返回是否符合在时间段内 - -- filemtime() 函数返回文件内容上次的修改时间。
		return FALSE;
	}
	$rs = ctoFileCacheParse ( $file_path, $slize );
	return $rs;
}
// 解析文件缓存
function ctoFileCacheParse($file = '', $slize = FALSE)
{
	if($slize){
		$str = file_get_contents ( $file );
		$rs = str_array ( $str, 1 );
		$rs = isset ( $rs['data'] ) ? $rs['data'] : FALSE;
	}else{
		$rs = include ($file);
	}
	return $rs;
}
// 删除缓存数据
function ctoFileCacheDel($param = array(), $slize = FALSE)
{
	$file_dir = $param['file_dir'];
	if(empty ( $param['file_sign'] )){
		return FALSE;
	}
	$file_md5 = md5 ( $param['file_sign'] );
	$file_md5_name = '/' . substr ( $file_md5, 0, 2 ) . '/' . substr ( $file_md5, 2, 2 );
	$file_name = substr ( $file_md5, 4, 28 ) . ($slize ? '.inc' : '.php');
	$file_path = $file_dir . $file_md5_name . '/' . $file_name;

	try{
		unlink ( $file_path );
	}
	catch ( Exception $e ){
		echo '<div style="color:red;">' . '清除缓存文件失败!请检查目录权限!' . '</div>';
	}
	// 存放缓存文件的默认目录
	// if ($handle = opendir ( $cfile )) {
	// 打开 images 目录,opendir打开一个目录句柄，可
	// while ( FALSE !== ($file = readdir ( $handle )) ) { // readdir() 函数返回由 opendir() 打开的目录句柄中的条目。
	// if (is_file ( $cfile )) { // is_file() 函数检查指定的文件名是否是正常的文件。
	// // 存在的话,删除文件 、文件为全名,若成功，则返回 true，失败则返回 FALSE。
	// unlink ( $cfile );
	// }
	// }
	// // 重置目录流 rewinddir($path);
	// closedir ( $handle ); // closedir() 函数关闭由 opendir() 函数打开的目录句柄。
	// }
}
function str_array($str = '', $sql = 0)
{
	if($sql){
		$str = stripslashes ( $str );
	}
	$us = trim ( $str ) != '' ? @unserialize ( $str ) : false;
	return is_array ( $us ) ? mystrips ( $us ) : array();
}
function mystrips($inp)
{
	if(is_array ( $inp )){
		$rs = array();
		foreach($inp as $k=>$v){
			$rs[$k] = mystrips ( $v );
		}
		return $rs;
	}
	return stripslashes ( $inp );
}
    