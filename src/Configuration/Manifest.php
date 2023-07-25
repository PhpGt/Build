<?php
namespace Gt\Build\Configuration;

use Gt\Build\JsonParseException;
use Gt\Build\MissingBuildFileException;
use Iterator;

/**
 * Represents the entire JSON configuration file, build.json
 * Each path pattern in the JSON is represented with a PathPattern object.
 */
class Manifest implements Iterator {
	/** @var TaskBlock[] */
	protected $taskBlockList;
	/** @var string|null Null if the index is out of bounds */
	protected $iteratorKey;
	/** @var int Numerical index to use in iteration */
	protected $iteratorIndex;

	public function __construct(string $jsonFilePath) {
		if(!is_file($jsonFilePath)) {
			throw new MissingBuildFileException($jsonFilePath);
		}

		$json = json_decode(file_get_contents($jsonFilePath));
		if(is_null($json)) {
			throw new JsonParseException(json_last_error_msg());
		}

		$this->taskBlockList = [];
		foreach($json as $glob => $details) {
			$this->taskBlockList []= new TaskBlock(
				$glob,
				$details
			);
		}
	}

	/** @link https://php.net/manual/en/iterator.rewind.php */
	public function rewind():void {
		$this->iteratorIndex = 0;
		$this->setIteratorKey();
	}

	/** @link https://php.net/manual/en/iterator.next.php */
	public function next():void {
		$this->iteratorIndex++;
		$this->setIteratorKey();
	}

	/** @link https://php.net/manual/en/iterator.valid.php */
	public function valid():bool {
		return !is_null($this->iteratorKey);
	}

	/** @link https://php.net/manual/en/iterator.key.php */
	public function key():string {
		return $this->iteratorKey;
	}

	/** @link https://php.net/manual/en/iterator.current.php */
	public function current():TaskBlock {
		return $this->taskBlockList[$this->iteratorKey];
	}

	protected function setIteratorKey():void {
		$keys = array_keys($this->taskBlockList);
		$this->iteratorKey = $keys[$this->iteratorIndex] ?? null;
	}
}
