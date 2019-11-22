<?php

namespace ctocode\phpframe\library;

/**
 * @class  表单，字段限定查询语句拼接
 * @author ctocode-zhw
 * @version 2017-07-20
 * @link http://ctocode.com
 */
class CTOCODE_Contentlist
{
	public $currtime;
	public $cfg;
	public $usecmt; // 按评论排序
	public $modelData;
	public $catlogCache;
	public $update;

	// 缺省配置，默认提供的提供的为当前表单，和常用操作 限定条件
	protected $defaults = array(
		'article_id' => '', // id
		'business_id' => 0,
		'noid' => '', // 排除id
		'cat_id' => '', // 栏目id
		'cat_id_in' => '', // 栏目id
		'nocid' => '', // 排除栏目
		'article_state' => '', // 状态
		'article_state_in' => '', // 状态
		'article_state_notin' => '', // 排除状态
		'article_keyword' => '', // 关键字
		                          // 'keyextra' => '',
		                          // 'keyfield' => 'keywords', // 关键词限制
		                          // 'wsql' => '', // where 语句
		                          // 'time' => 0,
		'orderby' => 'intime', // 默认排序字段
		'ordersort' => 'desc', // 默认排序-倒叙
		'page' => 1,
		'pagesize' => 10 // 默认limt 0,10

	// 'limit' => '', // 自定义limit语句
	// 'mrow' => 0,
	// 'parsealbum' => 0,
	// 'cache' => 0, // -1永久缓存 0不缓存 >0 (单位 秒)
	// 'func' => '',
	// 'extra' => '',
	// 'debug' => 0 /* 开启debug模式,输出sql语句 */
	);
	/**
	 * 
	 * @param number $model_id // 模型 id
	 * @param array $setting
	 */
	public function __construct($model_id = 0, $sql_join = array())
	{
		// getParseSql
	}
	public function getParseSql($sql_join = '')
	{ // 处理查询字段数据
		$setting = $this->parseSqlJoin ( $sql_join, null );

		// $tableFiledData = ctoSqlGetTableColumn ( $this->modelData['model_table'] );
		// foreach($tableFiledData as $key=>$val)
		// {
		// if($setting[$val['field']] !== NULL)
		// {
		// echo $setting[$val['field']];
		// }
		// }
		$cfg2 = array();
		foreach($this->defaults as $k=>$v){
			$cfg2[$k] = isset ( $setting[$k] ) ? $setting[$k] : $v;
		}
		// order by 排序
		$cfg2['orderby'] = trim ( $cfg2['orderby'] );
		$cfg2['ordersort'] = strtoupper ( trim ( $cfg2['ordersort'] ) );
		$cfg2['ordersort'] = in_array ( $cfg2['ordersort'], array(
			'DESC',
			'ASC'
		) ) ? $cfg2['ordersort'] : 'DESC';
		//
		// $this->currtime = time ();
		// $this->cfg = $cfg2;
		// $this->usecmt = ($this->cfg['orderby'] == 'cmt' || $this->cfg['orderby'] == 'comment');
		// ctoSqlGetTableColumn 获取表单字段

		// $this->catlogCache = catlog_gcache ();//获取栏目缓存
		// $this->update = 0;
		return $cfg2;
	}
	function contentlist($setting = array())
	{
		$this->__construct ( $setting );
	}
	// 用于 sql_join 默认解析
	function parseSqlJoin($default = '', $atts = null)
	{
		$row = array();
		if(! empty ( $default )){
			$defaultAry = explode ( ';', $default );
			foreach($defaultAry as $ary){
				list($k,$v) = explode ( '|', $ary );
				$row[$k] = $v;
			}
		}
		if(! empty ( $atts ) && is_array ( $atts )){
			foreach($atts as $m=>$att){
				$row[$m] = $att;
			}
		}
		return $row;
	}
	/*
	 * 重置配置
	 */
	function setcfg($key = '', $val = '')
	{
		$settings = array();
		if(is_array ( $key )){
			$settings = $key;
		}else{
			$settings[$key] = $val;
		}
		foreach($settings as $k=>$v){
			if(isset ( $this->cfg[$k] )){
				$this->cfg[$k] = $v;
			}
		}
	}
	/*
	 * 获取列表ids
	 */
	function getlist()
	{
		$sqls = $this->doSqlParse ( $this->cfg );
		return $sqls;
		if(! is_array ( $sqls )){
			return array();
		}
		if($this->cfg['debug']){
			var_dump ( $sqls );
		}
		if(! empty ( $this->cfg['debug'] ) && $this->cfg['debug'] == 1){
			print_r ( $sqls );
		}
		return $sqls;

		$md5 = md5 ( ($this->cfg['mrow'] ? '1' : '0') . '_' . join ( '', $sqls ) );
		// 是否可以使用缓存
		$cacheids = array();
		$gomysql = 1;
		if($this->cfg['cache'] != 0){
			if($this->cfg['debug']){
				echo 'getCache';
			}
			$cacheTime = $this->cfg['cache'] < 0 ? - 1 : $this->cfg['cache'];
			$caches = $this->getCache ( $md5, 'conList', $cacheTime );
			if($caches !== false){
				$gomysql = 0;
				$cacheids = $caches;
			}
		}
		if($gomysql){
			if($this->cfg['debug']){
				echo 'execquery';
			}
			foreach($sqls as $sql){
				$cacheids = array_merge ( $cacheids, $this->msql->select ( $sql ) );
			}
			// ?补齐条数
			if($this->cfg['mrow'] && count ( $cacheids ) < $this->cfg['row']){
				$msqls = $this->getMrows ( $cacheids );
				foreach($msqls as $sql){
					$cacheids = array_merge ( $cacheids, $this->msql->select ( $sql ) );
				}
				unset ( $msqls );
			}
			$cacheids = $this->ParseIds ( $cacheids, count ( $sqls ) > 1 );
			if($this->cfg['cache'] != 0){
				setfcache ( $md5, $cacheids, 'conList', true );
			}
		}
		return $this->getlist_data ( $cacheids );
	}
	function getUpdate()
	{
		return $this->update;
	}

