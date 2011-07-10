<?php 

class Form_PartnerConfiguration extends Infra_Form
{
    
    //const LIMITS_ARRAY_NAME = 'limitsArray';
    protected $limitSubForms = array();
    
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
		));	
			
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
		$partnerGroupTypes = new Kaltura_Form_Element_EnumSelect('partner_group_type', array('enum' => 'Kaltura_Client_Enum_PartnerGroupType'));
		$partnerGroupTypes->setLabel('Partner Group Type:');
		$this->addElements(array($partnerGroupTypes));
		
		$this->addElement ('text','partner_parent_id', array(
			'label'			=> 'Partner Parent Id:',
			'filters'		=> array('StringTrim'),
		));
//--------------------------- Account Packages ---------------------------		
		$this->addElement('select', 'partner_package', array(		
			'label'			=> 'Publisher Package:',
			'filters'		=> array('StringTrim'),
			'onChange'		=> 'openChangeServicePackageAlertBox()',
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

//--------------------------- Included Usage ---------------------------	
		$element = new Zend_Form_Element_Hidden('includedUsageLabel');
		$element->setLabel('For reporting purposes only. Leave empty for unlimited usage or when not applicable');		
		$this->addElements(array($element));
		
//-----------------------------------------------------------------------	
		$this->addElement('hidden', 'crossLine', array(
			'lable'			=> 'line',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));
		
		$this->addLimitsElements();	
		$this->addDisplayGroups();
	//--------------------------- Enable/Disable Features ---------------------------
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
				//check permission to access the link's page
				$indexLinkArray = explode('/', $modul->indexLink);
				$linkAllowed= false;
				if (($indexLinkArray[0])=='plugin'){
				 	$linkAllowed = Infra_AclHelper::isAllowed($indexLinkArray[1], null);
				}
				else{
					$linkAllowed = Infra_AclHelper::isAllowed($indexLinkArray[0], $indexLinkArray[1]);
				}
				if ($linkAllowed)
				{
					$element = $this->getElement($modul->permissionName);
					$element->setDescription('<a class=linkToPage href=# onClick=openLink("../'.$modul->indexLink.'")>(config)</a>');
					$element->addDecorators(array('ViewHelper',		      
				        array('Label', array('placement' => 'append')),
				        array('Description', array('escape' => false, 'tag' => false)),
				      ));
				}		      
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
			
	//---------------- Display DisplayGroups according to Permissions ---------------	
	$this->handlePermissions();
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
	
	/**
	 * make permission group elements readonly and disabled.
	 * @param unknown_type $dispalyGroupNames
	 */
	private function setPermissionGroupElementsToReadOnly($permissionGroupName)
	{
		foreach ($permissionGroupName as $dispalyGroupName)
		{
			$displayGroupElements = $this->getDisplayGroup($dispalyGroupName)->getElements();
			foreach ($displayGroupElements as $el)
			{
				$el->setAttrib('readonly', true);				
				$el->setAttrib('disable', 'disable');
				//disable links
				if ($dispalyGroupName == 'enableDisableFeatures'){
					$el->setDescription('<a class=linkToPage href=# onClick="return false;">(config)</a>');
				}
			}
		}
	}
	
	public function handlePermissions()
	{
		//permissions groups
		$configureAccountsGroup = array('groupAssociation');
		$configureAccountsOptions = array('generalInformation','accountPackages','accountOptions',
				'includedUsage','enableDisableFeatures');
		$configureAccountsTechData = array('publisherSpecificDeliverySettings', 'remoteStorageAccountPolicy',
				'advancedNotificationSettings', 'publisherSpecificIngestionSettings', 'passwordSecurity'); 
		
		//according to current permissin call to setPermissionGroupElementsToReadOnly		
		//with the correct group array as parameter
		if (!(Infra_AclHelper::isAllowed('partner', 'configure-tech-data'))){
			$this->setPermissionGroupElementsToReadOnly($configureAccountsTechData);
		}
		if (!(Infra_AclHelper::isAllowed('partner', 'configure-group-options'))){
			$this->setPermissionGroupElementsToReadOnly($configureAccountsGroup);
		}
		if (!(Infra_AclHelper::isAllowed('partner', 'configure-account-info'))){
			$this->setPermissionGroupElementsToReadOnly($configureAccountsOptions);
		}
	}
//------------------------------------------------------------------------------------------------------------------------------------------------------------------------		
	/**
	 * @param Kaltura_Client_SystemPartner_Type_SystemPartnerConfiguration $object
	 * @param bool $add_underscore
	 */
	public function populateFromObject($object, $add_underscore = true)
	{
		parent::populateFromObject($object, $add_underscore);
		
		if (is_array($object->limits))
		{
			foreach ($object->limits as $limit)
			{
				if (isset($this->limitSubForms[$limit->type]))
				{
					$subFormObject = $this->limitSubForms[$limit->type];
					$subFormObject->populateFromObject($this, $limit, false);
				}
			}			
		}
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
		foreach ($this->limitSubForms as $subForm)
		{
			if ($subForm instanceof Form_PartnerConfigurationLimitSubForm)
			{
				$limitType = $subForm->getName();
				$limit = $subForm->getObject('Kaltura_Client_SystemPartner_Type_SystemPartnerLimit', $properties[$limitType], false, $include_empty_fields);
				$systemPartnerConfiguration->limits[] = $limit;			
			}
		}		
		return $systemPartnerConfiguration;
	}
		
	protected function addLimitsElements()
	{
		$userLoginAttempsSubForm = new Form_PartnerConfigurationLimitSubForm(Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::USER_LOGIN_ATTEMPTS, 'Maximum login attemps:');
		$this->addLimitSubForm($userLoginAttempsSubForm, Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::USER_LOGIN_ATTEMPTS);
		//removing overage_price for this element
		$this->removeElement(Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::USER_LOGIN_ATTEMPTS.'_overagePrice');		
		
		$adminLoginUsersSubForm = new Form_PartnerConfigurationLimitSubForm(Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::ADMIN_LOGIN_USERS, 'Number of KMC admin users:');
		$this->addLimitSubForm($adminLoginUsersSubForm, Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::ADMIN_LOGIN_USERS);
		
		$numberOfPublishersSubForm = new Form_PartnerConfigurationLimitSubForm(Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::PUBLISHERS, 'Number of Publishers:');
		$this->addLimitSubForm($numberOfPublishersSubForm, Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::PUBLISHERS);
		
		$monthlyStreamsSubForm = new Form_PartnerConfigurationLimitSubForm(Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::MONTHLY_STREAM_ENTRIES, 'Monthly Streams:');
		$this->addLimitSubForm($monthlyStreamsSubForm, Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::MONTHLY_STREAM_ENTRIES);
		
		$monthlyBandwidthSubForm = new Form_PartnerConfigurationLimitSubForm(Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::MONTHLY_BANDWIDTH, 'Monthly Bandwidth:');
		$this->addLimitSubForm($monthlyBandwidthSubForm, Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::MONTHLY_BANDWIDTH);
		
		$monthlyStorageSubForm = new Form_PartnerConfigurationLimitSubForm(Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::MONTHLY_STORAGE, 'Monthly Storage:');
		$this->addLimitSubForm($monthlyStorageSubForm, Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::MONTHLY_STORAGE);
		
		$monthlyStorageAndBandwidthSubForm = new Form_PartnerConfigurationLimitSubForm(Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::MONTHLY_STORAGE_AND_BANDWIDTH, 'Monthly Storage + Bandwidth:');
		$this->addLimitSubForm($monthlyStorageAndBandwidthSubForm, Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::MONTHLY_STORAGE_AND_BANDWIDTH);

		$numberOfEndUsersSubForm = new Form_PartnerConfigurationLimitSubForm(Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::END_USERS, 'Number of End-Users:');
		$this->addLimitSubForm($numberOfEndUsersSubForm, Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::END_USERS);
		
		$numberOfEntriesSubForm = new Form_PartnerConfigurationLimitSubForm(Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::ENTRIES, 'Number of Entries:');
		$this->addLimitSubForm($numberOfEntriesSubForm, Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::ENTRIES);		
	}
	/**
	 * split the form elements into different display groups
	 */
	public function addDisplayGroups()
	{
		//adding display groups
		$this->addDisplayGroup(array('partner_name', 'description','admin_name', 'admin_email','crossLine'), 'generalInformation', array('legend' => 'General Information'));
		
		$this->addDisplayGroup(array('host', 'cdn_host', 'rtmp_url','crossLine'), 'publisherSpecificDeliverySettings', array('legend' => 'Publisher Specific Delivery Settings'));				
		$this->addDisplayGroup(array('storage_serve_priority', 'storage_delete_from_kaltura','import_remote_source_for_convert','crossLine'), 'remoteStorageAccountPolicy', array('legend' => 'Remote Storage Account Policy'));	
		$this->addDisplayGroup(array('notifications_config', 'allow_multi_notification','crossLine'), 'advancedNotificationSettings', array('legend' => 'Advanced Notification Settings'));
		$this->addDisplayGroup(array('def_thumb_offset','crossLine'), 'publisherSpecificIngestionSettings', array('legend' => 'Publisher Specific Ingestion Settings'));
		$this->addDisplayGroup(array(Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::USER_LOGIN_ATTEMPTS.'_max',
									 Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::USER_LOGIN_ATTEMPTS.'_overagePrice',
									 'login_block_period',
									 'num_prev_pass_to_keep',
									 'pass_replace_freq'),
									 'passwordSecurity', array('legend' => 'Password Security'));
		
		$this->addDisplayGroup(array('partner_group_type', 'partner_parent_id','crossLine'), 'groupAssociation', array('legend' => 'Group Association'));
		
		$this->addDisplayGroup(array('partner_package','crossLine'), 'accountPackages', array('legend' => 'Account Packages'));
		$this->addDisplayGroup(array('monitor_usage','is_first_login','crossLine'), 'accountOptions', array('legend' => 'Account Options'));
		$this->addDisplayGroup(array('includedUsageLabel', 
									Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::ADMIN_LOGIN_USERS.'_max',
									Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::ADMIN_LOGIN_USERS.'_overagePrice',
									Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::PUBLISHERS.'_max',
									Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::PUBLISHERS.'_overagePrice',
									Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::MONTHLY_STREAM_ENTRIES.'_max',
									Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::MONTHLY_STREAM_ENTRIES.'_overagePrice',
									Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::MONTHLY_BANDWIDTH.'_max',
									Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::MONTHLY_BANDWIDTH.'_overagePrice',
									Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::MONTHLY_STORAGE.'_max',
									Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::MONTHLY_STORAGE.'_overagePrice',
									Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::MONTHLY_STORAGE_AND_BANDWIDTH.'_max',
									Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::MONTHLY_STORAGE_AND_BANDWIDTH.'_overagePrice',
									Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::END_USERS.'_max',
									Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::END_USERS.'_overagePrice',
									Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::ENTRIES.'_max',
									Kaltura_Client_SystemPartner_Enum_SystemPartnerLimitType::ENTRIES.'_overagePrice',
									'crossLine'), 'includedUsage', array('legend' => 'Included Usage'));	
	}
	
	protected function addLimitSubForm($subForm, $subFormName)
	{
		$subForm->setName($subFormName);
		$this->limitSubForms[$subFormName] = $subForm;
		$subForm->addElementsToForm($this);
	}
			
}