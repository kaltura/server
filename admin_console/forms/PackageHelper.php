<?php
class Form_PackageHelper
{
	public static function addPackagesToForm(Zend_Form $form, $packages)
	{
		$arr = array();
		$arr[-1] = "N/A";
		foreach($packages as $package)
		{
			$arr[$package->id] = $package->name;
		}
		$form->getElement('partner_package')->setMultiOptions($arr);
	}
}