<?php 
/**
 * @package plugins.myspaceDistribution
 * @subpackage admin
 */
class Form_MyspaceProfileConfiguration extends Form_ProviderProfileConfiguration
{	
	protected function addProviderElements()
	{
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('MYSPACE Specific Configuration');
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

		$metadataProfiles = null;
		try
		{
			$metadataProfileFilter = new Kaltura_Client_Metadata_Type_MetadataProfileFilter();
//			$metadataProfileFilter->partnerIdEqual = $this->partnerId;
			$metadataProfileFilter->metadataObjectTypeEqual = Kaltura_Client_Metadata_Enum_MetadataObjectType::ENTRY;
			
			$client = Infra_ClientHelper::getClient();
			$metadataPlugin = Kaltura_Client_Metadata_Plugin::get($client);
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