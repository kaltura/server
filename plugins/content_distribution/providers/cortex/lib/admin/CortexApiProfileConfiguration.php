<?php
  
/**
 * @package plugins.cortexApiDistribution
 * @subpackage admin
 */
class Form_CortexApiProfileConfiguration extends Form_ConfigurableProfileConfiguration
{
	public function init()
	{
		parent::init();
		$this->getView()->addBasePath(realpath(dirname(__FILE__)));
		$this->addDecorator('ViewScript', array(
			'viewScript' => 'cortex-distribution.phtml',
			'placement' => 'APPEND'
		));
	}
	
	protected function addProviderElements()
	{
	    $this->setDescription(null);
	    
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('Cortex Specific Configuration');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElements(array($element));

		
		// General
		$this->addElement('text', 'host', array(
			'label'			=> 'API Host:',
			'filters'		=> array('StringTrim'),
		));
		$this->addElement('text', 'username', array(
			'label'			=> 'User:',
			'filters'		=> array('StringTrim'),
		));
		$this->addElement('text', 'password', array(
			'label'			=> 'Password:',
			'filters'		=> array('StringTrim'),
		));
		$this->addElement('text', 'folderrecordid', array(
			'label'			=> 'FolderRecordID(in Cortex):',
			'filters'		=> array('StringTrim'),
		));
		$this->addElement('text', 'metadataprofileid', array(
			'label'			=> 'Custom metadata profile id(of Cortex fields):',
			'filters'		=> array('StringTrim'),
		));
		$this->addElement('text', 'metadataprofileidpushing', array(
			'label'			=> 'Custom metadata profile id(for pushing to Cortex):',
			'filters'		=> array('StringTrim'),
		));

		$this->addDisplayGroup(
			array('host', 'username', 'password', 'folderrecordid', 'metadataprofileid', 'metadataprofileidpushing'),
			'general', 
			array('legend' => 'Cortex API Details', 'decorators' => array('FormElements', 'Fieldset'))
		);

	}
}
