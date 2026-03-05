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
			'label'			=> 'File Processing Grace Period (seconds):',
			'filters'		=> array('StringTrim'),
			'description'	=> 'Time to wait for recordings to complete processing before skipping them (e.g., 10800 for 3 hours, 21600 for 6 hours)',
		));
	}

}
