<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_PackageHelper
{
	public static function addPackagesToForm(Zend_Form $form, $packages, $fieldName, $addDefult = true, $defaultName = "N/A")
	{
		if ($addDefult)
		{
			$default = new stdClass();
			$default->id = '';
			$default->name = $defaultName;
			
			array_unshift($packages, $default);
		}
		
		self::addOptionsToForm($form, $packages, $fieldName, 'name');
	}

	public static function addOptionsToForm(Zend_Form $form, $options, $fieldName, $attributeName)
	{
		$arr = array();
		foreach($options as $option)
			$arr[$option->id] = $option->$attributeName;
			
		$form->getElement($fieldName)->setMultiOptions($arr);
	}
}