	/*
	 * 为了得到 缓存文件上次更新时间 从common.func.php拷贝了此函数 稍做了修改 *************************************************************
	 */
	function getCache($key = '', $dir = 'com', $time = - 1, $slize = true)
	{
		global $_glb;
		if(trim ( $key ) == '')
			return false;
		$md5 = md5 ( $key . $_glb['sitekey'] );
		$cfile = BAIYU_DATA . '/cache' . ($dir == '' ? '' : '/' . $dir) . '/' . substr ( $md5, 0, 2 ) . '/' . substr ( $md5, 2, 2 ) . '/' . substr ( $md5, 4, 28 ) . ($slize ? '.inc' : '.php');
		if(! is_file ( $cfile )){
			$this->update = $this->currtime;
			return false;
		}
		$date = @filemtime ( $cfile );
		if($time == - 1){
			$this->update = $date;
			$rs = parsefcache ( $cfile, $slize );
			return $rs;
		}
		$time = intval ( $time );
		if(($date + $time) < $this->currtime){
			$this->update = $this->currtime;
			return false;
		}
		$this->update = $date;
		$rs = parsefcache ( $cfile, $slize );
		return $rs;
	}

	/*
	 * 对查询得到的 ids 数据做 排序 附加模型ID 处理 *************************************************************
	 */
	function ParseIds($ids = array(), $isMulits = false)
	{
		$MulitSort = ($isMulits && $this->cfg['sort'] != 'rand');
		if($MulitSort){
			$sorts = array();
			$sorts_field = $this->usecmt ? 'comment' : $this->cfg['sort'];
		}
		foreach($ids as $k=>$v){
			if($this->usecmt){
				$v['comment'] = isset ( $v['total'] ) ? intval ( $v['total'] ) : 0;
				unset ( $v['total'] );
			}
			if($MulitSort){
				$sorts[$k] = $v[$sorts_field];
			}
			$ids[$k] = $v;
		}
		if($isMulits){
			if($this->cfg['sort'] == 'rand'){
				shuffle ( $ids );
			}else{
				array_multisort ( $sorts, ($this->cfg['sorttype'] == 'ASC' ? SORT_ASC : SORT_DESC), $ids );
			}
			if(count ( $ids ) > $this->cfg['row']){
				$ids = array_slice ( $ids, 0, $this->cfg['row'] );
			}
		}
		foreach($ids as $k=>$v){
			$v['model'] = $this->catlogCache[$v['cid']]['model'];
			unset ( $v['cid'] );
			if($isMulits && ! $this->usecmt){
				unset ( $v[$sorts_field] );
			}
			$ids[$k] = $v;
		}
		return $ids;
	}

