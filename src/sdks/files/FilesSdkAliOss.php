<?php

namespace ctocode\sdks\files;

use OSS\OssClient;
use OSS\Core\OssException;

/**
 * 上传到阿里云oss
 */
class FilesSdkAliOss extends FilesSdkCommon implements FilesSdkInterface
{
	protected $ossClient = null;
	protected $returnMsg = [];
	protected $bucket = [];
	public function __construct($ossConf = [])
	{
		$accessKeyId = $ossConf['sett_keyid'] ?? '';
		$accessKeySecret = $ossConf['sett_keysecret'] ?? '';
		$endpoint = $ossConf['sett_endpoint'] ?? '';
		$this->bucket = $ossConf['sett_bucket'] ?? '';
		if(empty ( $accessKeyId ) || empty ( $accessKeySecret ) || empty ( $endpoint ) || empty ( $this->bucket )){
			$this->ossClient = null;
			$this->returnMsg = array(
				'status' => 404,
				'error' => 'Not Found',
				'msg' => 'oss配置参数错误'
			);
		}else{
			$this->ossClient = new OssClient ( $accessKeyId, $accessKeySecret, $endpoint );
		}
	}
	/**
	 * 上传字符串作为object的内容
	 * @param OssClient $ossClient OSSClient实例
	 * @param string $bucket 存储空间名称
	 * @return null
	 */
	function putObject($object, $fileContent)
	{
		if(empty ( $this->ossClient )){
			return array(
				'status' => 404,
				'error' => 'Not Found',
				'msg' => 'oss配置参数错误'
			);
		}
		try{
			$this->ossClient->putObject ( $this->bucket, $object, $fileContent );
		}
		catch ( OssException $e ){
			// printf ( __FUNCTION__ . ": FAILED\n" );
			// printf ( $e->getMessage () . "\n" );
			return array(
				'status' => 404,
				'error' => 'Not Found',
				'msg' => 'oss 上传错误'
			);
		}
		return array(
			'status' => 200
		);
	}
	/**
	 * 上传指定的本地文件内容
	 * @param OssClient $ossClient OSSClient实例
	 * @param string $bucket 存储空间名称
	 * @return null
	 */
	function uploadFile($ossConf, $object, $filePath)
	{
		if(empty ( $this->ossClient )){
			return array(
				'status' => 404,
				'error' => 'Not Found',
				'msg' => 'oss配置参数错误'
			);
		}
		try{
			$this->ossClient->uploadFile ( $this->bucket, $object, $filePath );
		}
		catch ( OssException $e ){
			// printf ( __FUNCTION__ . ": FAILED\n" );
			// printf ( $e->getMessage () . "\n" );
			return array(
				'status' => 404,
				'error' => 'Not Found',
				'msg' => 'oss 上传错误'
			);
		}
		return array(
			'status' => 200
		);
	}

	// $accessKeyId = ;""; // <您从OSS获得的AccessKeyId>
	// $accessKeySecret = ""; // <您从OSS获得的AccessKeySecret>
	// $endpoint = "http://oss-cn-hangzhou.aliyuncs.com"; // <您选定的OSS数据中心访问域名，例如http://oss-cn-hangzhou.aliyuncs.com>
	// $bucket = ''; // hrhg-file <您使用的Bucket名字，注意命名规范>
	// $ossClient = '';
	/**
	 * 列出用户所有的Bucket
	 * @param OssClient $ossClient OssClient实例
	 * @return null
	 */
	function getBucketsList($ossClient)
	{
		$bucketList = null;
		try{
			$bucketListInfo = $ossClient->listBuckets ();
		}
		catch ( OssException $e ){
			printf ( __FUNCTION__ . ": FAILED\n" );
			printf ( $e->getMessage () . "\n" );
			return;
		}
		$bucketList = $bucketListInfo->getBucketList ();
		var_dump ( $bucketList );
		foreach($bucketList as $bucket){
			print ($bucket->getLocation () . "\t" . $bucket->getName () . "\t" . $bucket->getCreatedate () . "\n") ;
		}
	}
	/**
	 * 创建虚拟目录
	 * @param OssClient $ossClient OSSClient实例
	 * @param string $bucket 存储空间名称
	 * @return null
	 */
	function createDir($ossClient, $bucket)
	{
		try{
			$result = $ossClient->createObjectDir ( $bucket, "dir" );
			var_dump ( $result );
		}
		catch ( OssException $e ){
			printf ( __FUNCTION__ . ": FAILED\n" );
			printf ( $e->getMessage () . "\n" );
			return;
		}
		print (__FUNCTION__ . ": OK" . "\n") ;
	}
}