<?php
namespace Gt\Build\Test;

use Gt\Build\Task;
use PHPUnit\Framework\TestCase;
use stdClass;

class TaskTest extends TestCase {
	public function testCheckRequirements() {
		$details = new StdClass();
		$details->require = new StdClass();
		$details->require->{"centScript"} = "100";
		$details->require->{"decScript"} = "10";

		$task = new Task($details, "**/*");
	}
}