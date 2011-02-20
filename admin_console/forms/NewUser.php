<?php 
class Form_NewUser extends Zend_Form
{
	public function init()
	{
		// Set the method for the display form to POST
		$this->setMethod('post');
		$this->setAttrib('class', 'inline-form');

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
		
		// Add an last name element
		$this->addElement('text', 'last_name', array(
			'label'			=> 'Last Name:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
			'validators' 	=> array()
		));

		$this->addElement('select', 'role', array(
			'label'			=> 'Role:',
			'filters'		=> array('StringTrim'),
			'required'		=> true,
		));
		
		$element = $this->getElement('role');
		
		$client = Kaltura_ClientHelper::getClient();
		$filter = new KalturaUserRoleFilter();
		$filter->tagsMultiLikeAnd = 'admin_console';
		$userRoles = $client->userRole->listAction($filter);
		if ($userRoles && isset($userRoles->objects)) {
			$userRoles = $userRoles->objects;
			foreach($userRoles as $role) {
				$element->addMultiOption($role->id, $role->name);
			}
		}
		
		$this->addDisplayGroup(array('email', 'first_name', 'last_name', 'submit', 'role'), 'user_info', array(
			'decorators' => array(
				'Description', 
				'FormElements', 
				array('Fieldset'),
			)
		));
		
		
		// Add the submit button
		$this->addElement('button', 'submit', array(
			'type' => 'submit',
			'ignore'	=> true,
			'label'		=> 'Create',
			'decorators' => array('ViewHelper')
		));
		
		$this->addDisplayGroup(array('submit'), 'buttons1', array(
			'decorators' => array(
				'FormElements', 
				array('HtmlTag', array('tag' => 'div', 'class' => 'buttons')),
			)
		));
	}
}