<?php 
class Form_UserRoleConfiguration extends Zend_Form
{
	public function init()
	{
		// Set the method for the display form to POST
		$this->setMethod('post');
		$this->setAttrib('class', 'form');
		
		$this->loadDefaultDecorators();
		$this->addDecorator('Description', array('placement' => 'prepend'));
		
		
		// Add a name element
		$this->addElement('text', 'name', array(
			'label'			=> 'User Role Name:',
			'filters'		=> array('StringTrim'),
			'readonly'		=> true,
			'ignore' 		=> true,
		));
		
		// Add an email address element
		$this->addElement('text', 'description', array(
			'label'			=> 'Description:',
			'filters'		=> array('StringTrim'),
			'readonly'		=> true,
			'ignore' 		=> true,
		));
		
		$element = $this->getElement('role');
		
		$client = Infra_ClientHelper::getClient();
		$filter = new Kaltura_Client_Type_UserRoleFilter();
		$filter->nameIn = 'System Administrator';
		$filter->statusEqual = Kaltura_Client_Enum_UserRoleStatus::ACTIVE;
		$userRoles = $client->userRole->listAction($filter);
		if ($userRoles && isset($userRoles->objects) && count($userRoles->objects)) {
			$adminUserRole = $userRoles->objects[0];
			$permissions = explode(',', $adminUserRole->permissionNames);
			sort($permissions);
			foreach ($permissions as $permission)
				if ($permission != '')
					$this->addElement('checkbox', $permission, array(
						'label'=> $permission,
						'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('class' => 'partner_configuration_checkbox_field_only'))
					)));
		}
	}
	
	public function populateFromObject($user)
	{
		$this->getElement('name')->setValue($user->name);
		$this->getElement('description')->setValue($user->description);
		
		$permissions = explode(',', $user->permissionNames);
		
		foreach ($permissions as $permission)
		{
			if ($permission != '')
				$this->setDefault($permission, true);
		}
	}
	
	/* (non-PHPdoc)
	 * @see Infra_Form::getObject()
	 */
	public function getPermissionNames()
	{
		$permissionNames = '';
		foreach ($this->getElements() as $element)
		{
			if ($element instanceof Zend_Form_Element_Checkbox)
			{
				if ($element->isChecked())
					$permissionNames .=',' . $element->getId();
			}
		}
		
		return $permissionNames;		
	}
}