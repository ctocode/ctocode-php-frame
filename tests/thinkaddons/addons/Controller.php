<?php

// +----------------------------------------------------------------------
// | thinkphp5 Addons [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.zzstudio.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Byron Sampson <xiaobo.sun@qq.com>
// +----------------------------------------------------------------------
namespace think\addons;

use think\Request;
use think\Config;
use think\Loader;

/**
 * 插件基类控制器
 * Class Controller
 * @package think\addons
 */
class Controller extends \think\Controller
{
	protected $themes = 'default';
	protected $time;

	// 当前插件操作
	protected $addon = null;
	protected $controller = null;
	protected $action = null;
	// 当前template
	protected $template;
	// 模板配置信息
	protected $config = [
		'type' => 'Think',
		'view_path' => '',
		// 'view_suffix' => 'html',
		'strip_space' => true,
		'view_depr' => DS,
		'tpl_begin' => '{',
		'tpl_end' => '}',
		'taglib_begin' => '{',
		'taglib_end' => '}'
	];

	/**
	 * 架构函数
	 * @param Request $request Request对象
	 * @access public
	 */
	public function __construct(Request $request = null)
	{
		// 生成request对象
		$this->request = is_null($request) ? Request::instance() : $request;
		// 初始化配置信息
		$this->config = Config::get('template') ?: $this->config;
		// 处理路由参数
		$route = $this->request->param('route', '');
		$param = explode('-', $route);
		// 是否自动转换控制器和操作名
		$convert = \think\Config::get('url_convert');
		// 格式化路由的插件位置
		$this->action = $convert ? strtolower(array_pop($param)) : array_pop($param);
		$this->controller = $convert ? strtolower(array_pop($param)) : array_pop($param);
		$this->addon = $convert ? strtolower(array_pop($param)) : array_pop($param);
		// 生成view_path
		$view_path = $this->config['view_path'] ?: 'view';
		// 重置配置
		Config::set('template.view_path', ADDON_PATH . $this->addon . DS . $view_path . DS);
		parent::__construct($request);
	}
	public function _initialize()
	{
		$this->time = $_SERVER['REQUEST_TIME'];
		/*
		 * 当前模块 URL
		 */
		/*
		 * 当前控制器 URL
		 */
		$controller_name = $this->controller;
		// $controller_url_name = strtolower ( preg_replace ( '/(?<=[a-z])([A-Z])/', '_$1', $controller_name ) );
		/*
		 * 处理 方法，减轻tp5 强制错误级别
		 */
		$action = $this->action;
		$action_skip_arr = array(
			'edit'
		);
		if (in_array($action, $action_skip_arr)) {
			error_reporting(E_ERROR | E_WARNING | E_PARSE);
		}
		if (!empty($action)) {
			// error_reporting ( E_ERROR | E_WARNING | E_PARSE );
			error_reporting(0);
		}
	}
	/**
	 * 加载模板输出
	 * @access protected
	 * @param string $template 模板文件名
	 * @param array $vars 模板输出变量
	 * @param array $replace 模板替换
	 * @param array $config 模板参数
	 * @return mixed
	 */
	protected function fetch($template = '', $vars = [], $replace = [], $config = [])
	{
		// 生成view_path
		$controller = Loader::parseName($this->controller);
		$depr = $this->config['view_depr'];
		if ('think' == strtolower($this->config['type']) && $controller && 0 !== strpos($template, '/')) {
			$template = str_replace([
				'/',
				':'
			], $depr, $template);
		}
		if ('' == $template) {
			// 如果模板文件名为空 按照默认规则定位
			$template = DS . str_replace('.', DS, $controller) . $depr . $this->action;
		} elseif (false === strpos($template, $depr)) {
			$template = DS . str_replace('.', DS, $controller) . $depr . $template;
		}
		return parent::fetch($template, $vars, $replace, $config);
	}
}
