<?php

namespace Gt\Build;

class BuildRunner {
	public static function run(string $path):void {
		$buildConfigPath = $path . "/build.json";
		$build = new Build($buildConfigPath);
		$build->buildAll();
	}
}