	/*
	 * sql语句拼凑 *************************************************************
	 */
	private function doSqlParse($wsql = NULL)
	{
		$sql_select = " SELECT c.* ";
		$sql_from = " FROM {$this->modelData['model_table']} AS c ";
		/*
		 * =============== =============== =============== ===============
		 * =============== where 语句 ===============
		 */
		$sql_where = " WHERE 1=1 ";
		// 栏目限制 可指定多个
		// $cids = $this->getcids ( $this->cfg['cid'] );
		// 栏目排除 可指定多个(优先 同一个栏目在 指定与排除中同时出现 则排除)
		// $nocids = $this->getcids ( $this->cfg['nocid'] );
		// 自定义限制
		// 状态
		$state = array_unique ( array_filter ( explode ( ',', $wsql['state'] ) ) );
		if(count ( $state ) > 0){
			if(count ( $state ) == 1){
				$sql_where .= " AND c.state ='{$state[0]}' ";
			}else{
				// $sql_where .= ' AND c.state' . $this->getsql_warr ( $state );
			}
		}else{
			$sql_where .= ' AND c.state>=0 ';
			// 状态
			// $nostate = array_unique ( array_filter ( explode ( ',', $wsql['nostate'] ) ) );
			if(count ( $nostate ) > 0){
				// $sql_where .= ' AND c.state' . $this->getsql_warr ( $nostate, true );
			}
		}
		// 商户id限制
		if(! empty ( $wsql['business_id'] )){
			$sql_where .= ' AND c.business_id =' . $wsql['business_id'] . ' ';
		}
		// ID查找
		if($wsql['id'] > 0){
			$sql_where .= ' AND c.id =' . $wsql['id'] . ' ';
		}
		// 排除ID
		if($wsql['noid'] > 0){
			$sql_where .= ' AND c.id NOT IN(' . $wsql['noid'] . ') ';
		}
		if($wsql['cid'] != ''){
			$sql_where .= ' AND c.cat_id IN(' . $wsql['cid'] . ' )';
		}
		// $this->doSpecialWsqlData ( $wsql );
		if($wsql['cids']){
			// $sql_where .= ' AND c.cid' . $this->getsql_warr ( $wsql['cids'] );
		}
		if($wsql['nocids']){
			// $sql_where .= ' AND c.cid' . $this->getsql_warr ( $wsql['nocids'], true );
		}
		// 自定义限制
		if($wsql['wsql'] != ''){
			$sql_where .= ' AND ( ' . $wsql['wsql'] . ' )';
		}
		// 时间限制
		$time = intval ( $wsql['time'] );
		if($time > 0){
			$mtime = $this->currtime - $time * 3600 * 24;
			$sql_where .= " AND c.uptime>{$mtime} ";
		}
		/*
		 * =============== =============== =============== ===============
		 * =============== order by 语句 ,排序字段,排序方式 ===============
		 */
		$sql_order = '';
		if(! empty ( $wsql['orderby'] ) && ! empty ( $wsql['ordersort'] )){
			$sql_order .= " ORDER BY " . ($wsql['orderby'] == 'rand' ? ' RAND() ' : " {$wsql['orderby']} {$wsql['ordersort']} ");
		}
		/*
		 * =============== =============== =============== ===============
		 * =============== limit 语句 ,页条数Limit===============
		 */
		$sql_limit = '';
		if($wsql['limit'] !== ''){
			$limit_arr = explode ( ',', $wsql['limit'] );
			$limit_begin = is_numeric ( $limit_arr[0] ) ? intval ( $limit_arr[0] ) : 0;
			$limit_end = is_numeric ( $limit_arr[1] ) ? intval ( $limit_arr[1] ) : 0;
			$sql_limit .= " LIMIT {$limit_begin},{$limit_end} ";
		}else{ //
			$row = $wsql['row'];
			$offset = (max ( 1, $wsql['page'] ) - 1) * $row;
			$sql_limit .= " LIMIT {$offset},{$row} ";
		}
		//
		$sql_all = $sql_select . $sql_from . $sql_where . $sql_order . $sql_limit;
		return $sql_all;
	}
	private function doSpecialModelData()
	{

		// 手工指定了模型 则剔除栏目设置中 不属于手工指定模型的单元
		$models = array();
		$model = $this->cfg['model_id'] != '' ? array_unique ( array_filter ( explode ( ',', $this->cfg['model_id'] ) ) ) : array();
		if(count ( $model ) > 0){
			foreach($model as $c){
				$arr = array();
				$arr['id'] = $c;
				$arr['cids'] = false;
				$arr['nocids'] = false;
				if(isset ( $cids[$c] )){
					if(isset ( $nocids[$c] )){
						$cids_tmp = $cids[$c];
						foreach($cids_tmp as $kk=>$cc){
							if(in_array ( $cc, $nocids[$c] )){
								unset ( $cids_tmp[$kk] );
							}
						}
						$arr['cids'] = array_values ( $cids_tmp );
						unset ( $cids_tmp );
					}else{
						$arr['cids'] = $cids[$c];
					}
				}else{
					if(isset ( $nocids[$c] )){
						$arr['nocids'] = $nocids[$c];
					}
				}
				$models[] = $arr;
			}
		}else{
			foreach($cids as $k=>$v){
				$arr = array();
				$arr['id'] = $k;
				$arr['cids'] = false;
				$arr['nocids'] = false;
				if($v){
					if(isset ( $nocids[$k] )){
						foreach($v as $kk=>$vv){
							if(in_array ( $vv, $nocids[$k] )){
								unset ( $v[$kk] );
							}
						}
						$arr['cids'] = array_values ( $v );
					}else{
						$arr['cids'] = $v;
					}
				}else{
					if(isset ( $nocids[$k] )){
						$arr['nocids'] = $nocids[$k];
					}
				}
				$models[] = $arr;
			}
		}
		if(count ( $models ) < 1){
			foreach($this->modelData as $k=>$v){
				$models[] = array(
					'id' => $v['id'],
					'cids' => false,
					'nocids' => false
				);
			}
		}
		// 这里放一个类得 public变量 方便补齐条数使用
		$this->models = $models;
	}
	private function doSpecialWsqlData($wsql = NULL)
	{
		return '';
		// 关键词限制
		if($this->cfg['keyfield'] != ''){
			$keyfieldSQL = '';
			$keyfield = array_unique ( array_filter ( explode ( ',', $this->cfg['keyfield'] ) ) );
			if(count ( $keyfield ) > 1){
				$keyfieldSQL = "CONCAT_WS('',c." . join ( ',c.', $keyfield ) . ')';
			}else{
				$keyfieldSQL = 'c.' . $keyfield[0];
			}
			if($keyfieldSQL != ''){
				$keywords = array();
				if($this->cfg['keywords'] != ''){
					$keywords = explode ( ',', $this->cfg['keywords'] );
				}
				if($this->cfg['keyextra'] != ''){
					// pscws
					// $keywords = array_merge ( $keywords, pscws_getSword ( $this->cfg['keyextra'] ) );
				}
				$keywords = array_unique ( array_filter ( $keywords ) );
				$keynum = 0;
				$keysql = '';
				foreach($keywords as $key){
					$key = trim ( $key );
					if($key != ''){
						$keysql .= ($keysql == '' ? '' : 'OR ') . $keyfieldSQL . "  LIKE '%$key%' ";
						$keynum ++;
					}
					if($keynum >= 5){
						break;
					}
				}
				if($keysql != ''){
					$wheresql .= ' AND (' . $keysql . ')';
				}
				unset ( $keywords, $keynum, $keysql );
			}
			unset ( $keyfieldSQL, $keyfield );
		}
	}
	/*
	 * 补齐条数SQL拼凑 在数据够用的时候 这个基本不会被启动 :) *************************************************************
	 */
	function getMrows($ids)
	{
		$sqls = array();
		if(! isset ( $this->models )){
			return $sqls;
		}
		$models = $this->models;
		$limit = $this->cfg['row'] - count ( $ids );
		$wheresql = 'WHERE c.state>=0';
		$cacheids = array();
		foreach($ids as $v){
			$cacheids[] = $v['id'];
		}
		if($this->cfg['noid'] > 0 && ! in_array ( $this->cfg['noid'], $cacheids )){
			$cacheids[] = $this->cfg['noid'];
		}
		if(count ( $cacheids ) > 0){
			$wheresql .= ' AND c.id' . $this->getsql_warr ( $cacheids, true );
		}

		// 查询条数
		$offset = 0;
		$row = $limit;

		foreach($models as $model){
			$modelid = $model['id'];
			$catInfo = $this->modelData[$modelid];
			$table = $catInfo['table'];
			$wsql = $wheresql;
			if($model['cids']){
				$wsql .= ' AND c.cid' . $this->getsql_warr ( $model['cids'] );
			}
			if($model['nocids']){
				$wsql .= ' AND c.cid' . $this->getsql_warr ( $model['nocids'], true );
			}
			// 按评论排序
			if($this->usecmt){
				$csign = $catInfo['sign'];
				if($album > 0){
					$sqls[] = "SELECT $fields FROM `{db}albumids` a LEFT JOIN `{db}$table` c ON a.aid=c.id LEFT JOIN `{db}autocmt` m ON m.data=c.id AND m.type='$csign' $wsql AND a.tid='$album' AND a.fid='$model' ORDER BY m.total $sorttype LIMIT $offset,$row;";
				}else{
					$sqls[] = "SELECT $fields FROM `{db}$table` c LEFT JOIN `{db}autocmt` m ON m.data=c.id AND m.type='$csign' $wsql ORDER BY m.total $sorttype LIMIT $offset,$row";
				}
			}else{
				if($sort == 'good'){
					$digleft = "LEFT JOIN `{db}caches_digs` dg ON (dg.id=c.id AND dg.type='{$catInfo['sign']}')";
					$newsort = 'dg.good';
				}else{
					$digleft = '';
					$newsort = $sort != 'rand' ? 'c.' . $sort : 'rand()';
				}
				if($album > 0){
					$sqls[] = "SELECT $fields FROM `{db}albumids` a LEFT JOIN `{db}$table` c ON a.aid=c.id $digleft $wsql AND a.tid='$album' AND a.fid='$model' ORDER BY " . ($sort == 'rktime' ? 'a.rtime' : $newsort) . " DESC LIMIT $offset,$row;";
				}else{
					$sqls[] = "SELECT $fields FROM `{db}$table` c $digleft $wsql ORDER BY $newsort $sorttype LIMIT $offset,$row";
				}
			}
		}
		if($this->cfg['debug']){
			print_r ( $sqls );
		}
		return $sqls;
	}
	// 子栏目ID字符串 ($self - 是否包含本身)
	function catlog_sonids($cid = 0, $self = true)
	{
		$ids = array();
		$rs = is_array ( $ids ) ? implode ( ',', $ids ) : false;
		return $rs;
	}
	/*
	 * 获得指定cid的所有子栏目并 格式化为数组 *************************************************************
	 */
	function getcids($ccid = '')
	{
		$ccid = trim ( $ccid );
		$cids = array();
		if($ccid != ''){
			// 转数组
			$cids = array_unique ( array_filter ( explode ( ',', $ccid ) ) );
			if(count ( $cids ) > 0){
				$datas = array();
				foreach($cids as $k=>$v){
					if(isset ( $this->catlogCache[$v] )){
						$modelid = $this->catlogCache[$v]['model'];
						if(! isset ( $datas[$modelid] )){
							$datas[$modelid] = array();
						}
						$datas[$modelid][] = $v;
					}
				}
				$cids = $datas;
				unset ( $datas );
			}
		}
		return $cids;
	}

