<?php
namespace ctocode\phpframe\library;

class CtoRestfulApi extends CtoRestfulApiResponse
{

    // 返回结果
    public function sendResponse($result_data = null, $send_type = 'json')
    {
        // $statusCode = $result_data['status'];
        // $data = $result_data['status'];
        // $statusMessage = $this->getHttpStatusMessage ( $statusCode );
        // 输出结果
        // header ( $this->httpVersion . " " . $statusCode . " " . $statusMessage );
        $requestContentType = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : $_SERVER['HTTP_ACCEPT'];
        // TODO 目前强制为 json 返回
        $requestContentType = 'application/json';

        if (strpos($requestContentType, 'application/json') !== false) {
            header('Content-Type: application/json; charset=utf-8');
            echo $this->encodeJson($result_data);
            exit();
        } else if (strpos($requestContentType, 'application/') !== false) {
            header("Content-Type: application/xml");
            echo $this->encodeXml($data);
            exit();
        } else {
            // header ( 'Content-type: text/html; charset=utf-8' );
            header("Content-Type: application/html");
            echo $this->encodeHtml($data);
            exit();
        }
    }

    /**
     * 防止跨域,解决处理跨域问题
     *
     * @param array $MethodSett
     *            允许访问的方式，默认get，post
     * @param array $HeadersSett
     *            允许传递的 header 参数
     */
    public function doCrossDomain($AllowHeaders = array(), $RequestHeaders = array(), $Method = array(), $CacheOpt = array())
    {
        $this->doAllowHeaders($AllowHeaders);
        $this->doAllowMethods($Method);
        $this->doRequestHeaders($RequestHeaders);
        /* ========== 清空缓存 ========== */
        header("Cache-Control:no-cache");
        // header ( 'Cache-Control: max-age=0' );
        header('X-Accel-Buffering:no'); // 关闭输出缓存
        header('Pragma:no-cache');
    }

    /**
     * 准许跨域请求来源访问
     *
     * @param array $diyOpt
     */
    public function doAllowOrigin($diyOpt = array())
    {
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : ''; // 跨域访问的时候才会存在此字段
        if (in_array($origin, $diyOpt)) {
            header('Access-Control-Allow-Origin:' . $origin);
        } else {
            header('Access-Control-Allow-Origin: * ');
        }
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 1800');
    }

    /**
     * 允许传递的 请求 参数
     *
     * @param array $diyOpt
     */
    public function doRequestHeaders($diyOpt = array())
    {
        $allOpt = array_merge(array(
            'Origin',
            'X-Requested-With',
            'Content-Type',
            'Accept'
        ), $diyOpt);
        header('Access-Control-Request-Headers:' . implode(',', $allOpt));
    }

    /**
     * 允许传递的 header 参数
     *
     * @param array $diyOpt
     */
    public function doAllowHeaders($diyOpt = array())
    {
        $allOpt = array_merge(array(
            'Origin',
            'X-Requested-With',
            'Content-Type',
            'Accept',
            'Authorization'
        ), $diyOpt);
        header('Access-Control-Allow-Headers:' . implode(',', $allOpt));
    }

    /**
     * 允许访问的方式
     *
     * @param array $diyOpt
     */
    public function doAllowMethods($diyOpt = array())
    {
        $allOpt = array_merge(array(
            'GET',
            'POST'
            // 'PUT',
            // 'DELETE',
            // 'OPTIONS'
        ), $diyOpt);
        header('Access-Control-Allow-Methods:' . implode(',', $allOpt));
    }

    public function doc($docApiBaseUrl = '', $docApiData = '')
    {
        $version = 'v1';
        $htmls = '';
        $htmls .= $this->docApiStyle();

        $htmls .= "<p>接口：<b>{$docApiData['name']}</b></p>";
        $htmls .= "<p>URL：http://{$docApiBaseUrl}/{$docApiData['mod']}/{$docApiData['con']}</p>";
        $htmls .= '可选参数：';
        foreach ($docApiData['api'] as $key => $val) {
            $htmls .= "<p>&nbsp;&nbsp;{$key}=（{$val['type']}） {$val['comment']}</p>";
        }
        $htmls .= '<p>&nbsp;</p>';
        return $htmls;
    }

    public function getHypermedia($baseUrl, $apiMenu)
    {
        $version = 'v1';
        $htmls = '';
        $htmls .= $this->docApiStyle();

        foreach ($apiMenu as $key => $val) {
            foreach ($val['modules'] as $key2 => $val2) {
                foreach ($val2['controllers'] as $kye3 => $val3) {
                    if (empty($val3['api'])) {
                        continue;
                    }
                    $api_menu = $val3['api'];
                    $htmls .= '<p>接口：<b>' . "{$val['title']}_{$val2['name']}_{$val3['name']}" . '</b></p>';
                    $htmls .= '<p>URL：' . "{$baseUrl}{$version}/{$val['apps']}/{$val2['mod']}/{$val3['con']}{$api_menu['link']}" . '</p>';
                    if (! empty($api_menu['param'])) {
                        $htmls .= "参数：";
                        foreach ($api_menu['param'] as $k => $v) {
                            $htmls .= "<p>&nbsp;&nbsp;{$k}=({$v['type']})  {$v['remarks']}</p>";
                        }
                    }
                    $htmls .= '<p>&nbsp;</p>';
                }
            }
        }
        return $htmls;
    }

    // 文档风格
    protected function docApiStyle()
    {
        $style = '';
        $style .= '<style>';
        $style .= 'body{margin: 20px;font: 13px Helvetica Neue,Helvetica,PingFang SC,\5FAE\8F6F\96C5\9ED1,Tahoma,Arial,sans-serif;font-family: "Microsoft YaHei" ! important;}';
        $style .= 'p{margin:0;padding:0;margin-bottom:2px;letter-spacing:1px;} ';
        $htmls .= '</style>';
        return $style;
    }

    private function getDocLink($show_type = '')
    {
        $api_doc = $this->getHypermedia();
        if ($show_type == 'html') {
            $htmls = '';
            $htmls .= '<pre style="word-wrap: break-word; white-space: pre-wrap;">';
            $htmls .= '<p style="font-size: 16px;padding: 0;margin: 0;">提示1：可以点击url，查看更多详细api接口</p>';
            $htmls .= '<p style="font-size: 16px;padding: 0;margin: 0;">提示2：数据只返回status 和 data</p>';
            $htmls .= '<p>{</p>';
            foreach ($api_doc as $key => $val) {
                $htmls .= '<p>';
                $htmls .= '   "' . $val['sign'] . '" : ';
                $htmls .= '"<a href="' . $val['link'] . '" target="_blank">';
                $htmls .= $val['link'] . '</a>",';
                $htmls .= ' /* 【' . $val['title'] . '】_' . $val['remarks'] . '*/';
                $htmls .= '</p>';
            }
            $htmls .= '}</pre>';
            exit($htmls);
        } else {
            header('Content-Type:application/json; charset=utf-8');
            exit(json_encode($api_doc));
        }
    }
}