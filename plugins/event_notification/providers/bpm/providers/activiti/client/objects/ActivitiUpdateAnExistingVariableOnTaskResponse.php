<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiUpdateAnExistingVariableOnTaskResponse extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'name' => 'string',
			'scope' => 'string',
			'type' => 'string',
			'value' => 'string',
		));
	}
	
	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $scope;

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var string
	 */
	protected $value;

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
	public function getScope()
	{
		return $this->scope;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}

}

