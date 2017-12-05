<?php
namespace Gt\Build;

class BuildRunner {
	public static function run(string $path):void {
		$build = new Build($path);
		$build->buildAll();
	}
}