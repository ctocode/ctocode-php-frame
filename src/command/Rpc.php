<?php

namespace ctocode\command;

class Rpc extends Make
{
	protected $type = "Rpc";
	protected function configure()
	{
		parent::configure ();
		$this->setName ( 'cmake:rpc' )->setDescription ( 'Create a new model class' );
	}
	protected function getStub(): string
	{
		$stubPath = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR;
		return $stubPath . 'rpc.stub';
	}
	protected function getClassName(string $name): string
	{
		if(strpos ( $name, '\\' ) !== false){
			return $name;
		}
		if(strpos ( $name, '@' )){
			[
				$app,
				$name
			] = explode ( '@', $name );
		}else{
			$app = '';
		}
		if(strpos ( $name, '/' ) !== false){
			// 分割，取最后一个 name
			$lastName = '';
			$nameArr = explode ( "/", $name );
			if(is_array ( $nameArr )){
				$lastName = end ( $nameArr );
			}
			$name = str_replace ( $lastName, '', $name );
			$name = strtolower ( $name );
			$name = str_replace ( '//', "\\", $name );
			$name .= $lastName;
		}
		return $this->getNamespace ( $app ) . '\\' . $name . '\\' . $lastName . $this->type;
	}
	protected function getPathName(string $name): string
	{
		$name = str_replace ( 'app\\', '', $name );
		// 替换路径，最后一个字段
		$nameArr = explode ( "\\", $name );
		$arrLen = count ( $nameArr );
		array_splice ( $nameArr, ($arrLen - 1), 1, 'Rpc' );
		$name = implode ( "\\", $nameArr );
		//
		$path = $this->app->getBasePath () . ltrim ( str_replace ( '\\', '/', $name ), '/' ) . '.php';
		return $path;
	}
	protected function getUseClass($name)
	{
		$lastName = '';
		$nameArr = explode ( "\\", $name );
		if(is_array ( $nameArr )){
			$lastName = end ( $nameArr );
		}
		return str_replace ( 'Rpc', '', $lastName );
	}
	protected function getNamespace(string $app): string
	{
		return parent::getNamespace ( $app ) . '\\services';
	}
}
