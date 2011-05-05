<?php 
class Form_DropFolderConfigure extends Infra_Form
{
	public function init()
	{
		$this->setAttrib('id', 'frmDropFolderConfigure');
		$this->setMethod('post');
		

		$titleElement = new Zend_Form_Element_Hidden('generalTitle');
		$titleElement->setLabel('General');
		$titleElement->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElements(array($titleElement));
		
		
		$this->addElement('text', 'partnerId', array(
			'label' 		=> 'Related Publisher ID:',
			'required'		=> true,
			'filters' 		=> array('StringTrim'),
			'placement' => 'prepend',	
		));
		
		$this->addElement('text', 'name', array(
			'label' 		=> 'Drop Folder Name:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
			'placement' => 'prepend',
		));
		
		$this->addElement('text', 'description', array(
			'label' 		=> 'Description:',
			'required'		=> false,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'tags', array(
			'label' 		=> 'Tags:',
			'required'		=> false,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('hidden', 'crossLine1', array(
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));
		
		// --------------------------------
		
		$titleElement = new Zend_Form_Element_Hidden('ingestionSettingsTitle');
		$titleElement->setLabel('Ingestion Settings');
		$titleElement->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElements(array($titleElement));
		
		//TODO: change to ingestion profile name
		$this->addElement('text', 'ingestionProfileId', array(
			'label' 		=> 'Ingestion Profile ID:',
			'required'		=> false,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'fileNamePatterns', array(
			'label' 		=> 'Source Files Patterns:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
		));
		
		
		$fileHandlerTypes = new Kaltura_Form_Element_EnumSelect('fileHandlerType', array('enum' => 'Kaltura_Client_DropFolder_Enum_DropFolderFileHandlerType'));
		$fileHandlerTypes->setLabel('Ingestion Source:');
		$fileHandlerTypes->setRequired(true);
		$fileHandlerTypes->setAttrib('onchange', 'handlerTypeChanged()');
		$fileHandlerTypes->setAttrib('id', 'fileHandlerType()');
		$this->addElements(array($fileHandlerTypes));
		
		$this->addContentHandlerElements();
		
		$this->addElement('hidden', 'crossLine2', array(
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));
		
		// --------------------------------
		
		$titleElement = new Zend_Form_Element_Hidden('locationTitle');
		$titleElement->setLabel('Folder Location');
		$titleElement->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElements(array($titleElement));
		
		$this->addElement('text', 'dc', array(
			'label' 		=> 'Data Center:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'path', array(
			'label' 		=> 'Folder Path:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('hidden', 'crossLine3', array(
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));
		
		// --------------------------------
		
		$titleElement = new Zend_Form_Element_Hidden('policiesTitle');
		$titleElement->setLabel('Folder Policies');
		$titleElement->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElements(array($titleElement));
		
		$this->addElement('text', 'fileSizeCheckInterval', array(
			'label' 		=> 'Check file size every (seconds):',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
		));
		
		$fileDeletePolicies = new Kaltura_Form_Element_EnumSelect('fileDeletePolicy', array('enum' => 'Kaltura_Client_DropFolder_Enum_DropFolderFileDeletePolicy'));
		$fileDeletePolicies->setLabel('File Deletion Policy:');
		$fileDeletePolicies->setRequired(true);
		$this->addElements(array($fileDeletePolicies));
		
		$this->addElement('text', 'autoFileDeleteDays', array(
			'label' 		=> 'Auto delete files after (days):',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
		));
	}
	
	
	private function addContentHandlerElements()
	{
		$fileDeletePolicies = new Kaltura_Form_Element_EnumSelect('contentMatchPolicy', array('enum' => 'Kaltura_Client_DropFolder_Enum_DropFolderContentFileHandlerMatchPolicy'));
		$fileDeletePolicies->setLabel('Content Match Policy:');
		$fileDeletePolicies->setRequired(true);
		$this->addElements(array($fileDeletePolicies));
		
		$this->addElement('text', 'slugRegex', array(
			'label' 		=> 'Slug Regex:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
		));
		
		//TODO: set as dispaly:none and only show if CONTENT is selected
	}
	
}