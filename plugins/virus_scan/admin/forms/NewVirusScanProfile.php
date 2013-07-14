<?php 
/**
 * @package plugins.virusScan
 * @subpackage Admin
 */
class Form_NewVirusScanProfile extends Infra_Form
{
	public function init()
	{
		$this->setAttrib('id', 'addNewVirusScanProfile');
		$this->setDecorators(array(
			'FormElements', 
			array('HtmlTag', array('tag' => 'fieldset')),
			array('Form', array('class' => 'simple')),
		));
		
		$this->addElement('text', 'newPartnerId', array(
			'label'			=> 'Publisher ID:',
			'filters'		=> array('StringTrim'),
			'value'         => $this->filer_input,
		));
		
				
		// submit button
		$this->addElement('button', 'newVirusScanProfile', array(
			'label'		=> 'Create New Profile',
			'onclick'		=> "doAction('newVirusScanProfile', $('#newPartnerId').val())",
			'decorators'	=> array('ViewHelper'),
		));
	}
}