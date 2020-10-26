<?php

namespace ctocode\facade;

use think\Facade;

class CtoHttp extends Facade
{
    protected static function getFacadeClass()
    {
        return 'ctocode\library\Http';
    }
}
