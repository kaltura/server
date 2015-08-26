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

		if($object instanceof Kaltura_Client_TvinciDistribution_Type_TvinciDistributionProfile) {
			$upload = new Zend_File_Transfer_Adapter_Http();
			$files = $upload->getFileInfo();

			if (isset($files['xsltFile'])) {
				$file = $files['xsltFile'];
				if ($file['size']) {
					$content = file_get_contents($file['tmp_name']);
					$object->xsltFile = $content;
				}
			}
		}
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
	}

	public function populateFromObject($object, $add_underscore = true)
	{
		$this->_sort();

		$order = $this->_order[self::FORM_PLACEHOLDER_ELEMENT_ID];
		if ($object->xsltFile) {
			$this->getElement('xsltFileText')->setValue(json_encode($object->xsltFile));
		}
		$this->addTvinciElements($order++);

		parent::populateFromObject($object, $add_underscore);

	}

	private function addTagItems($tagName, &$order){
		$this->addElement('text', "{$tagName}_file_name", array(
			'label'			=> "{$tagName} file name:",
			'filters'		=> array('StringTrim'),
		));

		$this->addElement('text', "{$tagName}_ppv_module", array(
			'label'			=> "{$tagName} PPv Module:",
			'filters'		=> array('StringTrim'),
		));

		$this->addDisplayGroup(
			array("{$tagName}_file_name","{$tagName}_ppv_module" ),
			"{$tagName}",
			array(
				'legend' => "{$tagName} Configuration",
				'decorators' => array('FormElements', 'Fieldset'),
				'order' => $order++,
			)
		);
	}



	protected function addTvinciElements($order)
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
				array('ingest_url','username','password' ),
				'ingest',
				array(
					'legend' => 'Ingest URL Configuration',
					'decorators' => array('FormElements', 'Fieldset'),
					'order' => $order++,
				)
		);

		// tag specific configuration
		$this->addTagItems("ism", $order);
		$this->addTagItems("ipadnew", $order);
		$this->addTagItems("iphonenew", $order);
		$this->addTagItems("mbr", $order);
		$this->addTagItems("dash", $order);

		// xslt configuration
		$this->addElement('file', 'xsltFile', array(
			'label' => 'XSLT:',
		));

		$this->addElement('textarea', 'xsltFileText', array(
			'label' => 'XSLT Data:',
			'rows' => '2',
			'cols' => '50',
			'readonly' => '1'
		));

		$this->addDisplayGroup(
			array('xsltFile' , 'xsltFileText'),
			'additional',
			array(
				'legend' => 'Additional Configuration',
				'decorators' => array('FormElements', 'Fieldset'),
				'order' => $order++,
			)
		);

	}
}