<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class Form_CatalogItemConfigure extends ConfigureForm
{
	protected $newPartnerId;
	protected $catalogItemType;
	protected $disableAttributes;
	protected $catalogItemServiceType;
	protected $catalogItemTurnAroundTime;

	public function __construct($partnerId, $type = null ,$catalogItemServiceType = null, $catalogItemTurnAroundTime = null,  $disableAttributes = null)
	{
		$this->newPartnerId = $partnerId;
		$this->catalogItemType = $type;
		$this->catalogItemServiceType = $catalogItemServiceType;
		$this->catalogItemTurnAroundTime = $catalogItemTurnAroundTime;
		$this->disableAttributes = $disableAttributes;

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
			'readonly'		=> 'true',
		));

		$this->addElement('text', 'vendorPartnerId', array(
			'label' 		=> 'Vendor Partner ID:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
			'placement' => 'prepend',
			'readonly'		=> $this->disableAttributes,
		));

		$this->addElement('text', 'name', array(
			'label' 		=> 'Name:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
			'placement' => 'prepend',
			'readonly'		=> $this->disableAttributes,
		));

		$this->addElement('text', 'systemName', array(
			'label' 		=> 'System Name:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
			'placement' => 'prepend',
			'readonly'		=> $this->disableAttributes,
		));

		$catalogItemForView = new Kaltura_Form_Element_EnumSelect('serviceFeature', array('enum' => 'Kaltura_Client_Reach_Enum_VendorServiceFeature'));
		$catalogItemForView->setLabel('Service Feature:');
		if ($this->catalogItemType)
			$catalogItemForView->setValue($this->catalogItemType);
		else
			$catalogItemForView->setValue(Kaltura_Client_Reach_Enum_VendorServiceFeature::CAPTIONS);
		$catalogItemForView->setAttrib('disabled','disabled');
		$this->addElement($catalogItemForView);

		$serviceTypeForView = new Kaltura_Form_Element_EnumSelect('serviceType', array('enum' => 'Kaltura_Client_Reach_Enum_VendorServiceType'));
		$serviceTypeForView->setLabel('Service Type:');
		$serviceTypeForView->setRequired(true);
		if ($this->catalogItemServiceType)
			$serviceTypeForView->setValue($this->catalogItemServiceType);
		else
			$serviceTypeForView->setValue(Kaltura_Client_Reach_Enum_VendorServiceType::HUMAN);

		$this->addElement($serviceTypeForView );

		$turnAroundTimeForView = new Kaltura_Form_Element_EnumSelect('turnAroundTime', array('enum' => 'Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime'));
		$turnAroundTimeForView->setLabel('Turn Around Time:');
		$turnAroundTimeForView->setRequired(true);
		if ($this->catalogItemTurnAroundTime)
			$turnAroundTimeForView->setValue($this->catalogItemTurnAroundTime);
		else
			$turnAroundTimeForView->setValue(Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime::BEST_EFFORT);
		$this->addElement($turnAroundTimeForView );

		$sourceLanguage = new Kaltura_Form_Element_EnumSelect('sourceLanguage', array('enum' => 'Kaltura_Client_Reach_Enum_CatalogItemLanguage'));
		$sourceLanguage->setLabel('Source Language:');
		$sourceLanguage->setRequired(true);
		$sourceLanguage->setValue(Kaltura_Client_Reach_Enum_CatalogItemLanguage::EN);
		$this->addElement($sourceLanguage );

		if ($this->catalogItemType == Kaltura_Client_Reach_Enum_VendorServiceFeature::TRANSLATION)
		{
			$targetLanguage = new Kaltura_Form_Element_EnumSelect('targetLanguage', array('enum' => 'Kaltura_Client_Reach_Enum_CatalogItemLanguage'));
			$targetLanguage->setLabel('Target Language:');
			$targetLanguage->setRequired(true);
			$targetLanguage->setValue(Kaltura_Client_Reach_Enum_CatalogItemLanguage::EN);
			$this->addElement($targetLanguage);
		}

		$outputFormat = new Kaltura_Form_Element_EnumSelect('outputFormat', array('enum' => 'Kaltura_Client_Reach_Enum_VendorCatalogItemOutputFormat'));
		$outputFormat->setLabel('Output Format:');
		$outputFormat->setRequired(true);
		$outputFormat->setValue(Kaltura_Client_Reach_Enum_VendorCatalogItemOutputFormat::SRT);
		$this->addElement($outputFormat );

		$enableSpeakerId = new Kaltura_Form_Element_EnumSelect('enableSpeakerId', array('enum' => 'Kaltura_Client_Enum_NullableBoolean', 	'excludes' => array (
			Kaltura_Client_Enum_NullableBoolean::NULL_VALUE)));
		$enableSpeakerId->setLabel('Enable Speaker ID:');
		$enableSpeakerId->setRequired(true);
		$enableSpeakerId->setValue(Kaltura_Client_Enum_NullableBoolean::FALSE_VALUE);
		$this->addElement($enableSpeakerId);

		$this->addLine("Pricing Line");
		$this->addTitle('Pricing:');

		$this->addElement('text', 'fixedPriceAddons', array(
			'label' 		=> 'Fixed Price Addons:',
			'filters' 		=> array('StringTrim'),
			'placement' => 'prepend',
			'readonly'		=> $this->disableAttributes,
		));

		$pricingSubForm = new Form_VendorCatalogItemPricing(array('DisableLoadDefaultDecorators' => true));
		if ($this->disableAttributes)
			$pricingSubForm->setAttrib('disabled',$this->disableAttributes);

		$this->addSubForm($pricingSubForm, "pricing");
		$this->getSubForm("pricing")->removeDecorator("DtDdWrapper");

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

		$this->setDefault('type', $object->serviceFeature);
		$this->getSubForm("pricing")->populateFromObject($object->pricing);
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
//		$catalogItem->OutputFormats = null;
//		$catalogItem->SourceLanguages = null;
//		$catalogItem->TargetLanguages = null;
	}

	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore,$include_empty_fields);
		
		if ($properties['type'] == Kaltura_Client_Reach_Enum_VendorServiceFeature::TRANSLATION)
		{
//			$languages = $properties['TargetLanguages'];
//			$languagesArray = array();
//			foreach (json_decode($languages) as $language)
//			{
//				$languageItem = new Kaltura_Client_Reach_Type_LanguageItem();
//				$languageItem->language = $language->language;
//				$languagesArray[] = $languageItem;
//			}
//			$object->targetLanguages = $languagesArray;
		}
		return $object;
	}
}
