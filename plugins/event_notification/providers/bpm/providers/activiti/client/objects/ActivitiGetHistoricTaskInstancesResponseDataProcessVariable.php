<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiGetHistoricTaskInstancesResponseDataProcessVariable extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'name' => 'string',
			'variableScope' => 'string',
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
	protected $variableScope;

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
	public function getVariablescope()
	{
		return $this->variableScope;
	}

	/**
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}

}

