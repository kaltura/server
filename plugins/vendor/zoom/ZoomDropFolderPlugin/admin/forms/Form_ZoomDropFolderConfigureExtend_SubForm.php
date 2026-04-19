<?php
/**
 * @package plugins.dropFolder
 * @subpackage Admin
 */
class Form_ZoomDropFolderConfigureExtend_SubForm extends Form_DropFolderConfigureExtend_SubForm
{
	public function getTitle()
	{
		return 'Zoom settings';
	}
	
	public function init()
	{
		$this->addElement('text', 'zoomVendorIntegrationId', array(
			'label'			=> 'Vendor Integration id:',
			'disabled'		=> true,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'lastHandledMeetingTime', array(
			'label'			=> 'Last Handled Meeting Time:',
			'filters'		=> array('StringTrim'),
		));

		$this->addElement('text', 'fileProcessingGracePeriod', array(
			'label'			=> 'File processing grace period (seconds):',
			'description'	=> 'Time to wait before processing a file. Insert a value between 3600 (1 hour) and 54000 (15 hours)',
			'required'		=> false,
			'filters'		=> array('StringTrim'),
		));
	}
	
}
