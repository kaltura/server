<?php 
/**
 * @package plugins.freewheelGenericDistribution
 * @subpackage admin
 */
class Form_FreewheelGenericProfileConfiguration extends Form_ConfigurableProfileConfiguration
{
	public function init()
	{
		parent::init();
		$this->setDescription('Freewheel Distribution Profile');
		
		$this->getView()->addBasePath(realpath(dirname(__FILE__)));
		$this->addDecorator('ViewScript', array(
			'viewScript' => 'freewheel-form.phtml',
			'placement' => 'APPEND'
		));
	}
	
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
		
		return $object;
	}
	
	public function populateFromObject($object, $add_underscore = true)
	{
		parent::populateFromObject($object, $add_underscore);
		
		if ($object->replaceGroup)
			$this->setDefault('replace_group', 'true');
		else 
			$this->setDefault('replace_group', 'false');
			
		if ($object->replaceAirDates)
			$this->setDefault('replace_air_dates', 'true');
		else 
			$this->setDefault('replace_air_dates', 'false');
	}	
	
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
		
		$this->addElement('select', 'replace_group', array(
			'label'			=> 'Replace Group:',
			'filters'		=> array('StringTrim'),
			'multiOptions'	=> array(
				'true' => 'true',
				'false' => 'false'
			)
		));
		
		$this->addElement('select', 'replace_air_dates', array(
			'label'			=> 'Replace Air Dates:',
			'filters'		=> array('StringTrim'),
			'multiOptions'	=> array(
				'true' => 'true',
				'false' => 'false'
			)
		));
		
		$this->addDisplayGroup(
			array('apikey', 'email', 'sftp_login', 'sftp_pass', 'replace_group', 'replace_air_dates'), 
			'general', 
			array('legend' => 'General', 'decorators' => array('FormElements', 'Fieldset'))
		);

		$this->addElement('select', 'content_owner', array(
			'label'			=> 'fwContentOwner:',
			'filters'		=> array('StringTrim'),
			'multiOptions' => array(
				'OutFwContentOwner' => 'OutFwContentOwner',
				'InFwContentOwner' => 'InFwContentOwner',
				'UnassignContentOwner' => 'UnassignContentOwner',
				'SelfContentOwner' => 'SelfContentOwner'
			),
			'value' => 'SelfContentOwner',
		));
		
		$this->addElement('text', 'upstream_video_id', array(
			'label'			=> 'Upstream Video ID:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'upstream_network_name', array(
			'label'			=> 'Upstream Network Name:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'upstream_network_id', array(
			'label'			=> 'Upstream Network ID:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'category_id', array(
			'label'			=> 'Category ID:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addDisplayGroup(
			array('content_owner', 'upstream_video_id', 'upstream_network_name', 'upstream_network_id', 'category_id'), 
			'content_owner_group', 
			array(
				'legend' => 'Content Owner', 
				'decorators' => array(
					'FormElements', 
					'Fieldset'
				)
			)
		);
	}
}