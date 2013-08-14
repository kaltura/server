<?php 
/**
 * @package Admin
 * @subpackage Widgets
 */
class Form_Widget extends Infra_Form
{
	public function init()
	{
		// Set the method for the display form to POST
		$this->setMethod('post');
		$this->setAttrib('class', 'widget-form');
		

		$this->addElement('text', 'id', array(
			'label'			=> 'UI Conf ID:',
			'required'		=> false,
			'disabled' 		=> true,
			'filters'		=> array('StringTrim'),
			'validators' 	=> array()
		));
		
		$this->addElement('text', 'partner_id', array(
			'label'			=> 'Publisher ID:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'name', array(
			'label'			=> 'UI Conf Name:',
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
				Kaltura_Client_Enum_UiConfCreationMode::ADVANCED => 'Advanced',
				Kaltura_Client_Enum_UiConfCreationMode::WIZARD => 'AppStudio Wizard',
			)
		));

		$this->addElement('select', 'obj_type', array(
			'label'			=> 'UI Conf Type:',
			'filters'		=> array('StringTrim'),
			'required'		=> true,
			'multiOptions' 		=> array(
				'' => '',
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
			'label'			=> 'SWF URL:',
			'required'		=> true,
			'readonly' 		=> true,
			'filters'		=> array('StringTrim'),
			'validators' 	=> array()
		));

		$this->addElement('text', 'html5_url', array(
			'label'			=> 'HTML5 URL:',
			'required'		=> false,
			'filters'		=> array('StringTrim'),
			'validators' 	=> array()
		));
		
		$this->addElement('text', 'conf_vars', array(
			'label'			=> 'Additional flashvars:',
			'required'		=> false,
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
			'label'			=> 'Studio Features:',
			'validators' 	=> array()
		));
		$this->getElement('conf_file_features')->getDecorator('Description')->setEscape(false);
		
		$this->addElement('textarea', 'config', array(
			'label'			=> 'Config:',
			'validators' 	=> array()
		));
		$this->getElement('config')->getDecorator('Description')->setEscape(false);
		
		$this->addElement('text', 'tags', array(
			'label'			=> 'Tags:',
			'required'		=> false,
			'filters'		=> array('StringTrim'),
			'validators' 	=> array()
		));
		
		$this->addElement('checkbox', 'use_cdn', array(
			'label'			=> 'Use CDN:',
			'required'		=> true,
			'value'			=> '1',
			'filters'		=> array('StringTrim'),
			'validators' 	=> array()
		));
		
		$this->addElement('checkbox', 'is_public', array(
			'label'			=> 'Is Public:',
			'required'		=> true,
			'value'			=> '0',
			'filters'		=> array('StringTrim'),
			'validators' 	=> array()
		));
	}
	
	public function populateFromObject($object, $addUnderscore = true)
	{
		parent::populateFromObject($object, $addUnderscore);
		$this->setDefault('version', $this->getVersionFromSwfUrl());
	}
	
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		return parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
	}
	
	public function loadVersions($objType)
	{
		$client = Infra_ClientHelper::getClient();
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
		$enableVisualEditor = Zend_Registry::get('config')->settings->enableKCWVisualEditor;
		if (($this->getValue('obj_type') == Kaltura_Client_Enum_UiConfObjType::CONTRIBUTION_WIZARD) && ( is_null($enableVisualEditor) || $enableVisualEditor)) 
			$confFileButtons[] = $openVisualEditorButton;
		$confFileFeaturesButtons = array();
		$confFileFeaturesButtons[] = $openEditorButton;
		$configButtons = array();
		$configButtons[] = $openEditorButton;
		
		
		$this->getElement('conf_file')->setDescription(implode($confFileButtons, ' | '));
		$this->getElement('conf_file_features')->setDescription(implode($confFileFeaturesButtons, ' | '));
		$this->getElement('config')->setDescription(implode($configButtons, ' | '));
	}
	
	public function setObjTypes($array)
	{
		$this->getElement('obj_type')->addMultiOptions($array);
	}
}
