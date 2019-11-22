<?php
/**
 * 上传类
 * @author ctocode-zhw
 * @version 2015-03-18
 */
class CTOCODE_Upload
{
	private $error_code = array(
		1 => '其值为1,传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值',
		2 => '其值为 2,上传文件的大小超过了 HTML表单中MAX_FILE_SIZE选项指定的值',
		3 => '其值为3,文件只有部分被上传',
		4 => '其值为4,没有文件被上传',
		5 => '其值为5,上传文件大小为0',
		6 => '其值为6,找不到临时文件夹,PHP 4.3.10 和 PHP 5.0.3 引进',
		7 => '其值为7,文件写入失败,PHP 5.1.0 引进'
	);
	protected $upload_dir = '';
	public function __construct($uploadSett = array())
	{
		$this->upload_dir = ! empty ( $uploadSett['upload_dir'] ) ? $uploadSett['upload_dir'] : _CTOCODE_FILE_;
	}
	/**
	 * 上传所有,返回信息
	 */
	public function uploadAll()
	{
	}
	/**
	 * 上传图片,返回信息
	 */
	public function uploadImage()
	{
	}
	/**
	 * 上传音频,返回信息
	 */
	public function uploadVideo()
	{
	}
	/**
	 * 上传文件,返回信息
	 */
	public function uploadFile($file_name = '')
	{

		/*
		 * 重新定义文件路径及文件名 分离文件路径，
		 * 分离结果为：pathinfo() 返回一个关联数组包含有 path 的信息。
		 * 包括以下的数组单元：dirname，basename 和 extension。
		 * 注意：这里注意一个变量：
		 * $_FILES['zhw_upload']['name']，
		 * $_FILES的第一个参数是zhw_upload,表示就是接收我们上面定义的
		 * <input type='file' name='zhw_upload'/>，
		 * 所传上来的文件 我想讲的是：在PHP里的$_FILES的第一个参数的名字，要与HTML里input标签的name属性的值一样
		 */
		$fileUploadData = $_FILES[$file_name];
		$file_type = $fileUploadData['type']; // 获取类别
		$file_size = $fileUploadData['size']; // 文件大小
		$file_tmp_name = $fileUploadData['tmp_name']; // 动的文件
		$file_name = $fileUploadData['name']; // 获取名字
		$file_content = file_get_contents ( $file_tmp_name ); // 获取TEXT内容

		// 获取错误
		if($fileUploadData["error"] == 0){
			$houzhui = pathinfo ( $fileUploadData['name'] );
			// 判断上传文件的后缀格式是否满足要求
			if(in_array ( $houzhui['extension'], array(
				'mp3',
				'wma'
			) )){
			}
			/**
			 * 否则上传
			 */
			$filePath = "temp/"; // 保存路径
		}else{
			$errpr_msg = $this->error_code[$upload_error];
			$result_json = array(
				'msg' => ! empty ( $errpr_msg ) ? $errpr_msg : ''
			);
		}

		/* 用户文件上传 */
		$fielname = $_GET['filename'];
		$this->load->view ( 'user/upload' );
		if(! empty ( $fielname )){

			// $ZHW_UPLOAD_ID="zhw_upload";
			$zhw_upload_ID = "zhw_upload";
			// 判断是否错误
			if($_FILES[$zhw_upload_ID]["error"] > 0){
				/**
				 * 输出错误
				 */
				echo "Return Code: " . $_FILES[$zhw_upload_ID]["error"] . "<br >";
			}else{
				/**
				 * 否则上传
				 */
				// 保存路径-保存在当前同等级路径的temp文件下
				$filePath = "temp/";
				if(! file_exists ( $filePath )){
					// 如果指定文件夹不存在，则创建文件夹,权限777
					if(! mkdir ( $filePath, 0776 )){
						WriteErrMsg ( '创建保存文件目录失败,请联系管理员检查目录权限' );
					}
				}
				// $file_name =$_FILES [$zhw_upload_ID] ["error"];// 获取错误
				$file_name = $_FILES[$zhw_upload_ID]['name']; // 获取名字
				$file_type = $_FILES[$zhw_upload_ID]['type']; // 获取类别
				$file_size = $_FILES[$zhw_upload_ID]['size']; // 文件大小
				$file_tmp_name = $_FILES[$zhw_upload_ID]['tmp_name']; // 动的文件
				/*
				 * 获取TEXT内容 //echo file_get_contents($_FILES[$zhw_upload_ID]['tmp_name']);
				 */
				/*
				 * 重新定义文件路径及文件名 分离文件路径，分离结果为：pathinfo() 返回一个关联数组包含有 path 的信息。 包括以下的数组单元：dirname，basename 和 extension。
				 * 注意：这里注意一个变量：$_FILES['zhw_upload']['name']，$_FILES的第一个参数是zhw_upload,表示就是接收我们上面定义的 <input type='file'
				 * name='zhw_upload'>，所传上来的文件 我想讲的是：在PHP里的$_FILES的第一个参数的名字，要与HTML里input标签的name属性的值一样
				 */
				$houzhui = pathinfo ( $_FILES[$zhw_upload_ID]['name'] );
				// 判断上传文件的后缀格式是否满足要求
				if(in_array ( $houzhui['extension'], array(
					'jpg',
					'gif',
					'png',
					'JPG',
					'GIF',
					'PNG',
					'txt',
					'php',
					'doc',
					'zip'
				) )){
				}else{
					// 不满足
					echo "444444";
				}
			}
		}
	}

