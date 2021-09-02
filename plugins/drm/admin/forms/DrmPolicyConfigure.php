<?php
/**
 * @package plugins.drm
 * @subpackage Admin
 */
class Form_DrmPolicyConfigure extends Infra_Form
{
	protected $newPartnerId;
	protected $drmProvider;

	const EXTENSION_SUBFORM_NAME = 'extensionSubForm';

	public function __construct($partnerId, $type)
	{
		$this->newPartnerId = $partnerId;
		$this->drmProvider = $type;

		parent::__construct();
	}


	public function init()
	{
		$this->setAttrib('id', 'frmDrmPolicyConfigure');
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
			'label' 		=> 'Name:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
			'placement' => 'prepend',
		));

		$this->addElement('text', 'systemName', array(
			'label' 		=> 'System Name:',
			'filters'		=> array('StringTrim'),
			'placement' => 'prepend',
		));

		$this->addElement('text', 'description', array(
			'label' 		=> 'Description:',
			'required'		=> false,
			'filters'		=> array('StringTrim'),
		));

		$providerForView = new Kaltura_Form_Element_EnumSelect('typeForView', array('enum' => 'Kaltura_Client_Drm_Enum_DrmProviderType'));
		$providerForView->setLabel('Provider:');
		$providerForView->setAttrib('readonly', true);
		$providerForView->setAttrib('disabled', 'disabled');
		$providerForView->setValue($this->drmProvider);
		$this->addElement($providerForView);

		$this->addElement('hidden', 'provider', array(
			'filters' 		=> array('StringTrim'),
			'decorators'    => array('ViewHelper'),
		    'value'			=> $this->drmProvider,
		));

		$this->addElement('hidden', 'crossLine1', array(
				'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		    ));

		$titleElement = new Zend_Form_Element_Hidden('detailsTitle');
		$titleElement->setLabel('Details');
		$titleElement->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElement($titleElement);

		$enumElement = new Kaltura_Form_Element_EnumSelect('scenario', array('enum' => 'Kaltura_Client_Drm_Enum_DrmLicenseScenario'));
		$enumElement->setLabel('License Scenario:');
		$this->addElements(array($enumElement));

		$enumElement = new Kaltura_Form_Element_EnumSelect('licenseType', array('enum' => 'Kaltura_Client_Drm_Enum_DrmLicenseType'));
		$enumElement->setLabel('License Type:');
		$this->addElements(array($enumElement));

		$enumElement = new Kaltura_Form_Element_EnumSelect('licenseExpirationPolicy', array('enum' => 'Kaltura_Client_Drm_Enum_DrmLicenseExpirationPolicy'));
		$enumElement->setLabel('license Expiration Policy:');
		$this->addElements(array($enumElement));

		$this->addElement('text', 'duration', array(
			'label'     => 'License Duration (in days)',
			'required'  => false,
			'value'     => 1,
			'filters'   => array('StringTrim'),
		));

		// --------------------------------

		$extendTypeSubForm = KalturaPluginManager::loadObject('Form_DrmPolicyConfigureExtend_SubForm', $this->drmProvider);
		if ($extendTypeSubForm)
		{
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

		$extendTypeSubForm = $this->getSubForm(self::EXTENSION_SUBFORM_NAME);
		if ($extendTypeSubForm) {
		    $extendTypeSubForm->populateFromObject($object, $add_underscore);
		}
	}

	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		if (isset($properties[self::EXTENSION_SUBFORM_NAME]))
		{
		    $properties = array_merge($properties[self::EXTENSION_SUBFORM_NAME], $properties);
		}

	    $object = KalturaPluginManager::loadObject('Kaltura_Client_Drm_Type_DrmPolicy', $properties['provider']);

	    $object = parent::loadObject($object, $properties, $add_underscore, $include_empty_fields);

		$extendTypeSubForm = $this->getSubForm(self::EXTENSION_SUBFORM_NAME);
		if ($extendTypeSubForm)
		{
		    $object =  $extendTypeSubForm->getObject($object, $objectType, $properties, $add_underscore, $include_empty_fields);
		}
		return $object;
	}
}