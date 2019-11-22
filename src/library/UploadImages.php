<?php
/**
 * 上传图片类
 * @author ctocode-zhw
 * @version 2018-1219 21:07
 */
class CTOCODE_UploadImages
{
	public function uploadImage($options = array())
	{
		$time = time ();
		$settings = array_merge ( array(
			'save_first' => '', // 上传第一级路径
			'save_sign' => 'uploadimg', // 上传路径
			'save_addid' => '', // 添加者id
			'save_name' => '',
			'file_name' => '' // 上传表单名称
		), $options );
		$savefirst = $settings['save_first'];
		$savesign = $settings['save_sign'];
		$save_addid = $settings['save_addid'];
		$save_name = $settings['save_name'];
		$file_name = $settings['file_name'];
		$uploadImgArr = $_FILES[$file_name];
		if(empty ( $file_name ) || empty ( $uploadImgArr )){
			exit ( json_encode ( array(
				'status' => 404,
				'msg' => '文件错误'
			) ) );
		}
		//
		$uploadImgSaveOss = '';
		if(! empty ( $savefirst )){
			$uploadImgSaveOss .= $savefirst . '/';
		}
		$uploadImgSaveOss .= "{$savesign}/" . date ( 'Y', $time ) . "/" . date ( 'md', $time ) . "/";
		$uploadImgSaveOss = str_replace ( "//", "/", $uploadImgSaveOss );
		$uploadImgSaveOss = str_replace ( "//", "/", $uploadImgSaveOss );
		$uploadImgSaveOss = str_replace ( "//", "/", $uploadImgSaveOss );
		$uploadImgSaveOss = ltrim ( $uploadImgSaveOss, "/" );
		if(is_array ( $uploadImgArr )){
			if(count ( $uploadImgArr ) == count ( $uploadImgArr, 1 )){
				$imgCheck = ctoImgCheck ( $uploadImgArr['tmp_name'] );
				if($imgCheck['type'] != 'ok'){
					exit ( json_encode ( $imgCheck ) );
				}
				$uploadImgSaveName = '';
				if(! empty ( $save_name )){
					$uploadImgSaveName .= $save_name . $imgCheck['extension'];
				}else{
					$uploadImgSaveName .= $save_addid . "_" . mt_rand () . $imgCheck['extension'];
				}

				$content = file_get_contents ( $uploadImgArr['tmp_name'] );
				$result = ctoHttpCurl ( _URL_API_TOOL_ . 'file/opt', array(
					'TokenType' => _TOOL_TOKEN_TYPES_,
					'type' => 'upload',
					'sett_id' => _TOOL_FILE_SETT_ID_,
					'oss_file_content' => $content,/*文件路径*/
					'oss_file_save' => $uploadImgSaveOss . $uploadImgSaveName /* 保存的地址 */
				) );
				$resultArr = json_decode ( $result, true );
				$resultArr['save_time'] = $time;
				$resultArr['save_name'] = $uploadImgSaveName;
				return $resultArr;
			}else{
				foreach($uploadImgArr as $uploadImgItem){
					$imgCheck = ctoImgCheck ( $uploadImgItem['tmp_name'] );
					if($imgCheck['type'] != 'ok'){
						exit ( json_encode ( $imgCheck ) );
					}
					$uploadImgSaveName = '';
					if(! empty ( $save_name )){
						$uploadImgSaveName .= $save_name . $imgCheck['extension'];
					}else{
						$uploadImgSaveName .= $save_addid . "_" . mt_rand () . $imgCheck['extension'];
					}
					$content = file_get_contents ( $uploadImgItem['tmp_name'] );
					$result = ctoHttpCurl ( _URL_API_TOOL_ . 'file/opt', array(
						'TokenType' => _TOOL_TOKEN_TYPES_,
						'type' => 'upload',
						'sett_id' => _TOOL_FILE_SETT_ID_,
						'oss_file_content' => $content,/*文件路径*/
					'oss_file_save' => $uploadImgSaveOss . $uploadImgSaveName /* 保存的地址 */
					) );
					$resultArr = json_decode ( $result, true );
					$resultArr['save_time'] = $time;
					$resultArr['save_name'] = $uploadImgSaveName;
					return $resultArr;
				}
			}
		}
	}
}