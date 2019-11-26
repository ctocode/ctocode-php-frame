<?php

/**
 * 【ctocode】      常用函数 - load相关处理
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

// 加载 状态 201603300
use ctocode\phpframe\library\CtoArray;

function ctoLoadState($onlytime = 0, $more = 0, $msql_quetynum = 0)
{
	define ( 'MICROTIME_START', microtime () );
	$stime = explode ( ' ', MICROTIME_START );
	$etime = explode ( ' ', microtime () );
	if($onlytime)
		return number_format ( ($etime[1] + $etime[0] - $stime[1] - $stime[0]), 6 );
	$rs = 'processed in ' . number_format ( ($etime[1] + $etime[0] - $stime[1] - $stime[0]), 6 ) . ' second(s), ' . $msql_quetynum . ' queries.';
	if($more)
		$rs .= 'memory_use:' . number_format ( memory_get_usage () / 1024 / 1024, 3 ) . 'M,memory_max:' . number_format ( memory_get_peak_usage () / 1024 / 1024, 3 ) . 'M.';
	return $rs;
}
function ctoLoadIncludeFile($path = '')
{
	if(file_exists ( $path )){
		return include $path;
	}
	return false;
}
/**
 *
 * 获取加载菜单文件
 *
 */
function ctoLoadConsoleMenuGetMod($dir_path = '', $modules = '*', $param = '')
{
	$menu_file_arr = array();
	if(is_array ( $modules )){
		$modules = array_unique ( $modules );
		foreach($modules as $path){
			$menu_file_arr[] = $dir_path . "{$path}/menu.php";
		}
	}else{
		$menu_file_arr = glob ( $dir_path . "{$modules}/menu.php" );
	}
	$menu_arr = array();
	foreach($menu_file_arr as $path){
		$items = array();
		if(! file_exists ( $path )){
			continue;
		}
		$items = include $path;
		foreach($items as $val){
			$menu_arr[] = ctoLoadConsoleMenuDoMod ( $val, $param );
		}
	}
	foreach($menu_arr as $key=>$val){
		$menu_arr[$key]['menu_id'] = $key;
	}
	CtoArray::multiArrSort ( $menu_arr, 'menu_sort', SORT_ASC );
	return $menu_arr;
}
function ctoLoadConsoleMenuDoMod($items = '', $param = '')
{
	if(! empty ( $items['tree'] )){
		$items['tree'] = ctoLoadConsoleMenuDoCon ( $items['menu_modules'], $items['tree'], $param );
	}
	if(! empty ( $items['menu_controllers'] )){
		$menu_con_icon = str_replace ( '/', '-', $items['menu_controllers'] );
	}else{
		$menu_con_icon = '';
	}
	$items['menu_con_icon'] = $items['menu_modules'] . '-' . $menu_con_icon;
	$items['menu_mod_icon'] = 'mod-' . $items['menu_modules'];

	$menu_sign = $items['menu_modules'] . '_modules';
	$menu_sign = strtolower ( $menu_sign );
	$items['menu_icon'] = 'icon-' . $menu_sign;
	$items['menu_id'] = $menu_sign;
	$items['menu_sign'] = $menu_sign;
	$items['menu_rbac_flag'] = $menu_sign;
	$items['menu_sort'] = ! empty ( $items['menu_sort'] ) ? $items['menu_sort'] : 99;
	if(! empty ( $items['menu_link'] )){
		$items['menu_link'] = '#';
	}else{
		$items['menu_link'] = $param['link_flag'] . $items['menu_modules'];
	}
	return $items;
}
function ctoLoadConsoleMenuDoCon($modSign = '', $treeData = '', $param = '')
{
	foreach($treeData as $key=>$val){
		if(empty ( $val['menu_name'] )){
			unset ( $treeData[$key] );
			continue;
		}
		if(! empty ( $val['tree'] )){
			$treeData[$key]['tree'] = ctoLoadConsoleMenuDoCon ( $modSign, $val['tree'], $param );
		}
		$treeData[$key]['menu_con_icon'] = $modSign . '-' . str_replace ( '/', '-', $val['menu_controllers'] );

		$menu_sign = $modSign . '_' . str_replace ( '/', '_', $val['menu_controllers'] );
		$menu_sign = strtolower ( $menu_sign );
		$treeData[$key]['menu_icon'] = 'icon-' . $menu_sign;
		$treeData[$key]['menu_id'] = $menu_sign;
		$treeData[$key]['menu_sign'] = $menu_sign;
		$treeData[$key]['menu_rbac_flag'] = $menu_sign;
		$treeData[$key]['menu_sort'] = ! empty ( $treeData[$key]['menu_sort'] ) ? $treeData[$key]['menu_sort'] : 99;

		if(! empty ( $val['menu_link'] )){
			$treeData[$key]['menu_link'] = '#';
		}else{
			if(! empty ( $val['menu_modules'] )){
				$modSign = $val['menu_modules'];
			}
			if(! empty ( $val['menu_isaddons'] )){
				$treeData[$key]['menu_link'] = $param['link_flag'] . $val['menu_controllers'];
			}else{
				$treeData[$key]['menu_link'] = $param['link_flag'] . $modSign . '/' . $val['menu_controllers'];
			}
		}
	}
	CtoArray::multiArrSort ( $treeData, 'menu_sort', SORT_ASC );
	return $treeData;
}
function ctoLoadConsoleMenuHave($menu_arr = null, $menu_have = null)
{
	foreach($menu_arr as $key_1=>$val_1){
		if(! in_array ( $val_1['menu_rbac_flag'], $menu_have )){
			unset ( $menu_arr[$key_1] );
			continue;
		}
		foreach($val_1['tree'] as $key_2=>$val_2){
			if(! in_array ( $val_2['menu_rbac_flag'], $menu_have )){
				unset ( $menu_arr[$key_1]['tree'][$key_2] );
				continue;
			}
			foreach($val_2['tree'] as $key_3=>$val_3){
				if(! in_array ( $val_3['menu_rbac_flag'], $menu_have )){
					unset ( $menu_arr[$key_1]['tree'][$key_2]['tree'][$key_3] );
					continue;
				}
			}
		}
	}
	return $menu_arr;
}