<?php

namespace ctocode\core;

class CtoModelAnalys
{
	//
	public function analysField($data, $fieldSett = [])
	{
		if(empty ( $data ) || ! is_array ( $data )){
			return [];
		}
		if(empty ( $fieldSett ) || ! is_array ( $fieldSett )){
			return $data;
		}

		foreach($data as $dkey=>$dval){
			foreach($fieldSett as $fkey=>$fval){
				if(! empty ( $dval[$fkey] ) && ! empty ( $fval['type'] )){
					switch($fval['type'])
					{
						// 解析时间
						case 'date':
						case 'time':
							$fkey_new = str_replace ( "time", "date", $fkey );
							$data[$dkey][$fkey_new] = analysTimeToDate ( $dval[$fkey], $fval['format'] ?? '');
							break 1;
						case 'price':
							$fkey_new = $fkey . '_rmb2';
							$data[$dkey][$fkey_new] = analysMoneyDe ( $dval, $fkey );
							break 1;
						case 'pic_cdn':
							$fkey_new = $fkey . '_cdn';
							$pic_def = $fval['pic_def'] ?? '';
							$data[$dkey][$fkey_new] = analysImgDoTrnas ( $dval, $pic_def );
							break 1;
						default:
							break 1;
					}
				}
			}
			ksort ( $data[$dkey] );
		}
		return $data;
	}
	protected function doDataType()
	{
	}
}