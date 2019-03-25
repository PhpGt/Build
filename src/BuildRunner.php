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

	public function run(bool $continue = true):void {
		$workingDirectory = $this->workingDirectory;

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

		$startTime = microtime(true);

		$errors = [];
		$build = new Build($jsonPath, $workingDirectory);
		$build->check($errors);

		if(!empty($errors)) {
			$this->stream->writeLine("The following errors occurred:", Stream::ERROR);

			foreach($errors as $e) {
				$this->stream->writeLine(" • " . $e);
			}
			exit(1);
		}

		do {
			$updates = $build->build($errors);

			foreach($updates as $update) {
				$this->stream->writeLine(
					date("Y-m-d H:i:s")
					. "\t"
					. "Updated: $update"
				);
			}

			foreach($errors as $error) {
				$this->stream->writeLine(
					$error,
					Stream::ERROR
				);
			}

			// Quarter-second wait:
			usleep(250000);
		}
		while($continue);

		$deltaTime = round(
			microtime(true) - $startTime,
			1
		);
		$this->stream->writeLine("Build script completed in $deltaTime seconds");
	}
}
