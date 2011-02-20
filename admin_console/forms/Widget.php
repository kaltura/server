<?php 
class Form_Widget extends Kaltura_Form
{
	public function init()
	{
		// Set the method for the display form to POST
		$this->setMethod('post');
		$this->setAttrib('class', 'inline-form');
		

		$this->addElement('text', 'id', array(
			'label'			=> 'ID:',
			'required'		=> false,
			'disabled' 		=> true,
			'filters'		=> array('StringTrim'),
			'validators' 	=> array()
		));
		
		$this->addElement('text', 'name', array(
			'label'			=> 'Name:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
			'validators' 	=> array()
		));
		
		$this->addElement('text', 'width', array(
			'label'			=> 'Width:',
			'filters'		=> array('StringTrim'),
			'validators' 	=> array('Digits')
		));
		
		$this->addElement('text', 'height', array(
			'label'			=> 'Height:',
			'filters'		=> array('StringTrim'),
			'validators' 	=> array('Digits')
		));

		$this->addElement('select', 'creation_mode', array(
			'label'			=> 'Creation Mode:',
			'filters'		=> array('StringTrim'),
			'required'		=> true,
			'multiOptions' 		=> array(
				KalturaUiConfCreationMode::ADVANCED => 'Advanced',
				KalturaUiConfCreationMode::WIZARD => 'AppStudio Wizard',
			)
		));

		$this->addElement('select', 'obj_type', array(
			'label'			=> 'Widget Type:',
			'filters'		=> array('StringTrim'),
			'required'		=> true,
			'multiOptions' 		=> array(
				'' => '',
				KalturaUiConfObjType::PLAYER_V3 => 'KDP3',
				KalturaUiConfObjType::CONTRIBUTION_WIZARD => 'Contribution Wizard',
			)
		));

		$this->addElement('select', 'version', array(
			'label'			=> 'Version:',
			'filters'		=> array('StringTrim'),
			'required'		=> false,
			'multiOptions' 		=> array(
			)
		));
		
		$this->addElement('text', 'swf_url', array(
			'label'			=> 'SWF Url:',
			'required'		=> true,
			'readonly' 		=> true,
			'filters'		=> array('StringTrim'),
			'validators' 	=> array()
		));
		
		$this->addElement('text', 'conf_vars', array(
			'label'			=> 'Conf Vars:',
			'required'		=> false,
			'filters'		=> array('StringTrim'),
			'validators' 	=> array()
		));
		
		$this->addElement('checkbox', 'use_cdn', array(
			'label'			=> 'Use CDN:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
			'validators' 	=> array()
		));
		
		$this->addElement('text', 'tags', array(
			'label'			=> 'Tags:',
			'required'		=> false,
			'filters'		=> array('StringTrim'),
			'validators' 	=> array()
		));
		
		$this->addElement('textarea', 'conf_file', array(
			'label'			=> 'Conf File:',
			'validators' 	=> array()
		));
		$this->getElement('conf_file')->getDecorator('Description')->setEscape(false);
		
		$this->addElement('textarea', 'conf_file_features', array(
			'label'			=> 'Conf File Features:',
			'validators' 	=> array()
		));
		$this->getElement('conf_file_features')->getDecorator('Description')->setEscape(false);
		
		$this->addElement('text', 'tags', array(
			'label'			=> 'Tags:',
			'required'		=> false,
			'filters'		=> array('StringTrim'),
			'validators' 	=> array()
		));
		
		$elements = $this->getElements();
		$ids = array();
		foreach($elements as $element)
			$ids[] = $element->getId();
		
		$this->addDisplayGroup($ids, 'widget_info', array(
			'decorators' => array(
				'Description', 
				'FormElements', 
				array('Fieldset'),
			)
		));
		
		$this->addElement('button', 'submit', array(
			'label' => 'Create',
			'type' => 'submit',
			'decorators' => array('ViewHelper')
		));
		
		
		$this->addDisplayGroup(array('submit'), 'buttons1', array(
			'decorators' => array(
				'FormElements', 
				array('HtmlTag', array('tag' => 'div', 'class' => 'buttons')),
			)
		));
	}
	
	public function populateFromObject($object, $addUnderscore = true)
	{
		parent::populateFromObject($object, $addUnderscore);
		$this->setDefault('version', $this->getVersionFromSwfUrl());
		$this->setEditorButtons();
	}
	
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		return parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
	}
	
	public function loadVersions($objType)
	{
		$client = Kaltura_ClientHelper::getClient();
		$typesInfo = $client->uiConf->getAvailableTypes();
		$versionElement = $this->getElement('version');
		$versionElement->addMultiOption('', '');
		foreach($typesInfo as $typeInfo)
		{
			if ($typeInfo->type == $objType)
			{
				foreach($typeInfo->versions as $version)
					$versionElement->addMultiOption($version->value, $version->value);
			}
		}
	}
	
	public function setEditMode()
	{
		$this->getElement('submit')->setLabel('Save');
	}
	
	public function getVersionFromSwfUrl()
	{
		$swfVersion = null;
		if (preg_match('|/flash/(\w*)/(.*)/|', $this->getValue('swf_url'), $version)) 
		{
			$swfVersion = $version[2];
			if (strpos($swfVersion, "/") !== false) // for sub directories
			{
				$swfVersion = substr($swfVersion, strpos($swfVersion, "/") + 1);
			}
		}
		return $swfVersion;
	}
	
	public function setEditorButtons()
	{
		$openEditorButton = '<a class="open-editor">Open Editor</a>';
		$openVisualEditorButton = '<a class="open-visual-editor">Open Visual Editor</a>';
		
		$confFileButtons = array();
		$confFileButtons[] = $openEditorButton;
		if ($this->getValue('obj_type') == KalturaUiConfObjType::CONTRIBUTION_WIZARD)
			$confFileButtons[] = $openVisualEditorButton;

		$confFileFeaturesButtons = array();
		$confFileFeaturesButtons[] = $openEditorButton;
		
		$this->getElement('conf_file')->setDescription(implode($confFileButtons, ' | '));
		$this->getElement('conf_file_features')->setDescription(implode($confFileFeaturesButtons, ' | '));
	}
}