<?php 
class Form_NewButton extends Zend_Form
{
	public function init()
	{
		$this->setAttrib('id', 'new_button_form');
		$this->setDecorators(array(
			'FormElements', 
			array('HtmlTag', array('tag' => 'fieldset')),
			array('Form', array('class' => 'simple')),
		));
		
		// submit button
		$this->addElement('button', 'new_button', array(
			'label'		=> 'Create New',
			'decorators'	=> array('ViewHelper'),
		));
	}
}