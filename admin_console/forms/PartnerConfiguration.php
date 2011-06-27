<?php 

class Form_PartnerConfiguration extends Infra_Form
{
    
    const LIMITS_ARRAY_NAME = 'limitsArray';
    
	public function init()
	{
		// Set the method for the display form to POST
		$this->setMethod('post');
		$this->setAttrib('id', 'frmPartnerConfigure');

		$this->setDescription('partner-configure intro text');
		$this->loadDefaultDecorators();
		$this->addDecorator('Description', array('placement' => 'prepend'));	
		
//		$this->addElement('text', 'account_name', array(
//			'label' => 'Publisher Name:',
//			'decorators' 	=> array('Label', 'Description')
//		));
//--------------------------- General Information ---------------------------			
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
						
//--------------------------- Publisher specific Delivery Settings ---------------------------		 
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
//--------------------------- Remote Storage Account policy ---------------------------				
		$storageServP = new Kaltura_Form_Element_EnumSelect('storage_serve_priority', array('enum' => 'Kaltura_Client_Enum_StorageServePriority'));
		$storageServP->setLabel('Delivery Policy:');
		$this->addElements(array($storageServP));	
		
		$this->addElement('checkbox', 'storage_delete_from_kaltura', array(
			'label'	  => 'Delete exported storage from Kaltura',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => 'storage_delete_from_kaltura')))
		));
				
		$this->addElement('checkbox', 'import_remote_source_for_convert', array(
			'label'	  => 'Import remote source for convert',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => 'import_remote_source_for_convert')))
		));
	
//--------------------------- Advanced Notification settings ---------------------------		
		$this->addElement('text', 'notifications_config', array(
			'label'			=> 'Notification Configuration:',
			'filters'		=> array('StringTrim'),
		));
			
		$this->addElement('checkbox', 'allow_multi_notification', array(
			'label'	  => 'Allow multi-notifications:',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => 'storage_delete_from_kaltura')))
		));		
//--------------------------- Publisher Specific Ingestion Settings ---------------------------	
		$this->addElement('text', 'def_thumb_offset', array(
			'label'	  => 'Default Thumbnail Offset',
			//'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => 'live_stream_enabled')))
		));	
		
		//TODO: add XML Ingestion- Transformaion XSL (relevant for eagle).		
//--------------------------- Password Security ---------------------------			
		
		$this->addElement('text', 'login_block_period', array(
			'label'			=> 'Login Block Period (seconds):',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'num_prev_pass_to_keep', array(
			'label'			=> 'Number of recent passwords kept:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'pass_replace_freq', array(
			'label'			=> 'Password replacement frequency (seconds):',
			'filters'		=> array('StringTrim'),
		));				
//--------------------------- Group Association ---------------------------			
		//$this->addElement ('select','partner_group_type', array(
		//	'label'			=> 'Partner Group Type:',
		//	'filters'		=> array('StringTrim'),
		//));	
		//$partnerGroupTypes = new Kaltura_Form_Element_EnumSelect('storage_serve_priority', array('enum' => 'Kaltura_Client_Enum_StorageServePriority'));
		//$partnerGroupTypes->setLabel('Partner Group Type:');
		//$this->addElements(array($partnerGroupTypes));
		
		$this->addElement ('text','partner_parent_id', array(
			'label'			=> 'Partner Parent Id:',
			'filters'		=> array('StringTrim'),
		));
//--------------------------- Account Packages ---------------------------		
		$this->addElement('select', 'partner_package', array(
			'label'			=> 'Usage Package:',
			'filters'		=> array('StringTrim'),
		));
//--------------------------- Account Options ---------------------------			
												
		$this->addElement('checkbox', 'monitor_usage', array(
			'label'	  => 'Monitor Usage',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => 'live_stream_enabled')))
		));
		
		$this->addElement('checkbox', 'is_first_login', array(
			'label'	  => 'Force first login message',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => 'live_stream_enabled')))
		));		
//-----------------------------------------------------------------------		
		$this->addElement('hidden', 'crossLine', array(
			'lable'			=> 'line',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));
		
		$this->addLimitsElements();
		
			
	//adding display groups
	$this->addDisplayGroup(array('partner_name', 'description','admin_name', 'admin_email','crossLine'), 'generalInformation', array('legend' => 'General Information'));
	$this->addDisplayGroup(array('host', 'cdn_host', 'rtmp_url','crossLine'), 'publisherSpecificDeliverySettings', array('legend' => 'Publisher Specific Delivery Settings'));				
	$this->addDisplayGroup(array('storage_serve_priority', 'storage_delete_from_kaltura','import_remote_source_for_convert','crossLine'), 'remoteStorageAccountPolicy', array('legend' => 'Remote Storage Account Policy'));	
	$this->addDisplayGroup(array('notifications_config', 'allow_multi_notification','crossLine'), 'advancedNotificationSettings', array('legend' => 'Advanced Notification Settings'));
	$this->addDisplayGroup(array('def_thumb_offset','crossLine'), 'publisherSpecificIngestionSettings', array('legend' => 'Publisher Specific Ingestion Settings'));
	$this->addDisplayGroup(array(Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::USER_LOGIN_ATTEMPTS, 'login_block_period', 'num_prev_pass_to_keep', 'pass_replace_freq'), 'passwordSecurity', array('legend' => 'Password Security'));
	
	$this->addDisplayGroup(array('partner_group_type', 'partner_parent_id','crossLine'), 'groupAssociation', array('legend' => 'Group Association'));
	
	$this->addDisplayGroup(array('partner_package','crossLine'), 'accountPackages', array('legend' => 'Account Packages'));
	$this->addDisplayGroup(array(Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::ADMIN_LOGIN_USERS, 'monitor_usage','is_first_login'), 'accountOptions', array('legend' => 'Account Options'));
	
