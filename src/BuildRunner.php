<?php
namespace Gt\Build;

class BuildRunner {
	public static function run(string $path):int {
		require(__DIR__ . "/../vendor/autoload.php");
		$build = new Build($path);
		return $build->buildAll();
	}
}