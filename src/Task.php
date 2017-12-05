<?php
namespace Gt\Build;

use Webmozart\Glob\Glob;

class Task {
	protected $pathMatch;
	/** @var Requirement[] */
	protected $requirements;
	protected $execute;

	protected $fileHashList = [];

	public function __construct(string $pathMatch, $details) {
		$this->pathMatch = $pathMatch;
		$this->setDetails($details);
	}

	public function build(string $basePath):void {
		foreach($this->requirements as $requirement) {
			$requirement->check();
		}

		$absolutePath = $basePath . "/" . $this->pathMatch;
		foreach(Glob::glob($absolutePath) as $matchedPath) {
			$this->fileHashList[$matchedPath] = md5_file($matchedPath);
		}
	}

	protected function setDetails($details):void {
		$this->execute = $details->execute ?? null;

		if(isset($details->requires)) {
			foreach($details->requires as $key => $value) {
				$this->requirements []= new Requirement($key, $value);
			}
		}
	}
}