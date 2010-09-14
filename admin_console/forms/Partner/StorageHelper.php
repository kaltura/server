<?php
class Form_Partner_StorageHelper
{
	public static function addProtocolsToForm(Zend_Form $form)
	{
		$arr = array(
			KalturaStorageProfileProtocol::FTP => 'FTP',
			KalturaStorageProfileProtocol::SFTP => 'SFTP',
			KalturaStorageProfileProtocol::SCP => 'SCP',
		);
		$form->getElement('protocol')->setMultiOptions($arr);
	}
	
	public static function addPathManagersToForm(Zend_Form $form)
	{
		$arr = array(
			'kPathManager' => 'Kaltura Path',
			'kExternalPathManager' => 'External Path',
		);
		$form->getElement('pathManagerClass')->setMultiOptions($arr);
	}
	
	public static function addUrlManagersToForm(Zend_Form $form)
	{
		$arr = array(
			'' => 'Kaltura Delivery URL Format',
			'kLocalPathUrlManager' => 'QA FMS Server',
			'kLimeLightUrlManager' => 'Lime Light CDN',
			'kAkamaiUrlManager' => 'Akamai CDN',
			'kLevel3UrlManager' => 'Level 3 CDN',
		);
		$form->getElement('urlManagerClass')->setMultiOptions($arr);
	}
	
	public static function addTriggersToForm(Zend_Form $form)
	{
		$arr = array(
			3 => 'Flavor Ready',
			2 => 'Moderation Approved',
		);
		$form->getElement('trigger')->setMultiOptions($arr);
	}

	public static function addFlavorParamsToForm(Zend_Form $form, $flavorParams)
	{
//		$arr = array();
//		$arr[-1] = "N/A";
//		foreach($packages as $package)
//		{
//			$arr[$package->id] = $package->name;
//		}
//		$form->getElement('partner_package')->setMultiOptions($arr);
	}
}