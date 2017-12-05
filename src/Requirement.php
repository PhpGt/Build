<?php
namespace Gt\Build;

class Requirement {
	protected $name;
	protected $version;

	public function __construct(string $name, string $version) {
		$this->name = $name;
		$this->version = $version;
	}

	public function check():bool {
		// TODO: Check if the requirement is installed on the developer's system.
	}
}