<?php
namespace Gt\Build\Detail;

use Gt\Build\JsonParseException;
use Gt\Build\MissingBuildFileException;

class Manifest {
	/** @var PathMatch[] */
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
			$this->pathMatchList []= new PathMatch(
				$pathMatch,
				$details
			);
		}
	}
}