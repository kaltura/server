<?php
class Form_PackageHelper
{
	public static function addPackagesToForm(Zend_Form $form, $packages, $fieldName, $defaultName = "N/A")
	{
		$arr = array();
		$arr[''] = $defaultName;
		foreach($packages as $package)
		{
			$arr[$package->id] = $package->name;
		}
		$form->getElement($fieldName)->setMultiOptions($arr);
	}
}