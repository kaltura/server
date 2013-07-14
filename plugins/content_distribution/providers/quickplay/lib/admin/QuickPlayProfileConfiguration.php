<?php 
/**
 * @package plugins.quickPlayDistribution
 * @subpackage admin
 */
class Form_QuickPlayProfileConfiguration extends Form_ConfigurableProfileConfiguration
{
	public function init()
	{
		parent::init();
		$this->setDescription('QuickPlay Distribution Profile');
	}
	
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = true)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
		return $object;
	}
	
	protected function addProviderElements()
	{
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('QuickPlay Specific Configuration');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		
		$this->addElements(array($element));
		
		// General
		$this->addElement('text', 'sftp_host', array(
			'label'			=> 'SFTP Host:',
			'filters'		=> array('StringTrim'),
			'default'		=> 'husky.quickplay.ca',
			'required'		=> true
		));
	
		$this->addElement('text', 'sftp_login', array(
			'label'			=> 'SFTP Login:',
			'filters'		=> array('StringTrim'),
			'required'		=> true
		));
		
		$this->addElement('text', 'sftp_pass', array(
			'label'			=> 'SFTP Password:',
			'filters'		=> array('StringTrim'),
			'required'		=> true
		));

		$this->addElement('text', 'sftp_base_path', array(
			'label'			=> 'SFTP Base Path:',
			'filters'		=> array('StringTrim'),
			'value'			=> '/upload/',
			'required'		=> true
		));
		
		$this->addDisplayGroup(
			array('sftp_host', 'sftp_login', 'sftp_pass', 'sftp_base_path'),
			'general', 
			array('legend' => 'General', 'decorators' => array('FormElements', 'Fieldset'))
		);
		
		// Channel
		$element = new Zend_Form_Element_Text('channel_title');
		$element->setLabel('Channel Title:');
		$element->setRequired(true);
		$element->addValidator(new Zend_Validate_StringLength(0, 64));
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('channel_link');
		$element->setLabel('Channel Link:');
		$element->setRequired(true);
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('channel_description');
		$element->setLabel('Channel Description:');
		$element->setRequired(true);
		$element->addValidator(new Zend_Validate_StringLength(0, 256));
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('channel_managing_editor');
		$element->setLabel('Channel Managing Editor:');
		$element->setRequired(true);
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Select('channel_language');
		$element->setLabel('Channel Language:');
		$element->setMultiOptions(array(
			'en-ca' => 'en-ca', 
			'fr-ca' => 'fr-ca', 
			'en-us' => 'en-us', 
			'es-us' => 'es-us'
		));
		$element->setValue('en-ca');
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('channel_image_title');
		$element->setLabel('Channel Image Title:');
		$element->setRequired(true);
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('channel_image_width');
		$element->setLabel('Channel Image Width:');
		$element->setRequired(true);
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('channel_image_height');
		$element->setLabel('Channel Image Height:');
		$element->setRequired(true);
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('channel_image_link');
		$element->setLabel('Channel Image Link:');
		$element->setRequired(true);
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('channel_image_url');
		$element->setLabel('Channel Image Url:');
		$element->setRequired(true);
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('channel_copyright');
		$element->setLabel('Channel Copyright:');
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('channel_generator');
		$element->setLabel('Channel Generator:');
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('channel_rating');
		$element->setLabel('Channel Rating:');
		$this->addElement($element);
		
		$this->addDisplayGroup(
			array(
				'channel_title', 
				'channel_link', 
				'channel_description', 
				'channel_managing_editor', 
				'channel_language', 
				'channel_image_title',
				'channel_image_width',
				'channel_image_height',
				'channel_image_link',
				'channel_image_url',
				'channel_copyright',
				'channel_generator',
				'channel_rating'
			), 
			'channel', 
			array('legend' => 'Feed Configuration', 'decorators' => array('FormElements', 'Fieldset'))
		);
	}
}