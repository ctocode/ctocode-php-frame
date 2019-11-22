<?php
/*
 * php开启多进程的方法
 * author http://www.lai18.com
 * date 2015-04-19
 * version 1
 */
$IP = '192.168.1.1'; // Windows電腦的IP
$Port = '5900'; // VNC使用的Port
$ServerPort = '9999'; // Linux Server對外使用的Port
$RemoteSocket = false; // 連線到VNC的Socket
function SignalFunction($Signal)
{
	// 這是主Process的訊息處理函數
	global $PID; // Child Process的PID
	switch($Signal)
	{
		case SIGTRAP:
		case SIGTERM:
			// 收到結束程式的Signal
			if($PID){
				// 送一個SIGTERM的訊號給Child告訴他趕快結束掉嘍
				posix_kill ( $PID, SIGTERM );
				// 等待Child Process結束，避免zombie
				pcntl_wait ( $Status );
			}
			// 關閉主Process開啟的Socket
			DestroySocket ();
			exit ( 0 ); // 結束主Process
			break;
		case SIGCHLD:
   /*
當Child Process結束掉時，Child會送一個SIGCHLD訊號給Parrent
當Parrent收到SIGCHLD，就知道Child Process已經結束嘍 ，該做一些
結束的動作*/
   unset ( $PID ); // 將$PID清空，表示Child Process已經結束
			pcntl_wait ( $Status ); // 避免Zombie
			break;
		default:
	}
}
function ChildSignalFunction($Signal)
{
	// 這是Child Process的訊息處理函數
	switch($Signal)
	{
		case SIGTRAP:
		case SIGTERM:
			// Child Process收到結束的訊息
			DestroySocket (); // 關閉Socket
			exit ( 0 ); // 結束Child Process
		default:
	}
}
function ProcessSocket($ConnectedServerSocket)
{
	// Child Process Socket處理函數
	// $ConnectedServerSocket -> 外部連進來的Socket
	global $ServerSocket, $RemoteSocket, $IP, $Port;
	$ServerSocket = $ConnectedServerSocket;
	declare(ticks = 1)
		; // 這一行一定要加，不然沒辦法設定訊息處理函數。
		  // 設定訊息處理函數
	if(! pcntl_signal ( SIGTERM, "ChildSignalFunction" ))
		return;
	if(! pcntl_signal ( SIGTRAP, "ChildSignalFunction" ))
		return;
	// 建立一個連線到VNC的Socket
	$RemoteSocket = socket_create ( AF_INET, SOCK_STREAM, SOL_TCP );
	// 連線到內部的VNC
	@$RemoteConnected = socket_connect ( $RemoteSocket, $IP, $Port );
	if(! $RemoteConnected)
		return; // 無法連線到VNC 結束
		        // 將Socket的處理設為Nonblock，避免程式被Block住
	if(! socket_set_nonblock ( $RemoteSocket ))
		return;
	if(! socket_set_nonblock ( $ServerSocket ))
		return;
	while(true){
		// 這邊我們採用pooling的方式去取得資料
		$NoRecvData = false; // 這個變數用來判別外部的連線是否有讀到資料
		$NoRemoteRecvData = false; // 這個變數用來判別VNC連線是否有讀到資料
		@$RecvData = socket_read ( $ServerSocket, 4096, PHP_BINARY_READ );
		// 從外部連線讀取4096 bytes的資料
		@$RemoteRecvData = socket_read ( $RemoteSocket, 4096, PHP_BINARY_READ );
		// 從vnc連線連線讀取4096 bytes的資料
		if($RemoteRecvData === ''){
			// VNC連線中斷，該結束嘍
			echo "Remote Connection Close\n";
			return;
		}
		if($RemoteRecvData === false){
			/*
			 *
			 * 由於我們是採用nonblobk模式
			 *
			 * 這裡的情況就是vnc連線沒有可供讀取的資料
			 *
			 */
			$NoRemoteRecvData = true;
			// 清除掉Last Errror
			socket_clear_error ( $RemoteSocket );
		}
		if($RecvData === ''){
			// 外部連線中斷，該結束嘍
			echo "Client Connection Close\n";
			return;
		}
		if($RecvData === false){
			/*
			 *
			 * 由於我們是採用nonblobk模式
			 *
			 * 這裡的情況就是外部連線沒有可供讀取的資料
			 *
			 */
			$NoRecvData = true;
			// 清除掉Last Errror
			socket_clear_error ( $ServerSocket );
		}
		if($NoRecvData && $NoRemoteRecvData){
			// 如果外部連線以及VNC連線都沒有資料可以讀取時，
			// 就讓程式睡個0.1秒，避免長期佔用CPU資源
			usleep ( 100000 );
			// 睡醒後，繼續作pooling的動作讀取socket
			continue;
		}
		// Recv Data
		if(! $NoRecvData){
			// 外部連線讀取到資料
			while(true){
				// 把外部連線讀到的資料，轉送到VNC連線上
				@$WriteLen = socket_write ( $RemoteSocket, $RecvData );
				if($WriteLen === false){
					// 由於網路傳輸的問題，目前暫時無法寫入資料
					// 先睡個0.1秒再繼續嘗試。
					usleep ( 100000 );
					continue;
				}
				if($WriteLen === 0){
					// 遠端連線中斷，程式該結束了
					echo "Remote Write Connection Close\n";
					return;
				}
				// 從外部連線讀取的資料，已經完全送給VNC連線時，中斷這個迴圈。
				if($WriteLen == strlen ( $RecvData ))
					break;
				// 如果資料一次送不完就得拆成好幾次傳送，直到所有的資料全部送出為止
				$RecvData = substr ( $RecvData, $WriteLen );
			}
		}
		if(! $NoRemoteRecvData){
			// 這邊是從VNC連線讀取到的資料，再轉送回外部的連線
			// 原理跟上面差不多不再贅述
			while(true){
				@$WriteLen = socket_write ( $ServerSocket, $RemoteRecvData );
				if($WriteLen === false){
					usleep ( 100000 );
					continue;
				}
				if($WriteLen === 0){
					echo "Remote Write Connection Close\n";
					return;
				}
				if($WriteLen == strlen ( $RemoteRecvData ))
					break;
				$RemoteRecvData = substr ( $RemoteRecvData, $WriteLen );
			}
		}
	}
}
function DestroySocket()
{
	// 用來關閉已經開啟的Socket
	global $ServerSocket, $RemoteSocket;
	if($RemoteSocket){
		// 如果已經開啟VNC連線
		// 在Close Socket前必須將Socket shutdown不然對方不知到你已經關閉連線了
		@socket_shutdown ( $RemoteSocket, 2 );
		socket_clear_error ( $RemoteSocket );
		// 關閉Socket
		socket_close ( $RemoteSocket );
	}
	// 關閉外部的連線
	@socket_shutdown ( $ServerSocket, 2 );
	socket_clear_error ( $ServerSocket );
	socket_close ( $ServerSocket );
}
// 這裡是整個程式的開頭，程式從這邊開始執行
// 這裡首先執行一次fork
$PID = pcntl_fork ();
if($PID == - 1)
	die ( "could not fork" );