	/**
	 * 上传一个文件
	 * 表单名 保存文件名 上传格式 最大上传 是否水印 水印地址
	 * 结果
	 * -1 没有上传文件
	 * -2 格式不允许
	 * -3 超过大小限制
	 * -4 附件不合法
	 * -5 上传失败
	 * @author 老马
	 * @version 2015-03-22
	 */
	public function uploadOne($filefield, $setname = '', $upary = '', $maxsize = 0, $watermark = false, $waterfile = '', $isfls = true)
	{
		global $_glb;
		$upfile = isset ( $_FILES[$filefield] ) ? $_FILES[$filefield] : '';
		if($upfile == '' || ! is_uploaded_file ( $upfile["tmp_name"] ) || $upfile["error"] != 0){
			return - 1;
		}
		$safeext = array(
			'jpg',
			'jpeg',
			'gif',
			'png',
			'swf',
			'bmp',
			'txt',
			'zip',
			'rar',
			'doc',
			'mp3'
		);
		$exttrue = \CTOCODE_Files::getFileExt ( $upfile['name'] );
		$upary = empty ( $upary ) ? join ( ',', $safeext ) : $upary;
		// 格式不允许
		if(! in_array ( $exttrue, explode ( ',', $upary ) )){
			return - 2;
		}

		// 文件名包含这些 强制拒绝
		// $rege = "/\.(php|phtml|php3|php4|jsp|exe|dll|asp|cer|asa|shtml|shtm|aspx|asax|cgi|fcgi|pl)/i";
		// if( preg_replace( $rege , $upfile['name'] ) ){ return -4; }

		// 安全文件之外的 后缀重命名
		// $ext = in_array($exttrue, $safeext) ? $exttrue : $exttrue.'.rename';

		$ext = $exttrue;
		$isimg = 0;

		// 图片上传 源检测
		if(in_array ( $ext, array(
			'gif',
			'jpg',
			'jpeg',
			'png',
			'bmp'
		) )){
			$isimg = 1;
			$sparr = Array(
				"image/pjpeg",
				"image/jpeg",
				"image/gif",
				"image/png",
				"image/xpng",
				"image/x-png",
				"image/wbmp"
			);
			if($isfls)
				$sparr[] = "application/octet-stream";
			$imgfile_type = strtolower ( trim ( $upfile['type'] ) );
			if(! in_array ( $imgfile_type, $sparr )){
				return - 2;
			}
		}

		// 超过大小限制
		$maxsize = intval ( $maxsize );
		if($maxsize != 0 && isset ( $upfile['size'] ) && $upfile['size'] > $maxsize * 1000){
			return - 3;
		}
		$ntime = time ();
		if($setname == ''){
			$savename = ctoDate ( '/Y/md/', time () ) . ($ntime . rand ( 100, 69999 )) . '.' . $ext;
			$savefile = BAIYU_FILE . $savename;
		}else{
			$savename = $setname . '.' . $ext;
			$savefile = $setname . '.' . $ext;
		}
		$move = $this->filePut ( $upfile['tmp_name'], $savefile );
		$file_saved = $move == '' ? true : false;

		if($file_saved){
			@chmod ( $savefile, 0777 );
			$width = $height = $type = 0;
			if($isimg || $ext == 'swf'){
				$imagesize = @getimagesize ( $savefile );
				list($width,$height,$type) = ( array ) $imagesize;
				$size = $width * $height;
				if($size > 16777216 || $size < 4 || empty ( $type ) || ($isimg && ! in_array ( $type, array(
					1,
					2,
					3,
					6,
					13
				) ))){
					@unlink ( $savefile );
					return - 4;
				}
			}
			if($isimg && $watermark){
				// class image.func
				$waterfile = empty ( $waterfile ) ? (isset ( $_glb['waterfile'] ) ? $_glb['waterfile'] : '') : $waterfile;
				if($waterfile != ''){
					$waterfile = _CTOCODE_CONFIG_ . '/watermark/' . $waterfile;
					$waterpos = isset ( $_glb['waterpos'] ) ? $_glb['waterpos'] : 9;
					imagewatermark ( $savefile, $waterpos, $waterfile );
				}
			}
			$rs = array();
			$rs['isimg'] = $isimg;
			$rs['ext'] = $ext;
			$rs['filename'] = $upfile["name"];
			$rs['size'] = $upfile['size'];
			$rs['type'] = $upfile['type'];
			$rs['downurl'] = $savename;
			$rs['width'] = $rs['height'] = 0;
			if($isimg || $ext == 'swf'){
				$imagesize = @getimagesize ( $savefile );
				list($width,$height) = ( array ) $imagesize;
				$rs['width'] = $width;
				$rs['height'] = $height;
			}
			return ! empty ( $rs ) ? $rs : - 5;
		}
		return - 5;
	}
	// 上传 移动文件
	private function filePut($locafile, $new_path, $isFiledir = 0, $del_local = true)
	{
		if(empty ( $locafile ) || empty ( $new_path )){
			return '';
		}
		ctoFileDirCreate ( $new_path, 0777, $this->upload_dir );
		if(@move_uploaded_file ( $locafile, $new_path )){
			@unlink ( $locafile );
			$result = '';
		}else{
			$rns = $del_local ? @rename ( $locafile, $new_path ) : copy ( $locafile, $new_path );
			if($rns){
				$result = '';
			}else{
				$result = '移动文件失败';
			}
		}
		return $result;
	}
}