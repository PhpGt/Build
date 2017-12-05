<?php
namespace Gt\Build;

class Build {
	/** @var TaskList */
	protected $taskList;

	public function __construct(string $buildConfigPath) {
		$this->taskList = new TaskList($buildConfigPath);
	}

	public function buildAll() {
		foreach($this->taskList as $pathMatch => $task) {
			echo "Building $pathMatch!!!";
		}
	}
}