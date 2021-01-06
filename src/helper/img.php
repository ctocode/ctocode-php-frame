<?php

/**
 * 【ctocode】      常用函数 - img相关处理
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

/**
 * 图片验证
 * @author ctocode-zhw
 * @version 2016-08-06 
 * @param string $img_path 图片路径
 * @return array(
 * 			   extension 类型
 *             filename  名字
 *             basename  文件名
 * 		   )
 */
function ctoImgCheck($img_path = '')
{
    // 获取图片信息大小
    $imgData = getimagesize($img_path, $info);
    if (!in_array($imgData['mime'], array(
        'image/pjpeg',
        'image/jpeg',
        'image/gif',
        'image/png',
        'image/x-png',
        'image/xpng',
        'image/bmp',
        'image/wbmp'
    ), true)) {
        return array(
            'type' => 'no',
            'msg' => '图片类型错误'
        );
    }
    // 获取后缀名
    $_mime = explode('/', $imgData['mime']);
    $_ext = '.' . end($_mime);
    $imgData2 = pathinfo(parse_url($img_path)['path']);
    return array(
        'type' => 'ok',
        'filename' => $imgData2['filename'],
        'extension' => $_ext,
        'basename' => $imgData2['basename'],
        'imgData' => $imgData
    );
}
/*
 * 图片上传
 * $save_path 保存路径
 */
function ctoImgDoPath($save_path, $save_type = 1)
{
    // 存储路径,保存类别 ,默认： 根路径/年/月日
    if ($save_type == 1) {
        $patharr = array(
            $save_path
        );
    } elseif ($save_type == 2) {
        $patharr = array(
            $save_path
        );
        $return_path = $patharr[0];
    }
    // 如果路径不存在,创建文件夹
    foreach ($patharr as $val) {
        if (!file_exists($val)) {
            if (!mkdir($val, 0777, true)) { // 如果指定文件夹不存在，则创建文件夹,权限0777
                exit('创建保存文件目录失败,请联系管理员检查目录权限');
            }
        }
    }
    return TRUE;
}

// 远程下载2
function ctoImgRemoteDown2($url, $path = 'images/')
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    $file = curl_exec($ch);
    curl_close($ch);
    $filename = pathinfo($url, PATHINFO_BASENAME);
    $resource = fopen($path . $filename, 'a');
    fwrite($resource, $file);
    fclose($resource);
}
/**
 * 远程图片下载
 * @author ctocode-zhw
 * @version 2018-05-31
 * @param string $imgUrl  图片url地址
 * @param string $saveDir 本地存储路径 默认存储在当前路径
 * @param string $fileName 图片存储到本地的文件名 ,当保存文件名称为空时则使用远程文件原来的名称
 * @return mixed
 */
function ctoImgRemoteDown($imgUrl, $saveDir = './', $fileName = null)
{
    if (trim($imgUrl) == '') {
        return false;
    }
    if (preg_match("#^//\w+#", $imgUrl)) {
        $imgUrl = 'http:' . $imgUrl;
    }
    if (preg_match('/(http:\/\/)|(https:\/\/)/i', $imgUrl)) {
        $imgUrl = preg_replace('/(http:\/\/)|(https:\/\/)/i', 'http://', $imgUrl);
    }
    // 验证图片
    $imgCheck = ctoImgCheck($imgUrl);
    if ($imgCheck['type'] != 'ok')
        return $imgCheck;

    if (empty($fileName)) {
        // 生成唯一的文件名
        // $fileName = uniqid ( time (), true ) . $imgCheck['extension'];
        $fileName = $imgCheck['basename'];
    }

    // 开始抓取远程图片
    ob_start();
    readfile($imgUrl);
    $imgInfo = ob_get_contents();
    ob_end_clean();

    if (!file_exists($saveDir)) {
        mkdir($saveDir, 0777, true);
    }
    $fp = fopen($saveDir . $fileName, 'a');
    $imgLen = strlen($imgInfo); // 计算图片源码大小
    $_inx = 1024; // 每次写入1k
    $_time = ceil($imgLen / $_inx);
    for ($i = 0; $i < $_time; $i++) {
        fwrite($fp, substr($imgInfo, $i * $_inx, $_inx));
    }
    fclose($fp);

    return array(
        'ext' => $imgCheck['extension'],
        'file_name' => $fileName,
        'save_path' => $saveDir . $fileName
    );
}
/**
 * 图片 裁剪
 * @author ctocode-zhw
 * @version 2015-08-16
 * @param string $oldfile   原图
 * @param string $newfile   保存位置 留空则输入图片头
 * @param array  $cutOpt = array( 裁剪参数
 * 					'width'=>'',裁剪宽度
 * 					'height'=>'',裁剪高度
 * 					'x'=>'',裁剪起点X坐标
 * 					'y'=>'',裁剪起点Y坐标
 * 				  ) 
 * 				 array	
 * @param number $quality  图片质量
 * @param boolean $sharp   是否锐化
 * @return boolean
 */
