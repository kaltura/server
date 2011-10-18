<?php 
class Form_NewButton extends Zend_Form
{
	public function init()
	{
		$this->setAttrib('id', 'addNewForm');
		$this->setDecorators(array(
			'FormElements', 
			array('HtmlTag', array('tag' => 'fieldset')),
			array('Form', array('class' => 'simple')),
		));
		
		$this->addElement('text', 'newPartnerId', array(
			'label'			=> 'Publisher ID:',
			'filters'		=> array('StringTrim'),
			'value'			=> $this->byid,
		));
		
		// submit button
		$this->addElement('button', 'new_button', array(
			'label'		=> 'Create New',
			'decorators'	=> array('ViewHelper'),
			'onclick'		=> "doAction('newForm', $('#newPartnerId').val())",
		));
	}
}