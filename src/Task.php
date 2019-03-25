<?php
namespace Gt\Build;

use Webmozart\Glob\Glob;
use Webmozart\PathUtil\Path;

class Task {
	const MATCH_EVERYTHING = "**/*";

	protected $absolutePath;
	protected $basePath;
	protected $pathMatch;

	protected $name;
	/** @var Requirement[] */
	protected $requirements = [];
	protected $execute;

	protected $fileHashList = [];

	public function __construct(
		object $details,
		string $pathMatch = self::MATCH_EVERYTHING,
		string $basePath = ""
	) {
		$this->basePath = $this->expandRelativePath($basePath);
		$this->pathMatch = $pathMatch;
		$this->absolutePath = implode(DIRECTORY_SEPARATOR, [
			$this->basePath,
			$this->pathMatch,
		]);
		$this->absolutePath = Path::canonicalize($this->absolutePath);
		$this->setDetails($details);
	}

	public function __toString():string {
		return $this->name ?? $this->execute->command;
	}

	public function check(array &$errors = null):void {
		foreach($this->requirements as $requirement) {
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

	public function createNewRequirement(string $key, string $value):Requirement {
		return new Requirement(
			$key,
			$value
		);
	}

// TODO: PHP 7.2 object typehint
	protected function setDetails($details):void {
		$this->execute = $details->execute;
		$this->name = $details->name ?? $details->execute->command;

		if(isset($details->require)) {
			foreach($details->require as $key => $value) {
				$this->requirements []= $this->createNewRequirement(
					$key,
					$value
				);
			}
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
				$errors []= "ERROR executing "
					. $this->execute->command
					. ": "
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