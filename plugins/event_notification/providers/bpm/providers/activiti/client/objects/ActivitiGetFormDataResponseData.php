<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');
require_once(__DIR__ . '/ActivitiGetFormDataResponseDataFormProperty.php');
	

class ActivitiGetFormDataResponseData extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'formKey' => '',
			'deploymentId' => 'string',
			'processDefinitionId' => 'string',
			'processDefinitionUrl' => 'string',
			'taskId' => 'string',
			'taskUrl' => 'string',
			'formProperties' => 'array<ActivitiGetFormDataResponseDataFormProperty>',
		));
	}
	
	/**
	 * @var 
	 */
	protected $formKey;

	/**
	 * @var string
	 */
	protected $deploymentId;

	/**
	 * @var string
	 */
	protected $processDefinitionId;

	/**
	 * @var string
	 */
	protected $processDefinitionUrl;

	/**
	 * @var string
	 */
	protected $taskId;

	/**
	 * @var string
	 */
	protected $taskUrl;

	/**
	 * @var array<ActivitiGetFormDataResponseDataFormProperty>
	 */
	protected $formProperties;

	/**
	 * @return 
	 */
	public function getFormkey()
	{
		return $this->formKey;
	}

	/**
	 * @return string
	 */
	public function getDeploymentid()
	{
		return $this->deploymentId;
	}

	/**
	 * @return string
	 */
	public function getProcessdefinitionid()
	{
		return $this->processDefinitionId;
	}

	/**
	 * @return string
	 */
	public function getProcessdefinitionurl()
	{
		return $this->processDefinitionUrl;
	}

	/**
	 * @return string
	 */
	public function getTaskid()
	{
		return $this->taskId;
	}

	/**
	 * @return string
	 */
	public function getTaskurl()
	{
		return $this->taskUrl;
	}

	/**
	 * @return array<ActivitiGetFormDataResponseDataFormProperty>
	 */
	public function getFormproperties()
	{
		return $this->formProperties;
	}

}

