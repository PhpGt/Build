<?php
namespace Gt\Build;

class BuildRunner {
	public static function run(string $path, bool $continue = true):void {
		require(__DIR__ . "/../vendor/autoload.php");
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
}