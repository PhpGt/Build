<?php
namespace Gt\Build\Detail;

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