<?php

require_once(__DIR__ . '/../ActivitiRequestObject.php');

	

class ActivitiCreateorUpdateVariablesOnProcessInstanceRequestData extends ActivitiRequestObject
{
	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $type;

	/**
	 * @var int
	 */
	public $value;

	/**
	 * @param $name string
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @param $type string
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

	/**
	 * @param $value int
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}

}

