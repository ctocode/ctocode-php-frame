<?php

namespace ctocode\phpframe\helper;
/**
 * 【ctocode】      常用函数 - html相关处理
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

// 二维码 url http://www.liantu.com/pingtai/
// 获取二维码 [小庄_2016-03-10]
function ctoHtmlQrcode($url = NULL)
{
	return 'http://qr.topscan.com/api.php?el=l&text=' . urlencode ( $url );
}
// 自动跳转 [小庄_2016-03-31]
function ctoHtmlJumpLink($jumpLink = NULL)
{
	// @ob_clean ();
	// ctoUrlRedirect ($jumpLink, "location" );
	// exit ( "<script>self.location='$jumpLink';</script>" );
	// if(stripos($jumpLink,'http://')===false)
	// return false;
	$htmls = '';
	$htmls .= '<script>';
	$htmls .= 'if(/Android|webOS|iPhone|Windows Phone|iPod|BlackBerry|SymbianOS/i.test(window.navigator.userAgent) && !window.location.hash.match("fromapp"))';
	$htmls .= '{ window.location.href = "' . $jumpLink . '"; }';
	$htmls .= '</script>';
	echo $htmls;
}
function ctoHtmlForeach($tree)
{
	$html = '';
	foreach($tree as $t){
		if($t['store_upid'] == ''){
			$t['store_name'] = $t['store_name'];
		}else{
			$html .= "<li>" . $t['store_name'];
			$html .= ctoHtmlForeach ( $t['ip_id'] );
			$html = $html . "</li>";
		}
	}
	return $html ? '<ul>' . $html . '</ul>' : $html;
}
/*
 * 递归展示
 * 使用方式
 * <select name="cat_upid">
 * <?php ctoHtmlEchoMultiCat($cat_lists, 0); ?>
 * </select>
 */
function ctoHtmlEchoMultiCat($cat_lists, $level)
{
	if(is_array ( $cat_lists )){
		$level ++;
		foreach($cat_lists as $key=>$val){
			$str = '<option ';
			$str .= ' value="' . $val['cat_id'] . '" >';
			for($i = $level - 1;$i > 0;$i --){ // 子集分类加前导字符
				$str .= '&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			if($level - 1 > 0){ // 子集分类加前导字符
				$str .= '|--';
			}
			$str .= $val['cat_name'] . '</option>';
			echo $str;
			if(is_array ( $val['tree'] )){
				ctoHtmlEchoMultiCat ( $val['tree'], $level );
			}
		}
	}
}
function procHtml($tree)
{
	$html = '';
	foreach($tree as $t){
		if($t['store_upid'] == ''){
			$t['store_name'] = $t['store_name'];
		}else{
			$html .= "<li>" . $t['store_name'];
			$html .= $this->procHtml ( $t['ip_id'] );
			$html = $html . "</li>";
		}
	}
	return $html ? '<ul>' . $html . '</ul>' : $html;
}
function printTreeHtml($treeData = '', $html_for_type = '', $bindData = '')
{
	if($html_for_type == 'select'){
		return printTreeHtmlSelect ( $treeData, 0, $bindData );
	}
}
function printTreeHtmlSelect($cat_lists = '', $level = '', $bindData = '')
{
	$htmls = '';
	if(is_array ( $cat_lists )){
		$level ++;
		foreach($cat_lists as $key=>$val){
			$str = '<option ';
			$str .= ' value="' . $val['cat_id'] . '" >';
			for($i = $level - 1;$i > 0;$i --){ // 子集分类加前导字符
				$str .= '&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			if($level - 1 > 0){ // 子集分类加前导字符
				$str .= '|--';
			}
			$str .= $val['cat_name'] . '</option>';
			$htmls = $htmls . $str;
			if(is_array ( $val['tree'] )){
				$htmls .= printTreeHtmlSelect ( $val['tree'], $level, $bindData );
			}
		}
	}
	return $htmls;
}

