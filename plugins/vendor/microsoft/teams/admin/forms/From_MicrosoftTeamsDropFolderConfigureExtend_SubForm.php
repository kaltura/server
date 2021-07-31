<?php
/**
 * @package plugins.dropFolder
 * @subpackage Admin
 */
class Form_MicrosoftTeamsDropFolderConfigureExtend_SubForm extends Form_DropFolderConfigureExtend_SubForm
{
	public function getTitle()
	{
		return 'Microsoft Teams Integration Setting';
	}

	public function init()
	{
		$this->addElement('text', 'integrationId', array(
			'label'			=> 'Vendor Integration id:',
			'filters'		=> array('StringTrim'),
		));
	}

}