// 如果$PID不為0表示這是Parrent Process
// $PID就是Child Process
// 這是Parrent Process 自己結束掉，讓Child成為一個Daemon。
if($PID)
	die ( "Daemon PID:$PID\n" );
// 從這邊開始，就是Daemon模式在執行了
// 將目前的Process跟終端機脫離成為daemon模式
if(! posix_setsid ())
	die ( "could not detach from terminal\n" );
// 設定daemon 的訊息處理函數
declare(ticks = 1)
	;
if(! pcntl_signal ( SIGTERM, "SignalFunction" ))
	die ( "Error!!!\n" );
if(! pcntl_signal ( SIGTRAP, "SignalFunction" ))
	die ( "Error!!!\n" );
if(! pcntl_signal ( SIGCHLD, "SignalFunction" ))
	die ( "Error!!!\n" );
// 建立外部連線的Socket
$ServerSocket = socket_create ( AF_INET, SOCK_STREAM, SOL_TCP );
// 設定外部連線監聽的IP以及Port，IP欄位設0，表示經聽所有介面的IP
if(! socket_bind ( $ServerSocket, 0, $ServerPort ))
	die ( "Cannot Bind Socket!\n" );
// 開始監聽Port
if(! socket_listen ( $ServerSocket ))
	die ( "Cannot Listen!\n" );
