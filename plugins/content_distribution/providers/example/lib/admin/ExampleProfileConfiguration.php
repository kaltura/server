<?php 
/**
 * @package plugins.exampleDistribution
 * @subpackage admin
 */
class Form_ExampleProfileConfiguration extends Form_ProviderProfileConfiguration
{
	protected function addProviderElements()
	{
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('Example Specific Configuration');
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
	
		$this->addElement('text', 'account_id', array(
			'label'			=> 'Account:',
			'filters'		=> array('StringTrim'),
		));
	}
}