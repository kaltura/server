<?php
/**
 * @package plugins.dropFolder
 * @subpackage Admin
 */
class Form_DropFolderConfigure extends Infra_Form
{
	protected $newPartnerId;
	protected $dropFolderType;

	const EXTENSION_SUBFORM_NAME = 'extensionSubForm';

	public function __construct($partnerId, $type)
	{
		$this->newPartnerId = $partnerId;
		$this->dropFolderType = $type;

		parent::__construct();
	}


	public function init()
	{
		$this->setAttrib('id', 'frmDropFolderConfigure');
		$this->setMethod('post');

		$titleElement = new Zend_Form_Element_Hidden('generalTitle');
		$titleElement->setLabel('General');
		$titleElement->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElement($titleElement);

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

		$typeForView = new Kaltura_Form_Element_EnumSelect('typeForView', array('enum' => 'Kaltura_Client_DropFolder_Enum_DropFolderType'));
		$typeForView->setLabel('Type:');
		$typeForView->setAttrib('readonly', true);
		$typeForView->setAttrib('disabled', 'disabled');
		$typeForView->setValue($this->dropFolderType);
		$this->addElement($typeForView);

		$this->addElement('hidden', 'type', array(
			'filters' 		=> array('StringTrim'),
			'decorators'    => array('ViewHelper'),
		    'value'			=> $this->dropFolderType,
		));

		$this->addElement('text', 'tags', array(
			'label' 		=> 'Tags: (used by batch workers)',
			'required'		=> false,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('checkbox', 'incremental', array(
			'label'	  => 'Incremental',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'div', 'class' => 'rememeber')))
		));
		
		$this->addElement('text', 'lastFileTimestamp', array(
			'label' 		=> 'Last file timestamp:',
			'required'		=> true,
			'value'			=> 0,
			'filters'		=> array('StringTrim'),
		));

		$this->addElement('hidden', 'crossLine1', array(
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));

		// --------------------------------

		$titleElement = new Zend_Form_Element_Hidden('ingestionSettingsTitle');
		$titleElement->setLabel('Ingestion Settings');
		$titleElement->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElement($titleElement);

		$this->addConversionProfiles();

		$fileHandlerType = new Kaltura_Form_Element_EnumSelect('fileHandlerType', array('enum' => 'Kaltura_Client_DropFolder_Enum_DropFolderFileHandlerType'));
		$fileHandlerType->setLabel('Ingestion Workflow:');
		$fileHandlerType->setRequired(true);
		$fileHandlerType->setAttrib('onchange', 'handlerTypeChanged()');
		$this->addElement($fileHandlerType);

		$fileHandlerTypeForView = new Kaltura_Form_Element_EnumSelect('fileHandlerTypeForView', array('enum' => 'Kaltura_Client_DropFolder_Enum_DropFolderFileHandlerType'));
		$fileHandlerTypeForView->setAttrib('disabled', 'disabled');
		$fileHandlerTypeForView->setAttrib('style', 'display:none');
		$this->addElement($fileHandlerTypeForView);
		
		$fileHandlerTypes = Form_BaseFileHandlerConfig::getFileHandlerTypes();
		foreach($fileHandlerTypes as $type)
		{
			switch($type)
			{
				case Kaltura_Client_DropFolder_Enum_DropFolderFileHandlerType::CONTENT:
					$handlerConfigForm = new Form_ContentFileHandlerConfig();
					$this->addSubForm($handlerConfigForm, 'fileHandlerConfig' . $type);
					break;

				case Kaltura_Client_DropFolder_Enum_DropFolderFileHandlerType::XML:
					$handlerConfigForm = new Form_XmlFileHandlerConfig();
					$this->addSubForm($handlerConfigForm, 'fileHandlerConfig' . $type);
					break;
					
				default:
					$handlerConfigForm = KalturaPluginManager::loadObject('Form_BaseFileHandlerConfig', $type);
					if($handlerConfigForm)
						$this->addSubForm($handlerConfigForm, 'fileHandlerConfig' . $type);
			}
		}

		$this->addElement('text', 'fileNamePatterns', array(
			'label' 		=> 'Source File Name Patterns (to handle):',
			'required'		=> true,
		    'value'			=> '*.xml',
			'filters'		=> array('StringTrim'),
		));

		$this->addElement('text', 'ignoreFileNamePatterns', array(
			'label' 		=> 'Ignore file name patterns (don\'t even list them) :',
			'filters'		=> array('StringTrim'),
		));

		
		$this->addElement('hidden', 'crossLine2', array(
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));
		

		// --------------------------------

		$titleElement = new Zend_Form_Element_Hidden('locationTitle');
		$titleElement->setLabel('Local Storage Folder Location');
		$titleElement->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElement($titleElement);

		$this->addElement('text', 'dc', array(
			'label' 		=> 'Drop Folder Batch Jobs Datacenter Location:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
		));

