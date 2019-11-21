<?php
/**
 * 【ctocode】      核心文件 -数据库 
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
/**
 * 保存SESSION到Sqlite
 * @author WeakSun <52132522@qq.com>
 */
// namespace Think\Session\Driver;
// use SessionHandlerInterface;
// use PDO;
abstract class Sqlite2 implements SessionHandlerInterface
{
	protected static $tableNameName, $expire, $handler, $nowTime;
	public function __construct()
	{
		empty ( static::$expire ) && static::$expire = C ( 'SESSION_EXPIRE', null, false ) ? C ( 'SESSION_EXPIRE' ) : ini_get ( 'session.gc_maxlifetime' );
		empty ( static::$nowTime ) && static::$nowTime = isset ( $GLOBALS['_beginTime'] ) ? $GLOBALS['_beginTime'] : microtime ( true );
		empty ( static::$tableNameName ) && static::$tableNameName = C ( 'SESSION_TABLE' ) ? C ( 'SESSION_TABLE' ) : 'iSession';
		$dbFile = TEMP_PATH . 'Caches.tmp';
		$isCreate = is_file ( $dbFile );
		if(empty ( static::$handler )){
			static::$handler = new PDO ( "sqlite:{$dbFile}", null, null, array(
				PDO::ATTR_PERSISTENT => true 
			) );
			empty ( $isCreate ) && $this->exec ( "PRAGMA encoding = 'UTF8';PRAGMA temp_store = 2;PRAGMA auto_vacuum = 0;PRAGMA count_changes = 1;PRAGMA cache_size = 9000;" );
			$this->chkTable () || $this->createTable ();
		}
	}
	/**
	 * 创建SessionID
	 * @return string
	 */
	public function create_sid()
	{
		return uniqid ( sprintf ( '%08x', mt_rand ( 0, 2147483647 ) ) );
	}
	
	/**
	 * 打开session
	 * @param string $path
	 * @param string $name
	 * @return boolean
	 */
	public function open($path, $name)
	{
		return is_object ( static::$handler );
	}
	
	/**
	 * 关闭Session
	 * @return boolean
	 */
	public function close()
	{
		return true;
	}
	
	/**
	 * 读取Session
	 * @param string $id
	 * @return string
	 */
	public function read($id = null)
	{
		$table = static::$tableNameName;
		$sth = static::$handler->query ( "SELECT `value` FROM `{$table}` WHERE `id`='{$id}' AND `expire` > strftime('%s','now') LIMIT 1", PDO::FETCH_NUM );
		if(! empty ( $sth )){
			list($data) = $sth->fetch ();
			unset ( $sth );
		}else{
			$data = '';
		}
		return $data;
	}
	
	/**
	 * 写入Session
	 * @param string $id
	 * @param string $data
	 * @return integer
	 */
	public function write($id = null, $data = null)
	{
		$table = static::$tableNameName;
		$expire = ceil ( static::$expire + static::$nowTime );
		return $this->exec ( "REPLACE INTO `{$table}` VALUES('{$id}','{$data}',{$expire})" );
	}
	
	/**
	 * 销毁Session
	 * @param string $id
	 * @return integer
	 */
	public function destroy($id = 0)
	{
		$table = static::$tableNameName;
		return $this->exec ( "DELETE FROM `{$table}` WHERE `id` = '{$id}'" );
	}
	
	/**
	 * 垃圾回收
	 * @param string $expire
	 * @return integer
	 */
	public function gc($expire = 0)
	{
		$table = static::$tableNameName;
		return $this->exec ( "DELETE FROM `{$table}` WHERE `expire` < strftime('%s','now');VACUUM;" );
	}
	
	/**
	 * 检查当前表是否存在
	 * @return bool 返回检查结果，存在返回True，失败返回False
	 */
	protected function chkTable()
	{
		return in_array ( static::$tableNameName, $this->getTables () );
	}
	
