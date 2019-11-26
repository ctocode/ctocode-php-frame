<?php

namespace ctocode\phpframe\helper;
/**
 * 【ctocode】      常用函数 - str相关处理
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
 * @action 生成一个随机字符串
 * @author ctocode-zhw
 * @version 2017-08-03
 * @email  343196936@qq.com
 * @param string $type 随机字符串类型
 * @param number $length 随机字符串长度
 * @return string 随机码的长度
 */
function ctoStrRand($type, $length)
{
	$random_str = '';
	switch($type)
	{
		// 生成Hash字符串
		case 'hash':
			break;
		case 'num':
			$num = "";
			for($i = 0;$i < $length;$i ++){
				$id = rand ( 0, 9 );
				$num = $num . $id;
			}
			$random_str = $num;
			break;
		case 'id':
			date_default_timezone_set ( 'PRC' );
			$random_str = date ( 'YmdHis', time () );
			$random_str = $random_str . rand ( 1000, 999999 );
			break;
		case 'md5':
			break;
		case 'code': // 生成一个随机码
			$random_code = "";
			$strChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
			$max = strlen ( $strChars ) - 1;
			mt_srand ( ( double ) microtime () * 1000000 );
			for($i = 0;$i < $length;$i ++){
				$random_code .= $strChars[mt_rand ( 0, $max )];
			}
			$random_str = $random_code;
			break;
		case 'captcha':
			break;
	}
	return $random_str;
}
/**
 * 生产  $length 位随机码  函数
 * @param $length
 * @param bool|false $numeric
 * @return string 生成指定长度的唯一随机字符串并返回
 * 
 */
function ctoStrRandom($length = 6, $numeric = false, $exper = '')
{
	// PHP_VERSION < '4.2.0' ? mt_srand ( ( double ) microtime () * 1000000 ) : mt_srand ();
	$sign = microtime () . $_SERVER['DOCUMENT_ROOT'];
	// $sign = $exper . print_r ( $_SERVER, 1 ) . microtime ();
	$seed = base_convert ( md5 ( $sign ), 16, $numeric ? 10 : 35 );
	$seed = $numeric ? (str_replace ( '0', '', $seed ) . '012340567890') : ($seed . 'zZ' . strtoupper ( $seed ));
	if($numeric){
		$hash = '';
	}else{
		$hash = chr ( rand ( 1, 26 ) + rand ( 0, 1 ) * 32 + 64 );
		$length --;
	}
	$max = strlen ( $seed ) - 1;
	for($i = 0;$i < $length;$i ++){
		$hash .= $seed[mt_rand ( 0, $max )];
	}
	return $hash;
}

// 驼峰命名转下划线命名
function ctoStrToUnderScore($str)
{
	$dstr = preg_replace_callback ( '/([A-Z]+)/', function ($matchs)
	{
		return '_' . strtolower ( $matchs[0] );
	}, $str );
	return trim ( preg_replace ( '/_{2,}/', '_', $dstr ), '_' );
}

// 下划线命名到驼峰命名
function ctoStrToCamelCase($str)
{
	$array = explode ( '_', $str );
	$result = $array[0];
	$len = count ( $array );
	if($len > 1){
		for($i = 1;$i < $len;$i ++){
			$result .= ucfirst ( $array[$i] );
		}
	}
	return $result;
}
function ctoStrGethashv($key)
{
	$vstr_a = $vstr = '';
	$n = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	for($i = 0;$i < 25;$i ++){
		$j = mt_rand ( 0, (strlen ( $n ) - 1) );
		$vstr_a .= $n{$j};
	}
	$vstr_b = md5 ( $key . $vstr_a );
	$vstr = $vstr_b . '-' . $vstr_a;
	return $vstr;
}
// MD5加密截取 默认24位
function ctoStrNmd5($str, $len = 24, $start = 5)
{
	// 此值不要更改 否则会员会登录失败
	$hash = 'ctocode!@$=#=%+#com';
	return substr ( md5 ( $str . $hash ), $start, $len );
}
// 验证Hash字符串是否合法
function ctoStrCheckhashv($v, $key)
{
	if(trim ( $v ) == '' || ! strpos ( $v, '-' )){
		return false;
	}
	$str = explode ( '-', $v );
	if($str[0] != md5 ( $key . $str[1] )){
		return false;
	}
	return true;
}
/**
 *  出现科学计数法，还原成字符串
 * @author ctocode-zhw
 * @param string $num
 * @return mixed
 */
