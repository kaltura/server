<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiGetActiveActivitiesInAnExecutionResponse extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'userTaskForManager' => 'string',
		));
	}
	
	/**
	 * @var string
	 */
	protected $userTaskForManager;

	/**
	 * @return string
	 */
	public function getUsertaskformanager()
	{
		return $this->userTaskForManager;
	}

}

