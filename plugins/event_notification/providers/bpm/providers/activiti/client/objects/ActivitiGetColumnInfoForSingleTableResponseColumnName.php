<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiGetColumnInfoForSingleTableResponseColumnName extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'iD_' => 'string',
			'tYPE_' => 'string',
		));
	}
	
	/**
	 * @var string
	 */
	protected $iD_;

	/**
	 * @var string
	 */
	protected $tYPE_;

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->iD_;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->tYPE_;
	}

}