function ctoStrNumToStr($num)
{
	if(stripos ( $num, 'e' ) === false)
		return $num;
	$num = trim ( preg_replace ( '/[=\'"]/', '', $num, 1 ), '"' );
	$result = "";
	while($num > 0){
		$v = $num - floor ( $num / 10 ) * 10;
		$num = floor ( $num / 10 );
		$result = $v . $result;
	}
	return $result;
}
/**
 *  手机号中间位数用****代替
 * @author ctocode-zwj
 * @param string $phone
 * @return mixed
 */
function ctoStrHideTel($phone, $num = 4)
{
	$IsWhat = preg_match ( '/(0[0-9]{2,3}[\-]?[2-9][0-9]{6,7}[\-]?[0-9]?)/i', $phone ); // 固定电话
	if($IsWhat == 1){
		return preg_replace ( '/(0[0-9]{2,3}[\-]?[2-9])[0-9]{3,4}([0-9]{3}[\-]?[0-9]?)/i', '$1' . str_repeat ( '*', $num ) . '$2', $phone );
	}else{
		return preg_replace ( '/(1[3|4|5|6|7|8|9]{1}[0-9])[0-9]{4}([0-9]{4})/i', '$1' . str_repeat ( '*', $num ) . '$2', $phone );
	}
}
function sum()
{
	$ary = func_get_args ();
	$sum = 0;
	foreach($ary as $int){
		$sum += intval ( $int );
	}
	return $sum;
}
/**
 * 数字转中文
 * @param number $num
 * @return string|number
 */
function ctoStrNum2cn($num = 0)
{
	$cns = array(
		'零',
		'一',
		'二',
		'三',
		'四',
		'五',
		'六',
		'七',
		'八',
		'九',
		'十',
		'十一',
		'十二',
		'十三',
		'十四',
		'十五',
		'十六',
		'十七',
		'十八',
		'十九',
		'二十'
	);
	$num = intval ( $num );
	return isset ( $cns[$num] ) ? $cns[$num] : $num;
}

// 数字转大写
function ctoStrNumToRmb($num)
{
	$c1 = "零壹贰叁肆伍陆柒捌玖";
	$c2 = "分角元拾佰仟万拾佰仟亿";
	// 精确到分后面就不要了，所以只留两个小数位
	$num = round ( $num, 2 );
	// 将数字转化为整数
	$num = $num * 100;
	if(strlen ( $num ) > 10){
		return "金额太大，请检查";
	}
	$i = 0;
	$c = "";
	while(1){
		if($i == 0){
			// 获取最后一位数字
			$n = substr ( $num, strlen ( $num ) - 1, 1 );
		}else{
			$n = $num % 10;
		}
		// 每次将最后一位数字转化为中文
		$p1 = substr ( $c1, 3 * $n, 3 );
		$p2 = substr ( $c2, 3 * $i, 3 );
		if($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))){
			$c = $p1 . $p2 . $c;
		}else{
			$c = $p1 . $c;
		}
		$i = $i + 1;
		// 去掉数字最后一位了
		$num = $num / 10;
		$num = ( int ) $num;
		// 结束循环
		if($num == 0){
			break;
		}
	}
	$j = 0;
	$slen = strlen ( $c );
	while($j < $slen){
		// utf8一个汉字相当3个字符
		$m = substr ( $c, $j, 6 );
		// 处理数字中很多0的情况,每次循环去掉一个汉字“零”
		if($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零'){
			$left = substr ( $c, 0, $j );
			$right = substr ( $c, $j + 3 );
			$c = $left . $right;
			$j = $j - 3;
			$slen = $slen - 3;
		}
		$j = $j + 3;
	}
	// 这个是为了去掉类似23.0中最后一个“零”字
	if(substr ( $c, strlen ( $c ) - 3, 3 ) == '零'){
		$c = substr ( $c, 0, strlen ( $c ) - 3 );
	}
	// 将处理的汉字加上“整”
	if(empty ( $c )){
		return "零元整";
	}else{
		return $c . "整";
	}
}

/**@action 截取短数组---文章简介等
 * @author Name zhw Email 343196936@qq.com Data 2016年3月28日
 * @param string $str 需要截取的数组
 * @param int $max_length 截取的长度
 * @param string $istags 是否去除html标签
 * @return mixed
 */
