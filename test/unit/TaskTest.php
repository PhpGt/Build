<?php
namespace Gt\Build\Test;

use Gt\Build\Requirement;
use Gt\Build\Task;
use Gt\Build\UnsatisfiedRequirementVersion;
use PHPUnit\Framework\TestCase;
use stdClass;

class TaskTest extends TestCase {
	public function testCheckMissingRequirement() {
		$details = new StdClass();
		$details->require = new StdClass();
		$details->require->{"centScript"} = "100";
		$details->require->{"decScript"} = "10";

		$goodRequirement = self::createMock(Requirement::class);
		$goodRequirement->method("check")
			->willReturn(true);

		$task = self::createMock(Task::class);
		$task->method("createNewRequirement")
			->willReturn($goodRequirement);
		/** @var Task $task */

//		self::expectException(UnsatisfiedRequirementVersion::class);
		$task->check();
	}
}