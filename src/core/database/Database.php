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
// =================================
// require_once (dirname ( __FILE__ ) . "/config.php");
// require_once ("../".dirname ( __FILE__ ) . "/config.php");
// demo_database ();
// 案例:数据库连接查询
function demo_database()
{
	$db = new database ( $config );
	$sql = "";
	// $sql = " SELECT COUNT(DISTINCT(dch.name )) AS d FROM `db_click_hits` AS dch ";
	$result = $db->querty ( $sql );
	var_dump ( $result );
	// demo_cache ( "db", $result ); // 插入缓存
}
// =================================
class database
{
	protected $_dbConf = array(
		'dsn' => null, // 完整的DSN连接数据库的字符串描述。
		'hostname' => null, // 数据库服务器的主机名。
		'username' => null, // 用户名
		'password' => null, // 密码
		'database' => null, // 数据库的名称
		'hostport' => 3306, // 端口
		'dbdriver' => null, // 数据库驱动程序。例如：mysqli。
		'dbprefix' => null // 你可以添加一个可选的前缀，这将增加的表名使用查询生成器类的时候
	);
	protected $_dbObj = null; // 链接标识符

	/*
	 * | 目前支持：
	 * | cubrid，IBASE，MSSQL，MySQL，mysqli，oci8，
	 * | ODBC，PDO，Postgre，SQLite，sqlite3，sqlsrv
	 * |
	 */
	public $pconnect = FALSE; // 真/假---是否使用持久连接
	public $db_debug = TRUE; // 真/假---是否要显示数据库错误。
	public $cache_on = FALSE; // 真/假---启用/禁用查询缓存
	public $cachedir = null; // 文件夹缓存文件应该存放的路径
	public $char_set = 'utf8'; // 使用的字符集
	public $dbcollat = 'utf8_general_ci'; // 字整理用于与数据库
	/*
	 * | 注：MySQL和mysqli数据库，此设置仅用于
	 * | 作为备份，如果你的服务器运行PHP或MySQL < < 5.2.3 5.0.7
	 * |（在表创建查询DB伪造了）。
	 * | 有mysqli_real_escape_string()PHP的不相容性
	 * | 可以如果您使用的是使你的网站容易受到SQL注入
	 * | 多字节字符集和正在运行的版本低于。
	 * | 使用拉丁文或UTF-8数据库字符集和整理的网站不受影响。
	 */
	public $swap_pre = null; // 默认表前缀，应与dbprefix交换
	public $encrypt = FALSE; // 是否使用加密连接。
	public $compress = FALSE; // 是否使用客户端压缩（MySQL只）
	public $stricton = FALSE; // 真/假部队严格模式的关系 为确保严格SQL而发展良好的| -
	public $failover = array(); // 阵列阵列具有0个或更多的数据连接，如果主失败。
	public $save_queries = TRUE; // 真/假是否“保存”所有执行的查询。
	/*
	 * | 注意：禁用这也将有效地禁用
	 * | $this->db->last_query() DB查询和分析。
	 * | 当你运行一个查询，此设置设置为True（默认），
	 * | CodeIgniter将存储的SQL语句进行调试。
	 * | 然而，这可能会导致高的内存使用情况，尤其是如果你运行
	 * | 大量SQL查询…禁用此来避免这个问题。
	 */
	public $querysql = null; // sql语句
	                         // 构造函数,初始化,类成员(变量)的值
	function __construct($config = array ())
	{
		// foreach遍历 初始化属性
		foreach($config as $key=>$val){
			$this->$key = $val;
		}
	}
	public function dbConnect()
	{
		switch($this->dbdriver)
		{
			case 'mysqli':
				// 连接 MySQL
				$this->_dbObj = mysqli_connect ( $this->_dbConf['hostname'], $this->_dbConf['username'], $this->_dbConf['password'] );
				// 判断是否存在
				if(! $this->_dbObj){
					die ( '【错误原因】   ' . mysqli_error () . '<br>&nbsp;连接数据库失败，可能数据库密码不对或数据库服务器出错！<br>' );
					die ( 'Connect Database Error:<br>' . mysqli_error () );
					exit ();
				}else{
					echo 'Connect Database Success(连接成功)<br>';
				}
				break;
		}
	}
	public function dbClose()
	{
		switch($this->dbdriver)
		{
			case 'mysqli':
				// 关闭连接
				if(mysqli_close ( $this->_dbObj )){
					// echo "Close Database Success";
				}else{
					// echo "Close Database Error";
				}
				break;
		}
	}

