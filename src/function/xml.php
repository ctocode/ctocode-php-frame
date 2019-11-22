<?php
/**
 * 【ctocode】      常用函数 - xml相关处理
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
$xml = new DOMDocument ();
$xml->load ( 'yourDir/manifest.xml' );
$modules = $xml->getElementsByTagName ( 'modules' );
// simplexml
foreach($modules as $mol){
	$menu = $mol->getElementsByTagName ( 'menu' );
	$value = $mol->firstChild->nodeValue;
	echo $menu . '<br />';
}
