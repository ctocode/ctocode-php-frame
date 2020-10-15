<?php
// 核心启动类
class CtoFramework
{
	// 让项目启动 static静态方法 配合Framework::run();
	public static function run()
	{
		// echo "running";
		// echo __DIR__;//D:\WWW\xxx\framework\core
		// echo getcwd () ;//D:\WWW\xxx
		// 调用下面方法
		self::init();
		self::autoload();
		self::router();
	}
	// 初始化方法
	public static function init()
	{
		// 前后台的控制器和视图目录怎么定义？需要解析url携带的参数，如：p=admin&c=goods&a=add才可以确定前后台的路径
		// isset：判断是否有值 ucfirst（）：首字母大写 p是平台
		define("PLATFORM", isset($_REQUEST['p']) ? $_REQUEST['p'] : "home");

		define("CONTROLLER", isset($_REQUEST['c']) ? ucfirst($_REQUEST['c']) : "Index");
		define("ACTION", isset($_REQUEST['a']) ? $_REQUEST['a'] : "index");
		// CUR表示当前的
		define("CUR_CONTROLLER_PATH", CONTROLLER_PATH . PLATFORM . DS);
		define("CUR_VIEW_PATH", VIEW_PATH . PLATFORM . DS);
	}
	// 路由方法
	public static function router()
	{
		// 确定类名和方法名
		$controller_name = CONTROLLER . "Controller"; // 如GoodsController
		$action_name = ACTION . "action"; // 如addAction
		// 实例化控制器，然后调用相应的方法
		$controller = new $controller_name();
		$controller->$action_name;
	}

	// 自动加载：提到自动加载，一定会想到__autoload，魔术函数
	// 它是一个普通的函数，不是类的方法。如果直接在类中定义一个__autoload的方法，它并不会实现自动加载。
	// 在类中定义一个方法，然后将其注册为自动加载方法。（推荐）--- spl_autoload_regisiter
	// 注意spl_autoload_register函数的用法，如果是普通函数，只需要填写函数名即可
	// 如果是类中的方法，需要告知是哪个类的哪个方法，使用数组的形式传递 _CLASS__表示当前类
	// 在我的自动加载方法中，只负责加载 application下面的 控制器类和模型类。
	// 注册加载方法
	public static function autoload()
	{
		// echo __CLASS__;//Framework
		spl_autoload_regisiter(array(
			__CLASS__,
			"load"
		));
	}
	// 加载函数
	public static function load($classname)
	{
		// 只负责加载application下面的控制器和模型类如GoodsController，AdminModel
		// {}做等体解析
		if (substr($classname, -10) == 'Controller') {
			require CUR_CONTROLLER_PATH . "{$classname}.class.php";
		} elseif (ubstr($classname, -5) == 'Model') {
			require MODEL_PATH . "{$classname}.class.php";
		} else {
			// 其他情况
		}
	}
}
