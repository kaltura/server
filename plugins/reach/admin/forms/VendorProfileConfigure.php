<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class Form_VendorProfileConfigure extends ConfigureForm
{
	protected $newPartnerId;
	protected $disableAttributes;

	public function __construct($partnerId, $disableAttributes = null)
	{
		$this->newPartnerId = $partnerId;
		$this->disableAttributes = $disableAttributes;

		parent::__construct();
	}

	public function init()
	{
		$this->setAttrib('id', 'frmVendorProfileConfigure');
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

		$profileType = new Kaltura_Form_Element_EnumSelect('profileType', array('enum' => 'Kaltura_Client_Reach_Enum_VendorProfileType'));
		$profileType->setLabel('Profile Type:');
		$profileType->setValue(Kaltura_Client_Reach_Enum_VendorProfileType::FREE_TRIAL);
		$this->addElement($profileType);

		$defaultSourceLanguageView = new Kaltura_Form_Element_EnumSelect('defaultSourceLanguage', array('enum' => 'Kaltura_Client_Enum_Language'));
		$defaultSourceLanguageView->setLabel('Default Source Language:');
		$defaultSourceLanguageView->setValue(Kaltura_Client_Enum_Language::AA);
		$this->addElement($defaultSourceLanguageView);

		$defaultOutputFormatView = new Kaltura_Form_Element_EnumSelect('defaultOutputFormat', array('enum' => 'Kaltura_Client_Reach_Enum_VendorCatalogItemOutputFormat'));
		$defaultOutputFormatView->setLabel('Default Output Format:');
		$defaultOutputFormatView->setValue(Kaltura_Client_Reach_Enum_VendorCatalogItemOutputFormat::SRT);
		$this->addElement($defaultOutputFormatView);

		$enableMachineModeration = new Kaltura_Form_Element_EnumSelect('enableMachineModeration', array('enum' => 'Kaltura_Client_Enum_NullableBoolean', 	'excludes' => array (
			Kaltura_Client_Enum_NullableBoolean::NULL_VALUE)));
		$enableMachineModeration->setLabel('Enable Machine Moderation:');
		$enableMachineModeration->setRequired(true);
		$enableMachineModeration->setValue(Kaltura_Client_Enum_NullableBoolean::FALSE_VALUE);
		$this->addElement($enableMachineModeration);

		$enableHumanModeration = new Kaltura_Form_Element_EnumSelect('enableHumanModeration', array('enum' => 'Kaltura_Client_Enum_NullableBoolean', 	'excludes' => array (
			Kaltura_Client_Enum_NullableBoolean::NULL_VALUE)));
		$enableHumanModeration->setLabel('Enable Human Moderation:');
		$enableHumanModeration->setRequired(true);
		$enableHumanModeration->setValue(Kaltura_Client_Enum_NullableBoolean::FALSE_VALUE);
		$this->addElement($enableHumanModeration);

		$autoDisplayMachineCaptionsOnPlayer = new Kaltura_Form_Element_EnumSelect('autoDisplayMachineCaptionsOnPlayer', array('enum' => 'Kaltura_Client_Enum_NullableBoolean', 	'excludes' => array (
			Kaltura_Client_Enum_NullableBoolean::NULL_VALUE)));
		$autoDisplayMachineCaptionsOnPlayer ->setLabel('Auto Display Machine Captions On Player:');
		$autoDisplayMachineCaptionsOnPlayer ->setRequired(true);
		$autoDisplayMachineCaptionsOnPlayer ->setValue(Kaltura_Client_Enum_NullableBoolean::FALSE_VALUE);
		$this->addElement($autoDisplayMachineCaptionsOnPlayer );

		$autoDisplayHumanCaptionsOnPlayer = new Kaltura_Form_Element_EnumSelect('autoDisplayHumanCaptionsOnPlayer', array('enum' => 'Kaltura_Client_Enum_NullableBoolean', 	'excludes' => array (
			Kaltura_Client_Enum_NullableBoolean::NULL_VALUE)));
		$autoDisplayHumanCaptionsOnPlayer ->setLabel('Auto Display Human Captions On Player:');
		$autoDisplayHumanCaptionsOnPlayer ->setRequired(true);
		$autoDisplayHumanCaptionsOnPlayer ->setValue(Kaltura_Client_Enum_NullableBoolean::FALSE_VALUE);
		$this->addElement($autoDisplayHumanCaptionsOnPlayer );

		$enableMetadataExtraction = new Kaltura_Form_Element_EnumSelect('enableMetadataExtraction', array('enum' => 'Kaltura_Client_Enum_NullableBoolean', 	'excludes' => array (
			Kaltura_Client_Enum_NullableBoolean::NULL_VALUE)));
		$enableMetadataExtraction->setLabel('Enable Metadata Extraction:');
		$enableMetadataExtraction->setRequired(true);
		$enableMetadataExtraction->setValue(Kaltura_Client_Enum_NullableBoolean::FALSE_VALUE);
		$this->addElement($enableMetadataExtraction);

		$enableSpeakerChangeIndication = new Kaltura_Form_Element_EnumSelect('enableSpeakerChangeIndication', array('enum' => 'Kaltura_Client_Enum_NullableBoolean', 	'excludes' => array (
			Kaltura_Client_Enum_NullableBoolean::NULL_VALUE)));
		$enableSpeakerChangeIndication->setLabel('Enable Speaker Change Indication:');
		$enableSpeakerChangeIndication->setRequired(true);
		$enableSpeakerChangeIndication->setValue(Kaltura_Client_Enum_NullableBoolean::FALSE_VALUE);
		$this->addElement($enableSpeakerChangeIndication);

		$enableAudioTags = new Kaltura_Form_Element_EnumSelect('enableAudioTags', array('enum' => 'Kaltura_Client_Enum_NullableBoolean', 	'excludes' => array (
			Kaltura_Client_Enum_NullableBoolean::NULL_VALUE)));
		$enableAudioTags->setLabel('Enable Audion Tags:');
		$enableAudioTags->setRequired(true);
		$enableAudioTags->setValue(Kaltura_Client_Enum_NullableBoolean::FALSE_VALUE);
		$this->addElement($enableAudioTags);

		$enableProfanityRemoval = new Kaltura_Form_Element_EnumSelect('enableProfanityRemoval', array('enum' => 'Kaltura_Client_Enum_NullableBoolean', 	'excludes' => array (
			Kaltura_Client_Enum_NullableBoolean::NULL_VALUE)));
		$enableProfanityRemoval->setLabel('Enable Profanity Removal:');
		$enableProfanityRemoval->setRequired(true);
		$enableProfanityRemoval->setValue(Kaltura_Client_Enum_NullableBoolean::FALSE_VALUE);
		$this->addElement($enableProfanityRemoval);

		$this->addElement('text', 'maxCharactersPerCaptionLine', array(
			'label' 		=> 'Max Characters Per Caption Line:',
			'filters'		=> array('StringTrim'),
			'placement' => 'prepend',
		));

		$this->addRulesSection();
		$this->addRulesTemplate();
	}

	public static $rulesMap = array("Kaltura_Client_Reach_Type_VendorProfileRuleEntryAdded" => "Entry_Created",
		"Kaltura_Client_Reach_Type_VendorProfileRuleCategoryEntryAdded" => "Category_Entry_Added");

	private function addRulesSection()
	{
		$this->addLine("3");
		$this->addTitle('Vendor Profile Rules:');

		$options = self::$rulesMap;
		$this->addSelectElement("ruleType", $options);

		$ruleSubForm = new Zend_Form_SubForm(array('DisableLoadDefaultDecorators' => true));
		$ruleSubForm->addDecorator('ViewScript', array(
			'viewScript' => 'rules-sub-form.phtml',
		));

		$this->addSubForm($ruleSubForm, 'VendorProfileRules_');
	}
	private function addRulesTemplate()
	{
		foreach(self::$rulesMap as $name => $class) {
			$ruleSubForm = new Form_RulesSubForm($name);
			$this->addSubForm($ruleSubForm, "vendorProfileRuleTemplate_" . $class);
		}
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

		$this->populateRules($object);
	}

	private function populateRules($object)
	{
		$rules = array();
		foreach ($object->rules as $rule)
		{
			$rules[] = $rule;
		}
		$this->setDefault('VendorProfileRules',  json_encode($rules));
	}

	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore,$include_empty_fields);

		$rules = $properties['VendorProfileRules'];
		$rulesArray = array();
		foreach (json_decode($rules) as $rule)
		{
			$class = array_search ($rule->ruleType, self::$rulesMap);
			/* @var $ruleObject Kaltura_Client_Reach_Type_VendorProfileRuleOption  */
//			$ruleObject = new $class();
//			$ruleItem = $ruleObject->getVendorProfileRule($rule);
			$a = new Kaltura_Client_Reach_Type_VendorProfileRule();
			$rulesArray[] = $a;
		}
		$object->rules = $rulesArray;

		return $object;
	}

	/**
	 * Set to null all the attributes that shouldn't be updated
	 * @param Kaltura_Client_Reach_Type_VendorProfile $vendorProfile
	 */
	public function resetUnUpdatebleAttributes(Kaltura_Client_Reach_Type_VendorProfile $vendorProfile)
	{
		// reset readonly attributes
		$vendorProfile->id = null;
		$vendorProfile->partnerId = null;
		$vendorProfile->createdAt = null;
		$vendorProfile->updatedAt = null;
		$vendorProfile->VendorProfileRules = null;
	}
}
