<?php
/**
 * @package Var
 * @subpackage Partners
 */
class Form_PackageHelper
{
	public static function addPackagesToForm(Zend_Form $form, $packages, $fieldName, $addDefult = true, $defaultName = "N/A")
	{
		$arr = array();
		
		if ($addDefult)
			$arr[''] = $defaultName;
		
		foreach($packages as $package)
		{
			$arr[$package->id] = $package->name;
		}
		$form->getElement($fieldName)->setMultiOptions($arr);
	}
}