function ctoImgCut($oldfile = '', $newfile = '', $cutOpt = array(
    'width' => 100,
    'height' => 100,
    'x' => 0,
    'y' => 0
), $quality = 100, $sharp = false)
{
    if (!is_string($oldfile) || $oldfile == '' || !is_file($oldfile)) {
        return false;
    }
    // 验证图片
    $imgCheck = ctoImgCheck($oldfile);
    if ($imgCheck['type'] != 'ok')
        return false;

    $old_width = $imgCheck['imgData'][0];
    $old_height = $imgCheck['imgData'][1];
    if (!$old_width || !$old_height) {
        return false;
    }
    switch ($imgCheck['imgData']['mime']) {
        case 'image/gif':
            $creationFunction = 'ImageCreateFromGif';
            $outputFunction = 'ImagePng';
            $mime = 'image/png';
            $doSharpen = false;
            break;
        case 'image/x-png':
        case 'image/png':
            $creationFunction = 'ImageCreateFromPng';
            $outputFunction = 'ImagePng';
            $doSharpen = false;
            break;
        default:
            $creationFunction = 'ImageCreateFromJpeg';
            $outputFunction = 'ImageJpeg';
            $doSharpen = true;
            break;
    }
    if (function_exists($creationFunction) && function_exists($outputFunction)) {
        $save_width = min($old_width, $cutOpt['width']);
        $save_height = min($old_height, $cutOpt['height']);
        if ($cutOpt['x'] + $save_width >= $old_width) {
            $cutOpt['x'] = $old_width - $save_width;
        }
        $cutOpt['x'] = max(0, $cutOpt['x']);
        if ($cutOpt['y'] + $save_height >= $old_height) {
            $cutOpt['y'] = $old_height - $save_height;
        }
        $cutOpt['y'] = max(0, $cutOpt['y']);
        $src = $creationFunction($oldfile);
        $dst = imagecreatetruecolor($save_width, $save_height);
        if (function_exists('ImageCopyResampled')) {
            imagecopyresampled($dst, $src, 0, 0, $cutOpt['x'], $cutOpt['y'], $save_width, $save_height, $save_width, $save_height);
        } else {
            imagecopyresized($dst, $src, 0, 0, $cutOpt['x'], $cutOpt['y'], $save_width, $save_height, $save_width, $save_height);
        }
        if ($sharp) {
            $dst = imgSharp($dst, 0.2);
        }
        if ($newfile != '') {
            if ($outputFunction == 'ImageJpeg') {
                $outputFunction($dst, $newfile, $quality);
            } else {
                $outputFunction($dst, $newfile);
            }
        } else {
            ob_start();
            if ($outputFunction == 'ImageJpeg') {
                $outputFunction($dst, null, $quality);
            } else {
                $outputFunction($dst);
            }
            $data = ob_get_contents();
            ob_end_clean();
            header("Content-type: $mime");
            header('Content-Length: ' . strlen($data));
            echo $data;
        }
        imagedestroy($dst);
        imagedestroy($src);
        return true;
    }
    return false;
}
function ctoImgWatermark($groundImage, $waterPos = 0, $waterImage = "", $waterText = "", $textFont = 14, $textColor = "#FF0000")
{
    return imagewatermark_func($groundImage, $groundImage, $waterPos, $waterImage, $waterText, 95, $textFont, $textColor);
}
