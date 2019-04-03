<?php
namespace Gt\Build\Detail;

class PathMatch {
	/** @var RequireBlock */
	public $require;
	/** @var ExecuteBlock */
	public $execute;
	/** @var string */
	public $pathMatch;

	public function __construct(string $pathMatch, object $details) {
		$this->pathMatch = $pathMatch;
		$this->require = new RequireBlock($details->require);
		$this->execute = new ExecuteBlock($details->execute);
	}
}