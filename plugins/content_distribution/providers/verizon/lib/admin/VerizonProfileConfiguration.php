<?php 
/**
 * @package plugins.verizonDistribution
 * @subpackage admin
 */
class Form_VerizonProfileConfiguration extends Form_ProviderProfileConfiguration
{	
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
		
		if($object instanceof Kaltura_Client_VerizonDistribution_Type_VerizonDistributionProfile)
		{
			$requiredFlavorParamsIds = explode(',', $object->requiredFlavorParamsIds);
			$optionalFlavorParamsIds = explode(',', $object->optionalFlavorParamsIds);
			
			if($object->vrzFlavorParamsId)
			{
				if(!in_array($object->vrzFlavorParamsId, $requiredFlavorParamsIds))
					$requiredFlavorParamsIds[] = $object->vrzFlavorParamsId ? $object->vrzFlavorParamsId : '0';
					
				$flavorKey = array_search($object->vrzFlavorParamsId, $optionalFlavorParamsIds);
				if($flavorKey !== false)
					unset($optionalFlavorParamsIds[$flavorKey]);
			}
						
			$object->requiredFlavorParamsIds = implode(',', $requiredFlavorParamsIds);
			$object->optionalFlavorParamsIds = implode(',', $optionalFlavorParamsIds);
		}
		return $object;
	}

	protected function addProviderElements()
	{
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('VERIZON Specific Configuration');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElements(array($element));
		
		$this->addElement('text', 'username', array(
			'label'			=> 'Username:',
			'filters'		=> array('StringTrim'),
		));
	
		$this->addElement('text', 'password', array(
			'label'			=> 'Password:',
			'filters'		=> array('StringTrim'),
		));
	
		$this->addElement('text', 'domain', array(
			'label'			=> 'Domain:',
			'filters'		=> array('StringTrim'),
		));

		$this->addElement('text', 'vrz_flavor_params_id', array(
			'label'			=> 'Verizon Flavor Params ID:',
			'filters'		=> array('StringTrim'),
		));

		$this->addElement('text', 'provider_name', array(
			'label'			=> 'Verizon provider name:',
			'filters'		=> array('StringTrim'),
		));

		$this->addElement('text', 'provider_id', array(
			'label'			=> 'Verizon provider id:',
			'filters'		=> array('StringTrim'),
		));
		
		$metadataProfiles = null;
		try
		{
			$metadataProfileFilter = new Kaltura_Client_Metadata_Type_MetadataProfileFilter();
//			$metadataProfileFilter->partnerIdEqual = $this->partnerId;
			$metadataProfileFilter->metadataObjectTypeEqual = Kaltura_Client_Metadata_Enum_MetadataObjectType::ENTRY;
			
			$client = Infra_ClientHelper::getClient();
			Infra_ClientHelper::impersonate($this->partnerId);
			$metadataProfileList = $client->metadataProfile->listAction($metadataProfileFilter);
			Infra_ClientHelper::unimpersonate();
			
			$metadataProfiles = $metadataProfileList->objects;
		}
		catch (Kaltura_Client_Exception $e)
		{
			$metadataProfiles = null;
		}
		
		if(count($metadataProfiles))
		{
			$this->addElement('select', 'metadata_profile_id', array(
				'label'			=> 'Metadata Profile ID:',
				'filters'		=> array('StringTrim'),
			));
			
			$element = $this->getElement('metadata_profile_id');
			foreach($metadataProfiles as $metadataProfile)
				$element->addMultiOption($metadataProfile->id, $metadataProfile->name);
		}
		else 
		{
			$this->addElement('hidden', 'metadata_profile_id', array(
				'value'			=> 0,
			));
		}


		
	}
}