// 中文字符串截取 长度指字节数 字母一字节 汉字两字节
function ctoStrStrcut($str, $max_length, $istags = true)
{
	/* 去除html 标签,并且 截取 一段文字 */
	if($istags != false){
		$content = strip_tags ( $str );
	}
	// 按照字节来划分(不会出现乱码)
	$str = mb_strcut ( $content, 0, $max_length, 'utf-8' );
	// $str = mb_substr ( $str, 0, $max_length, 'utf-8' );// 函数2
	return $str;
}
function ctoStrSubstring($str, $len, $dot = '')
{
	$strlen = strlen ( $str );
	if($strlen <= $len)
		return $str;
	$str = str_replace ( array(
		'&nbsp;',
		'&amp;',
		'&quot;',
		'&#039;',
		'&ldquo;',
		'&rdquo;',
		'&mdash;',
		'&lt;',
		'&gt;',
		'&middot;',
		'&hellip;'
	), array(
		' ',
		'&',
		'"',
		"'",
		'“',
		'”',
		'—',
		'<',
		'>',
		'·',
		'…'
	), $str );
	$rs = '';
	$web_lang = 'gbk';
	if(strtolower ( $web_lang ) == 'gb2312' || strtolower ( $web_lang ) == 'gbk'){
		$dotlen = strlen ( $dot );
		$maxi = $len - $dotlen - 1;
		for($i = 0;$i < $maxi;$i ++){
			$rs .= ord ( $str[$i] ) > 127 ? $str[$i] . $str[++ $i] : $str[$i];
		}
	}else{
		$n = $tn = $noc = 0;
		while($n < $strlen){
			$t = ord ( $str[$n] );
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)){
				$tn = 1;
				$n ++;
				$noc ++;
			}elseif(194 <= $t && $t <= 223){
				$tn = 2;
				$n += 2;
				$noc += 2;
			}elseif(224 <= $t && $t < 239){
				$tn = 3;
				$n += 3;
				$noc += 2;
			}elseif(240 <= $t && $t <= 247){
				$tn = 4;
				$n += 4;
				$noc += 2;
			}elseif(248 <= $t && $t <= 251){
				$tn = 5;
				$n += 5;
				$noc += 2;
			}elseif($t == 252 || $t == 253){
				$tn = 6;
				$n += 6;
				$noc += 2;
			}else{
				$n ++;
			}
			if($noc >= $len)
				break;
		}
		if($noc > $len)
			$n -= $tn;
		if($dot != '')
			$n -= strlen ( $dot );
		$rs = substr ( $str, 0, $n );
	}
	$rs = str_replace ( array(
		'&',
		'"',
		"'",
		'<',
		'>'
	), array(
		'&amp;',
		'&quot;',
		'&#039;',
		'&lt;',
		'&gt;'
	), $rs );
	return $rs . $dot;
	if(strpos ( $str, '&lt;' ) !== false){
		$str = str_replace ( '&lt;', '<', $str );
	}
	if(strpos ( $str, '&gt;' ) !== false){
		$str = str_replace ( '&gt;', '>', $str );
	}
	if($_glb['web_lang'] == 'UTF-8'){
		$str = ctoStrIconv ( $str, 'UTF-8', 'GBK' );
		$strcut = ctoStrIconv ( $strcut, 'GBK', 'UTF-8' );
	}
	return $strcut . ($rdot ? $dot : '');
	return mb_substr ( $str, 0, $len, 'GBK' ) . $dot;
	return mb_substr ( $str, 0, $len, 'UTF-8' ) . $dot;
}
function ctoStrIconv($str, $in_charset, $out_charset = '')
{
	echo "das";
	exit ();
	$in_charset = strtoupper ( trim ( $in_charset ) );
	$out_charset = strtoupper ( trim ( $out_charset ) );
	if($in_charset == 'UTF-8' && ($out_charset == 'GBK' || $out_charset == 'GB2312')){
		return utf_gbk ( $str );
	}elseif($out_charset == 'UTF-8' && ($in_charset == 'GBK' || $in_charset == 'GB2312')){
		return gbk_utf ( $str );
	}elseif($in_charset == 'GBK' && $out_charset == 'BIG5'){
		return big5_gbk ( $str );
	}elseif($in_charset == 'BIG5' && $out_charset == 'GBK'){
		return gbk_big5 ( $str );
	}elseif($in_charset == 'BIG5' && $out_charset == 'UTF-8'){
		return gbk_utf ( big5_gbk ( $str ) );
	}elseif($in_charset == 'UNICODE'){
		return un_gbk ( $str );
	}elseif($in_charset == 'PINYIN'){
		return gbk_pinyin ( $str );
	}elseif($in_charset == 'PY'){
		return gbk_py ( $str );
	}else{
		return $str;
	}
}
// 替换字符串中php代码
/**
 * 字符串,替换
 * @param string $str
 * @return mixed
 */
