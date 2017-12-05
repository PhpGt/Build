<?php
namespace Gt\Build;

use Iterator;

class TaskList implements Iterator {
	protected $pathMatches = [];
	protected $tasks = [];

	protected $iteratorKey;

	public function __construct(string $jsonFilePath) {
		if(!is_file($jsonFilePath)) {
			throw new MissingBuildFileException($jsonFilePath);
		}

		$json = file_get_contents($jsonFilePath);
		$obj = json_decode($json);
		if(is_null($obj)) {
			throw new JsonParseException(json_last_error());
		}

		foreach($obj as $pathMatch => $details) {
			$this->pathMatches []= $pathMatch;
			$this->tasks[$pathMatch] = new Task($pathMatch, $details);
		}
	}

	public function current():Task {
		return $this->tasks[$this->pathMatches[$this->iteratorKey]];
	}

	public function next():void {
		$this->iteratorKey ++;
	}

	public function key():string {
		return $this->pathMatches[$this->iteratorKey];
	}

	public function valid():bool {
		return isset($this->pathMatches[$this->iteratorKey]);
	}

	public function rewind():void {
		$this->iteratorKey = 0;
	}
}