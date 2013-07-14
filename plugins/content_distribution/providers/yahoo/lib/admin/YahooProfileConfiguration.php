<?php 
/**
 * @package plugins.yahooDistribution
 * @subpackage admin
 */
class Form_YahooProfileConfiguration extends Form_ConfigurableProfileConfiguration
{	
	protected function addProviderElements()
	{
		
		$this->setDescription('');
		$this->loadDefaultDecorators();
		$this->addDecorator('Description', array('placement' => 'prepend'));	
		
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('YAHOO Specific Configuration');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElements(array($element));
		
		$this->addElement('text', 'contact_telephone', array(
			'label'			=> 'Contact Telephone:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'contact_email', array(
			'label'			=> 'Contact Email:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'ftp_host', array(
			'label'			=> 'FTP Host:',
			'filters'		=> array('StringTrim'),
		));
				
		$this->addElement('text', 'ftp_username', array(
			'label'			=> 'FTP user name:',
			'filters'		=> array('StringTrim'),
		));
	
		$this->addElement('text', 'ftp_password', array(
			'label'			=> 'FTP password:',
			'filters'		=> array('StringTrim'),
		));
	
		$this->addElement('text', 'ftp_path', array(
			'label'			=> 'FTP Path:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('select', 'process_feed', array(
			'label'			=> 'Process Feed:',
			'filters'		=> array('StringTrim'),
			'multiOptions' => array(
	            0 => 'Manual',
	            1 => 'Automatic',
	        ),
		));
	}
}