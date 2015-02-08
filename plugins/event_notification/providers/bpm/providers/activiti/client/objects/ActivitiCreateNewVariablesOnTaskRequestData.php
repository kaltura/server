<?php

require_once(__DIR__ . '/../ActivitiRequestObject.php');

	

class ActivitiCreateNewVariablesOnTaskRequestData extends ActivitiRequestObject
{
	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $scope;

	/**
	 * @var string
	 */
	public $type;

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
	 * @param $scope string
	 */
	public function setScope($scope)
	{
		$this->scope = $scope;
	}

	/**
	 * @param $type string
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

	/**
	 * @param $value string
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}

}