// 將Socket設為nonblock模式
if(! socket_set_nonblock ( $ServerSocket ))
	die ( "Cannot Set Server Socket to Block!\n" );
// 清空$PID變數，表示目前沒有任何的Child Process
unset ( $PID );
while(true){
	// 進入pooling模式，每隔1秒鐘就去檢查有沒有連線進來。
	sleep ( 1 );
	// 檢查有沒有連線進來
	@$ConnectedServerSocket = socket_accept ( $ServerSocket );
	if($ConnectedServerSocket !== false){
		// 有人連進來嘍
		// 起始一個Child Process用來處理連線
		$PID = pcntl_fork ();
		if($PID == - 1)
			die ( "could not fork" );
		if($PID)
			continue; // 這是daemon process，繼續回去監聽。
			          // 這裡是Child Process開始
			          // 執行Socket裡函數
		ProcessSocket ( $ConnectedServerSocket );
		// 處理完Socket後，結束掉Socket
		DestroySocket ();
		// 結束Child Process
		exit ( 0 );
	}
}
// =============================PHP多线程批量采集下载美女图片的实现代码

// 使用curl的多线程，另外curl可以设置请求时间，遇到很慢的url资源，可以果断的放弃，这样没有阻塞，另外有多线程请求，效率应该比较高
// 参考：《CURL的学习和应用[附多线程]》 http://www.lai18.com/content/372470.html

// 我们再来测试一下；

/**
 * curl 多线程
 *
 * @author http://www.lai18.com
 * @param array $array
 *        	并行网址
 * @param int $timeout
 *        	超时时间
 * @return mixed
 */
function Curl_http($array, $timeout = '15')
{
	$res = array();
	
	$mh = curl_multi_init (); // 创建多个curl语柄
	
	foreach($array as $k=>$url){
		$conn[$k] = curl_init ( $url ); // 初始化
		
		curl_setopt ( $conn[$k], CURLOPT_TIMEOUT, $timeout ); // 设置超时时间
		curl_setopt ( $conn[$k], CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)' );
		curl_setopt ( $conn[$k], CURLOPT_MAXREDIRS, 7 ); // HTTp定向级别 ，7最高
		curl_setopt ( $conn[$k], CURLOPT_HEADER, false ); // 这里不要header，加块效率
		curl_setopt ( $conn[$k], CURLOPT_FOLLOWLOCATION, 1 ); // 302 redirect
		curl_setopt ( $conn[$k], CURLOPT_RETURNTRANSFER, 1 ); // 要求结果为字符串且输出到屏幕上
		curl_setopt ( $conn[$k], CURLOPT_HTTPGET, true );
		
		curl_multi_add_handle ( $mh, $conn[$k] );
	}
	// 防止死循环耗死cpu 这段是根据网上的写法
	do{
		$mrc = curl_multi_exec ( $mh, $active ); // 当无数据，active=true
	}while($mrc == CURLM_CALL_MULTI_PERFORM); // 当正在接受数据时
	while($active and $mrc == CURLM_OK){ // 当无数据时或请求暂停时，active=true
		if(curl_multi_select ( $mh ) != - 1){
			do{
				$mrc = curl_multi_exec ( $mh, $active );
			}while($mrc == CURLM_CALL_MULTI_PERFORM);
		}
	}
	
	foreach($array as $k=>$url){
		if(! curl_errno ( $conn[$k] )){
			$data[$k] = curl_multi_getcontent ( $conn[$k] ); // 数据转换为array
			$header[$k] = curl_getinfo ( $conn[$k] ); // 返回http头信息
			curl_close ( $conn[$k] ); // 关闭语柄
			curl_multi_remove_handle ( $mh, $conn[$k] ); // 释放资源
		}else{
			unset ( $k, $url );
		}
	}
	
	curl_multi_close ( $mh );
	
	return $data;
}

