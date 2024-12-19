<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class Form_CatalogItemConfigure extends ConfigureForm
{
	protected $catalogItemType;
	protected $disableAttributes;
	protected $catalogItemServiceType;
	protected $catalogItemTurnAroundTime;

	public function __construct($type = null, $catalogItemServiceType = null, $catalogItemTurnAroundTime = null, $disableAttributes = null)
	{
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
		$titleElement->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag', array('tag' => 'b'))));
		$this->addElement($titleElement);

		$this->addElement('text', 'id', array(
			'label' => 'ID:',
			'filters' => array('StringTrim'),
			'readonly' => true,
			'disabled' => 'disabled',
		));

		$this->addElement('text', 'vendorPartnerId', array(
			'label' => 'Vendor Partner ID:',
			'required' => true,
			'filters' => array('StringTrim'),
			'placement' => 'prepend',
			'readonly' => $this->disableAttributes,
		));

		$this->addElement('text', 'name', array(
			'label' => 'Name:',
			'required' => true,
			'filters' => array('StringTrim'),
			'placement' => 'prepend',
		));

		$this->addElement('text', 'systemName', array(
			'label' => 'System Name:',
			'required' => true,
			'filters' => array('StringTrim'),
			'placement' => 'prepend',
			'readonly' => $this->disableAttributes,
		));

		$this->addElement('text', 'createdBy', array(
			'label' => 'Created By:',
			'filters' => array('StringTrim'),
			'placement' => 'prepend',
		));

		$catalogItemForView = new Kaltura_Form_Element_EnumSelect('serviceFeature', array('enum' => 'Kaltura_Client_Reach_Enum_VendorServiceFeature'));
		$catalogItemForView->setLabel('Service Feature:');
		if ($this->catalogItemType)
			$catalogItemForView->setValue($this->catalogItemType);
		else
			$catalogItemForView->setValue(Kaltura_Client_Reach_Enum_VendorServiceFeature::CAPTIONS);
		$catalogItemForView->setAttrib('disabled', 'disabled');
		$this->addElement($catalogItemForView);

		$catalogItemForView = new Kaltura_Form_Element_EnumSelect('engineType', array('enum' => 'Kaltura_Client_Reach_Enum_ReachVendorEngineType'));
		$catalogItemForView->setLabel('Engine Type:');

		$this->addElement($catalogItemForView);

		$serviceTypeForView = new Kaltura_Form_Element_EnumSelect('serviceType', array('enum' => 'Kaltura_Client_Reach_Enum_VendorServiceType'));
		$serviceTypeForView->setLabel('Service Type:');
		$serviceTypeForView->setRequired(true);
		if ($this->catalogItemServiceType)
			$serviceTypeForView->setValue($this->catalogItemServiceType);
		else
			$serviceTypeForView->setValue(Kaltura_Client_Reach_Enum_VendorServiceType::HUMAN);
		$this->addElement($serviceTypeForView);

		$stage = new Kaltura_Form_Element_EnumSelect('stage', array('enum' => 'Kaltura_Client_Reach_Enum_VendorCatalogItemStage', 'label' => 'Stage:'));
		$this->addElement($stage);

		$turnAroundTimeForView = new Kaltura_Form_Element_EnumSelect('turnAroundTime', array('enum' => 'Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime'));
		$turnAroundTimeForView->setLabel('Turn Around Time:');
		$turnAroundTimeForView->setRequired(true);
		if ($this->catalogItemTurnAroundTime)
			$turnAroundTimeForView->setValue($this->catalogItemTurnAroundTime);
		else
			$turnAroundTimeForView->setValue(Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime::BEST_EFFORT);
		$this->addElement($turnAroundTimeForView);

		$sourceLanguage = new Kaltura_Form_Element_EnumSelect('sourceLanguage', array('enum' => 'Kaltura_Client_Reach_Enum_CatalogItemLanguage'));
		$sourceLanguage->setLabel('Source Language:');
		$sourceLanguage->setRequired(true);
		$sourceLanguage->setValue(Kaltura_Client_Reach_Enum_CatalogItemLanguage::EN);
		$this->addElement($sourceLanguage);
		
		if(in_array($this->catalogItemType, array(Kaltura_Client_Reach_Enum_VendorServiceFeature::TRANSLATION, Kaltura_Client_Reach_Enum_VendorServiceFeature::DUBBING, Kaltura_Client_Reach_Enum_VendorServiceFeature::LIVE_TRANSLATION)))
		{
			$targetLanguage = new Kaltura_Form_Element_EnumSelect('targetLanguage', array('enum' => 'Kaltura_Client_Reach_Enum_CatalogItemLanguage'));
			$targetLanguage->setLabel('Target Language:');
			$targetLanguage->setRequired(true);
			$targetLanguage->setValue(Kaltura_Client_Reach_Enum_CatalogItemLanguage::EN);
			$this->addElement($targetLanguage);
		}
		
		if ($this->catalogItemType == Kaltura_Client_Reach_Enum_VendorServiceFeature::TRANSLATION)
		{
			$requireSource = new Kaltura_Form_Element_EnumSelect('requireSource', array('enum' => 'Kaltura_Client_Enum_NullableBoolean', 'excludes' => array(Kaltura_Client_Enum_NullableBoolean::NULL_VALUE)));
			$requireSource->setLabel('Require Source Language Captions:');
			$requireSource->setRequired(true);
			$requireSource->setValue(Kaltura_Client_Enum_NullableBoolean::TRUE_VALUE);
			$this->addElement($requireSource);
		}

		if ($this->catalogItemType == Kaltura_Client_Reach_Enum_VendorServiceFeature::VIDEO_ANALYSIS)
		{
			$videoAnalysisType = new Kaltura_Form_Element_EnumSelect('videoAnalysisType', array('enum' => 'Kaltura_Client_Reach_Enum_VendorVideoAnalysisType'));
			$videoAnalysisType->setLabel('Analysis Type:');
			$videoAnalysisType->setRequired(true);
			$videoAnalysisType->setValue(Kaltura_Client_Reach_Enum_VendorVideoAnalysisType::OCR);
			$this->addElement($videoAnalysisType);
		}
		
		$audioCatalogItemTypesArray = array(Kaltura_Client_Reach_Enum_VendorServiceFeature::AUDIO_DESCRIPTION,
											Kaltura_Client_Reach_Enum_VendorServiceFeature::EXTENDED_AUDIO_DESCRIPTION,
											Kaltura_Client_Reach_Enum_VendorServiceFeature::DUBBING);
		if (in_array($this->catalogItemType, $audioCatalogItemTypesArray))
		{
			$this->addElement('text', 'flavorParamsId', array(
				'label' => 'Flavor Params ID:',
				'filters' => array('StringTrim'),
				'placement' => 'prepend',
				'required' 		=> true,
			));
			
			$this->addElement('text', 'clearAudioFlavorParamsId', array(
				'label' => 'Clear Audio Flavor Params ID:',
				'filters' => array('StringTrim'),
				'placement' => 'prepend',
				'required' 		=> true,
			));
		}
		
		if (($this->catalogItemType != Kaltura_Client_Reach_Enum_VendorServiceFeature::AUDIO_DESCRIPTION) &&
			($this->catalogItemType != Kaltura_Client_Reach_Enum_VendorServiceFeature::DUBBING))
		{
			$outputFormat = new Kaltura_Form_Element_EnumSelect('outputFormat', array('enum' => 'Kaltura_Client_Reach_Enum_VendorCatalogItemOutputFormat'), array( null => "PartnerDefault"));
			$outputFormat->setLabel('Output Format:');
			$outputFormat->setValue(null);
			$this->addElement($outputFormat);
			
			$enableSpeakerId = new Kaltura_Form_Element_EnumSelect('enableSpeakerId', array('enum' => 'Kaltura_Client_Enum_NullableBoolean', 'excludes' => array(
				Kaltura_Client_Enum_NullableBoolean::NULL_VALUE)));
			$enableSpeakerId->setLabel('Enable Speaker ID:');
			$enableSpeakerId->setRequired(true);
			$enableSpeakerId->setValue(Kaltura_Client_Enum_NullableBoolean::FALSE_VALUE);
			$this->addElement($enableSpeakerId);
		}
		
		if ($this->catalogItemType == Kaltura_Client_Reach_Enum_VendorServiceFeature::EXTENDED_AUDIO_DESCRIPTION)
		{
			$outputFormat = new Kaltura_Form_Element_EnumSelect('outputFormat',
			                                                    array('enum' => 'Kaltura_Client_Reach_Enum_VendorCatalogItemOutputFormat'),
			                                                    array(null => "PartnerDefault"));
			$outputFormat->setLabel('Output Format:');
			$outputFormat->setValue(VendorCatalogItemOutputFormat::VTT);
			$this->addElement($outputFormat);
		}

		$allowResubmission = new Kaltura_Form_Element_EnumSelect('allowResubmission', array('enum' => 'Kaltura_Client_Enum_NullableBoolean', 'excludes' => array(
			Kaltura_Client_Enum_NullableBoolean::NULL_VALUE)));
		$allowResubmission->setLabel('Allow Resubmission:');
		$allowResubmission->setRequired(true);
		$allowResubmission->setValue(Kaltura_Client_Enum_NullableBoolean::FALSE_VALUE);
		$this->addElement($allowResubmission);

		$this->addElement('text', 'contract', array(
			'label' => 'Contract:',
			'filters' => array('StringTrim'),
			'placement' => 'prepend',
		));

		$this->addElement('text', 'notes', array(
			'label' => 'Notes:',
			'filters' => array('StringTrim'),
			'placement' => 'prepend',
		));

		$liveCatalogItemTypesArray = array(Kaltura_Client_Reach_Enum_VendorServiceFeature::LIVE_CAPTION, Kaltura_Client_Reach_Enum_VendorServiceFeature::LIVE_TRANSLATION);
		if (in_array($this->catalogItemType, $liveCatalogItemTypesArray))
		{
			$this->addElement('text', 'minimalRefundTime', array(
				'label' => 'Minimal Refund Time:',
				'filters' => array('StringTrim'),
				'placement' => 'prepend',
				'readonly' => $this->disableAttributes,
			));

			$this->addElement('text', 'durationLimit', array(
				'label' => 'Duration Limit:',
				'filters' => array('StringTrim'),
				'placement' => 'prepend',
				'readonly' => $this->disableAttributes,
			));

			$this->addElement('text', 'minimalOrderTime', array(
				'label' => 'Minimal Order Time:',
				'filters' => array('StringTrim'),
				'placement' => 'prepend',
				'readonly' => $this->disableAttributes,
			));
		}

		if ($this->catalogItemType === Kaltura_Client_Reach_Enum_VendorServiceFeature::VIDEO_ANALYSIS)
		{
			$this->addElement('text', 'maxVideoDuration', array(
				'label' => 'Max Video Duration Limit (sec):',
				'filters' => array('StringTrim'),
				'placement' => 'prepend',
			));
		}

		$this->addLine("Pricing Line");
		$this->addTitle('Pricing:');

		$this->addElement('text', 'fixedPriceAddons', array(
			'label' => 'Fixed Price Addons:',
			'filters' => array('StringTrim'),
			'placement' => 'prepend',
			'readonly' => $this->disableAttributes,
		));

		$pricingSubForm = new Form_VendorCatalogItemPricing(array('DisableLoadDefaultDecorators' => true));
		if ($this->disableAttributes)
			$pricingSubForm->setAttrib('disabled', $this->disableAttributes);

		$this->addSubForm($pricingSubForm, "pricing");
		$this->getSubForm("pricing")->removeDecorator("DtDdWrapper");

		$this->addElement('hidden', 'type', array(
			'filters' => array('StringTrim'),
			'decorators' => array('ViewHelper'),
			'value' => $this->catalogItemType,
		));
	}

	public function populateFromObject($object, $add_underscore = true)
	{
		parent::populateFromObject($object, $add_underscore);

		$props = $object;
		if (is_object($object))
			$props = get_object_vars($object);

		$allElements = $this->getElements();
		foreach ($allElements as $element)
		{
			if ($element instanceof Kaltura_Form_Element_EnumSelect)
			{
				$elementName = $element->getName();
				if (isset($props[$elementName]))
				{
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
		
		if(isset($catalogItem->outputFormat) && $catalogItem->outputFormat == "")
			$catalogItem->outputFormat = null;
	}

	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);

		return $object;
	}
}
