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
		
		$this->addDisplayGroup(
			array('apikey', 'email', 'sftp_login', 'sftp_pass'), 
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