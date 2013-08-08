<?php
/**
 * @package UI-infra
 * @subpackage forms
 */
class Infra_Form extends Zend_Form
{
    /**
     * @param array $options
     * @see Zend_Form::__construct()
     */
    public function __construct($options = null)
    {
    	parent::__construct($options);
        //$this->initKey();
    }

	/**
	 * Add hidden key field to the form, the key is validated against saved session.
	 * The key validation should prevent form submission from external sites.
	 */
	protected function initKey()
	{
		$this->addElementPrefixPath('Kaltura', APPLICATION_PATH . '/lib/Kaltura');

		$validator = new Infra_SecurityKey(get_class($this));
		$this->addElement('hidden', 'k', array(
			'decorators' => array('ViewHelper'),
			'required' => true,
			'value' => $validator->getKey(),
		));
		$kElement = $this->getElement('k');
		$kElement->setAutoInsertNotEmptyValidator(false);
		$kElement->addValidator($validator);
	}

	/**
	 * @param Kaltura_Client_ObjectBase $object
	 * @param boolean $add_underscore
	 */
	public function populateFromObject($object, $add_underscore = true)
	{
		$props = $object;
		if(is_object($object))
			$props = get_object_vars($object);

		foreach($props as $prop => $value)
		{
			if($add_underscore)
			{
				$pattern = '/(.)([A-Z])/';
				$replacement = '\1_\2';
				$prop = strtolower(preg_replace($pattern, $replacement, $prop));
			}
			$this->setDefault($prop, $value);
		}
	}

	/**
	 * @param string $objectType Kaltura client class name
	 * @param array $properties
	 * @param boolean $add_underscore
	 * @param boolean $include_empty_fields
	 * @return Kaltura_Client_ObjectBase
	 */
	public function loadObject($object, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		foreach($properties as $prop => $value)
		{
			if($add_underscore)
			{
				$parts = explode('_', strtolower($prop));
				$prop = '';
				foreach ($parts as $part)
					$prop .= ucfirst(trim($part));
				$prop[0] = strtolower($prop[0]);
			}

			if ($value !== '' || $include_empty_fields)
			{
				if(is_array($value) && isset($object->$prop) && $object->$prop)
				{
					foreach($value as $sub_prop => $sub_value)
						$object->$prop->$sub_prop = $sub_value;
				}
				else
				{
					$object->$prop = $value;
				}
			}
		}

		return $object;
	}

	/**
	 * @param string $objectType Kaltura client class name
	 * @param array $properties
	 * @param boolean $add_underscore
	 * @param boolean $include_empty_fields
	 * @return Kaltura_Client_ObjectBase
	 */
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = new $objectType;
		return $this->loadObject($object, $properties, $add_underscore, $include_empty_fields);
	}
	
	/**
	 * @var string $displayGroupName
	 * @var string $elementName
	 */
	public function addElementToDisplayGroup ($displayGroupName, $elementName)
	{
		$displayGroup = $this->_displayGroups[$displayGroupName];
		$element = $this->getElement($elementName);
		/* @var $displayGroup Zend_Form_DisplayGroup */
		$displayGroup->addElement($element);
		unset($this->_order[$elementName]);
		$this->_orderUpdated = true;
        return $this;
	}
}