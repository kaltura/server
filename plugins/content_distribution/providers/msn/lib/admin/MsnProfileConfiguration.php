<?php 
/**
 * @package plugins.msnDistribution
 * @subpackage admin
 */
class Form_MsnProfileConfiguration extends Form_ProviderProfileConfiguration
{
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
		
		if($object instanceof KalturaMsnDistributionProfile)
		{
			$requiredFlavorParamsIds = array(
				$object->movFlavorParamsId,
				$object->flvFlavorParamsId,
				$object->wmvFlavorParamsId,
			);
			
			$object->requiredFlavorParamsIds = implode(',', $requiredFlavorParamsIds);
		}
		return $object;
	}
	
	public function addFlavorParamsFields(Kaltura_Client_Type_FlavorParamsListResponse $flavorParams, array $optionalFlavorParamsIds = array(), array $requiredFlavorParamsIds = array())
	{
		// overrides the default flavors form
	}
	
	protected function addProviderElements()
	{
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('MSN Specific Configuration');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElements(array($element));
		
		$this->addMetadataProfile();
		
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
		
		$this->addElement('text', 'cs_id', array(
			'label'			=> 'CS ID:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'source', array(
			'label'			=> 'Source:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'mov_flavor_params_id', array(
			'label'			=> 'MOV Flavor Params ID:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'flv_flavor_params_id', array(
			'label'			=> 'FLV Flavor Params ID:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'wmv_flavor_params_id', array(
			'label'			=> 'WMV Flavor Params ID:',
			'filters'		=> array('StringTrim'),
		));
	}
}