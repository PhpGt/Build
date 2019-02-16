<?php
namespace Gt\Build\Command;

use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Command\Command;

class RunCommand extends Command {
	public function __construct() {
		$this->setName("watch");
		$this->setOptionalParameter(
			false,
			"watch",
			"w"
		);
	}

	public function run(ArgumentValueList $arguments = null):void {
		$this->writeLine("Run command");
	}
}