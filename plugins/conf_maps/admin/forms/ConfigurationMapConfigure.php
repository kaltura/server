<?php
/**
 * @package plugins.confMaps
 * @subpackage Admin
 */
class Form_ConfigurationMapConfigure extends ConfigureForm
{
	protected $disableAttributes;
	protected $disableEdit;

	public function __construct($disableAttributes = null, $isEditable = null)
	{
		$this->disableAttributes = $disableAttributes;
		if($isEditable === false)
			$this->disableEdit = true;
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

		$this->addElement('text', 'userId', array(
			'label' => 'Creator:',
			'filters' => array('StringTrim'),
			'placement' => 'prepend',
			'readonly' => true,
		));

		$this->addElement('text', 'createdAt', array(
			'label' => 'Last Updated Date:',
			'filters' => array('StringTrim'),
			'placement' => 'prepend',
			'readonly' => true,
		));

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

		$this->addElement('textarea', 'rawData', array(
			'label' => 'Content:',
			'filters' => array('StringTrim'),
			'placement' => 'prepend',
			'readonly' => $this->disableEdit,
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
	 * @param Kaltura_Client_ConfMaps_Type_ConfMaps $configurationItem
	 */
	public function resetUnUpdatebleAttributes(Kaltura_Client_ConfMaps_Type_ConfMaps $configurationItem)
	{
		// reset readonly attributes
		$configurationItem->lastUpdate = null;
		$configurationItem->version = null;
		$configurationItem->isEditable = null;
		$configurationItem->sourceLocation = null;
	}

	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);

		return $object;
	}

	public function isValid($data)
	{
		$res = parent::isValid($data);
		if (!$res)
		{
			return false;
		}
		if(!$data['rawData'])
		{
			return true;
		}
		$content = preg_replace('/^;.*$/m', '', $data['rawData']); // remove comments
		$content = preg_replace('/^\h*\v+/m', '', $content ); // remove empty line
		if ( preg_match('/^((?!(\[.*\])|(.*=.)).)*$/im',$content , $matches)) //match invalid ini lines
		{
				return false;
		}
		return $res;
	}
}
