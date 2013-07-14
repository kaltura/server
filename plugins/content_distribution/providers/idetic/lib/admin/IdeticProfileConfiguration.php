<?php 
/**
 * @package plugins.ideticDistribution
 * @subpackage admin
 */
class Form_IdeticProfileConfiguration extends Form_ConfigurableProfileConfiguration
{	
	protected function addProviderElements()
	{
		
		$this->setDescription('indetic-distribution-note');
		$this->loadDefaultDecorators();
		$this->addDecorator('Description', array('placement' => 'prepend'));	
		
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('IDETIC Specific Configuration');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElements(array($element));
		
		$this->addElement('text', 'ftp_path', array(
			'label'			=> 'FTP Path:',
			'filters'		=> array('StringTrim'),
		));
		
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
	}
}