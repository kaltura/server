<?php

/**
 * Info about Asset type
 *
 * @package Core
 * @subpackage model
 */ 
class assetTypeInfo
{
	/**
	 * Asset type.
	 * @var	int
	 */
	protected $type;

	public function getType()
	{
		return $this->type;
	}

	public function setType($type)
	{
		$this->type = $type;
	}
}
