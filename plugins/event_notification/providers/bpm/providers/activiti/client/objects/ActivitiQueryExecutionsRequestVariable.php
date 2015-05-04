<?php

require_once(__DIR__ . '/../ActivitiRequestObject.php');

	

class ActivitiQueryExecutionsRequestVariable extends ActivitiRequestObject
{
	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var int
	 */
	public $value;

	/**
	 * @var string
	 */
	public $operation;

	/**
	 * @var string
	 */
	public $type;

	/**
	 * @param $name string
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @param $value int
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}

	/**
	 * @param $operation string
	 */
	public function setOperation($operation)
	{
		$this->operation = $operation;
	}

	/**
	 * @param $type string
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

}

