<?php
/**
 * @package plugins.interactivity
 * @subpackage model
 */

abstract class kInteractivityDataFieldsFilter extends BaseObject
{
	const FIELDS_DELIMITER = ',';

	/**
	 * @var array
	 */
	protected $fields;

	/**
	 * @return string
	 */
	public function getFields()
	{
		return implode ( self::FIELDS_DELIMITER, $this->fields);
	}

	/**
	 * @param string $fields
	 */
	public function setFields($fields)
	{
		$this->fields = array_map('trim', explode(self::FIELDS_DELIMITER, $fields));
	}

	/**
	 * @return array
	 */
	public function getFieldsAsArray()
	{
		return $this->fields;
	}
}