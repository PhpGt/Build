<?php
namespace Gt\Build\Command;

use Gt\Build\BuildRunner;
use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Command\Command;
use Gt\Cli\Parameter\NamedParameter;
use Gt\Cli\Parameter\Parameter;
use Gt\Cli\Stream;

class RunCommand extends Command {
	public function run(ArgumentValueList $arguments = null):void {
		$stream = new Stream(
			"php://stdin",
			"php://stdout",
			"php://stderr"
		);
		$buildRunner = new BuildRunner(getcwd(), $stream);

		if($arguments->contains("default")) {
			$buildRunner->setDefault($arguments->get("default"));
		}

		$buildRunner->run($arguments->contains("watch"));
	}

	public function getName():string {
		return "run";
	}

	public function getDescription():string {
		return "Start the local webserver, crontab and client side build watcher.";
	}

	/** @return  NamedParameter[] */
	public function getRequiredNamedParameterList():array {
		return [];
	}

	/** @return  NamedParameter[] */
	public function getOptionalNamedParameterList():array {
		return [];
	}

	/** @return  Parameter[] */
	public function getRequiredParameterList():array {
		return [];
	}

	/** @return  Parameter[] */
	public function getOptionalParameterList():array {
		return [
			new Parameter(
				false,
				"watch",
				"w",
				"Pass this flag to continue the build runner after first build. Any changes to the filesystem will be rebuilt automatically."
			),
			new Parameter(
				true,
				"config",
				"c",
				"Path to the build.json. This defaults to the project root directory."
			),
			new Parameter(
				true,
				"default",
				"d",
				"Path to a default build.json to use if there is no build.json in the project root."
			),
		];
	}
}