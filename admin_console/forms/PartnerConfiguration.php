<?php 
class Form_PartnerConfiguration extends Infra_Form
{
	public function init()
	{
		// Set the method for the display form to POST
		$this->setMethod('post');

		$this->setDescription('partner-configure intro text');
		$this->loadDefaultDecorators();
		$this->addDecorator('Description', array('placement' => 'prepend'));

//		$this->addElement('text', 'account_name', array(
//			'label' => 'Publisher Name:',
//			'decorators' 	=> array('Label', 'Description')
//		));
		 
		$this->addElement('text', 'partner_name', array(
			'label'			=> 'Publisher Name:',
			'filters'		=> array('StringTrim'),
		));
		 
		$this->addElement('text', 'description', array(
			'label'			=> 'Description:',
			'filters'		=> array('StringTrim'),
		));
		
		// change to read only
		$this->addElement('text', 'admin_name', array(
			'label'			=> 'Administrator Name:',
			'filters'		=> array('StringTrim'),
			'readonly'		=> true,
			'ignore' 		=> true,
			'disable'       => 'disable',
		));
		
		// change to read only		 
		$this->addElement('text', 'admin_email', array(
			'label'			=> 'Administrator E-Mail:',
			'filters'		=> array('StringTrim'),
			'readonly'		=> true,
			'ignore' 		=> true,
			'disable'       => 'disable',
		));
		 
		$this->addElement('text', 'host', array(
			'label'			=> 'Publisher Specific Host:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'cdn_host', array(
			'label'			=> 'Publisher Specific CDN Host:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'rtmp_url', array(
			'label'			=> 'RTMP URL:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'admin_login_users_quota', array(
			'label'			=> 'Number of KMC admin users:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('select', 'partner_package', array(
			'label'			=> 'Usage Package:',
			'filters'		=> array('StringTrim'),
		));
				
		$this->addElement('text', 'def_thumb_offset', array(
			'label'	  => 'Default Thumbnail Offset',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => 'live_stream_enabled')))
		));
				
		$this->addElement('checkbox', 'monitor_usage', array(
			'label'	  => 'Monitor Usage',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => 'live_stream_enabled')))
		));
		
		$this->addElement('hidden', 'crossLine', array(
			'lable'			=> 'line',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));
		
		$element = new Zend_Form_Element_Hidden('setPublisherFunctionality');
		$element->setLabel('Set Publisher Functionality');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b', 'class' => 'setPublisherFunctionality'))));
		
		$this->addElements(array($element));
		
		$moduls = Zend_Registry::get('config')->moduls;
		if ($moduls)
		{
			foreach($moduls as $name => $modul)
			{
				$attributes = array(
					'label'	  => $modul->label,
					'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => $name)))
				);
				if(!$modul->enabled)
					$attributes['disabled'] = true;
					
				$this->addElement('checkbox', $modul->permissionName, $attributes);
			}
		}
		
		$this->addElement('checkbox', 'moderate_content', array(
			'label'	  => 'Content Moderation',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => 'moderate_content')))
		));
		
		$this->addElement('checkbox', 'storage_delete_from_kaltura', array(
			'label'	  => 'Delete exported storage from Kaltura',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => 'storage_delete_from_kaltura')))
		));
		
		$this->addElement('checkbox', 'import_remote_source_for_convert', array(
			'label'	  => 'Import remote source for convert',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => 'import_remote_source_for_convert')))
		));
		
		$storageServP = new Kaltura_Form_Element_EnumSelect('storage_serve_priority', array('enum' => 'Kaltura_Client_StorageProfile_Enum_StorageServePriority'));
		$storageServP->setLabel('Delivery Policy:');
		$this->addElements(array($storageServP));
	}

	/**
	 * @param Kaltura_Client_SystemPartner_Type_SystemPartnerConfiguration $object
	 * @param bool $add_underscore
	 */
	public function populateFromObject($object, $add_underscore = true)
	{
		parent::populateFromObject($object, $add_underscore);
		
		if(!$object->permissions || !count($object->permissions))
			return;
			
		foreach($object->permissions as $permission)
			$this->setDefault($permission->name, ($permission->status == Kaltura_Client_Enum_PermissionStatus::ACTIVE));		
	}
	
	/* (non-PHPdoc)
	 * @see Infra_Form::getObject()
	 */
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$systemPartnerConfiguration = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
		
		$moduls = Zend_Registry::get('config')->moduls;
		if ($moduls)
		{
			if(is_null($systemPartnerConfiguration->permissions))
				$systemPartnerConfiguration->permissions = array();
				
			foreach($moduls as $name => $modul)
			{
				if(!$modul->enabled)
					continue;
					
				$permission = new Kaltura_Client_Type_Permission();
				$permission->type = $modul->permissionType;
				$permission->name = $modul->permissionName;
				$permission->status = Kaltura_Client_Enum_PermissionStatus::ACTIVE;
				
				if(!isset($properties[$modul->permissionName]) || !$properties[$modul->permissionName])
					$permission->status = Kaltura_Client_Enum_PermissionStatus::BLOCKED;
					
				$systemPartnerConfiguration->permissions[] = $permission;
			}
		}
		
		return $systemPartnerConfiguration;
	}
}