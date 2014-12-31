<?php
/**
 * @package plugins.unicornDistribution
 * @subpackage admin
 */
class Form_UnicornProfileConfiguration extends Form_ConfigurableProfileConfiguration
{
	protected function addProviderElements()
	{
		$this->setDescription('Unicorn Distribution Profile');
		
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('Unicorn Specific Configuration');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag', array('tag' => 'b'))));
		$this->addElements(array($element));
		
		$this->addElement('text', 'api_host_url', array(
			'label' => 'API host URL:', 
			'filters' => array('StringTrim')
		));
		
		$this->addElement('text', 'username', array(
			'label' => 'Username:', 
			'filters' => array('StringTrim')
		));
		
		$this->addElement('text', 'password', array(
			'label' => 'Password:', 
			'filters' => array('StringTrim')
		));
		
		$this->addElement('text', 'domain_name', array(
			'label' => 'Domain name:', 
			'filters' => array('StringTrim')
		));
		
		$this->addElement('text', 'domain_guid', array(
			'label' => 'Domain GUID:', 
			'filters' => array('StringTrim')
		));
		
		$this->addElement('text', 'ad_free_application_guid', array(
			'label' => 'Ad free application GUID:', 
			'filters' => array('StringTrim')
		));
		
		$this->addElement('select', 'remote_asset_params_id', array(
			'label' => 'Remote asset params ID:',
			'registerInArrayValidator' => false,
		));
	
		$storageProfiles = array();
		try
		{
			$client = Infra_ClientHelper::getClient();
			Infra_ClientHelper::impersonate($this->partnerId);
			$storageProfileList = $client->storageProfile->listAction();
			Infra_ClientHelper::unimpersonate();
		
			foreach($storageProfileList->objects as $storageProfile)
			{
				/* @var $storageProfile Kaltura_Client_Type_StorageProfile */
				$storageProfiles[$storageProfile->id] = $storageProfile->name;
			}
		}
		catch (Kaltura_Client_Exception $e)
		{
		}
		
		$this->addElement('select', 'storage_profile_id', array(
			'label' => 'Storage profile ID:',
			'multiOptions' => $storageProfiles
		));
	}
	
	public function addFlavorParamsFields(Kaltura_Client_Type_FlavorParamsListResponse $flavorParams, array $optionalFlavorParamsIds = array(), array $requiredFlavorParamsIds = array())
	{
		$options = array();
		foreach($flavorParams->objects as $flavorParamsItem)
		{
			/* @var $flavorParamsItem Kaltura_Client_Type_FlavorParams */
			$options[$flavorParamsItem->id] = $flavorParamsItem->name;
		}
		$this->getElement("remote_asset_params_id")->setMultiOptions($options);
		
		parent::addFlavorParamsFields($flavorParams, $optionalFlavorParamsIds, $requiredFlavorParamsIds);
	}
}