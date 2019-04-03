<?php
namespace Gt\Build\Detail;

class ExecuteBlock {
	/** @var string The command to execute */
	public $command;
	/** @var string Arguments to pass to command */
	public $arguments;

	public function __construct(object $details) {
		if(empty($details->command)) {
			throw new MissingConfigurationKeyException("execute.command");
		}
		if(empty($details->arguments)) {
			throw new MissingConfigurationKeyException("execute.arguments");
		}

		$this->command = $details->command;
		$this->arguments = $details->arguments;
	}
}