	/*
	 * 多限制 wsql 处理 *************************************************************
	 */
	function getsql_warr($arr = array(), $un = false)
	{
		if($un){
			return count ( $arr ) > 1 ? ' NOT IN (' . join ( ',', $arr ) . ') ' : '!=' . $arr[0];
		}else{
			return count ( $arr ) > 1 ? ' IN (' . join ( ',', $arr ) . ') ' : '=' . $arr[0];
		}
	}

	/*
	 * 最终返回数据处理 *************************************************************
	 */
	function getlist_data($ids = array())
	{
		$rsAry = array();
		foreach($ids as $s){
			$v = art_getcache ( $s['model'], $s['id'] );
			if(! is_array ( $v ))
				continue;
			if($this->usecmt){
				$v['comment'] = $s['comment'];
			}
			if(isset ( $s['good'] )){
				$v['good'] = $s['good'];
			}
			if($this->cfg['parsealbum'] && count ( $v['albums'] ) > 0){
				foreach($v['albums'] as $a=>$album){
					if($album['id']){
						$albums = album_gcache ( $album['id'] );
						if($albums){
							$v['albums'][$a]['name'] = $albums['name'];
							$v['albums'][$a]['link'] = $albums['link'];
						}
					}
				}
			}
			// 要对底层数据进行扩展 使用 func标签 extra可传递自定义参数
			if($this->cfg['func'] != ''){
				$v = $func ( $v, $this->cfg['extra'] );
			} // End special
			$rsAry[] = $v;
		}
		return $rsAry;
	}
}
?>