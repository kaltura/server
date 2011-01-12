<?php 
class Form_GenericProviderProfileConfiguration extends Form_ProviderProfileConfiguration
{
	private static $metadataProfileFields;
	
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
	
		if($object instanceof KalturaGenericDistributionProfile)
		{
			$this->getActionObject($object, 'submit', $properties);
			$this->getActionObject($object, 'update', $properties);
			$this->getActionObject($object, 'delete', $properties);
			$this->getActionObject($object, 'report', $properties, 'fetchReportAction');
		}
		
		$updateRequiredEntryFields = array();
		$updateRequiredMetadataXpaths = array();
		
		$entryFields = array_keys($this->getEntryFields());
		$metadataXpaths = array_keys($this->getMetadataFields());
		
		foreach($properties as $property => $value)
		{
			if(!$value)
				continue;
				
			$matches = null;
			if(preg_match('/update_required_entry_fields_(\d+)$/', $property, $matches))
			{
				$index = $matches[1];
				if(isset($entryFields[$index]))
					$updateRequiredEntryFields[] = $entryFields[$index];
			}
		
			if(preg_match('/update_required_metadata_xpaths_(\d+)$/', $property, $matches))
			{
				$index = $matches[1];
				if(isset($metadataXpaths[$index]))
					$updateRequiredMetadataXpaths[] = $metadataXpaths[$index];
			}
		}
		
		$object->updateRequiredEntryFields = implode(',', $updateRequiredEntryFields);
		$object->updateRequiredMetadataXPaths = implode(',', $updateRequiredMetadataXpaths);
		
