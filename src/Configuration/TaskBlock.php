<?php
namespace Gt\Build\Configuration;

/**
 * Represents a single path pattern block in the build.json and specified
 * the properties that are allowed/expected.
 */
class PathPattern {
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