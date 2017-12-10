<?php
namespace Gt\Build;

use Webmozart\Glob\Glob;

class Task {
	protected $absolutePath;
	protected $pathMatch;

	protected $name;
	/** @var Requirement[] */
	protected $requirements;
	protected $execute;

	protected $fileHashList = [];

	public function __construct(string $basePath, string $pathMatch, $details) {
		$this->pathMatch = $pathMatch;
		$this->absolutePath = $basePath . "/" . $this->pathMatch;
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

	protected function setDetails($details):void {
		$this->execute = $details->execute;
		$this->name = $details->name ?? $details->execute->command;

		if(isset($details->requires)) {
			foreach($details->requires as $key => $value) {
				$this->requirements []= new Requirement(
					$key,
					$value
				);
			}
		}
	}

	protected function execute():void {
		$fullCommand = implode(" ", [
			$this->execute->command,
			$this->execute->arguments,
		]);

		exec($fullCommand, $output, $return);

		if($return !== 0) {
			throw new TaskExecutionFailureException($fullCommand);
		}
	}
}