		return $object;
	}

	public function populateFromObject($object, $add_underscore = true)
	{
		parent::populateFromObject($object, $add_underscore);
		
		if($object instanceof KalturaGenericDistributionProfile)
		{
			$this->populateFromActionObject($object, 'submit', $add_underscore);
			$this->populateFromActionObject($object, 'update', $add_underscore);
			$this->populateFromActionObject($object, 'delete', $add_underscore);
			$this->populateFromActionObject($object, 'report', $add_underscore, 'fetchReportAction');
			
			$entryFields = array_keys($this->getEntryFields());
			$metadataXpaths = array_keys($this->getMetadataFields());
			
			$updateRequiredEntryFields = explode(',', $object->updateRequiredEntryFields);
			$updateRequiredMetadataXPaths = explode(',', $object->updateRequiredMetadataXPaths);
		
			foreach($updateRequiredEntryFields as $entryField)
			{
				$index = array_search($entryField, $entryFields);
				if($index !== false)
					$this->setDefault("update_required_entry_fields_{$index}", true);
			}
			foreach($updateRequiredMetadataXPaths as $metadataXpath)
			{
				$index = array_search($metadataXpath, $metadataXpaths);
				if($index !== false)
					$this->setDefault("update_required_metadata_xpaths_{$index}", true);
			}
		}
	}

	protected function getMetadataFields()
	{
		if(count(self::$metadataProfileFields))
			return self::$metadataProfileFields;
			
		self::$metadataProfileFields = array();
		$client = Kaltura_ClientHelper::getClient();
		Kaltura_ClientHelper::impersonate($this->partnerId);
		
		try
		{
			$metadataProfileList = $client->metadataProfile->listAction();
			foreach($metadataProfileList->objects as $metadataProfile)
			{
				$metadataFieldList = $client->metadataProfile->listFields($metadataProfile->id);
				foreach($metadataFieldList->objects as $metadataField)
					self::$metadataProfileFields[$metadataField->xPath] = $metadataField->label;
			}
		}
		catch (Exception $e)
		{
			KalturaLog::err($e->getMessage());
			return array();
		}
		
		Kaltura_ClientHelper::unimpersonate();
		
		return self::$metadataProfileFields;
	}
	
	protected function getEntryFields()
	{
		return array(
			'entry.KSHOW_ID' => 'Kaltura Show',
			'entry.KUSER_ID' => 'Kaltura User',
			'entry.NAME' => 'Name',
			'entry.DATA' => 'Data',
			'entry.THUMBNAIL' => 'Thumbnail',
			'entry.COMMENTS' => 'Comments',
			'entry.TOTAL_RANK' => 'Total Rank',
			'entry.RANK' => 'Rank',
			'entry.TAGS' => 'Tags',
			'entry.STATUS' => 'Status',
			'entry.LENGTH_IN_MSECS' => 'Duration',
			'entry.DISPLAY_IN_SEARCH' => 'Display in Search',
			'entry.GROUP_ID' => 'Group',
			'entry.PARTNER_DATA' => 'Partner Data',
			'entry.DESCRIPTION' => 'Description',
			'entry.MEDIA_DATE' => 'Media Date',
			'entry.ADMIN_TAGS' => 'Admin Tags',
			'entry.MODERATION_STATUS' => 'Moderation Status',
			'entry.MODERATION_COUNT' => 'Moderation Count',
			'entry.PUSER_ID' => 'Partner User',
			'entry.ACCESS_CONTROL_ID' => 'Access Control',
			'entry.CATEGORIES_IDS' => 'Categories',
			'entry.START_DATE' => 'Start Date',
			'entry.END_DATE' => 'End Date',
			'moderate' => 'Moderate',
			'current_kshow_version' => 'Current Show Version',
			'hasDownload' => 'Has Download',
			'encodingIP1' => 'Encoding IP 1',
			'encodingIP2' => 'Encoding IP 2',
			'streamUsername' => 'Stream Username',
			'streamPassword' => 'Stream Password',
			'offlineMessage' => 'Offline Message',
			'streamRemoteId' => 'Stream Remote ID',
			'streamRemoteBackupId' => 'Stream Remote Backup ID',
			'streamUrl' => 'Stream Url',
			'streamBitrates' => 'Stream Bitrates',
			'ismVersion' => 'ISM Version',
			'height' => 'Height',
			'width' => 'Width',
			'security_policy' => 'Security Policy',
		);
	}

	protected function addEntryFields()
	{
		$this->addElement('hidden', 'crossLineEntryFields', array(
			'lable'			=> 'line',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));
		
		$element = new Zend_Form_Element_Hidden('addEntryFields');
		$element->setLabel('Entry fields that require update');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElements(array($element));

		$index = 0;
		foreach($this->getEntryFields() as $field => $fieldName)
		{
			$this->addElement('checkbox', "update_required_entry_fields_{$index}", array(
				'label'	  => $fieldName,
				'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt')))
			));
			$index++;
		}
	}

	protected function addMetadataFields()
	{
		$this->addElement('hidden', 'crossLineMetadataFields', array(
			'lable'			=> 'line',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));
		
		$element = new Zend_Form_Element_Hidden('addMetadataFields');
		$element->setLabel('Metadata nodes that require update');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElements(array($element));

		$index = 0;
		foreach($this->getMetadataFields() as $xPath => $fieldName)
		{
			$this->addElement('checkbox', "update_required_metadata_xpaths_{$index}", array(
				'label'	  => $fieldName,
				'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt')))
			));
			$index++;
		}
	}
	
	public function getActionObject(KalturaGenericDistributionProfile $object, $action, array $properties, $attributeName = null)
	{
		if(is_null($attributeName))
			$attributeName = "{$action}Action";
			
		if(!$object->$attributeName)
			$object->$attributeName = new KalturaGenericDistributionProfileAction();
		
		if(!$properties || !isset($properties["{$action}_enabled"]) || $properties["{$action}_enabled"] == KalturaDistributionProfileActionStatus::DISABLED)
			return;
			
		foreach($properties as $property => $value)
		{
			$matches = null;
			if(preg_match("/{$action}_(.+)$/", $property, $matches))
			{
				$propertyName = $matches[1];
				
				$parts = explode('_', strtolower($propertyName));
				$propertyName = '';
				foreach ($parts as $part) 
					$propertyName .= ucfirst(trim($part));
				$propertyName[0] = strtolower($propertyName[0]);
				
				$object->$attributeName->$propertyName = $value;
			}
		}
	}
	
	public function populateFromActionObject($object, $action, $add_underscore = true, $attributeName = null)
	{
		if(is_null($attributeName))
			$attributeName = "{$action}Action";
			
		if(!$object->$attributeName)
			return;
		
		$displayGroup = $this->getDisplayGroup("{$action}_action_group");
		if(!$displayGroup)
			return;
		
		$props = $object->$attributeName;
		if(is_object($object->$attributeName))
			$props = get_object_vars($object->$attributeName);
			
		foreach($props as $prop => $value)
		{
			if($add_underscore)
			{
				$pattern = '/(.)([A-Z])/'; 
				$replacement = '\1_\2'; 
				$prop = strtolower(preg_replace($pattern, $replacement, $prop));
			}
			$element = $displayGroup->getElement("{$action}_{$prop}");
			if($element)
				$element->setValue($value);
			else
				KalturaLog::err("element [{$action}_{$prop}] not found");
		}
	}
	
	public function resetUnUpdatebleAttributes(KalturaDistributionProfile $distributionProfile)
	{
		parent::resetUnUpdatebleAttributes($distributionProfile);
		
		if($distributionProfile instanceof KalturaGenericDistributionProfile)
		{
			$distributionProfile->genericProviderId = null;
		}
	}
	
	protected function addProviderElements()
	{
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('Generic Provider Configuration');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElements(array($element));
		
		$client = Kaltura_ClientHelper::getClient();
		Kaltura_ClientHelper::impersonate($this->partnerId);
		$genericDistributionProviderList = $client->genericDistributionProvider->listAction();
		Kaltura_ClientHelper::unimpersonate();
		
		$this->addElement('select', 'generic_provider_id', array(
			'label'	  =>  'Provider',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'prepend')), array('HtmlTag',  array('tag' => 'dt')))
		));
		
		$element = $this->getElement('generic_provider_id');
		
		if($genericDistributionProviderList && $genericDistributionProviderList->totalCount)
		{
			foreach($genericDistributionProviderList->objects as $genericDistributionProvider)
				$element->addMultiOption($genericDistributionProvider->id, $genericDistributionProvider->name);
		}
		
		$this->addEntryFields();
		$this->addMetadataFields();
	}
	
	/**
	 * @param string $action
	 * @return Zend_Form_DisplayGroup
	 */
	protected function addProfileAction($action)
	{
		$displayGroup = parent::addProfileAction($action);
		
		$element = $this->createElement('select', "{$action}_protocol", array(
			'label'	  =>  'Protocol',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'prepend')), array('HtmlTag',  array('tag' => 'dt', 'style' => 'display: none', 'class' => "action-fields-$action")))
		));
		
		$element->addMultiOption(KalturaDistributionProtocol::FTP, 'FTP');
		$element->addMultiOption(KalturaDistributionProtocol::SFTP, 'SFTP');
		$element->addMultiOption(KalturaDistributionProtocol::SCP, 'SCP');
		$element->addMultiOption(KalturaDistributionProtocol::HTTP, 'HTTP');
		$element->addMultiOption(KalturaDistributionProtocol::HTTPS, 'HTTPS');
		$displayGroup->addElement($element);
			
		$element = $this->createElement('text', "{$action}_server_url", array(
			'label'	  =>  'Server Address',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'prepend')), array('HtmlTag',  array('tag' => 'dt', 'style' => 'display: none', 'class' => "action-fields-$action")))
		));
		$displayGroup->addElement($element);
		
		$element = $this->createElement('text', "{$action}_server_path", array(
			'label'	  =>  'Remote Path',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'prepend')), array('HtmlTag',  array('tag' => 'dt', 'style' => 'display: none', 'class' => "action-fields-$action")))
		));
		$displayGroup->addElement($element);
		
		$element = $this->createElement('text', "{$action}_username", array(
			'label'	  =>  'Remote Username',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'prepend')), array('HtmlTag',  array('tag' => 'dt', 'style' => 'display: none', 'class' => "action-fields-$action")))
		));
		$displayGroup->addElement($element);
		
		$element = $this->createElement('text', "{$action}_password", array(
			'label'	  =>  'Remote Password',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'prepend')), array('HtmlTag',  array('tag' => 'dt', 'style' => 'display: none', 'class' => "action-fields-$action")))
		));
		$displayGroup->addElement($element);
		
		$element = $this->createElement('checkbox', "{$action}_ftp_passive_mode", array(
			'label'	  =>  'FTP Passive Mode',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'prepend')), array('HtmlTag',  array('tag' => 'dt', 'style' => 'display: none', 'class' => "action-fields-$action")))
		));
		$displayGroup->addElement($element);
		
		$element = $this->createElement('text', "{$action}_http_field_name", array(
			'label'	  =>  'HTTP Field Name',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'prepend')), array('HtmlTag',  array('tag' => 'dt', 'style' => 'display: none', 'class' => "action-fields-$action")))
		));
		$displayGroup->addElement($element);
		
		$element = $this->createElement('text', "{$action}_http_file_name", array(
			'label'	  =>  'HTTP File Name',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'prepend')), array('HtmlTag',  array('tag' => 'dt', 'style' => 'display: none', 'class' => "action-fields-$action")))
		));
		$displayGroup->addElement($element);
		
		return $displayGroup;
	}
}
