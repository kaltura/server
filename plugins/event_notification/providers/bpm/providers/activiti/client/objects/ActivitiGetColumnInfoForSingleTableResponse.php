<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');
require_once(__DIR__ . '/ActivitiGetColumnInfoForSingleTableResponseColumnName.php');
require_once(__DIR__ . '/ActivitiGetColumnInfoForSingleTableResponseColumnType.php');
	

class ActivitiGetColumnInfoForSingleTableResponse extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'tableName' => 'string',
			'columnNames' => 'array<ActivitiGetColumnInfoForSingleTableResponseColumnName>',
			'columnTypes' => 'array<ActivitiGetColumnInfoForSingleTableResponseColumnType>',
		));
	}
	
	/**
	 * @var string
	 */
	protected $tableName;

	/**
	 * @var array<ActivitiGetColumnInfoForSingleTableResponseColumnName>
	 */
	protected $columnNames;

	/**
	 * @var array<ActivitiGetColumnInfoForSingleTableResponseColumnType>
	 */
	protected $columnTypes;

	/**
	 * @return string
	 */
	public function getTablename()
	{
		return $this->tableName;
	}

	/**
	 * @return array<ActivitiGetColumnInfoForSingleTableResponseColumnName>
	 */
	public function getColumnnames()
	{
		return $this->columnNames;
	}

	/**
	 * @return array<ActivitiGetColumnInfoForSingleTableResponseColumnType>
	 */
	public function getColumntypes()
	{
		return $this->columnTypes;
	}

}

