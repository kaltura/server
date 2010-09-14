<?php 
class Form_MyInfo extends Zend_Form
{
	public function init()
	{
		// Set the method for the display form to POST
		$this->setMethod('post');
		$this->setAttrib('class', 'form');

		$this->addElement('text', 'email_address', array(
			'label'			=> 'Email address:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
			'validators'	=> array(
				'EmailAddress',
			)
		));
		
		$this->addElement('password', 'old_password', array(
			'label'			=> 'Old Password:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
			'validators'	=> array()
		));
		
		$this->addElement('password', 'new_password', array(
			'label'			=> 'New Password:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
			'validators'	=> array()
		));
		
		$this->addElement('password', 'new_password_again', array(
			'label'			=> 'New Password Again:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
			'validators'	=> array()
		));

		// Add the submit button
		$this->addElement('button', 'submit', array(
			'type' => 'submit',
			'ignore'	=> true,
			'label'		=> 'Save Changes',
			'decorators' => array('ViewHelper')
		));
		
		$this->setDecorators(array(
			'FormElements',
			array('HtmlTag', array('tag' => 'dl')),
			array('Description', array('placement' => 'prepend')),
			'Fieldset',
			'Form',
		));
	}
	
	public function isValid($data)
	{
		$isValid = parent::isValid($data);
		if (!$isValid)
			return false;
			
		if ($this->getElement('new_password')->getValue() != $this->getElement('new_password_again')->getValue())
		{
			$this->getElement('new_password_again')
								->addErrorMessage('Password doesn\'t match')
								->markAsError();
			return false;
		}
		
		return true;
	}
}