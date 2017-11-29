<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class Form_CatalogItemConfigure extends Infra_Form
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

		$catalogItemForView = new Kaltura_Form_Element_EnumSelect('catalogItemTypeForView', array('enum' => 'Kaltura_Client_Reach_Enum_VendorCatalogItemType'));
		$catalogItemForView->setLabel('Type:');
		$catalogItemForView->setValue($this->catalogItemType);
		$this->addElement($catalogItemForView);

		$isDefault = new Kaltura_Form_Element_EnumSelect('isDefault', array('enum' => 'Kaltura_Client_Enum_NullableBoolean', 	'excludes' => array (
			Kaltura_Client_Enum_NullableBoolean::NULL_VALUE)));
		$isDefault->setLabel('Is Default:');
		$isDefault->setRequired(true);
		$isDefault->setValue(Kaltura_Client_Enum_NullableBoolean::FALSE_VALUE);
		$this->addElement($isDefault);

		$serviceTypeForView = new Kaltura_Form_Element_EnumSelect('serviceType', array('enum' => 'Kaltura_Client_Reach_Enum_VendorServiceType'));
		$serviceTypeForView ->setLabel('Service Type:');
		$serviceTypeForView ->setRequired(true);
		$serviceTypeForView ->setValue(Kaltura_Client_Reach_Enum_VendorServiceType::HUMAN);
		$this->addElement($serviceTypeForView );

		$turnAroundTimeForView = new Kaltura_Form_Element_EnumSelect('turnAroundTime', array('enum' => 'Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime'));
		$turnAroundTimeForView ->setLabel('Turn Around Time:');
		$turnAroundTimeForView ->setRequired(true);
		$turnAroundTimeForView ->setValue(Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime::BEST_EFFORT);
		$this->addElement($turnAroundTimeForView );

		$sourceLanguage = new Kaltura_Form_Element_EnumSelect('sourceLanguage', array('enum' => 'Kaltura_Client_Enum_Language'));
		$sourceLanguage  ->setLabel('Source Language:');
		$sourceLanguage  ->setRequired(true);
		$this->addElement($sourceLanguage  );

		if ($this->catalogItemType == Kaltura_Client_Reach_Enum_VendorCatalogItemType::TRANSLATION)
		{
			$targetLanguage = new Kaltura_Form_Element_EnumSelect('targetLanguage', array('enum' => 'Kaltura_Client_Enum_Language'));
			$targetLanguage->setLabel('Target Language:');
			$targetLanguage->setRequired(true);
			$this->addElement($targetLanguage);
		}

		$outputFormat = new Kaltura_Form_Element_EnumSelect('outputFormat', array('enum' => 'Kaltura_Client_Reach_Enum_VendorCatalogItemOutputFormat'));
		$outputFormat ->setLabel('Output Format:');
		$outputFormat ->setRequired(true);
		$this->addElement($outputFormat );

		$enableSpeakerId = new Kaltura_Form_Element_EnumSelect('enableSpeakerId', array('enum' => 'Kaltura_Client_Enum_NullableBoolean', 	'excludes' => array (
			Kaltura_Client_Enum_NullableBoolean::NULL_VALUE)));
		$enableSpeakerId ->setLabel('Enable Speaker ID:');
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

		$this->setDefault('catalogItemTypeForView', $object->type);
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
	}
}