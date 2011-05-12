<?php 
class Form_ResetPassword extends Zend_Form
{
	public function init()
	{
		// Set the method for the display form to POST
		$this->setMethod('post');

		// Add an email address element
		$this->addElement('text', 'email', array(
			'label'	 		=> 'Email address:',
			'required' 		=> true,
			'filters'		=> array('StringTrim'),
			'validators'	=> array(
				'EmailAddress',
			),
			'decorators' => array('Label', 'ViewHelper'),
		));
		
		// Add the submit button
		$this->addElement('button', 'submit', array(
			'type' => 'submit',
			'ignore'	=> true,
			'label'		=> 'Send',
			'decorators' => array('ViewHelper'),
		));
		
		$this->setDecorators(array(
			'Description',
			'FormElements',
			array('Form', array('class' => 'reset'))
		));
	}
	
	public function hideForm()
	{
		$this->setElements(array());
		$this->addElement('button', 'continue_button', array(
			'ignore'	=> true,
			'label'		=> 'Continue',
		));
	}
	
}