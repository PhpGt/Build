<?php
namespace Gt\Build;

class Task {
	protected $pathMatch;
	protected $requirements = [];
	protected $execute;

	public function __construct(string $pathMatch, $details) {
		$this->pathMatch = $pathMatch;
		$this->setDetails($details);
	}

	public function build():void {

	}

	protected function setDetails($details):void {
		$this->execute = $details->execute ?? null;

		if(isset($details->requires)) {
			foreach($details->requires as $key => $value) {
				$requirements []= new Requirement($key, $value);
			}
		}
	}
}