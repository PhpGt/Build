<?php
namespace Gt\Build\Configuration;

use Gt\Build\JsonParseException;
use Gt\Build\MissingBuildFileException;

/**
 * Represents the entire JSON configuration file, build.json
 * Each path pattern in the JSON is represented with a PathPattern object.
 */
class Manifest {
	/** @var PathPattern[] */
	protected $pathMatchList;

	public function __construct(string $jsonFilePath) {
		if(!is_file($jsonFilePath)) {
			throw new MissingBuildFileException($jsonFilePath);
		}

		$json = json_decode(file_get_contents($jsonFilePath));
		if(is_null($json)) {
			throw new JsonParseException(json_last_error());
		}

		$this->pathMatchList = [];
		foreach($json as $pathMatch => $details) {
			$this->pathMatchList []= new PathPattern(
				$pathMatch,
				$details
			);
		}
	}
}