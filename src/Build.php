<?php
namespace Gt\Build;

use Webmozart\Glob\Glob;

class Build {
	/** @var TaskList */
	protected $taskList;
	protected $baseDir;

	public function __construct(
		string $jsonFilePath,
		string $workingDirectory
	) {
		$this->taskList = new TaskList(
			$jsonFilePath,
			$workingDirectory
		);
	}

	public function check(array &$errors = null):int {
		$count = 0;

		foreach($this->taskList as $pathMatch => $task) {
			$absolutePathMatch = implode(DIRECTORY_SEPARATOR, [
				getcwd(),
				$pathMatch,
			]);
			$fileList = Glob::glob($absolutePathMatch);
			if(!empty($fileList)) {
				$task->check($errors);
			}

			$count ++;
		}

		return $count;
	}

	/**
	 * @return Task[]
	 */
	public function build(array &$errors = null):array {
		$updatedTasks = [];
		foreach($this->taskList as $pathMatch => $task) {
			if($task->build($errors)) {
				$updatedTasks []= $task;
			}
		}

		return $updatedTasks;
	}
}