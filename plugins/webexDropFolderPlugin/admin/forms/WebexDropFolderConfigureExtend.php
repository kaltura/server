<?php
/**
 * @package plugins.dropFolder
 * @subpackage Admin
 */
class Form_WebexDropFolderConfigureExtend_SubForm extends Form_DropFolderConfigureExtend_SubForm
{
	public function getTitle()
	{
	    return 'Webex settings';
	}    
    
    public function init()
	{
        $this->addElement('text', 'webexServiceUrl', array(
			'label'			=> 'Webex service URL:',
			'filters'		=> array('StringTrim'),
		));
		
	    $this->addElement('text', 'webexUserId', array(
			'label'			=> 'User ID:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'webexPassword', array(
			'label'			=> 'Password:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'webexSiteId', array(
			'label'			=> 'Site ID:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'webexPartnerId', array(
			'label'			=> 'Partner ID:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'webexServiceType', array(
			'label'			=> 'Service Type (Default - MeetingCenter):',
			'value'			=> 'MeetingCenter',
			'filters'		=> array('StringTrim'),
		));
		
		
		$this->addElement('text', 'webexHostIdMetadataFieldName', array(
			'label'			=> 'Host ID Metadata Field Name:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('checkbox', 'deleteFromRecycleBin', array(
			'label'	  => 'Delete From Recycle Bin',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'div', 'class' => 'rememeber')))
		));
		
		$this->addElement('text', 'deleteFromTimestamp', array(
			'label'			=> 'Delete only files newer than:',
			'value'			=> time(),
			'required'		=> true,
			'filters'		=> array('StringTrim'),
		));
		
	}
	
}
