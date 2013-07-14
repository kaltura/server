<?php 
/**
 * @package plugins.freewheelDistribution
 * @subpackage admin
 */
class Form_FreewheelProfileConfiguration extends Form_ProviderProfileConfiguration
{
	protected function addProviderElements()
	{
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('Freewheel Specific Configuration');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElements(array($element));
		
		$this->addElement('text', 'apikey', array(
			'label'			=> 'API Key:',
			'filters'		=> array('StringTrim'),
		));
	
		$this->addElement('text', 'email', array(
			'label'			=> 'E-Mail:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'sftp_login', array(
			'label'			=> 'SFTP Login:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'sftp_pass', array(
			'label'			=> 'SFTP Password:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addMetadataProfile();
	}
}