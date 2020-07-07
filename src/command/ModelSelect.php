<?php

namespace ctocode\command;

class ModelSelect extends Make
{
	protected $type = "Select";
	protected function configure()
	{
		parent::configure ();
		$this->setName ( 'cmake:model_select' )->setDescription ( 'Create a new model class' );
	}
	protected function getStub(): string
	{
		$stubPath = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR;
		return $stubPath . 'model_select.stub';
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
	protected function getExtendName($name)
	{
		$lastName = '';
		$nameArr = explode ( "\\", $name );
		if(is_array ( $nameArr )){
			$lastName = end ( $nameArr );
		}
		return str_replace ( 'Select', 'Model', $lastName );
	}
	protected function getNamespace(string $app): string
	{
		return parent::getNamespace ( $app ) . '\\services';
	}
}