	// 查询数据
	function querty($sql = NULL)
	{
		// 选择数据库
		mysqli_select_db ( $this->_dbConf['database'], $this->_dbObj );
		// 执行 sql查询语句
		mysqli_query ( "SET NAMES utf8" );
		$result = mysqli_query ( $sql, $this->_dbObj );
		if(FALSE == $result){
			die ( '【错误原因】   ' . mysqli_error () . '<br>' );
			exit ( "Querry failed!" );
		}
		$lists = array();
		$total_rows = mysqli_num_rows ( $result );

		if($total_rows == 0){
			echo $total_rows;
			exit ();
		}else{
			while($row = mysqli_fetch_array ( $result, MYSQL_ASSOC )){
				$lists[] = $row;
			}
		}
		mysqli_free_result ( $result );
		return $lists;

		// return mysqli_affected_rows ( $this->_dbObj );
		for($i = 0;$i < mysqli_num_rows ( $result );$i ++){ // 取总行数
		}
		$row = mysqli_fetch_array ( $result, MYSQL_ASSOC ); // 取数组
		$meta_c = mysqli_fetch_row ( $result );
		var_dump ( $row );
		var_dump ( $meta_c );
		echo $meta_c;
		echo "1";
		exit ();
		while($row = mysqli_fetch_row ( $result[0] )){
			if(strtolower ( $row[0] ) == strtolower ( $tbname )){
				mysqli_freeresult ( $this->result[0] );
				return true;
			}
		}
		var_dump ( mysqli_fetch_row ( $result ) );

		// var_dump ( $row );

		$j = 0;

		for($i = 0;$i < mysqli_num_rows ( $result );$i ++){ // 取总行数

			$meta_c = 0;
			if($meta_c = mysqli_fetch_row ( $result )) // 取每一行的结果集
			{
				while($j < mysqli_num_fields ( $result )) // 取一行的列数
				{
					echo $meta_c[$j];
				}
				echo "<br>";
			} // while;
			$j = 0;
		}
		// 释放结果集
		mysqli_free_result ( $result );
		return $result;
		// 关闭连接
		// if (mysqli_close ( $this->$_dbObj )) {
		// echo "Close mysql Success";
		// } else {
		// echo "Close mysql Error";
		// }
	}

	/**
	 * 查询数据
	 * @param string $database
	 * @return boolean
	 */
	function connect_mysql($database = NULL)
	{
		// sql 语句
		$select_sql = "";
		// 执行查询语句
		$result = mysqli_query ( $select_sql, $link );

		// $row = mysqli_fetch_array ( $result ); // 取数组
		// while ( $row = mysqli_fetch_row ( $result [0] ) ) {
		// if (strtolower ( $row [0] ) == strtolower ( $tbname )) {
		// mysqli_freeresult ( $this->result [0] );
		// return true;
		// }
		// }
		// var_dump ( mysqli_fetch_row ( $result ) );
		// // $row = mysqli_num_rows ( $res );// 返回行数
		// var_dump ( $row );
		if(FALSE == $result){
			echo "Querry failed!";
		}
		$i = 0;
		$j = 0;
		while($i ++ < mysqli_num_rows ( $result )) // 取总行数
		{
			$meta_c = 0;
			if($meta_c = mysqli_fetch_row ( $result )) // 取每一行的结果集
			{
				while($j < mysqli_num_fields ( $result )) // 取一行的列数
				{
					echo $meta_c[$j];
				}
				echo "<br>";
			} // while;
			$j = 0;
		}
		// 释放结果集
		mysqli_free_result ( $result );
	}
	// 创建数据库
	// Create database，创建一个数据库，名字为 my_db
	function CreateDB()
	{
		if(mysqli_query ( "CREATE DATABASE my_db", $link )){
			echo "Database created is Successful(or Correct)";
			echo "<br>";
		}else{
			echo "Error creating database: " . "<br>" . mysqli_error ();
			echo "<br>";
		}
		echo "<br>";
	}
	// 创建表
	function CreateTable()
	{
		// Create table in my_db database 创建数据表 函数
		mysqli_select_db ( "my_db", $link );
		$sql = "CREATE TABLE Persons
				(
					FirstName varchar(15),
					LastName varchar(15),
					Age int
				)";
		if(mysqli_query ( $sql, $link )){
			echo "Table created is Successful(or Correct)";
			echo "<br>";
		}else{
			echo "Error creating table: " . "<br>" . mysqli_error ();
			echo "<br>";
		}
		echo "<br>";
	}
	function login()
	{
		$value = $_POST['value'];
		$obj = json_decode ( $value );
		$uname = $obj->uname;
		$upassword = $obj->upassword;
		if(! empty ( $row )){
			$mselect = "select * from `quser` where uname = '" . $uname . "' and upass = '" . $upassword . "'";
			$res = mysqli_query ( $mselect );
			$row = mysqli_num_rows ( $res );
			if(! empty ( $row )){
				$arr = array();
				// 结果集中取得一行作为关联数组。
				// 用assoc来取得结果集中的 一行 是array（[username]=>'test',[password]=>'123456'）
				while($row = mysqli_fetch_assoc ( $res )){
					$arr[] = $row;
				}
				die ( json_encode ( $arr ) );
			}else{
				printf ( "nopass" );
			}
		}else{
			printf ( "nouser" );
		}
		die ();
	}
}
