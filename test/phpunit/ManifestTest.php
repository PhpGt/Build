<?php
namespace Gt\Build\Test;

use Gt\Build\Configuration\Manifest;
use Gt\Build\Configuration\TaskBlock;
use PHPUnit\Framework\TestCase;

class ManifestTest extends TestCase {
	public function testIterator():void {
		$jsonFile = "test/phpunit/Helper/Json/build.json";
		$jsonObj = json_decode(file_get_contents($jsonFile), true);
		$sut = new Manifest($jsonFile);
		/** @var TaskBlock $taskBlock */
		foreach($sut as $taskBlock) {
			$currentJsonObj = current($jsonObj);
			self::assertSame($currentJsonObj["name"], $taskBlock->getName());
			self::assertSame($currentJsonObj["execute"]["arguments"], $taskBlock->getExecuteBlock()->arguments);
			next($jsonObj);
		}
	}

	public function testIterator_mode():void {
		$jsonFile = "test/phpunit/Helper/Json/build.json";
		$jsonFileOther = "test/phpunit/Helper/Json/build.other-mode.json";
		$jsonObjOther = json_decode(file_get_contents($jsonFileOther), true);
		$jsonObj = json_decode(file_get_contents($jsonFile), true);
		$jsonObj = array_merge($jsonObj, $jsonObjOther);

		$sut = new Manifest($jsonFile, "other-mode");
		/** @var TaskBlock $taskBlock */
		foreach($sut as $taskBlock) {
			$currentJsonObj = current($jsonObj);
			self::assertSame($currentJsonObj["name"], $taskBlock->getName());
			self::assertSame($currentJsonObj["execute"]["arguments"], $taskBlock->getExecuteBlock()->arguments);
			next($jsonObj);
		}
	}
}
