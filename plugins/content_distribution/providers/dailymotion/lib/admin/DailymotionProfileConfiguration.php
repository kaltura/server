<?php 
/**
 * @package plugins.dailymotionDistribution
 * @subpackage admin
 */
class Form_DailymotionProfileConfiguration extends Form_ConfigurableProfileConfiguration
{
	protected function addProviderElements()
	{
	    $this->setDescription(null);
	    
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('Dailymotion Specific Configuration');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElements(array($element));
				
		$this->addElement('text', 'user', array(
			'label'			=> 'User:',
			'filters'		=> array('StringTrim'),
		));
	
		$this->addElement('text', 'password', array(
			'label'			=> 'Password:',
			'filters'		=> array('StringTrim'),
		));

		$this->addElement('select', 'geo_blocking_mapping', array(
			'label'			=> 'Geo Blocking Mapping:',
			'filters'		=> array('StringTrim'),
			'multiOptions'	=> array(
				Kaltura_Client_DailymotionDistribution_Enum_DailymotionGeoBlockingMapping::DISABLED => 'Disabled',
				Kaltura_Client_DailymotionDistribution_Enum_DailymotionGeoBlockingMapping::ACCESS_CONTROL => 'Access Control',
				Kaltura_Client_DailymotionDistribution_Enum_DailymotionGeoBlockingMapping::METADATA => 'Custom Data',
			)
		));
	}
}