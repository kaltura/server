<?php 
class Form_NewVirusScanProfile extends Zend_Form
{
	public function init()
	{
		$this->setAttrib('id', 'addNewVirusScanProfile');
		$this->setDecorators(array(
			'FormElements', 
			array('HtmlTag', array('tag' => 'fieldset')),
			array('Form', array('class' => 'simple')),
		));
		
				
		// submit button
		$this->addElement('button', 'newVirusScanProfile', array(
			'label'		=> 'Create New Profile',
			'onclick'		=> "doAction('newVirusScanProfile', $('#newPartnerId').val())",
			'decorators'	=> array('ViewHelper'),
		));
	}
}