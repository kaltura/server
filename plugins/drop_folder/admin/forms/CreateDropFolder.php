<?php 
class Form_CreateDropFolder extends Zend_Form
{
	public function init()
	{
		$this->setAttrib('id', 'frmCreateDropFolder');
		$this->setDecorators(array(
			'FormElements', 
			array('HtmlTag', array('tag' => 'fieldset')),
			array('Form', array('class' => 'simple')),
		));
		
		$this->addElement('text', 'newPartnerId', array(
			'label'			=> 'Publisher ID:',
			'filters'		=> array('StringTrim'),
		));		
				
		// submit button
		$this->addElement('button', 'submit', array(
			'ignore'	=> true,
			'label'		=> 'Create New',
			'onclick'		=> "newDropFolder($('#newPartnerId').val())",
			'decorators'	=> array('ViewHelper'),
		));
	}
}