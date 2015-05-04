<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiGetAnEventOnTaskResponseMessage extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'gonzo' => 'string',
		));
	}
	
	/**
	 * @var string
	 */
	protected $gonzo;

	/**
	 * @return string
	 */
	public function getGonzo()
	{
		return $this->gonzo;
	}

}

