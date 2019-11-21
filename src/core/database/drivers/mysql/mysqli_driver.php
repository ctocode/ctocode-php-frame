<?php
class Cto_Db_Driver
{
	public $data = array();
	public function db_connect($config)
	{
		// 连接MySQL
		$link = mysqli_connect ( $config['db_host'], $config['db_name'], $config['db_password'] );
		if(! $link){ // 判断是否存在
			die ( 'Connect Database Error:<br>' . mysqli_error () );
		}else{
			echo 'Connect Database Success(连接成功)<br>';
		}
	}
	public function db_select()
	{
	}
}