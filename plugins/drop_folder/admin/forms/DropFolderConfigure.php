<?php 
class Form_DropFolderConfigure extends Infra_Form
{
	protected $newPartnerId;
	
	public function __construct($partnerId)
	{
		$this->newPartnerId = $partnerId;
		
		parent::__construct();
	}
	
	
	public function init()
	{
		$this->setAttrib('id', 'frmDropFolderConfigure');
		$this->setMethod('post');			
		
		$titleElement = new Zend_Form_Element_Hidden('generalTitle');
		$titleElement->setLabel('General');
		$titleElement->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElements(array($titleElement));
		
		$this->addElement('text', 'id', array(
			'label'			=> 'ID:',
			'filters'		=> array('StringTrim'),
			'readonly'		=> true,
			'disabled'		=> 'disabled',
		));
		
		$this->addElement('text', 'partnerId', array(
			'label' 		=> 'Related Publisher ID:',
			'required'		=> true,
			'filters' 		=> array('StringTrim'),
			'placement' => 'prepend',
			'readonly'		=> true,
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
		
		$this->addConversionProfiles();
		
		$this->addElement('text', 'fileNamePatterns', array(
			'label' 		=> 'Source Files Patterns:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
		));
		
		
		$fileHandlerTypes = new Kaltura_Form_Element_EnumSelect('fileHandlerType', array('enum' => 'Kaltura_Client_DropFolder_Enum_DropFolderFileHandlerType'));
		$fileHandlerTypes->setLabel('Ingestion Source:');
		$fileHandlerTypes->setRequired(true);
		$fileHandlerTypes->setAttrib('onchange', 'handlerTypeChanged()');
		$this->addElements(array($fileHandlerTypes));
		
		$handlerConfigForm = new Form_ContentFileHandlerConfig();
		$this->addSubForm($handlerConfigForm, 'contentHandlerConfig'); 

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
	
	
	
	public function populateFromObject($object, $add_underscore = true)
	{
		parent::populateFromObject($object, $add_underscore);
		
		if ($object->fileHandlerType === Kaltura_Client_DropFolder_Enum_DropFolderFileHandlerType::CONTENT) {
			$this->getSubForm('contentHandlerConfig')->populateFromObject($object->fileHandlerConfig, false);
		}
				
		$props = $object;
		if(is_object($object))
			$props = get_object_vars($object);
		
		$allElements = $this->getElements();
		foreach ($allElements as $element)
		{
			if ($element instanceof Kaltura_Form_Element_EnumSelect)
			{
				$elementName = $element->getName();
				$element->setValue(array($props[$elementName]));
			}
		}
	}
	
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
		if ($object->fileHandlerType === Kaltura_Client_DropFolder_Enum_DropFolderFileHandlerType::CONTENT) {
			$object->fileHandlerConfig = $this->getSubForm('contentHandlerConfig')->getObject('Kaltura_Client_DropFolder_Type_DropFolderContentFileHandlerConfig', $properties, $add_underscore, $include_empty_fields);
		}
		return $object;
	}
	
	
	protected function addConversionProfiles()
	{
		$conversionProfiles = null;
		if (!is_null($this->newPartnerId))
		{
			try 
			{
				$conversionProfileFilter = new Kaltura_Client_Type_ConversionProfileFilter();

				$client = Infra_ClientHelper::getClient();
				Infra_ClientHelper::impersonate($this->newPartnerId);
				$conversionProfileList = $client->conversionProfile->listAction($conversionProfileFilter);
				Infra_ClientHelper::unimpersonate();
				
				$conversionProfiles = $conversionProfileList->objects;
			}
			catch (Kaltura_Client_Exception $e)
			{
				$conversionProfiles = null;
			}
		}
		
		if(!is_null($conversionProfiles) && count($conversionProfiles))
		{
			$this->addElement('select', 'conversionProfileId', array(
				'label' 		=> 'Conversion Profile ID:',
				'required'		=> false,
				'filters'		=> array('StringTrim'),
			));
				
			$element = $this->getElement('conversionProfileId');
			
			foreach($conversionProfiles as $conversionProfile) {
				$element->addMultiOption($conversionProfile->id, $conversionProfile->id.' - '.$conversionProfile->name);
			}
		}
		else 
		{
			$this->addElement('text', 'conversionProfileId', array(
				'label' 		=> 'Conversion Profile ID:',
				'required'		=> false,
				'filters'		=> array('StringTrim'),
			));
		}
	}
			
}