	/**
	 * 获取当前数据库的数据表列表
	 * @return array 返回获取到的数据表列表数组
	 */
	protected function getTables()
	{
		$tables = $data = array();
		$sth = $this->query ( "SELECT `name` FROM `sqlite_master` WHERE `type` = 'table' UNION ALL SELECT `name` FROM `sqlite_temp_master`" );
		if(! empty ( $sth )){
			while($row = $sth->fetch ( PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT )){
				$tables[] = $row[0];
			}
			unset ( $sth, $row );
		}
		return $tables;
	}
	
	/**
	 * 创建当前数据表
	 * @return integer 成功返回1，失败返回0
	 */
	protected function createTable()
	{
		$tableName = static::$tableNameName;
		return $this->exec ( "CREATE TABLE IF NOT EXISTS `{$tableName}` (`id` VARCHAR PRIMARY KEY ON CONFLICT FAIL NOT NULL COLLATE 'NOCASE',`value` TEXT NOT NULL,`expire` INTEGER NOT NULL);" );
	}
	public function __call($method, $arguments)
	{
		if(method_exists ( self::$handler, $method )){
			return call_user_func_array ( array(
				self::$handler,
				$method 
			), $arguments );
		}else{
			E ( __CLASS__ . ':' . $method . L ( '_METHOD_NOT_EXIST_' ) );
			return;
		}
	}
}
abstract class CTOCODE_DB
{
	public $db = NULL;
	public $table = NULL;
	public $config = array(
		'dsn' => 'mysql:dbname=test;host=127.0.0.1',
		'user' => 'root',
		'password' => 'root',
		'charset' => 'utf8',
		'persistent' => false 
	); // 持久性链接
	public $options = array(
		PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " 
	);
	public function __construct($table)
	{
		$this->table = $table;
		$this->options[PDO::MYSQL_ATTR_INIT_COMMAND] .= $this->config['charset'];
		if($this->config['persistent']){
			$this->options[PDO::ATTR_PERSISTENT] = true;
		}
		try{
			// 长连接，如有需要将array(PDO::ATTR_PERSISTENT=>true) 作为第四个参数
			$this->db = $pdo = new \PDO ( $this->config['dsn'], $this->config['user'], $this->config['password'], $this->options );
			
			// $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_SILENT); //不显示错误
			
			$pdo->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING ); // 显示警告错误，并继续执行
			
			$this->db->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION ); // 产生致命错误，PDOException
				                                                                       // $this->db->exec('set names utf8');
		}
		catch ( \Exception $e ){
			throw new \Exception ( $e->getMessage () );
		}
	}
	public function query($sql)
	{
		return $re = $this->db->query ( $sql );
	}
	public function select($sql) /*查询*/ 
            {
		$re = $this->query ( $sql );
		return $re->fetchAll ();
	}
	public function add($arr = array())
	{ /* 添加 */
		$sql = 'insert into ' . $this->table . ' (' . implode ( ',', array_keys ( $arr ) ) . ')';
		$sql .= " values ('";
		$sql .= implode ( "','", array_values ( $arr ) );
		$sql .= "')";
		$stmt = $this->db->prepare ( $sql );
		$stmt->execute ( $arr );
		echo $this->db->lastinsertid ();
	}
	public function update($arr, $where = ' where 1 limit 1')
	{ /* 修改 */
		// $sql = "UPDATE `user` SET `password`=:password WHERE `user_id`=:userId";
		$sql = 'update ' . $this->table . ' set ';
		foreach($arr as $k=>$v){
			$sql .= $k . "='" . $v . "',";
		}
		$sql = rtrim ( $sql, ',' );
		$sql .= $where;
		$stmt = $this->db->prepare ( $sql );
		$stmt->execute ( $arr );
		return $stmt->rowCount ();
	}
	public function delete($sql)
	{ /* 删除 */
		$stmt = $this->db->prepare ( $sql );
		$stmt->execute ();
		return $stmt->rowCount ();
	}
}
// //可以每一张表一个类 统一继承DB类。。。。。
class Article extends CTOCODE_DB
{
}
