<?php 
/**
 * @package Admin
 * @subpackage Users
 */
class Form_ChangeUserRole extends Infra_Form
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

		// Add a new multi-checkbox element for user roles
        $this->addElement('multiCheckbox', 'roles_checkbox', array(
            'label' => 'User Roles:',
            'filters' => array('StringTrim'),
            'required' => false,
        ));

		// Retrieve the 'roles_checkbox' element from the form
		$checkboxElement = $this->getElement('roles_checkbox');
		$client = Infra_ClientHelper::getClient();
		$filter = new Kaltura_Client_Type_UserRoleFilter();
		$filter->tagsMultiLikeAnd = 'admin_console';

		// Fetch the list of user roles using the filter
		$userRoles = $client->userRole->listAction($filter);

		// Check if the response contains user roles
		if ($userRoles && isset($userRoles->objects))
		{
			// Extract the user roles objects
			$userRoles = $userRoles->objects;
			$options = array();
			// Iterate through each role and map its ID to its name
			foreach ($userRoles as $role)
			{
				$options[$role->id] = $role->name;
			}

			// Add the role options to the 'roles_checkbox' element
			$checkboxElement->addMultiOptions($options);
		}
	}
}