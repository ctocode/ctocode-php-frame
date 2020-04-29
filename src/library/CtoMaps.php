<?php

namespace ctocode\library;

/**
 * 地图操作类
 * @author ctocode-zhw
 * @version 2018-0619 22:07
 */
class CtoMaps
{
	public function huoxingToBaidu()
	{
		// 将火星坐标系GCJ-02 坐标 转换成百度坐标系 BD-09 坐标
		$coordinate = new Coordinate ( 113.3998, 34.5443 ); // 吃货天堂的坐标
		$coord = CoordinateTool::gcj_bd ( $coordinate );
	}
	public function BaiduToHuoxing()
	{

		// 将百度坐标系 BD-09 坐标 转换成 火星坐标系GCJ-02 坐标
		$coordinate = new Coordinate ( 113.3996, 34.543 );
		$coord = CoordinateTool::bd_gcj ( $coordinate );
	}
	public function guojiToHuoxing()
	{ // 将国际通用坐标系WGS84坐标 转换成 火星坐标系GCJ-02 坐标
		$coordinate = new Coordinate ( 113.3924, 34.54036 );
		$coord = CoordinateTool::wgs_gcj ( $coordinate );
	}
	public function huoxingToGuoji()
	{ // 将火星坐标系GCJ-02坐标 转换成 国际通用坐标系WGS84坐标
		$coordinate = new Coordinate ( 113.3998, 34.5443 );
		$coord = CoordinateTool::gcj_wgs ( $coordinate );
	}
	/**
	 *  计算两组经纬度坐标 之间的距离
	 *   @param $lng1 $lng2 经度；
	 *   @param $lat1 $lat2 纬度；
	 *   @param $len_type （1:米 2:千米);
	 *   return $s 距离（默认单位：米）
	 */
	function mapGetDistance($lng1, $lat1, $lng2, $lat2, $len_type = 1, $decimal = 0)
	{
		$radLat1 = $lat1 * PI () / 180.0; // PI()圆周率
		$radLat2 = $lat2 * PI () / 180.0;
		$a = $radLat1 - $radLat2;
		$b = ($lng1 * PI () / 180.0) - ($lng2 * PI () / 180.0);
		$s = 2 * asin ( sqrt ( pow ( sin ( $a / 2 ), 2 ) + cos ( $radLat1 ) * cos ( $radLat2 ) * pow ( sin ( $b / 2 ), 2 ) ) );
		$s = $s * 6378.137;
		$s = round ( $s * 1000 );
		if($len_type == 2){
			$s /= 1000;
		}
		return round ( $s, $decimal );
	}
	/**
	 * 根据两点间的经纬度计算距离
	 * @param $lng1
	 * @param $lat1
	 * @param $lng2
	 * @param $lat2
	 * @return int
	 */
	function mapGetDistance2($lng1, $lat1, $lng2, $lat2)
	{
		$lng1 = ( float ) $lng1;
		$lat1 = ( float ) $lat1;
		$lng2 = ( float ) $lng2;
		$lat2 = ( float ) $lat2;
		// 将角度转为狐度
		$radLat1 = deg2rad ( $lat1 ); // deg2rad()函数将角度转换为弧度
		$radLat2 = deg2rad ( $lat2 );
		$radLng1 = deg2rad ( $lng1 );
		$radLng2 = deg2rad ( $lng2 );
		$a = $radLat1 - $radLat2;
		$b = $radLng1 - $radLng2;
		$s = 2 * asin ( sqrt ( pow ( sin ( $a / 2 ), 2 ) + cos ( $radLat1 ) * cos ( $radLat2 ) * pow ( sin ( $b / 2 ), 2 ) ) ) * 6378.137 * 1000;
		return round ( $s, 0 );
	}

	/**
	 * 百度坐标转腾讯坐标
	 * @param array $wsql
	 */
	public function baiduToQq(&$wsql = array())
	{
		if(! empty ( $wsql['map_longitude'] ) && ! empty ( $wsql['map_latitude'] )){
			$map = $this->map_conversion ( array(
				0 => array(
					"lng" => $wsql['map_longitude'],
					"lat" => $wsql['map_latitude']
				)
			) );
			$map = json_decode ( $map, true );
			$wsql['map_latitude'] = $map['result'][0]['y'];
			$wsql['map_longitude'] = $map['result'][0]['x'];
		}
	}
	protected function map_conversion($map = array(), $from = 5, $to = 3)
	{
		$url = "http://api.map.baidu.com/geoconv/v1/?";
		$url .= "ak=dT85sFHzWrGyHVGvZkUsHxLrdSuKlGuA&from={$from}&to={$to}";
		$coords = "";
		$num = count ( $map ) - 1;
		foreach($map as $key=>$val){
			$coords .= $val['lng'] . "," . $val['lat'];
			if($num != $key){
				$coords .= ";";
			}
		}
		$url .= "&coords=" . $coords;
		return ctoHttpCurl ( $url );
	}
}