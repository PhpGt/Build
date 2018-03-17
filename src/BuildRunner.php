<?php
namespace Gt\Build;

class BuildRunner {
	protected static $defaultPath;

	public static function run(string $path, bool $continue = true):void {
		if(!is_file($path)) {
			$path = self::$defaultPath;
		}

		$build = new Build($path);
		$build->check();

		do {
			$updates = $build->build();

			foreach($updates as $update) {
				echo date("Y-m-d H:i:s");
				echo "\t";
				echo "Updated: $update" . PHP_EOL;
			}
			usleep(100000);
		} while($continue);
	}

	public static function setDefault(string $path):void {
		self::$defaultPath = $path;
	}
}
