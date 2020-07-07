<?php
declare(strict_types = 1)
	;

namespace ctocode\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class Test extends Command
{
	protected function configure()
	{
		$this->setName ( 'CM' )
			/* 模型名字 */
			->addArgument ( 'name', Argument::OPTIONAL, "your create model-name" )
			/* 表单名字 */
			->addOption ( 'table', null, Option::VALUE_REQUIRED, 'table name' )
			->setDescription ( 'ctocode model' );
	}
	protected function execute(Input $input, Output $output)
	{
		$name = trim ( $input->getArgument ( 'name' ) );
		$name = $name ?: '';
		if(empty ( $name )){
			$output->writeln ( "CM, model-name not null" );
			return false;
		}
		if($input->hasOption ( 'table' )){
			$table = PHP_EOL . 'table ' . $input->getOption ( 'table' );
		}else{
			$table = '';
		}

		$output->writeln ( "Hello," . $name . '!' . $table );
	}
}