// 参数接收
$callback = $_GET['callback'];
$hrefs = $_GET['hrefs'];
$urlarray = explode ( ',', trim ( $hrefs, ',' ) );
$date = date ( 'Ymd', time () );
// 实例化
// $img = new HttpImg ();
$stime = $img->getMicrotime (); // 开始时间

$data = $img->Curl_http ( $urlarray, '20' ); // 列表数据
mkdir ( './img/' . $date, 0777 );
foreach(( array ) $data as $k=>$v){
	preg_match_all ( "/(href|src)=([\"|']?)([^ \"'>]+.(jpg|png|PNG|JPG|gif))\2/i", $v, $matches[$k] );
	if(count ( $matches[$k][3] ) > 0){
		$dataimg = $img->Curl_http ( $matches[$k][3], '20' ); // 全部图片数据二进制
		$j = 0;
		foreach(( array ) $dataimg as $kk=>$vv){
			if($vv != ''){
				$rand = rand ( 1000, 9999 );
				$basename = time () . "_" . $rand . "." . jpg; // 保存为jpg格式的文件
				$fname = './img/' . $date . "/" . "$basename";
				file_put_contents ( $fname, $vv );
				$j ++;
				echo "创建第" . $j . "张图片" . "$fname" . "<br>";
			}else{
				unset ( $kk, $vv );
			}
		}
	}else{
		unset ( $matches );
	}
}
$etime = $img->getMicrotime (); // 结束时间
echo "用时" . ($etime - $stime) . "秒";
exit ();

// 2、给出一段PHP多线程、与For循环，抓取百度搜索页面的PHP代码示例：
class CTOCODE_Thread
{
}
/*
 * http://zyan.cc/
 *
 * 张宴
 *
 *
 *
 */
class test_thread_run extends CTOCODE_Thread
{
	public $url;
	public $data;
	public function __construct($url)
	{
		$this->url = $url;
	}
	public function run()
	{
		if(($url = $this->url)){
			$this->data = model_http_curl_get ( $url );
		}
	}
}
function model_thread_result_get($urls_array)
{
	foreach($urls_array as $key=>$value){
		$thread_array[$key] = new test_thread_run ( $value["url"] );
		$thread_array[$key]->start ();
	}
	
	foreach($thread_array as $thread_array_key=>$thread_array_value){
		while($thread_array[$thread_array_key]->isRunning ()){
			usleep ( 10 );
		}
		if($thread_array[$thread_array_key]->join ()){
			$variable_data[$thread_array_key] = $thread_array[$thread_array_key]->data;
		}
	}
	return $variable_data;
}
function model_http_curl_get($url, $userAgent = "")
{
	$userAgent = $userAgent ? $userAgent : 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.2)';
	$curl = curl_init ();
	curl_setopt ( $curl, CURLOPT_URL, $url );
	curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $curl, CURLOPT_TIMEOUT, 5 );
	curl_setopt ( $curl, CURLOPT_USERAGENT, $userAgent );
	$result = curl_exec ( $curl );
	curl_close ( $curl );
	return $result;
}

for($i = 0;$i < 100;$i ++){
	$urls_array[] = array(
		"name" => "baidu",
		"url" => "http://www.baidu.com/s?wd=" . mt_rand ( 10000, 20000 ) 
	);
}

$t = microtime ( true );
$result = model_thread_result_get ( $urls_array );
$e = microtime ( true );
echo "多线程：" . ($e - $t) . "\n";

$t = microtime ( true );
foreach($urls_array as $key=>$value){
	$result_new[$key] = model_http_curl_get ( $value["url"] );
}
$e = microtime ( true );
echo "For循环：" . ($e - $t) . "\n";
?>