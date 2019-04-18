<?php
namespace Gt\Build;

use Gt\Cli\Stream;

/** Responsible for running all build tasks and optionally watching for changes */
class BuildRunner {
	/** @var string */
	protected $defaultPath;
	/** @var string */
	protected $workingDirectory;
	/** @var Stream */
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
		$this->defaultPath = implode(DIRECTORY_SEPARATOR, [
			getcwd(),
			"build.json",
		]);
		$this->workingDirectory = $path;
		$this->stream = $stream;
	}

	public function run(bool $continue = true):void {
// Find path to JSON configuration file, and normalise the working directory.
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
			$jsonPath = $this->defaultPath;
		}
		if(!is_file($jsonPath)) {
			$whichPath =
				$jsonPath === $this->defaultPath
				? "default"
				: "user";

			$this->stream->writeLine(
				"No build config found. Trying $whichPath path: $jsonPath",
				Stream::ERROR
			);
			exit(1);
		}

		$startTime = microtime(true);

// Check that the developer has all the necessary requirements.
// $errors will be passed by reference to Build::check. Passing an array by
// reference will suppress exceptions, instead filling the array with error
// strings for output back to the terminal.
		$errors = [];
		try {
			$build = new Build(
				$jsonPath,
				$workingDirectory
			);
		}
		catch(JsonParseException $exception) {
			$this->stream->writeLine("Syntax error in $jsonPath", Stream::ERROR);
			exit(1);
		}

		$build->check($errors);

// Without the correct requirements, the build runner can't proceed.
		if(!empty($errors)) {
			$this->stream->writeLine("The following errors occurred:", Stream::ERROR);

			foreach($errors as $e) {
				$this->stream->writeLine(" • " . $e);
			}
			exit(1);
		}
// Infinite loop while $continue is true. This allows for builds to take place
// as soon as changes happen on the relevant files. It also allows the $continue
// variable to be changed mid-run by an outside force such as a unit test.
		$watchMessage = $continue ? "Watching for changes..." : null;
		do {
			$updates = $build->build($errors);
			foreach($updates as $update) {
				$this->stream->writeLine(
					date("Y-m-d H:i:s")
					. "\t"
					. "Success: $update"
				);
			}

			foreach($errors as $error) {
				$this->stream->writeLine(
					date("Y-m-d H:i:s")
					. "\t"
					. "Error: $error",
					Stream::ERROR
				);
			}

			// Quarter-second wait:
			usleep(250000);

			if($watchMessage) {
				$this->stream->writeLine($watchMessage);
				$watchMessage = null;
			}
		}
		while($continue);

		$deltaTime = round(
			microtime(true) - $startTime,
			1
		);
		$this->stream->writeLine("Build script completed in $deltaTime seconds");
	}

	public function setDefault(string $path):void {
		$this->defaultPath = $path;
	}
}
