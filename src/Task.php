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

// TODO: PHP 7.2 object typehint
	public function __construct(
		$details,
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

	public function check():void {
		foreach($this->requirements as $requirement) {
			if(!$requirement->check()) {
				throw new UnsatisfiedRequirementVersion($requirement);
			}
		}
	}

	public function build():bool {
		$changes = false;

		foreach(Glob::glob($this->absolutePath) as $matchedPath) {
			$md5 = md5_file($matchedPath);
			$existingMd5 = $this->fileHashList[$matchedPath] ?? null;

			if($md5 !== $existingMd5) {
				$this->execute();
				$changes = true;
			}

			$this->fileHashList[$matchedPath] = $md5;
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

	protected function execute():void {
		$previousCwd = getcwd();
		chdir($this->basePath);

		$fullCommand = implode(" ", [
			$this->execute->command,
			$this->execute->arguments,
		]);

		exec($fullCommand, $output, $return);
		chdir($previousCwd);

		if($return !== 0) {
			throw new TaskExecutionFailureException($fullCommand);
		}
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