<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiGetEnginePropertiesResponse extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'nextDbid' => 'string',
			'schemaHistory' => 'string',
			'schemaVersion' => 'string',
		));
	}
	
	/**
	 * @var string
	 */
	protected $nextDbid;

	/**
	 * @var string
	 */
	protected $schemaHistory;

	/**
	 * @var string
	 */
	protected $schemaVersion;

	/**
	 * @return string
	 */
	public function getNextdbid()
	{
		return $this->nextDbid;
	}

	/**
	 * @return string
	 */
	public function getSchemahistory()
	{
		return $this->schemaHistory;
	}

	/**
	 * @return string
	 */
	public function getSchemaversion()
	{
		return $this->schemaVersion;
	}

}

