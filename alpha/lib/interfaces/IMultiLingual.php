<?php
/**
 * @package Core
 * @subpackage model.interfaces
 */

Interface IMultiLingual extends IBaseObject
{
	/**
	 * @return array
	 */
	public static function getMultiLingualSupportedFields();
	
	/**
	 * @param string $field
	 * @param string $value
	 * @param bool $update_db
	 * @return string
	 */
	public function alignFieldValue($field, $value);
	
	/**
	 * finds the correct value for a specific field according to the object default language
	 *
	 * @param string $fieldName
	 * @return string
	 */
	public function getDefaultFieldValue($fieldName);
}