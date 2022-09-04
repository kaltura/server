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
	
	public function getDefaultFieldValue($fieldName);
}