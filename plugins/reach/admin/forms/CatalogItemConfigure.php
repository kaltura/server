<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class Form_CatalogItemConfigure extends ConfigureForm
{
	protected $newPartnerId;
	protected $catalogItemType;

	public function __construct($partnerId, $type)
	{
		$this->newPartnerId = $partnerId;
		$this->catalogItemType = $type;

		parent::__construct();
	}

	public function init()
	{
		$this->setAttrib('id', 'frmCatalogItemConfigure');
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

		$this->addElement('text', 'vendorPartnerId', array(
			'label' 		=> 'Vendor Partner ID:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
			'placement' => 'prepend',
		));

		$this->addElement('text', 'name', array(
			'label' 		=> 'Name:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
			'placement' => 'prepend',
		));

		$this->addElement('text', 'systemName', array(
			'label' 		=> 'System Name:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
			'placement' => 'prepend',
		));

		$catalogItemForView = new Kaltura_Form_Element_EnumSelect('serviceFeature', array('enum' => 'Kaltura_Client_Reach_Enum_VendorServiceFeature'));
		$catalogItemForView->setLabel('Service Feature:');
		$catalogItemForView->setValue($this->catalogItemType);
		$catalogItemForView->setAttrib('disabled','disabled');
		$this->addElement($catalogItemForView);

		$isDefault = new Kaltura_Form_Element_EnumSelect('isDefault', array('enum' => 'Kaltura_Client_Enum_NullableBoolean', 	'excludes' => array (
			Kaltura_Client_Enum_NullableBoolean::NULL_VALUE)));
		$isDefault->setLabel('Is Default:');
		$isDefault->setRequired(true);
		$isDefault->setValue(Kaltura_Client_Enum_NullableBoolean::FALSE_VALUE);
		$this->addElement($isDefault);

		$serviceTypeForView = new Kaltura_Form_Element_EnumSelect('serviceType', array('enum' => 'Kaltura_Client_Reach_Enum_VendorServiceType'));
		$serviceTypeForView->setLabel('Service Type:');
		$serviceTypeForView->setRequired(true);
		$serviceTypeForView->setValue(Kaltura_Client_Reach_Enum_VendorServiceType::HUMAN);
		$this->addElement($serviceTypeForView );

		$turnAroundTimeForView = new Kaltura_Form_Element_EnumSelect('turnAroundTime', array('enum' => 'Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime'));
		$turnAroundTimeForView->setLabel('Turn Around Time:');
		$turnAroundTimeForView->setRequired(true);
		$turnAroundTimeForView->setValue(Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime::BEST_EFFORT);
		$this->addElement($turnAroundTimeForView );
		
		$this->addLine("Pricing Line");
		$this->addTitle('Pricing:');

		$pricingSubForm = new Form_VendorCatalogItemPricing(array('DisableLoadDefaultDecorators' => true));
		$this->addSubForm($pricingSubForm, "pricing");
		$this->getSubForm("pricing")->removeDecorator("DtDdWrapper");

		$this->addLine("Languages Line");
		$this->addTitle('Source Languages:');
		$sourceLanguagesSubForm = new Zend_Form_SubForm(array('DisableLoadDefaultDecorators' => true));
		$sourceLanguagesSubForm->addDecorator('ViewScript', array(
			'viewScript' => 'source-languages-sub-form.phtml',
		));
		$this->addSubForm($sourceLanguagesSubForm, 'SourceLanguages_');
		$innerSourceLanguagesSubForm = new Form_SourceLanguagesSubForm('Kaltura_Client_Reach_Type_LanguageItem');
		$this->addSubForm($innerSourceLanguagesSubForm , "SourceLanguageTemplate");

		if ($this->catalogItemType == Kaltura_Client_Reach_Enum_VendorServiceFeature::TRANSLATION)
		{
			$this->addTitle('Target Languages:');
			$targetLanguagesSubForm = new Zend_Form_SubForm(array('DisableLoadDefaultDecorators' => true));
			$targetLanguagesSubForm->addDecorator('ViewScript', array(
				'viewScript' => 'target-languages-sub-form.phtml',
			));
			$this->addSubForm($targetLanguagesSubForm, 'TargetLanguages_');
			$innerTargetLanguagesSubForm = new Form_TargetLanguagesSubForm('Kaltura_Client_Reach_Type_LanguageItem');
			$this->addSubForm($innerTargetLanguagesSubForm , "TargetLanguageTemplate");
		}

		$this->addLine("OutputFormatsLine");
		$this->addTitle('Output Formats:');
		$outputFormatsSubForm = new Zend_Form_SubForm(array('DisableLoadDefaultDecorators' => true));
		$outputFormatsSubForm->addDecorator('ViewScript', array(
			'viewScript' => 'output-formats-sub-form.phtml',
		));
		$this->addSubForm($outputFormatsSubForm, 'OutputFormats_');
		$innerOutputFormatsSubForm = new Form_OutputFormatsSubForm('Kaltura_Client_Reach_Type_OutputFormatItem');
		$this->addSubForm($innerOutputFormatsSubForm  , "OutputFormatTemplate");

		$enableSpeakerId = new Kaltura_Form_Element_EnumSelect('enableSpeakerId', array('enum' => 'Kaltura_Client_Enum_NullableBoolean', 	'excludes' => array (
			Kaltura_Client_Enum_NullableBoolean::NULL_VALUE)));
		$enableSpeakerId->setLabel('Enable Speaker ID:');
		$enableSpeakerId->setRequired(true);
		$enableSpeakerId->setValue(Kaltura_Client_Enum_NullableBoolean::FALSE_VALUE);
		$this->addElement($enableSpeakerId);

		$this->addElement('text', 'fixedPriceAddons', array(
			'label' 		=> 'Fixed Price Addons:',
			'filters' 		=> array('StringTrim'),
			'placement' => 'prepend',
		));

		$this->addElement('hidden', 'type', array(
			'filters' 		=> array('StringTrim'),
			'decorators'    => array('ViewHelper'),
			'value'			=> $this->catalogItemType,
		));


	}

	public function populateFromObject($object, $add_underscore = true)
	{
		parent::populateFromObject($object, $add_underscore);

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

		$this->setDefault('serviceFeature', $object->serviceFeature);
		$this->populateLanguages($object);
		$this->populateOutputFormats($object);
		$this->getSubForm("pricing")->populateFromObject($object->pricing);
	}

	private function populateLanguages($object)
	{
		$sourceLanguages = array();
		foreach ($object->sourceLanguages as $sourceLanguage)
		{
			$newLanguage = array();
			$newLanguage['language'] = $sourceLanguage->language;
			$sourceLanguages[] = $newLanguage;
		}
		$this->setDefault('SourceLanguages',  json_encode($sourceLanguages));

		$targetLanguages = array();
		foreach ($object->targetLanguages as $targetLanguage)
		{
			$newLanguage = array();
			$newLanguage['language'] = $targetLanguage->language;
			$targetLanguages[] = $newLanguage;
		}
		$this->setDefault('TargetLanguages',  json_encode($targetLanguages));
	}

	private function populateOutputFormats($object)
	{
		$outputFormats = array();
		foreach ($object->outputFormats as $outputFormat)
		{
			$newOutputFormat = array();
			$newOutputFormat['outputFormat'] = $outputFormat->outputFormat;
			$outputFormats[] = $newOutputFormat ;
		}
		$this->setDefault('OutputFormats',  json_encode($outputFormats));
	}


	/**
	 * Set to null all the attributes that shouldn't be updated
	 * @param Kaltura_Client_Reach_Type_VendorCatalogItem $catalogItem
	 */
	public function resetUnUpdatebleAttributes(Kaltura_Client_Reach_Type_VendorCatalogItem $catalogItem)
	{
		// reset readonly attributes
		$catalogItem->id = null;
		$catalogItem->partnerId = null;
		$catalogItem->createdAt = null;
		$catalogItem->updatedAt = null;
		$catalogItem->OutputFormats = null;
		$catalogItem->SourceLanguages = null;
		$catalogItem->TargetLanguages = null;
	}

	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore,$include_empty_fields);
		
		$languages = $properties['SourceLanguages'];
		$languagesArray = array();
		foreach (json_decode($languages) as $language)
		{
			$languageItem = new Kaltura_Client_Reach_Type_LanguageItem();
			$languageItem->language = $language->language;
			$languagesArray[] = $languageItem;
		}
		$object->sourceLanguages = $languagesArray;

		if ($properties['type'] == Kaltura_Client_Reach_Enum_VendorServiceFeature::TRANSLATION)
		{
			$languages = $properties['TargetLanguages'];
			$languagesArray = array();
			foreach (json_decode($languages) as $language)
			{
				$languageItem = new Kaltura_Client_Reach_Type_LanguageItem();
				$languageItem->language = $language->language;
				$languagesArray[] = $languageItem;
			}
			$object->targetLanguages = $languagesArray;
		}
		
		$outputFormats = $properties['OutputFormats'];
		$outputFormatsArray = array();
		foreach (json_decode($outputFormats) as $outputFormat)
		{
			$outputFormatItem = new Kaltura_Client_Reach_Type_OutputFormatItem();
			$outputFormatItem->outputFormat = $outputFormat->outputFormat;
			$outputFormatsArray[] = $outputFormatItem;
		}
		$object->outputFormats = $outputFormatsArray;

		return $object;
	}
}
