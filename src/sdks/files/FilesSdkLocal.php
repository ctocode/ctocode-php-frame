<?php

namespace ctocode\sdks\files;

/**
 * 本地文件管理类
 * @author ctocode-zhw
 * @version 2018-05-07
 */
class FilesSdkLocal extends FilesSdkCommon implements FilesSdkInterface
{
	/* 创建 */
	public function objectCreate($path)
	{
		$dir = isset ( $path ) ? $path : '';
		$dir = $this->myDirext ( $dir );
		if($dir != '' && $dir != '/'){
			$result = array();
			$rs = File_creatdir ( DBG_FILE . $dir );
			if($rs == ''){
				$result['result'] = 1;
				$result['dir'] = $dir;
			}else{
				$result['result'] = 0;
				$result['msg'] = '创建文件夹失败';
			}
			echo adminjs ( 'window.parent.CrtEnd(' . json_encode ( $result ) . ');' );
		}
		exit ();
	}
	/* 删除 */
	public function objectDelete($path = '')
	{
		$dir = isset ( $path ) ? $path : '';
		$dir = $this->myDirext ( $dir );

		$isdir = 0;
		$isfile = is_file ( DBG_FILE . $dir );
		if(! $isfile){
			$isdir = is_dir ( DBG_FILE . $dir );
		}
		if(! $isdir && ! $isfile){
			msgbox ( "要删除的文件(夹)不存在" );
			exit ();
		}
		if($isfile){
			$rs = @unlink ( DBG_FILE . $dir );
		}else{
			$rs = @rmdir ( DBG_FILE . $dir );
		}
		if($rs){
			$path = dirname ( $dir );
			$path = $this->myDirext ( $path );
			$url = "index.php?tn=$tn&dir=" . urlencode ( $path );
			msgbox ( "成功删除一个文件" . ($isdir ? '夹' : ''), $url );
		}else{
			msgbox ( "删除文件(夹)失败" );
		}
	}
	/* 重命名 */
	public function objectRename()
	{
	}
	function MyDirext($dir = '')
	{
		if($dir != ''){
			$dir = str_replace ( "\\", "/", $dir );
			$dir = preg_replace ( "/(.+?)\/*$/", "/\\1", $dir );
			$dir = preg_replace ( "/\/{1,}/", '/', $dir );
		}
		return $dir;
	}
}
