<?php

require_once(__DIR__ . '/../ActivitiRequestObject.php');

	

class ActivitiCreateorUpdateVariablesOnAnExecutionRequestData extends ActivitiRequestObject
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
	 * @var string
	 */
	public $scope;

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

	/**
	 * @param $scope string
	 */
	public function setScope($scope)
	{
		$this->scope = $scope;
	}

}

