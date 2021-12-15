<?php
/**
 * @package plugins.dropFolder
 * @subpackage Admin
 */
class Form_OneDriveDropFolderConfigureExtend_SubForm extends Form_DropFolderConfigureExtend_SubForm
{
	public function getTitle()
	{
		return 'One Drive Integration Setting';
	}

	public function init()
	{
		$this->addElement('text', 'integrationId', array(
			'label'			=> 'Vendor Integration id:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'defaultCategoryIds', array(
			'label'			=> 'Default Category Ids:',
			'filters'		=> array('StringTrim'),
		));
	}

}
