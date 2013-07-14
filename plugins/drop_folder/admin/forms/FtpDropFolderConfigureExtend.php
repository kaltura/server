<?php
/**
 * @package plugins.dropFolder
 * @subpackage Admin
 */
class Form_FtpDropFolderConfigureExtend_SubForm extends Form_DropFolderConfigureExtend_SubForm
{
	public function getTitle()
	{
	    return 'FTP settings';
	}    
    
    public function init()
	{
        $this->addElement('text', 'host', array(
			'label'			=> 'Host:',
			'filters'		=> array('StringTrim'),
		));
		
	    $this->addElement('text', 'port', array(
			'label'			=> 'Port:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'username', array(
			'label'			=> 'Username:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'password', array(
			'label'			=> 'Password:',
			'filters'		=> array('StringTrim'),
		));
	}
	
}