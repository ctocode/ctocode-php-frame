<?php
/**
 * 【ctocode】      核心文件 - MVC
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
// set_exception_handler(array("Factory","last_fun")); 设置一个用户定义的异常处理函数
CTOCODE_MVC::getIns ( $_SERVER['REQUEST_URI'] );
abstract class ProductInterface
{
	public $data = array(); // 用于接收传过来的内容
	public static $magic_quotes = NULL;
	public function __construct()
	{
		self::$magic_quotes = (version_compare ( PHP_VERSION, '5.4' ) < 0 and get_magic_quotes_gpc ());
		// 清洁所有请求变量
		$_GET = self::sanitize ( $_GET );
		$_POST = self::sanitize ( $_POST );
		$_COOKIE = self::sanitize ( $_COOKIE );
	}
	public static function sanitize($value)
	{
		if(is_array ( $value ) or is_object ( $value )){
			foreach($value as $key=>$val){
				$value[$key] = self::sanitize ( $val );
			}
		}elseif(is_string ( $value )){
			if(self::$magic_quotes === TRUE){
				$value = stripslashes ( $value );
			}
			if(strpos ( $value, "\r" ) !== FALSE){
				$value = str_replace ( array(
					"\r\n",
					"\r"
				), "\n", $value );
			}
		}
		return $value;
	}
	public function __set($k, $v)
	{
		$this->$k = $v;
	}
	public function __call($method, $arg)
	{
		echo '错误指定页面';
		print_r ( $arg ); // 错误指定页面
	}
	public static function __callStatic($method, $arg)
	{
		print_r ( $arg ); // 错误指定页面 静态的好像不太可能 先写这儿了
	}
	public function assign($key, $value)
	{
		$this->data[$key] = $value;
	}

	// 引入模板
	public function display($template, $ext = '.html')
	{
		$template_path = $template . $ext;
		if(file_exists ( $template_path )){
			foreach($this->data as $k=>$v){
				$this->__set ( $k, $v );
			}
			include $template_path;
		}
	}
}
class CTOCODE_MVC
{
	protected static $ins = null;
	protected function __construct()
	{
		ob_start (); // 打开缓冲区
		static::autoLoad ();
		register_shutdown_function ( array(
			'Factory',
			'last'
		) ); // 程序执行完后执行
	}
	public static function getIns($url)
	{
		if(self::$ins instanceof self){
			self::$ins->execute ( $url );
		}else{
			self::$ins = new self ();
			self::$ins->execute ( $url );
		}
	}
	protected function set_router($uri)
	{
		/*
		 * $uri='index/admin';
		 * // $route_regex='#^(?:(?P[^/.,;?\n]++)(?:/(?P[^/.,;?\n]++)(?:/(?P[^/.,;?\n]++))?)?)?$#uD';
		 * //$route_regex='#^captcha(?:/(?P[^/.,;?\n]++))?$#uD';
		 * $route_regex='#^(?:(?P<controller>[^/.,;?\n]++)(?:/(?P<action>[^/.,;?\n]++)(?:/(?P<id>[^/.,;?\n]++))?)?)?$#uD';
		 * if ( ! preg_match($route_regex, $uri, $matches)) {
		 * echo '没有匹配上';
		 * } else {
		 * print_r($matches);
		 * }
		 */
		$arr = array();
		if(strpos ( $uri, '?' ) === false){
			$pathinfo = array();

			if(! empty ( $_SERVER['PATH_INFO'] )){
				$arg = trim ( $_SERVER['PATH_INFO'], '/' );
			}else if($request_uri = parse_url ( $_SERVER['REQUEST_URI'], PHP_URL_PATH )){
				// 有效的URL路径发现,设置它。
				$arg = trim ( $request_uri, '/' );
			}
			if(stripos ( $arg, '/' ) !== false){
				$pathinfo = explode ( '/', $arg );
			}
			$arr['ctrolloer'] = isset ( $pathinfo[0] ) ? $pathinfo[0] : 'index';
			array_shift ( $pathinfo );
			$arr['action'] = isset ( $pathinfo[0] ) ? $pathinfo[0] : 'index';
			array_shift ( $pathinfo );
			$num = count ( $pathinfo );
			for($i = 0;$i < $num;$i += 2){
				$arr['param'][$pathinfo[$i]] = $pathinfo[$i + 1];
			}

			return $arr;
		}else{
			$ln = parse_url ( $uri );
			parse_str ( $ln['query'], $array );
			$arr['ctrolloer'] = $array['m'] ? strtolower ( $array['m'] ) : 'index';
			$arr['action'] = $array['c'] ? strtolower ( $array['c'] ) : 'index';
			unset ( $array['m'] );
			unset ( $array['c'] );
			$arr['param'] = $array;
			return $arr;
		}
	}
	protected function execute($url)
	{
		$arr = self::$ins->set_router ( $url );
		$_GET = isset ( $arr['param'] ) ? $arr['param'] : array();
		$ProductType = ucfirst ( $arr['ctrolloer'] ) . 'Action';
		if(class_exists ( $ProductType )){
			$p = new $ProductType ();
			$p->$arr['action'] ( $arr );
		}else{
			throw new Exception ( "Error Processing Request", 1 );
		}
	}
	public static function last() // echo '脚本执行完了'. PHP_EOL;
	{
		$info = ob_get_contents (); // 得到缓冲区的内容并且赋值给$info
		$file = fopen ( $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'indexs.html', 'w' ); // /打开文件 指定缓存目录
		fwrite ( $file, $info ); // 写入信息到info.txt
		fclose ( $file ); // 关闭文件info.txt
		                  // ob_end_clean();
		ob_end_flush ();
		flush ();
	}
	static public function loadFile($className)
	{
		$dir = array();
		$dir[0] = '/application/'; // 指定多个目录
		$dir[1] = '/module/';
		$file_arr = self::find_file ( $dir, $className );
		foreach($file_arr as $value){
			if(is_file ( $value )){
				return include_once $value;
			}else{
				throw new \Exception ( '找不到' . $className . '类' );
			}
		}
	}
	public static function find_file($paths = array(), $name)
	{
		$name = ucfirst ( $name ) . '.php';
		$found = array();
		foreach($paths as $dir){
			if(is_file ( $dir . $name )){
				$found[] = $dir . $name;
			}
		}
		return $found;
	}

	// 注册自动装载机
	static public function autoLoad()
	{
		spl_autoload_register ( array(
			__CLASS__,
			'loadFile'
		) );
	}

	// 注册自动装载机2 //如有必要注册多个自动加载函数
	static public function autoLoad2()
	{
		spl_autoload_register ( array(
			__CLASS__,
			'loadFile2'
		) );
	}
}
?>
<script>
    var x=1,y=z=0;
    function add(n){
      n=n+1;
    }
    y=add(x);
    function add(n){
      n=n+3;
    }
z=add(x); 
</script>