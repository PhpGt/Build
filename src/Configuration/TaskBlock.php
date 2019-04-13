<?php
namespace Gt\Build\Configuration;

/**
 * Represents a single path pattern block in the build.json and specified
 * the properties that are allowed/expected.
 */
class TaskBlock {
	/** @var string|null Name property if set; defaults to execution command */
	public $name;
	/** @var string */
	public $glob;
	/** @var RequireBlock|null */
	public $require;
	/** @var ExecuteBlock */
	public $execute;

	public function __construct(string $glob, object $details) {
		$this->glob = $glob;

		if(isset($details->require)) {
			$this->require = new RequireBlock($details->require);

		}
		else {
			$this->require = null;
		}

		$this->execute = new ExecuteBlock($details->execute);
		$this->name = $details->name ?? $this->execute->command;
	}
}