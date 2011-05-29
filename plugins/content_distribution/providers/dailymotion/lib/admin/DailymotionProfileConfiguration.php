<?php 
/**
 * @package plugins.dailymotionDistribution
 * @subpackage admin
 */
class Form_DailymotionProfileConfiguration extends Form_ProviderProfileConfiguration
{
	protected function addProviderElements()
	{
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
		
		$this->addMetadataProfile();		
	}
}