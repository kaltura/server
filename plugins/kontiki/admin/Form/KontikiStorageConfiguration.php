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
	}
}
