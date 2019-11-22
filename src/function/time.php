<?php
/**
 * 【ctocode】      常用函数 - time相关处理
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
// 1、Unix时间戳转日期
// ctoDate 替代 date
function ctoDate($format = "Y-m-d H:i:s", $unixtime = 0, $timezone = 'PRC')
{
	// DateTime类的bug，加入@可以将Unix时间戳作为参数传入
	$datetime = new DateTime ( "@$unixtime" );
	$datetime->setTimezone ( new DateTimeZone ( $timezone ) );
	return $datetime->format ( $format );

	// global $_glb;
	// $_glb['time_zone'] = isset ( $_glb['time_zone'] ) ? $_glb['time_zone'] : 8;
	// $format = empty ( $format ) ? 'Y-m-d H:i' : $format;
	// // return date($format,$times+$_glb['time_zone']*3600);
	// return date ( $format, $times );
}
// 2、日期转Unix时间戳
// ctoStrToTime 替代 strtotime
function ctoStrToTime($date, $timezone = 'PRC')
{
	$datetime = new DateTime ( $date, new DateTimeZone ( $timezone ) );
	return $datetime->format ( 'U' );
}
/**
 * @author zhw   @action 计算时间差
 * @param string $start_time 传递进来的开始时间
 * @return string 返回差值
 */
function ctoTimeDeviation($start_time = null, $end_time = null, $format = 'Y-m-d')
{
	date_default_timezone_set ( 'PRC' );
	$doTimeType = 2;
	switch($doTimeType)
	{
		case 1:
			break;

		case 2:
			/* 方法2,根据时间戳,秒数来判断 */
			if($end_time != NULL){
				$endtime = $end_time;
			}else{
				$endtime = time ();
			}
			$deviation = $endtime - $start_time;
			if($deviation < 0 || $deviation > 3600){
				if(date ( 'Y', $endtime ) != date ( 'Y', $start_time )){
					return date ( 'Y-m-d', $start_time );
				}
				return date ( 'm-d H:i', $start_time );
				// if(ctoDate ( 'Y', $nimes ) == ctoDate ( 'Y', $times )){
				// return ctoDate ( 'm-d H:i' $times);
				// }
				// return ctoDate ( $format ,$times);
			}
			if($deviation < 30){
				return ' 刚刚';
			}elseif($deviation < 60){
				return $deviation . ' 秒钟前';
			}else{
				return floor ( $deviation / 60 ) . ' 分钟前';
			}
			break;
	}

	// 方法1
	// $startdate = $start_time;
	$startdate = date ( 'Y-m-d H:i:s', $start_time );
	$enddate = date ( 'Y-m-d H:i:s', time () );
	// 差距-天
	$time_date = floor ( (strtotime ( $enddate ) - strtotime ( $startdate )) / 86400 );
	if($time_date <= 0){
		// 差距-小时
		$time_hour = floor ( (strtotime ( $enddate ) - strtotime ( $startdate )) % 86400 / 3600 );
		// 差距-分钟
		$time_minute = floor ( (strtotime ( $enddate ) - strtotime ( $startdate )) % 86400 / 60 );
		// 差距-秒
		$time_second = floor ( (strtotime ( $enddate ) - strtotime ( $startdate )) % 86400 % 60 );

		if($time_hour <= 0){
			if($time_minute < 1){
				if($time_second < 30){
					return "刚刚";
				}else if($time_second < 60){
					return $time_second . " 秒前";
				}
			}elseif($time_minute <= 60){
				return $time_minute . " 分钟前";
			}
		}else if($time_hour > 0 && $time_hour < 1){
			return $time_hour . " 小时前";
		}else{
			return date ( "m-d H:i", strtotime ( $startdate ) ) . "";
		}
	}else if($time_date > 0 && $time_date < 365){
		/**
		 * 1年内
		 */
		return date ( "m-d H:i", strtotime ( $startdate ) ) . "";
	}else{
		return date ( "Y-m-d", strtotime ( $startdate ) ) . "";
	}
	// echo $time_date . "天<br>";
	// echo $time_hour . "时<br>";
	// echo $time_minute . "分钟<br>";
	// echo $time_second . "秒<br>";
}
function dbgTimeDiffer($begin_time, $end_time)
{
	if(empty ( $end_time )){
		$end_time = time ();
	}
	if($begin_time < $end_time){
		$starttime = $begin_time;
		$endtime = $end_time;
	}else{
		$starttime = $end_time;
		$endtime = $begin_time;
	}
	// 计算天数
	$timediff = $endtime - $starttime;
	$days = intval ( $timediff / 86400 );
	// 计算小时数
	$remain = $timediff % 86400;
	$hours = intval ( $remain / 3600 );
	// 计算分钟数
	$remain = $remain % 3600;
	$mins = intval ( $remain / 60 );
	// 计算秒数
	$secs = $remain % 60;
	$res = array(
		"day" => $days,
		"hour" => $hours,
		"min" => $mins,
		"sec" => $secs
	);
	$return_differ = '';
	if($days > 0){
		$return_differ .= $days . '天';
	}
	if($hours > 0){
		$return_differ .= $hours . '时';
	}
	if($mins > 0){
		$return_differ .= $mins . '分';
	}
	return $return_differ;
}

/*
 * 时间戳处理 ***********************************************
 */
function stotime($str)
{
	global $_glb;
	$_glb['time_zone'] = isset ( $_glb['time_zone'] ) ? $_glb['time_zone'] : 8;
	$str = trim ( preg_replace ( '/[ \r\n\t\f\日\秒]{1,}/', ' ', $str ) );
	$str = preg_replace ( '/[\年\月]{1,}/', '-', $str );
	$str = preg_replace ( '/[\时\点\分]{1,}/', ':', $str );
	$dates = $str;
	$times = '';
	if(strpos ( $str, " " ) !== false){
		$dtstr = explode ( " ", $str );
		$dates = $dtstr[0];
		$times = explode ( ":", $dtstr[1] );
		$times[0] = empty ( $times[0] ) ? '00' : $times[0];
		$times[1] = empty ( $times[1] ) ? '00' : $times[1];
		$times[2] = empty ( $times[2] ) ? '00' : $times[2];
		$times = implode ( ':', $times );
	}
	// $rstime = strtotime($dates.' '.$times)- $_glb['time_zone']*3600;
	$rstime = strtotime ( $dates . ' ' . $times );
	return $rstime;
}