function ctoStrFilterENCODE($str = '')
{
	// 符号 - html转义符
	// html转义符 - 符号
	// $symbolToHtmlcode
	// $HtmlcodeToSymbo
	$result = str_replace ( array(
		'"',
		"'",
		'<',
		'>'
	), array(
		'&quot;',
		'&#039;',
		'&lt;',
		'&gt;'
	), $str );

	return $result;
}
// 清除空格--等一些字符,留下纯文本
function ctoStrReplaceTrim($str = '')
{
	$qian = array(
		" ",
		"　",
		"\t",
		"\n",
		"\r"
	);
	$hou = array(
		"",
		"",
		"",
		"",
		""
	);
	$result = str_replace ( $qian, $hou, $str );
	return $result;
}
// 替换html
function ctoStrReplaceHTML($str = '')
{
	// 用于替换
	$search = array(
		"'<script[^>]*?>.*?<!-- </script> -->'si", // 去掉 javascript
		"'<script[^>]*?>.*?</script>'si", // 去掉 javascript
		"'javascript[^>]*?>.*?'si", // 去掉 javascript
		"'<style[^>]*?>.*?</style>'si", // 去掉 css
		"'<[/!]*?[^<>]*?>'si", // 去掉 HTML 标记
		"'<[\/\!]*?[^<>]*?>'si", // 去掉 HTML 标记
		"'<!--[/!]*?[^<>]*?>'si", // 去掉 注释标记
		"'([rn])[s]+'", // 去掉空白字符
		"'([\r\n])[\s]+'", // 去掉空白字符
		"'&(quot|#34);'i", // 替换 HTML 实体
		"'&(amp|#38);'i",
		"'&(lt|#60);'i",
		"'&(gt|#62);'i",
		"'&(nbsp|#160);'i",
		"'&(iexcl|#161);'i",
		"'&(cent|#162);'i",
		"'&(pound|#163);'i",
		"'&(copy|#169);'i",
		"'&#(d+);'e",
		"'&#(\d+);'e"
	);
	// 作为 PHP 代码运行
	$replace = array(
		"",
		"",
		"",
		"",
		"",
		"",
		"",
		"\1",
		"\\1",
		"\"",
		"&",
		"<",
		">",
		" ",
		chr ( 161 ),
		chr ( 162 ),
		chr ( 163 ),
		chr ( 169 ),
		"chr(\1)",
		"chr(\\1)"
	);
	// $document为需要处理字符串，如果来源为文件可以$document = file_get_contents('http://www.sina.com.cn');
	$out = preg_replace ( $search, $replace, $str );

	$out = str_replace ( "<", "", $out );
	$out = str_replace ( ">", "", $out );
	$out = str_replace ( "alert", "", $out );
	$out = str_replace ( "java", "", $out );
	$out = str_replace ( "script", "", $out );
	$out = str_replace ( "(", "", $out );
	$out = str_replace ( ")", "", $out );
	return $out;
}
function ctoStrSuperReplace($str = '', $type = '')
{
	if(trim ( $str ) == '')
		return '';
	switch($type)
	{
		case 'nosql': // 加反斜杠。防止sql，
			$str = addslashes ( $str );
			break;
		case 'trim': // 去除连续空格
			$str = str_replace ( "　", ' ', stripslashes ( $str ) );
			$str = preg_replace ( "/[\r\n\t ]{1,}/", ' ', $str );
			break;
		case 'trim_all': // 删除全部空格
			$str = str_replace ( "　", ' ', stripslashes ( $str ) );
			$str = preg_replace ( "/[\r\n\t ]/", '', $str );
			break;
		case 'blank': // 删除空白的
			$str = preg_replace ( "/[\r\n]{1,}/", "\n", $str );
			break;
	}
}