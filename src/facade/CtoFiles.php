<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
declare(strict_types = 1)
	;

namespace ctocode\facade;

/**
 * @see \ctocode\library\CtoFiles
 * @package think\facade
 * @mixin \ctocode\library\CtoFiles
 */
class CtoFiles extends CtoFacade
{
	/**
	 * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
	 * @access protected
	 * @return string
	 */
	protected static function getFacadeClass()
	{
		return '\ctocode\library\CtoFiles';
	}
}
