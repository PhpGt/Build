<?php
namespace Gt\Build;

use Gt\Cli\Stream;

class BuildRunner {
	protected $defaultPath;
	protected $workingDirectory;
	protected $stream;

	public function __construct($path = null, Stream $stream = null) {
		if(is_null($path)) {
			$path = getcwd();
		}
		if(is_null($stream)) {
			$stream = new Stream(
				"php://stdin",
				"php://stdout",
				"php://stderr"
			);
		}
		$this->workingDirectory = $path;
		$this->stream = $stream;
	}

	public function setDefault(string $path):void {
		$this->defaultPath = $path;
	}

	public function run(
		string $workingDirectory,
		bool $continue = true
	):void {
		if(is_file($workingDirectory)) {
			$workingDirectory = dirname($workingDirectory);
		}

		$workingDirectory = rtrim($workingDirectory, "/\\");
		$jsonPath = $workingDirectory;
		if(is_dir($jsonPath)) {
			$jsonPath .= DIRECTORY_SEPARATOR;
			$jsonPath .= "build.json";
		}

		if(!is_file($jsonPath)) {
			$jsonPath= $this->defaultPath;
		}

		$build = new Build($jsonPath, $workingDirectory);
		$build->check();

		do {
			$updates = $build->build();

			foreach($updates as $update) {
				$this->stream->writeLine(
					date("Y-m-d H:i:s")
					. "\t"
					. "Updated: $update"
				);
			}

			// Quarter-second wait:
			usleep(250000);
		}
		while($continue);
	}
}
