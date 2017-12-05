<?php
namespace Gt\Build;

class Build {
	/** @var TaskList */
	protected $taskList;
	protected $baseDir;

	public function __construct(string $buildConfigDir) {
		$this->baseDir = $buildConfigDir;

		$buildConfigFilePath = $buildConfigDir . "/build.json";
		$this->taskList = new TaskList($buildConfigFilePath);
	}

	public function buildAll() {
		foreach($this->taskList as $pathMatch => $task) {
			$task->build($this->baseDir);
		}
	}
}