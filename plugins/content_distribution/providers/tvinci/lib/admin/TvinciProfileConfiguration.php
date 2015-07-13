<?php
/**
 * @package plugins.tvinciDistribution
 * @subpackage admin
 */
class Form_TvinciProfileConfiguration extends Form_ConfigurableProfileConfiguration
{
	/**
	 * This element id is used to separate the default profile elements and tvinci profile elements, so later we could
	 * insert the dynamic elements at the correct position
	 */
	const FORM_PLACEHOLDER_ELEMENT_ID = 'tvinci_placeholder';

	public function init()
	{
		parent::init();
		$this->getView()->addBasePath(realpath(dirname(__FILE__)));
		$this->addDecorator('ViewScript', array(
			'viewScript' => 'tvinci-distribution.phtml',
			'placement' => 'APPEND'
		));
	}

	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
		return $object;
	}

	protected function addProviderElements()
	{
	    $this->setDescription(null);

		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('Tvinci Specific Configuration');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElements(array($element));
		$this->addElement('hidden', self::FORM_PLACEHOLDER_ELEMENT_ID);
		$this->_sort();
		$order = $this->_order[self::FORM_PLACEHOLDER_ELEMENT_ID];
		$this->resetOrderOfLastElements();
		$this->layoutForm($order++);
	}



	public function resetOrderOfLastElements()
	{
		$found = false;
		foreach ($this->_order as $key => &$order)
		{
			if ($found)
				$order = null;

			if ($key == self::FORM_PLACEHOLDER_ELEMENT_ID)
				$found = true;
		}
	}

	protected function layoutForm($order)
	{
		// Ingest Configuration
		$this->addElement('text', 'ingest_url', array(
				'label'			=> 'Ingest url:',
				'filters'		=> array('StringTrim'),
		));

		$this->addElement('text', 'username', array(
				'label'			=> 'Username:',
				'filters'		=> array('StringTrim'),
		));

		$this->addElement('text', 'password', array(
				'label'			=> 'Password:',
				'filters'		=> array('StringTrim'),
		));

		$this->addDisplayGroup(
				array('ingest_url','username','password'),
				'ingest',
				array(
					'legend' => 'Ingest URL Configuration',
					'decorators' => array('FormElements', 'Fieldset'),
					'order' => $order++,
				)
		);

		$this->addElement('select', 'schema_id', array(
			'label'			=> 'Schema ID:',
			'multioptions' => array(
				'' => '2',
				'1' => '1',
				'2' => '2',
			)

		));

		$this->addElement('text', 'language', array(
			'label'			=> 'Language :',
			'filters'		=> array('StringTrim'),
		));

		$this->addDisplayGroup(
			array('schema_id', 'language'),
			'additional',
			array(
				'legend' => 'Additional Configuration',
				'decorators' => array('FormElements', 'Fieldset'),
				'order' => $order++,
			)
		);
	}
}