<?php
class Form_PackageHelper
{
	public static function addPackagesToForm(Zend_Form $form, $packages, $fieldName)
	{
		$arr = array();
		$arr[''] = "N/A";
		foreach($packages as $package)
		{
			$arr[$package->id] = $package->name;
		}
		$form->getElement($fieldName)->setMultiOptions($arr);
	}
}