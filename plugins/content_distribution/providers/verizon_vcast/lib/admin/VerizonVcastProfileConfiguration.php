<?php 
/**
 * @package plugins.verizonVcastDistribution
 * @subpackage admin
 */
class Form_VerizonVcastProfileConfiguration extends Form_ConfigurableProfileConfiguration
{
	public function init()
	{
		parent::init();
		$this->setDescription('Verizon VCast Distribution Profile');
	}
	
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = true)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
		return $object;
	}
	
	protected function addProviderElements()
	{
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('VerizonVcast Specific Configuration');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		
		$this->addElements(array($element));
		
		$this->addElement('text', 'ftp_host', array(
			'label'			=> 'FTP Host:',
			'filters'		=> array('StringTrim'),
			'default'		=> 'ftp-int.vzw.real.com'
		));
	
		$this->addElement('text', 'ftp_login', array(
			'label'			=> 'FTP Login:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'ftp_pass', array(
			'label'			=> 'FTP Password:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'provider_name', array(
			'label'			=> 'Provider:',
			'filters'		=> array('StringTrim')
		));
		
		$this->addElement('text', 'provider_id', array(
			'label'			=> 'Provider id:',
			'filters'		=> array('StringTrim')
		));
		
		$this->addDisplayGroup(
			array('ftp_host', 'ftp_login', 'ftp_pass', 'provider_name', 'provider_id'), 
			'general', 
			array('legend' => 'General', 'decorators' => array('FormElements', 'Fieldset'))
		);
		
		$this->addElement('select', 'entitlement', array(
			'label'			=> 'Entitlement:',
			'filters'		=> array('StringTrim'),
			'multiOptions'	=> array(
				'BASIC' => 'BASIC',
				'PREMIUM' => 'PREMIUM',
				'SUBSCRIPTION' => 'SUBSCRIPTION'
			)
		));
		
		$this->addElement('text', 'priority', array(
			'label'			=> 'Priority:',
			'filters'		=> array('StringTrim')
		));
		
		$this->addElement('select', 'allow_streaming', array(
			'label'			=> 'Allow streaming:',
			'filters'		=> array('StringTrim'),
			'multiOptions'	=> array(
				'Y' => 'Yes',
				'N' => 'No',
			)
		));
		
		$this->addElement('text', 'streaming_price_code', array(
			'label'			=> 'Streaming price code:',
			'filters'		=> array('StringTrim')
		));
		
		$this->addElement('select', 'allow_download', array(
			'label'			=> 'Allow download:',
			'filters'		=> array('StringTrim'),
			'multiOptions'	=> array(
				'Y' => 'Yes',
				'N' => 'No',
			)
		));
		
		$this->addElement('text', 'download_price_code', array(
			'label'			=> 'Download price code:',
			'filters'		=> array('StringTrim')
		));
		
		$this->addDisplayGroup(
			array('entitlement', 'priority', 'allow_streaming', 'streaming_price_code', 'allow_download', 'download_price_code'), 
			'default_config_group', 
			array(
				'legend' => 'Default Metadata', 
				'decorators' => array(
					'FormElements', 
					'Fieldset'
				)
			)
		);
	}
}