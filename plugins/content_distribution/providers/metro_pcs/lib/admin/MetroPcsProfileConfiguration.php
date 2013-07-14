<?php 
/**
 * @package plugins.metroPcsDistribution
 * @subpackage admin
 */
class Form_MetroPcsProfileConfiguration extends Form_ConfigurableProfileConfiguration
{
	
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = true)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
		return $object;
	}
	
	protected function addProviderElements()
	{		
		$this->setDescription('');
		$this->loadDefaultDecorators();
		$this->addDecorator('Description', array('placement' => 'prepend'));
		
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('MetroPcs Specific Configuration');
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
		
		$this->addElement('text', 'ftp_path', array(
			'label'			=> 'FTP Path:',
			'filters'		=> array('StringTrim'),
		));

		/*
		$this->addElement('text', 'provider_name', array(
			'label'			=> 'Provider:',
			'filters'		=> array('StringTrim')
		));
		*/
		$this->addElement('text', 'provider_id', array(
			'label'			=> 'Provider id:',
			'filters'		=> array('StringTrim')
		));
		
		$this->addDisplayGroup(
			array('ftp_host', 'ftp_login', 'ftp_pass', 'ftp_path' ,'provider_name', 'provider_id'), 
			'general', 
			array('legend' => 'General', 'decorators' => array('FormElements', 'Fieldset'))
		);
		
		$this->addElement('select', 'entitlements', array(
			'label'			=> 'Entitlement:',
			'filters'		=> array('StringTrim'),
			'multiOptions'	=> array(
				'METROPCS_VIDEO_BASIC' => 'METROPCS_VIDEO_BASIC',
				'PREMIUM' => 'PREMIUM',
				'SUBSCRIPTION' => 'SUBSCRIPTION'
			)
		));
		
		$this->addElement('text', 'copyright', array(
			'label'			=> 'Copyright:',
			'filters'		=> array('StringTrim')
		));
		
		$this->addElement('text', 'rating', array(
			'label'			=> 'Rating:',
			'filters'		=> array('StringTrim')
		));
		
		$this->addElement('text', 'item_type', array(
			'label'			=> 'Type:',
			'filters'		=> array('StringTrim')
		));
		
		$this->addDisplayGroup(
			array('entitlements', 'copyright', 'rating', 'item_type'), 
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