/**
 * @action 分页函数
 * @author ctocode-zhw
 * @copyright Email 343196936@qq.com  
 * @version 2017-06-22
 * @param number $currentPage 当前页
 * @param number $pageTotal 总页数
 * @param number $pageSize 每页个数
 * @param string $url 地址
 * @param string $style  
 * @return mixed 
 */
function ctoHtmlPagebreak($currentPage = 1, $total = 0, $pageSize = null, $url = null, $style = null)
{
	/* style风格样式 */
	if($style == NULL){
		$style = '<style type="text/css">
    div.zhw_htmlpage{text-align: center;padding: 5px 10px;height: 70px; overflow: hidden;}
    div.zhw_htmlpage a{border:1px solid #e4e4e4; font-family:"Tahoma","Arial"; font-size:14px; height:30px; line-height: 30px; 
        padding:0 12px; margin-left: 2px; display: inline-block; overflow: hidden; background: #FFF; color:#6a6a6a;vertical-align:middle;}
    div.zhw_htmlpage a:hover{background:#0666c5;color:#FFF;text-decoration:none}
    div.zhw_htmlpage a.on{background:#6e2685;color:#FFF}
    div.zhw_htmlpage input#ctoGoDiyPage{padding:5px 8px 5px 8px;margin-left:2px;display:inline-block;
        width: 50px;height:30px;line-height: 22px;font-size:14px;font-weight:bold;border:1px solid #d8d8d8;border-radius:4px;overflow: hidden;vertical-align:middle;}</style>';
	}
	/* javascript设置选中 */
	$javascript = '<script type="text/javascript">
				var zhw_htmlpagea =document.getElementById("ctoGoPage' . $currentPage . '");
				if(zhw_htmlpagea!=undefined){zhw_htmlpagea.setAttribute("class","on");}</script>';
	$javascript .= <<<EOF
			<script type="text/javascript">
			function ctoGoDiyPageFunc(){
				var page = document.getElementById("ctoGoDiyPage").value;
				var url = "$url";
				if(page != '' || page != 0){
					window.location.href = url + page;
				}
			}
			</script>
EOF;
	$pagetotal = ceil ( $total / $pageSize ); // 向上取整,算出分页
	$paging = '<div class="zhw_htmlpage ">';
	if($pagetotal == 1){
		$paging .= '<a id="ctoGoPage1" href=" ' . $url . '1" >1</a>';
	}else{
		// 开头部分,是否显示上一页
		if(($currentPage - 3) > 1){
			$paging .= '<a id="ctoGoPage1" href=" ' . $url . '1" >1...</a>
				<a  href=" ' . $url . ($currentPage - 1) . '" class="next">上一页</a>';
		}
		// 中间部分,输出7个分页
		for($i = $currentPage - 3;$i < $currentPage + 4;$i ++){
			if($i < 1 || $i > $pagetotal){
				continue;
			}
			$paging .= '<a id="ctoGoPage' . $i . '" href=" ' . $url . $i . '">' . $i . '</a>';
		}
		// 结尾部分,是否显示下一页
		if(($currentPage + 4) <= $pagetotal){
			$paging .= '<a href=" ' . $url . ($currentPage + 1) . '" class="next">下一页</a>
						<a id="ctoGoPage' . $pagetotal . '" href="' . $url . $pagetotal . '">...' . $pagetotal . '</a>';
		}
	}
	// 是否开启跳转
	$paging .= '&nbsp;&nbsp;<input type="text" id="ctoGoDiyPage" value="' . $currentPage . '"><a href="javascript:ctoGoDiyPageFunc();">跳转</a>';
	$paging .= "<br>(总共" . $pagetotal . "页 共" . $total . "条记录 )</div>";
	return $style . $paging . $javascript;
}

/**
 * @action 过滤富文本的img,a标签和纯文本
 * @author ctocode-zwj
 * @return array
 */
function ctoHtmlFilter($content)
{
	$content = preg_replace ( "/<p.*?>|<\/p>/is", "", $content );
	$content = preg_replace ( "/<span.*?>|<\/span>/is", "", $content ); // 过滤span标签
	$pregImgRule = "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.jpg|\.jpeg|\.png|\.gif|\.bmp]))[\'|\"].*?[\/]?>/";
	$content = preg_replace ( $pregImgRule, '#@#ctocode-img-${1}#@#', $content );
	$pregARule = "/<a[^<>]+href *\= *[\"']?([^ '\"]+).*<\/a>/i";
	$content = preg_replace ( $pregARule, '#@#ctocode-a-${1}#@#', $content );
	$content = explode ( '#@#', $content );
	$content = array_filter ( $content );
	$data = array();
	foreach($content as $v){
		$resultImg = explode ( 'ctocode-img-', $v );
		$resultA = explode ( 'ctocode-a-', $v );
		if(! empty ( $resultImg[1] )){
			// img
			$data[] = array(
				'img' => $resultImg[1]
			);
		}else if(! empty ( $resultA[1] )){
			// a
			$data[] = array(
				'a' => $resultA[1]
			);
		}else{
			$appEmotion = "";
			$appEmotion = preg_replace_callback ( '/@E(.{6}==)/', function ($r)
			{
				return base64_decode ( $r[1] );
			}, strip_tags ( $v ) );
			$data[] = array(
				'text' => $appEmotion
			);
		}
	}
	return $data;
}
/**
 * 压缩 css,js文件
 * css -js 资源路径,后期修改方法,一条加载并且压缩
 * @param array $uiArr
 * @param number $ver
 * @return string
 */
function ctoHtmlUiMerge($uiArr = array(), $baseurl = '', $ver = 0)
{
	$baseurl = ! empty ( $baseurl ) ? $baseurl : ctoUrlBase ();
	$news_assets = array();
	if(! empty ( $uiArr )){
		$t = '';
		foreach($uiArr as $file){
			$file_path = $file;
			$news_assets[] = $file_path;
			$t .= @filemtime ( $file_path );
		}
		$news_assets_str = join ( ',', $news_assets );
		unset ( $news_assets );
		return $baseurl . 'minify.php?f=' . $news_assets_str . (empty ( $ver ) ? '&v=' . substr ( md5 ( $t ), 0, 8 ) : '&v=' . $ver);
	}else
		return '';
}
// 生成静态文件
function ctoHtmlCreate($file)
{
	$data = ob_get_contents ();
	ob_clean ();
	// class dir.func
	$strlen = creatfile ( $file, $data );
	return $strlen;
}
/*
 * 模板引用解析
 * ***********************************************
 */
function ctoHtmlTemplateLoad($fname)
{
	ctoLoadClass ( 'template.func' );
	$fname = preg_replace ( '#[/\\\]+#', '/', $fname );
	return temp_refresh ( $fname );
}
function ctoHtmlTemplateRefresh($tempfile)
{
	$tpl = '';
	$default = 1;
	$fname = $default ? BAIYU_ADMIN . '/tpls/' . $tpl . '.html' : BAIYU_ADMIN . '/tpls/' . $tpl;
	$tempfile = preg_replace ( '#[/\\\]+#', '/', $fname );
	$tempfile = str_replace ( "\\", "/", $tempfile );
	if(! is_file ( $tempfile )){
		echo "Template Not Found.";
	}
	$md5 = md5 ( $tempfile );
	$cachedir = BAIYU_DATA . '/tpls/' . substr ( $md5, 0, 2 ) . '/' . substr ( $md5, 2, 2 );
	$cachename = substr ( $md5, 4, 28 ) . '.php';
	$cachetemp = $cachedir . '/' . $cachename;
	if(! file_exists ( $cachetemp ) || ($_glb['cachetpl'] && @filemtime ( $tempfile ) > @filemtime ( $cachetemp ))){
		if(! is_dir ( $cachedir )){
			// class dir.func
			creatdir ( $cachedir );
		}
		$dir = dirname ( $tempfile );
		$str = @file_get_contents ( $tempfile );
		$str = temp_parse ( $str, $dir, 1 );
		$strlen = @file_put_contents ( $cachetemp, $str );
		@chmod ( $cachetemp, 0777 );
	}
	return $cachetemp;
}