<?php
/**
 * @package plugins.kontiki
 * @subpackage Admin
 */
class Form_KontikiStorageConfiguration extends Form_Partner_BaseStorageConfiguration
{
	public function init()
	{
		parent::init();
		
		$this->addElement('text', 'serviceToken', array(
			'label'			=> 'Kontiki Service Token',
			'filters'		=> array('StringTrim'),
		
		));
		$this->addElementToDisplayGroup('storage_info', 'serviceToken');
		
		$this->addElement('select', 'urlManagerClass', array(
			'label'			=> 'Delivery URL format :',
			'filters'		=> array('StringTrim'),
			'multiOptions'  => array('' => 'Kaltura Delivery URL Format',
									'kKontikiUrlManager' => 'Kontiki URL Manager',
									),		
			
		));
		$this->getElement('urlManagerClass')->setRegisterInArrayValidator(false);
		
		$this->addElementToDisplayGroup('playback_info', 'urlManagerClass');
	}
}
