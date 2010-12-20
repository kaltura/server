<?php 
class Form_GenericProviderProfileConfiguration extends Form_ProviderProfileConfiguration
{
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
	
		if($object instanceof KalturaGenericDistributionProfile)
		{
			$this->getActionObject($object, 'submit', $properties);
			$this->getActionObject($object, 'update', $properties);
			$this->getActionObject($object, 'delete', $properties);
			$this->getActionObject($object, 'report', $properties, 'fetchReport');
		}
		
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
			$this->populateFromActionObject($object, 'report', $add_underscore, 'fetchReport');
		}
	}
	
	public function getActionObject(KalturaGenericDistributionProfile $object, $action, array $properties, $attributeName = null)
	{
		if(is_null($attributeName))
			$attributeName = $action;
			
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
			$attributeName = $action;
			
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
