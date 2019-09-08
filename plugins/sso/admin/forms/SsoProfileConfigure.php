<?php
/**
 * @package plugins.sso
 * @subpackage Admin
 */
class Form_SsoProfileConfigure extends ConfigureForm
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
		$this->setAttrib('id', 'frmSsoProfileConfigure');
		$this->setMethod('post');

		$this->addElement('text', 'id', array(
			'label' => 'ID:',
			'filters' => array('StringTrim'),
			'readonly' => true,
			'disabled' => 'disabled',
		));

		$this->addElement('text', 'partnerId', array(
			'label' => 'Related Publisher ID:',
			'filters' => array('StringTrim'),
			'placement' => 'prepend',
			'readonly' => 'true',
		));

		$this->addElement('text', 'applicationType', array(
			'label' => 'Application type:',
			'required' => true,
			'filters' => array('StringTrim'),
			'placement' => 'prepend',
		));

		$this->addElement('text', 'domain', array(
			'label' => 'Domain:',
			'required' => true,
			'filters' => array('StringTrim'),
			'placement' => 'prepend',
		));

		$this->addElement('text', 'redirectUrl', array(
			'label' => 'Redirect Url:',
			'required' => true,
			'filters' => array('StringTrim'),
			'placement' => 'prepend',
		));

		$element3 = new Infra_Form_Html ('place_holder3', array('content' => '<span/>'));
		$this->addElement($element3);

	}

	public function populateFromObject($object, $add_underscore = true)
	{
		parent::populateFromObject($object, $add_underscore);

		$props = $object;
		if (is_object($object))
			$props = get_object_vars($object);

		if (isset($props['partnerId']) && $props['partnerId'] == 0)
		{
			$this->getElement('partnerId')->setValue(null);
		}

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
	}

	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
		if ($object->partnerId == '')
			$object->partnerId = null;
		return $object;
	}
}
