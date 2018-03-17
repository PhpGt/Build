<?php
namespace Gt\Build;

class Build {
	/** @var TaskList */
	protected $taskList;
	protected $baseDir;

	public function __construct(string $buildPath) {
		$this->taskList = new TaskList($buildPath);
	}

	public function check():int {
		$count = 0;

		foreach($this->taskList as $pathMatch => $task) {
			$task->check();
			$count ++;
		}

		return $count;
	}

	/**
	 * @return Task[]
	 */
	public function build():array {
		$updatedTasks = [];
		foreach($this->taskList as $pathMatch => $task) {
			if($task->build()) {
				$updatedTasks []= $task;
			}
		}

		return $updatedTasks;
	}
}