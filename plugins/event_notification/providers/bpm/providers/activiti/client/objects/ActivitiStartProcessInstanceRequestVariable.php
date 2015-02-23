<?php

require_once(__DIR__ . '/../ActivitiRequestObject.php');

	

class ActivitiStartProcessInstanceRequestVariable extends ActivitiRequestObject
{
	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
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
	 * @param $value string
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}

}

