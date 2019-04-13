<?php
namespace Gt\Build;

use Gt\Build\Configuration\ExecuteBlock;
use Gt\Build\Configuration\RequireBlockItem;
use Gt\Build\Configuration\TaskBlock;
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
		TaskBlock $taskBlock,
		string $glob = self::MATCH_EVERYTHING,
		string $basePath = ""
	) {
		$this->basePath = $this->expandRelativePath($basePath);
		$this->glob = $glob;
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
		$this->execute = $details->execute;
		$this->name = $details->name;

		if($details->require) {
			$this->requirementList = $details->require->getRequirementList();
		}
	}

	protected function execute(array &$errors = null):bool {
		$previousCwd = getcwd();
		chdir($this->basePath);

		$fullCommand = implode(" ", [
			$this->execute->command,
			$this->execute->arguments,
		]);

		$descriptor = [
			0 => ["pipe", "r"],
			1 => ["pipe", "w"],
			2 => ["pipe", "w"],
		];
		$proc = proc_open($fullCommand, $descriptor, $pipes);

		do {
			$status = proc_get_status($proc);
		} while($status["running"]);
		chdir($previousCwd);
		$output = "";

		$return = $status["exitcode"];
		$output .= stream_get_contents($pipes[1]);
		$output .= stream_get_contents($pipes[2]);
		proc_close($proc);

		if($return !== 0) {
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