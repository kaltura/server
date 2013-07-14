<?php 
/**
 * @package Admin
 * @subpackage Users
 */
class Form_UserRoleConfiguration extends Infra_Form
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
		$filter = new Kaltura_Client_Type_PermissionFilter();
		$filter->statusEqual = Kaltura_Client_Enum_PermissionStatus::ACTIVE;
		$filter->typeEqual = Kaltura_Client_Enum_PermissionType::NORMAL;
		$filter->orderBy = Kaltura_Client_Enum_PermissionOrderBy::NAME_ASC;
		
		$pager = new Kaltura_Client_Type_FilterPager();
		$pager->pageSize = 1000;
		
		$permissions = $client->permission->listAction($filter, $pager);
		/* @var $permissions Kaltura_Client_Type_PermissionListResponse */
		if ($permissions && isset($permissions->objects) && count($permissions->objects)) 
		{
			foreach($permissions->objects as $index => $permission)
			{
				/* @var $permission Kaltura_Client_Type_Permission */
				
				$permissionId = str_replace(".", "___", $permission->name);
				$this->addElement('checkbox', $permissionId, array(
					'label'=> $permission->friendlyName,
					'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('class' => 'partner_configuration_checkbox_field_only'))
				)));
			}
			
//			$adminUserRole = $userRoles->objects[0];
//			$permissions = explode(',', $adminUserRole->permissionNames);
//			sort($permissions);
//			foreach ($permissions as $permission)
//			{
//				if ($permission != '')
//				{
//					$permissionId = str_replace(".", "___", $permission);
//					$this->addElement('checkbox', $permissionId, array(
//						'label'=> $permission,
//						'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('class' => 'partner_configuration_checkbox_field_only'))
//					)));
//				}
//			}
		}
	}
	
	public function populateFromObject($userRole)
	{
		$this->getElement('name')->setValue($userRole->name);
		$this->getElement('description')->setValue($userRole->description);
		
		$permissions = explode(',', $userRole->permissionNames);
		
		foreach ($permissions as $permission)
		{
			if ($permission != '')
			{
				$permissionId = str_replace(".", "___", $permission);
				$this->setDefault($permissionId, true);
			}
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
				{
					$permissionId = str_replace("___", ".", $element->getId());
					$permissionNames .=',' . $permissionId;
				}
			}
		}
		
		return $permissionNames;		
	}
}