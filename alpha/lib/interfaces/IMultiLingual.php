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
	 * finds the correct value for a specific field according to the object default language
	 *
	 * @param string $fieldName
	 * @return string
	 */
	public function getDefaultFieldValue($fieldName);
}