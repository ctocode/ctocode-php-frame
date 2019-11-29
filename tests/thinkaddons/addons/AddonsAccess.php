<?php

namespace think\addons;

use think\Db;

class AddonsAccess extends Controller
{
	protected $serviceIsBus = false; // 是否是商户相关
	protected $serviceArr = [
		'serType' => '',
		'serName' => '',
		'serPrimary' => '',
		'serTitile' => '',
		'serVersion' => '',
		'serAuthod' => ''
	];
	public function _initialize()
	{
		parent::_initialize ();
		if(empty ( $this->serviceArr['serName'] )){
			if($this->request->isAjax ()){
				echoJsonError ( '功能服务未配置~' );
			}
			echoHtmlError ( '功能服务未配置~' );
		}
	}
	// 默认
	public function index()
	{
		$this->viewsCommonData ( 'index' );
		return $this->fetch ();
	}
	// 通用数据
	public function viewsCommonData($type = '')
	{
		$dataType = ctoRequest ( 'dataType', 'string' );
		$dataType = empty ( $type ) ? $dataType : $type;
		$wsql = $search = array();
		$wsql['page'] = ctoRequest ( 'page', 'int', 1 );
		$wsql['pagesize'] = 10;
		$wsql['keyword'] = $search['keyword'] = ctoRequest ( 'keyword', 'string' );
		$begindate = ctoRequest ( 'begindate' );
		$wsql['begindate'] = empty ( $begindate ) ? 0 : strtotime ( $begindate );
		$search['begindate'] = empty ( $begindate ) ? '' : $begindate;
		$enddate = ctoRequest ( 'enddate' );
		$wsql['enddate'] = empty ( $enddate ) ? strtotime ( date ( 'Y-m-d' ) ) + 86400 - 1 : strtotime ( $enddate ) + 86400 - 1;
		$search['enddate'] = empty ( $enddate ) ? date ( 'Y-m-d' ) : $enddate;
		if($wsql['begindate'] > $wsql['enddate']){
			$wsql['begindate'] = 0;
		}
		if($this->serviceIsBus){
			$wsql['business_id'] = $this->_businessData['business_id'];
		}
		//
		$this->viewsCommonDiy ( $wsql );
		$rpcObj = loadRpcModelClass ( $this->serviceArr['serType'], $this->serviceArr['serName'] );
		switch($dataType)
		{
			case 'index':
			default:
				$dataType = 'index';
				$result_data = $rpcObj->getListData ( $wsql );
				break;
		}

		$url = addon_url ( "{$this->addon}://{$this->controller}/index" );
		$url .= "?";
		$url .= ctoHttpUrlImplode ( $search );
		$url .= "&page=";
		$pagebreak = ctoHtmlPagebreak ( $wsql['page'], $result_data['total'], $wsql['pagesize'], $url );
		$this->assign ( 'pagebreak', $pagebreak );
		$this->assign ( 'search', $search );
		$this->assign ( 'lists', $result_data['data'] );
	}
	protected function viewsCommonDiy(&$wsql)
	{
	}
	// 编辑
	public function edit()
	{
		if(empty ( $this->serviceArr['serType'] ) || empty ( $this->serviceArr['serName'] )){
			echoJsonError ( '功能服务配置错误~' );
		}
		$wsql = array();
		if($this->serviceIsBus){
			$wsql['business_id'] = $this->_businessData['business_id'];
		}
		$primary_id = $this->serviceArr['serPrimary'];
		$wsql[$primary_id] = ctoRequest ( 'id', 'int' );
		$rows = array();
		if(! empty ( $wsql[$primary_id] )){
			$rpcObj = loadRpcModelClass ( $this->serviceArr['serType'], $this->serviceArr['serName'] );
			$result_data = $rpcObj->getRowData ( $wsql );
			$rows = $result_data['data'][0];
		}
		$this->assign ( 'rows', $rows );
		return $this->fetch ();
	}
	// 更新
	public function update()
	{
		if($this->request->isPost ()){
			if(empty ( $this->serviceArr['serType'] ) || empty ( $this->serviceArr['serName'] )){
				echoJsonError ( '功能服务配置错误~' );
			}
			$request_data = ctoRequestAll ();
			if($this->serviceIsBus){
				$request_data['business_id'] = $this->_businessData['business_id'];
			}
			// 开启事务处理
			Db::startTrans ();
			// 特殊处理
			$rpcModelObj = loadRpcModelClass ( $this->serviceArr['serType'], $this->serviceArr['serName'] );
			$modelHandleResult = $rpcModelObj->update ( $request_data );
			// 这边可做,是否回滚
			if($modelHandleResult['status'] !== 200){
				Db::rollback ();
			}else{
				Db::commit ();
			}
			exit ( json_encode ( $modelHandleResult ) );
		}
	}
	public function editDia()
	{
		if(empty ( $this->serviceArr['serPrimary'] )){
			echoHtmlError ( '功能主键未配置' );
		}
		$wsql = array();
		if($this->serviceIsBus){
			$wsql['business_id'] = $this->_businessData['business_id'];
		}
		$primary_id = $this->serviceArr['serPrimary'];
		$wsql[$primary_id] = ctoRequest ( 'id', 'int' );
		$rows = array();
		if(! empty ( $wsql[$primary_id] )){
			$rpcModelObj = loadRpcModelClass ( $this->serviceArr['serType'], $this->serviceArr['serName'] );
			$result_data = $rpcModelObj->getRowData ( $wsql );
			$rows = $result_data['data'][0];
		}
		$this->assign ( 'rows', $rows );
		$html_content = $this->fetch ( 'edit_dia' );
		exit ( $html_content );
	}
	// 删除
	public function delete()
	{
		if(empty ( $this->serviceArr['serType'] ) || empty ( $this->serviceArr['serName'] )){
			echoJsonError ( '功能服务配置错误~' );
		}
		$request_data = ctoRequestAll ();
		if($this->serviceIsBus){
			$request_data['business_id'] = $this->_businessData['business_id'];
		}
		// 开启事务处理
		Db::startTrans ();
		$rpcModelObj = loadRpcModelClass ( $this->serviceArr['serType'], $this->serviceArr['serName'] );
		$modelHandleResult = $rpcModelObj->delete ( $request_data );
		if($modelHandleResult['status'] == 200){
			Db::commit ();
		}else{
			// 这边可做,是否回滚
			Db::rollback ();
		}
		exit ( json_encode ( $modelHandleResult ) );
	}
}