//--------------------------- Enable/Disable Features ---------------------------
	/*
		$element = new Zend_Form_Element_Hidden('setPublisherFunctionality');
		$element->setLabel('Set Publisher Functionality');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b', 'class' => 'setPublisherFunctionality'))));		
		$this->addElements(array($element));
	*/
	$moduls = Zend_Registry::get('config')->moduls;
	if ($moduls)
	{
		$permissionNames = array();
		foreach($moduls as $name => $modul)
		{
			$attributes = array(
				'label'	  => $modul->label,
				'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => $name)))
			);
			if(!$modul->enabled)
				$attributes['disabled'] = true;
				
			$this->addElement('checkbox', $modul->permissionName, $attributes);
			
			
			if ($modul->indexLink != null)
			{
				$element = $this->getElement($modul->permissionName);
				
				$element->setDescription('<a id=linkToPage href=# onClick=openLink("../'.$modul->indexLink.'")>(config)</a>');
			
			    $element->addDecorators(array('ViewHelper',		      
			        array('Label', array('placement' => 'append')),
			        array('Description', array('escape' => false, 'tag' => false)),
			      ));			      
			}
			$permissionNames[] = $modul->permissionName;
		}
	}

	$this->addElement('checkbox', 'moderate_content', array(
		'label'	  => 'Content Moderation',
		'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => 'moderate_content')))
	));
	$permissionNames[] = 'moderate_content';
	
	//adding display group to all features
	$this->addDisplayGroup($permissionNames, 'enableDisableFeatures',array('legend' => 'Enable/Disable Features:'));
		
	//removing decorators from display groups
	$displayGroups = $this->getDisplayGroups();
	foreach ($displayGroups as $displayGroup)
	{
		$displayGroup->removeDecorator ('label');
  		$displayGroup->removeDecorator('DtDdWrapper');		
	}
	//creating divs for left right dividing
	$this->setDisplayColumn('generalInformation', 'passwordSecurity');
	$this->setDisplayColumn('groupAssociation', 'enableDisableFeatures');
		
}

	/**
	 * creating a form display in two columns (left and right).	
	 * $firstColumnElement - the first display group in the column
	 * $lastColumnElement - the last display group in the column
	 */
	public function setDisplayColumn($firstColumnElement, $lastColumnElement)
	{
		$openLeftDisplayGroup = $this->getDisplayGroup($firstColumnElement);
    	$openLeftDisplayGroup->setDecorators(array(
             'FormElements',
             'Fieldset',
             array('HtmlTag',array('tag'=>'div','openOnly'=>true,'class'=>'partnerConfigureFormColumn'))
    	 ));
	
    	$closeLeftDisplayGroup = $this->getDisplayGroup($lastColumnElement);
    	$closeLeftDisplayGroup->setDecorators(array(
             'FormElements',
             'Fieldset',
              array('HtmlTag',array('tag'=>'div','closeOnly'=>true))
     	));   
	} 
	
//------------------------------------------------------------------------------------------------------------------------------------------------------------------------		
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

		$this->populateLimitsFromObject($object->limits);
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
		
		if (isset($properties[self::LIMITS_ARRAY_NAME]))
		{
		    $systemPartnerConfiguration->limits = $this->getLimitsObject($properties[self::LIMITS_ARRAY_NAME]);
		}

		return $systemPartnerConfiguration;
	}
	
	
	protected function addLimitsElements()
	{
	    $this->addElement('text', Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::USER_LOGIN_ATTEMPTS, array(
			'label'			=> 'Maximum login attemps:',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement(Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::USER_LOGIN_ATTEMPTS)->setBelongsTo(self::LIMITS_ARRAY_NAME);
		
		$this->addElement('text', Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::ADMIN_LOGIN_USERS, array(
			'label'			=> 'Number of KMC admin users:',
			'filters'		=> array('StringTrim'),
		
		));
		$this->getElement(Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::ADMIN_LOGIN_USERS)->setBelongsTo(self::LIMITS_ARRAY_NAME);
	}
	
	protected function populateLimitsFromObject($limitsObject)
	{
	    if (is_array($limitsObject))
		{
			foreach ($limitsObject as $limit)
			{
				if ($limit instanceof Kaltura_Client_SystemPartner_Type_SystemPartnerLimit)
				{
				    $this->setDefault($limit->type, $limit->max);
				}
			}
		}
	}
	
	protected function getLimitsObject(array $limits)
	{
		$limitArray = array();
		foreach ($limits as $key => $maxValue)
		{
			$limit = new Kaltura_Client_SystemPartner_Type_SystemPartnerLimit();
			$limit->type = $key;
			$limit->max = $maxValue;
			$limitArray[] = $limit;
		}
		return $limitArray;
	}
	
}