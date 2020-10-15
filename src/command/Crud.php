<?php

declare(strict_types=1);

namespace ctocode\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\Console;

class Crud extends Command
{
	protected function configure()
	{
		$this->setName('cmake:crud')
			/* 模型名字 */
			->addArgument('name', Argument::OPTIONAL, "your create model-name")
			/* 表单名字 */
			->addOption('table', null, Option::VALUE_REQUIRED, 'table name')
			->setDescription('ctocode model');
	}
	protected function execute(Input $input, Output $output)
	{
		$name = trim($input->getArgument('name'));
		$name = $name ?: '';
		if (empty($name)) {
			$output->writeln("CM, model-name not null");
			return false;
		}
		$output->writeln("---[ crud ] start ---");
		$output->writeln("");
		// ======= 创建 模型
		$mk10 = Console::call('cmake:model', [
			$name
		])->fetch();
		$output->writeln("---1、create model ---  " . $mk10);
		// ======= 创建 模型
		$mk11 = Console::call('cmake:model_select', [
			$name
		])->fetch();
		$output->writeln("---1.1、create cmake:model_select ---  " . $mk11);
		// ======= 创建 验证器
		$mk20 = Console::call('cmake:validate', [
			$name
		])->fetch();
		$output->writeln("---2、create validate ---  " . $mk20);

		// ======= 创建 rpc
		$mk50 = Console::call('cmake:rpc', [
			$name
		])->fetch();
		$output->writeln("---5、create rpc ---  " . $mk50);

		$output->writeln("---[ crud ] end ---");
	}
}
