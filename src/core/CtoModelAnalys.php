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
				if(isset ( $dval[$fkey] ) && ! empty ( $fval['type'] )){
					try{
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
								$data[$dkey][$fkey_new] = analysMoneyDe ( $dval[$fkey], $fkey );
								break 1;
							case 'picture':
								$fkey_new = $fkey . '_cdn';
								$pic_def = ! empty ( $fval['picdef'] ) ? $fval['picdef'] : null;
								$data[$dkey][$fkey_new] = analysImgDoTrnas ( $dval[$fkey] ?? '', $pic_def );
								break 1;
							default:
								break 1;
						}
					}
					catch ( \Exception $e ){
						var_dump ( $e->getMessage (), $dval );
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