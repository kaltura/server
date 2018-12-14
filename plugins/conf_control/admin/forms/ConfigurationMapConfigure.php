<?php
/**
 * @package plugins.confControl
 * @subpackage Admin
 */
class Form_ConfigurationMapConfigure extends ConfigureForm
{
	protected $disableAttributes;

	public function __construct($disableAttributes = null)
	{
		$this->disableAttributes = $disableAttributes;

		parent::__construct();
	}

	public function init()
	{
		$this->setAttrib('id', 'frmConfigurationMapConfigure');
		$this->setMethod('post');

		$titleElement = new Zend_Form_Element_Hidden('generalTitle');
		$titleElement->setLabel('General');
		$titleElement->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag', array('tag' => 'b'))));
		$this->addElement($titleElement);

		$this->addElement('text', 'name', array(
			'label' => 'Map Name:',
			'required' => true,
			'filters' => array('StringTrim'),
			'placement' => 'prepend',
			'readonly' => $this->disableAttributes,
		));

		$this->addElement('text', 'relatedHost', array(
			'label' => 'Host Name:',
			'filters' => array('StringTrim'),
			'placement' => 'prepend',
			'readonly' => $this->disableAttributes,
		));

		$this->addElement('text', 'version', array(
			'label' => 'Version:',
			'filters' => array('StringTrim'),
			'placement' => 'prepend',
			'readonly' => $this->disableAttributes,
		));

		$this->addElement('textarea', 'content', array(
			'label' => 'Content:',
			'filters' => array('StringTrim'),
			'placement' => 'prepend',
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
	}

	/**
	 * Set to null all the attributes that shouldn't be updated
	 * @param Kaltura_Client_ConfControl_Type_ConfigMap $configurationItem
	 */
	public function resetUnUpdatebleAttributes(Kaltura_Client_ConfControl_Type_ConfigMap $configurationItem)
	{
		// reset readonly attributes
		$configurationItem->lastUpdate = null;
	}

	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);

		return $object;
	}

	public function isValid($data)
	{
		$res = parent::isValid($data);
		if(!$data['content'])
		{
			return true;
		}
		$content = preg_replace('/^\h*\v+/m', '', $data['content']); // remove empty line
		if ( preg_match('/^((?!(\[.*\])|(.*=.)).)*$/im',$content , $matches)) //match invalid ini lines
		{
				return false;
		}
		if ( parse_ini_string( $data['content'], true, INI_SCANNER_RAW) === false)
			return false;

		return $res;
	}
}
