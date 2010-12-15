<?php 
class Form_NewUser extends Zend_Form
{
	public function init()
	{
		// Set the method for the display form to POST
		$this->setMethod('post');
		$this->setAttrib('class', 'form');

		// Add an email address element
		$this->addElement('text', 'email', array(
			'label'			=> 'Email address:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
			'validators' 	=> array(
				'EmailAddress',
			)
		));
		
		// Add an first name element
		$this->addElement('text', 'first_name', array(
			'label'			=> 'First Name:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
			'validators' 	=> array()
		));
		
		// Add an first name element
		$this->addElement('text', 'last_name', array(
			'label'			=> 'Last Name:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
			'validators' 	=> array()
		));
		
		// Add a password element
		$this->addElement('text', 'password', array(
			'label'			=> 'Password:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
			'validators' 	=> array()
		));

		// Add the submit button
		$this->addElement('button', 'submit', array(
			'type' => 'submit',
			'ignore'	=> true,
			'label'		=> 'Create User',
			'decorators' => array('ViewHelper')
		));
		
		$role = new Kaltura_Form_Element_EnumSelect('role', array('enum' => 'KalturaAdminConsoleUserRole'));
		$role->setLabel('Role:');
		$this->addElements(array($role));
		
		$this->setDecorators(array(
			'FormElements',
			array('HtmlTag', array('tag' => 'dl')),
			array('Description', array('placement' => 'prepend')),
			'Fieldset',
			'Form',
		));
	}
}