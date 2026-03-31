<?php

/**
 * @package plugins.reach
 * @subpackage model
 */
class ExternalTaskObject
{
	private $id;

	public function __construct($id)
	{
		$this->id = $id;
	}

	public function getId()
	{
		return $this->id;
	}
}
