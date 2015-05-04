<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiCreateorUpdateVariablesOnAnExecutionResponse extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'name' => 'string',
			'type' => 'string',
			'value' => 'int',
			'scope' => 'string',
		));
	}
	
	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var int
	 */
	protected $value;

	/**
	 * @var string
	 */
	protected $scope;

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return int
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @return string
	 */
	public function getScope()
	{
		return $this->scope;
	}

}