		$this->addElement('text', 'path', array(
			'label' 		=> 'Drop Folder Storage Path:',
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
		$this->addElement($titleElement);

		$this->addElement('text', 'fileSizeCheckInterval', array(
			'label' 		=> 'Check file size every (seconds):',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
		));

		$fileDeletePolicies = new Kaltura_Form_Element_EnumSelect('fileDeletePolicy', array('enum' => 'Kaltura_Client_DropFolder_Enum_DropFolderFileDeletePolicy'));
		$fileDeletePolicies->setLabel('File Deletion Policy:');
		$fileDeletePolicies->setRequired(true);
		$fileDeletePolicies->setValue(Kaltura_Client_DropFolder_Enum_DropFolderFileDeletePolicy::AUTO_DELETE);
		$this->addElement($fileDeletePolicies);

		$this->addElement('text', 'autoFileDeleteDays', array(
			'label' 		=> 'Auto delete files after (days):',
			'required'		=> true,
			'value'			=> 0,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('checkbox', 'shouldValidateKS', array(
			'label'			=> 'Validate KS',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'div', 'class' => 'rememeber')))
		));
		
		// --------------------------------

		$extendTypeSubForm = KalturaPluginManager::loadObject('Form_DropFolderConfigureExtend_SubForm', $this->dropFolderType);
		if ($extendTypeSubForm) {
    		$this->addElement('hidden', 'crossLine4', array(
				'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		    ));
		    $extendTypeSubFormTitle = new Zend_Form_Element_Hidden(self::EXTENSION_SUBFORM_NAME.'_title');
    		$extendTypeSubFormTitle->setLabel($extendTypeSubForm->getTitle());
    		$extendTypeSubFormTitle->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
    		$this->addElement($extendTypeSubFormTitle);
    		$extendTypeSubForm->setDecorators(array(
    	        'FormElements',
            ));
		    $this->addSubForm($extendTypeSubForm, self::EXTENSION_SUBFORM_NAME);
		}

		//------------------------------------
	}



	public function populateFromObject($object, $add_underscore = true)
	{
		parent::populateFromObject($object, $add_underscore);

		$fileHandlerConfig = $this->getSubForm('fileHandlerConfig' . $object->fileHandlerType);
		/* @var $fileHandlerConfig Form_BaseFileHandlerConfig */
		$fileHandlerConfig->populateFromObject($object->fileHandlerConfig, $object, false);

		//add troubleshoot form only to existing object
		$troubleshootForm = new Form_TroubleshootConfig();
		$this->addSubForm($troubleshootForm, 'troubleshootConfig');
		$troubleshootForm->populateFromObject($object, false);

		$props = $object;
		if(is_object($object))
			$props = get_object_vars($object);

		$allElements = $this->getElements();
		foreach ($allElements as $element)
		{
			if ($element instanceof Kaltura_Form_Element_EnumSelect)
			{
				$elementName = $element->getName();
				if (isset($props[$elementName])) {
				    $element->setValue(array($props[$elementName]));
				}
			}
		}

		$this->setDefault('typeForView', $object->type);

		$extendTypeSubForm = $this->getSubForm(self::EXTENSION_SUBFORM_NAME);
		if ($extendTypeSubForm) {
		    $extendTypeSubForm->populateFromObject($object, $add_underscore);
		}
	}

	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		if (isset($properties[self::EXTENSION_SUBFORM_NAME])) {
		    $properties = array_merge($properties[self::EXTENSION_SUBFORM_NAME], $properties);
		}

	    $object = KalturaPluginManager::loadObject('Kaltura_Client_DropFolder_Type_DropFolder', $properties['type']);

		$fileHandlerType = $properties['fileHandlerType'];
		switch($fileHandlerType)
		{
			case Kaltura_Client_DropFolder_Enum_DropFolderFileHandlerType::CONTENT:
				$object->fileHandlerConfig = new Kaltura_Client_DropFolder_Type_DropFolderContentFileHandlerConfig();
				$handlerConfigForm = new Form_ContentFileHandlerConfig();
				break;

			case Kaltura_Client_DropFolder_Enum_DropFolderFileHandlerType::XML:
				$object->fileHandlerConfig = new Kaltura_Client_DropFolderXmlBulkUpload_Type_DropFolderXmlBulkUploadFileHandlerConfig();
				$handlerConfigForm = new Form_XmlFileHandlerConfig();
				break;
				
			default:
				$object->fileHandlerConfig = KalturaPluginManager::loadObject('Kaltura_Client_DropFolder_Type_DropFolderFileHandlerConfig', $fileHandlerType);
				$handlerConfigForm = KalturaPluginManager::loadObject('Form_BaseFileHandlerConfig', $fileHandlerType);
		}
	    $object = parent::loadObject($object, $properties, $add_underscore, $include_empty_fields);

		$extendTypeSubForm = $this->getSubForm(self::EXTENSION_SUBFORM_NAME);
		if ($extendTypeSubForm) {
		    $object =  $extendTypeSubForm->getObject($object, $objectType, $properties, $add_underscore, $include_empty_fields);
		}
		
		/* @var $handlerConfigForm Form_BaseFileHandlerConfig */
		if($handlerConfigForm)
			$handlerConfigForm->applyObjectAttributes($object);
		
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
				$conversionProfileFilter->typeEqual = Kaltura_Client_Enum_ConversionProfileType::MEDIA;

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

		if(!is_null($conversionProfiles) && count($conversionProfiles) && (count($conversionProfiles) == $conversionProfileList->totalCount))
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
