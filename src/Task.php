<?php
namespace Gt\Build;

use Gt\Build\Configuration\ExecuteBlock;
use Gt\Build\Configuration\RequireBlockItem;
use Gt\Build\Configuration\TaskBlock;
use Gt\Daemon\Process;
use Webmozart\Glob\Glob;
use Webmozart\PathUtil\Path;

class Task {
	const MATCH_EVERYTHING = "**/*";

	protected $absolutePath;
	protected $basePath;
	protected $glob;

	protected $name;
	/** @var Requirement[] */
	protected $requirementList = [];
	/** @var ExecuteBlock */
	protected $execute;

	protected $fileHashList = [];

	/**
	 * @param object $taskBlock Details from the JSON data for this task
	 * @param string $glob Path match for files to check for changes
	 * @param string $basePath Path within project directory to check
	 */
	public function __construct(
		TaskBlock $taskBlock
	) {
		$this->glob = $taskBlock->getGlob();
		$this->basePath = getcwd();
		$this->absolutePath = implode(DIRECTORY_SEPARATOR, [
			$this->basePath,
			$this->glob,
		]);
		$this->absolutePath = Path::canonicalize($this->absolutePath);
		$this->setDetails($taskBlock);
	}

	public function __toString():string {
		return $this->name ?? $this->execute->command;
	}

	public function check(array &$errors = null):void {
		foreach($this->requirementList as $requirement) {
			if(!$requirement->check($errors)) {
				if(is_null($errors)) {
					throw new UnsatisfiedRequirementVersion($requirement);
				}
				else {
					$errors []= "Unsatisfied version: " . $requirement;
				}
			}
		}
	}

	public function build(array &$errors = null):bool {
		$changes = false;

		foreach(Glob::glob($this->absolutePath) as $matchedPath) {
			$hash = filemtime($matchedPath);
			$existingHash = $this->fileHashList[$matchedPath] ?? null;

			if($hash !== $existingHash) {
				$changes = $this->execute($errors);
			}

			$this->fileHashList[$matchedPath] = $hash;
		}

		return $changes;
	}

	public function requirementFromRequireBlockItem(
		RequireBlockItem $item
	):Requirement {
		return new Requirement(
			$item->command,
			$item->version
		);
	}

	protected function setDetails(TaskBlock $details):void {
		$this->execute = $details->getExecuteBlock();
		$this->name = $details->getName();

		if($details->getRequireBlock()) {
			$this->requirementList = $details->getRequireBlock()->getRequirementList();
		}
	}

	protected function execute(array &$errors = null):bool {
		$previousCwd = getcwd();
		chdir($this->basePath);

		$fullCommand = implode(" ", [
			$this->execute->command,
			$this->execute->arguments,
		]);

		$process = new Process($fullCommand);
		$process->exec();

		do {
			$output = $process->getOutput();
			$errorOutput = $process->getErrorOutput();

			if(!empty($output)) {
				fwrite(STDOUT, $output);
			}
			if(!empty($errorOutput)) {
				fwrite(STDERR, $errorOutput);
			}

		}
		while($process->isRunning());

		chdir($previousCwd);

		if($process->getExitCode() !== 0) {
			if(is_null($errors)) {
				throw new TaskExecutionFailureException($fullCommand);
			}
			else {
				$errors []= $this->execute->command
					. PHP_EOL
					. $output;
			}

			return false;
		}

		return true;
	}

	protected function expandRelativePath(string $basePath):string {
		if(!$this->isAbsolutePath($basePath)) {
			$basePath = getcwd() . substr(
				$basePath,
				1
			);
		}

		return $basePath;
	}

	protected function isAbsolutePath(string $path):bool {
		if($path === '') {
			return false;
		}

		return
			// Unix:
			$path[0] === DIRECTORY_SEPARATOR
			// Windows:
			|| preg_match('~\A[A-Z]:(?![^/\\\\])~i',$path) > 0;
	}
}