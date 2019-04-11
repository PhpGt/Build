<?php
namespace Gt\Build\Configuration;

class RequireBlock {
	/** @var RequireBlockItem[] */
	protected $requireBlockItemList;

	public function __construct(object $commandVersionRequirements) {
		foreach($commandVersionRequirements as $command => $version) {
			$this->requireBlockItemList []= new RequireBlockItem(
				$command,
				$version
			);
		}
	}
}