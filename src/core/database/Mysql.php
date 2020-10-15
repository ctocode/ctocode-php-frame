<?php

/**
 * 查询数据
 *
 * @param string $database
 * @return boolean
 *
 */
function ctoSqlconnect_mysql($database = NULL)
{
	// connect_mysql ( 'benshouji' );
	// 连接MySQL
	$link = mysqli_connect("localhost", "root", "123456");
	if (!$link) { // 判断是否存在
		die('Connect Database Error:<br/>' . mysqli_error());
	} else {
		echo 'Connect Database Success(连接成功)<br/>';
	}
	// 选择数据库
	mysqli_select_db($database, $link);
	// sql 语句
	$select_sql = "";
	// 执行查询语句
	$result = mysqli_query($select_sql, $link);

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

	if (FALSE == $result) {
		echo "Querry failed!";
	}
	$i = 0;
	$j = 0;
	while ($i++ < mysqli_num_rows($result)) // 取总行数
	{
		$meta_c = 0;
		if ($meta_c = mysqli_fetch_row($result)) // 取每一行的结果集
		{
			while ($j < mysqli_num_fields($result)) // 取一行的列数
			{
				echo $meta_c[$j];
			}
			echo "<br>";
		} // while;
		$j = 0;
	}
	// 释放结果集
	mysqli_free_result($result);
	// 关闭连接
	if (mysqli_close($link)) {
		// echo "Close Database Success";
	} else {
		// echo "Close Database Error";
	}
}
// 创建数据库
// Create database，创建一个数据库，名字为 my_db
function ctoSqlCreateDB()
{
	if (mysqli_query("CREATE DATABASE my_db", $link)) {
		echo "Database created is Successful(or Correct)";
		echo "<br/>";
	} else {
		echo "Error creating database: " . "<br/>" . mysqli_error();
		echo "<br/>";
	}
	echo "<br/>";
}
// 创建表
function ctoSqlCreateTable()
{
	// Create table in my_db database 创建数据表 函数
	mysqli_select_db("my_db", $link);
	$sql = "CREATE TABLE Persons
				(
					FirstName varchar(15),
					LastName varchar(15),
					Age int
				)";
	if (mysqli_query($sql, $link)) {
		echo "Table created is Successful(or Correct)";
		echo "<br/>";
	} else {
		echo "Error creating table: " . "<br/>" . mysqli_error();
		echo "<br/>";
	}
	echo "<br/>";
}
function ctoSqllogin()
{
	$value = $_POST['value'];
	$obj = json_decode($value);
	$uname = $obj->uname;
	$upassword = $obj->upassword;
	if (!empty($row)) {
		$mselect = "select * from `quser` where uname = '" . $uname . "' and upass = '" . $upassword . "'";
		$res = mysqli_query($mselect);
		$row = mysqli_num_rows($res);
		if (!empty($row)) {
			$arr = array();
			// 结果集中取得一行作为关联数组。
			// 用assoc来取得结果集中的 一行 是array（[username]=>'test',[password]=>'123456'）
			while ($row = mysqli_fetch_assoc($res)) {
				$arr[] = $row;
			}
			die(json_encode($arr));
		} else {
			printf("nopass");
		}
	} else {
		printf("nouser");
	}
	die();
}
/**
 * @action 数据转移
 */
function ctoSqlZhuanyi()
{
	return;
	set_time_limit(0); // 防止出现500 Internal Server Error
	$sql_select = "SELECT  n.title, n.intime, n.uptime, n.description, nc.content,
			CONCAT(n.classid, '/', c.classname) AS columnid,
			CONCAT('/d/file/',c.classpath,'/',f.path,'/',f.thumb) AS thumb
		FROM phome_ecms_news AS n
	  	LEFT JOIN phome_ecms_news_data_1 AS nc  ON nc.id = n.id
	  	LEFT JOIN phome_enewsclass       AS c   ON n.classid = c.classid
	  	LEFT JOIN phome_enewsfile        AS f   ON n.filename = f.fileid
		WHERE 1=1
		ORDER BY intime ASC;";

	$daochu = \think\facade\Db::query($sql_select);
	$tool = 0;
	foreach ($daochu as $key => $val) {
		$val['columnid'] = trim($val['columnid']);
		$field['columnid'] = $columnid;
		$field['adminid'] = 2; // 责任编辑
		$field['authorid'] = 0; // 作者
		$field['state'] = 0; // 状态
		$field['hits'] = 0; // 点击量

		$field['uptime'] = $val['uptime'];
		$field['intime'] = $val['intime'];
		$field['title'] = $val['title'];
		$field['content'] = $val['content'];
		// $field['content'] = stripcslashes ( $val['content'] );
		$field['description'] = trim($val['description']); // 描述
		$field['thumb'] = $val['thumb'];
		$sql_result = ctoDbWrite('', $field, ''); /* CI 自带的执行SQL */
		if ($sql_result == true) {
			$tool = ($tool + 1);
			echo $tool . '<br>';
		}
	}
}
