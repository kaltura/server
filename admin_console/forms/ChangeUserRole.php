<?php 
class Form_ChangeUserRole extends Zend_Form
{
	public function init()
	{
		// Set the method for the display form to POST
		$this->setMethod('post');
		$this->setAttrib('class', 'form');
		
		$this->setDescription('user change role');
		$this->loadDefaultDecorators();
		$this->addDecorator('Description', array('placement' => 'prepend'));
		
		
		// Add a name element
		$this->addElement('text', 'name', array(
			'label'			=> 'User Name:',
			'filters'		=> array('StringTrim'),
			'readonly'		=> true,
			'ignore' 		=> true,
		));
		
		// Add an email address element
		$this->addElement('text', 'email', array(
			'label'			=> 'Email address:',
			'filters'		=> array('StringTrim'),
			'readonly'		=> true,
			'ignore' 		=> true,
		));
		
		// Add a current role element
		$this->addElement('text', 'currentRole', array(
			'label'			=> 'Current role:',
			'filters'		=> array('StringTrim'),
			'readonly'		=> true,
			'ignore' 		=> true,
		));
		
		// Add a new role element
		$this->addElement('select', 'role', array(
			'label'			=> 'New role:',
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
	
	}
}