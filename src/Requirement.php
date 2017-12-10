<?php
namespace Gt\Build;

use Composer\Semver\Semver;

class Requirement {
	const VERSION_ARGUMENTS = [
		"*" => "--version",
	];

	const VERSION_REGEXES = [
		"*" => "/([\d\.]+)/",
	];

	protected $name;
	protected $version;

	public function __construct(string $name, string $version) {
		$this->name = $name;
		$this->version = $version;
	}

	public function __toString():string {
		return implode(" ", [
			$this->name,
			$this->version,
		]);
	}

	public function check():bool {
		$arg = $this->getArgumentToCheckVersionOfCommand();
		$versionCommand = implode(" ", [
			$this->name,
			$arg,
		]);

		exec($versionCommand, $output, $return);

		if($return !== 0) {
			throw new VersionCheckException($this->name);
		}

		$outputString = implode(" ", $output);
		$versionInstalled = $this->getVersionFromVersionString($outputString);

		return $this->isVersionStringValid($versionInstalled);
	}

	protected function getArgumentToCheckVersionOfCommand():string {
		if(array_key_exists($this->name, self::VERSION_ARGUMENTS)) {
			return self::VERSION_ARGUMENTS[$this->name];
		}

		return self::VERSION_ARGUMENTS["*"];
	}

	protected function getVersionFromVersionString(string $string):string {
		if(array_key_exists($this->name, self::VERSION_REGEXES)) {
			$pattern = self::VERSION_REGEXES[$this->name];
		}
		else {
			$pattern = self::VERSION_REGEXES["*"];
		}

		preg_match($pattern, $string, $matches);
		return $matches[1];
	}

	protected function isVersionStringValid(string $versionInstalled):bool {
		return Semver::satisfies($versionInstalled, $this->version);
	}
}