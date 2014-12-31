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
//		$this->loadDefaultDecorators();
//		$this->addDecorator('Description', array('placement' => 'prepend'));
		
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('Unicorn Specific Configuration');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag', array('tag' => 'b'))));
		$this->addElements(array($element));
		
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
		
		$this->addElement('text', 'api_host_url', array(
			'label' => 'API host URL:', 
			'filters' => array('StringTrim')
		));
	}
}