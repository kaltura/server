<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiGetColumnInfoForSingleTableResponseColumnType extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'vARCHAR' => 'string',
		));
	}
	
	/**
	 * @var string
	 */
	protected $vARCHAR;

	/**
	 * @return string
	 */
	public function getVarchar()
	{
		return $this->vARCHAR;
	}

}

