<?php

namespace ctocode\phpframe\core\database;

use ctocode\library\CtoLogger;

class CtoDbMysql
{
	public static $pdo_r = null;
	public static $pdo_w = null;
	public static $connected = false;
	public static $err = "";
	public static $count = 0;
	public static $cLogs = null;
	function __construct($logPath, $dbActR, $dbActW)
	{
		// 数据库 连接 读帐号
		try{
			$dbHost = $dbActR['host'];
			$dbUser = $dbActR['user'];
			$dbPass = $dbActR['pass'];
			$dbName = $dbActR['dbname'];
			self::$pdo_r = new \PDO ( 'mysql:dbname=' . $dbName . ';host=' . $dbHost, $dbUser, $dbPass );
			if(self::$pdo_r){
				self::$pdo_r->query ( 'set names utf8;' );
				self::$connected = true;
			}
		}
		catch ( \PDOException $e ){
			// echo $e->getMessage();
			self::$connected = false;
			self::$err = "db connect error ...";
			return;
		}
		// 数据库 连接 写帐号
		try{
			$dbHost = $dbActW['host'];
			$dbUser = $dbActW['user'];
			$dbPass = $dbActW['pass'];
			$dbName = $dbActW['dbname'];
			self::$pdo_w = new \PDO ( 'mysql:dbname=' . $dbName . ';host=' . $dbHost, $dbUser, $dbPass );
			if(self::$pdo_w){
				self::$pdo_w->query ( 'set names utf8;' );
				self::$connected = true;
			}
		}
		catch ( \PDOException $e ){
			// echo $e->getMessage();
			self::$connected = false;
			self::$err = "db connect error ...";
		}
		self::$cLogs = new CtoLogger ( $logPath, "dblog" );
	}
	function getErrInfo()
	{
		// 获取错误信息
		return self::$err;
	}
	function getCount()
	{
		// 获取记录数
		return self::$count;
	}
	function getConnected()
	{
		// 获取是否连接 成攻
		return self::$connected;
	}
	function getValue($row, $field)
	{
		// 获取指定字段值
		$str = serialize ( $row );
		if(strstr ( $str, ':"' . $field . '";' )){
			return $row[$field];
		}else{
			return "";
		}
	}
	function getValueInt($row, $field)
	{
		// 获取指定字段值
		$str = serialize ( $row );
		if(strstr ( $str, ':"' . $field . '";' )){
			return $row[$field];
		}else{
			return "0";
		}
	}
	function sqlQuery($sql)
	{
		// 查询SQL语句
		try{
			$result = self::$pdo_r->query ( $sql );
			return $result;
		}
		catch ( \PDOException $e ){
			self::$err = "sql error :::$sql";
		}
	}
	function sqlGetValueOne($sql)
	{
		// 查询SQL语句 返回第一个值
		try{
			$result = self::$pdo_r->query ( $sql );
			if($result){
				$rows = $result->fetch ();
				return $rows[0];
			}else{
				return "0";
			}
		}
		catch ( \PDOException $e ){
			self::$err = "sql error :::$sql";
		}
	}
	function sqlEexec($sql)
	{
		// 执行exec SQL语句
		try{
			$logTime = time ();
			self::$cLogs->writeLog ( $logTime . "=>" . $sql ); // 写日志
			self::$count = self::$pdo_w->exec ( $sql );
			self::$cLogs->writeLog ( $logTime . "=>结果：" . self::$count ); // 写日志
			return self::$count;
		}
		catch ( \PDOException $e ){
			self::$count = 0;
			self::$err = "sql error :::$sql";
		}
	}
	function sqlInsert($table, $fields)
	{
		// 执行insert into 语句
		$sqlFields = "";
		$sqlValue = "";
		while(list($key,$value) = each ( $fields )){
			$sqlFields = $sqlFields . $key . ",";
			$sqlValue = $sqlValue . sprintf ( " '%s',", $value );
		}
		$sqlFields = substr ( $sqlFields, 0, strlen ( $sqlFields ) - 1 );
		$sqlValue = substr ( $sqlValue, 0, strlen ( $sqlValue ) - 1 );
		$insql = sprintf ( " insert into %s (%s) values (%s) ", $table, $sqlFields, $sqlValue );
		$logTime = time ();
		self::$cLogs->writeLog ( $logTime . "=>" . $insql ); // 写日志
		self::$count = self::$pdo_w->exec ( $insql );
		$id = self::$pdo_w->lastInsertId ();
		self::$cLogs->writeLog ( $logTime . "=>结果：" . $id ); // 写日志
		return $id;
	}
	function sqlUpdate($table, $fields, $where)
	{
		// 执行update 语句
		$sqlFields = "";
		while(list($key,$value) = each ( $fields )){
			$sqlFields = $sqlFields . sprintf ( " %s='%s',", $key, $value );
		}
		$sqlFields = substr ( $sqlFields, 0, strlen ( $sqlFields ) - 1 );
		$upsql = sprintf ( "update %s set %s where %s ", $table, $sqlFields, $where );
		$logTime = time ();
		self::$cLogs->writeLog ( $logTime . "=>" . $upsql ); // 写日志
		self::$count = self::$pdo_w->exec ( $upsql );
		self::$cLogs->writeLog ( $logTime . "=>结果：" . self::$count ); // 写日志
		return self::$count;
	}
}