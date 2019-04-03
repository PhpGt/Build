<?php
namespace Gt\Build\Detail;

class RequireBlockItem {
	public $command;
	public $version;

	public function __construct(string $command, string $version) {
		$this->command = $command;
		$this->version = $version;
	}
}