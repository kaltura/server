<?php
/**
 * @package plugins.tvinciDistribution
 * @subpackage admin
 */
class Form_TvinciProfileConfiguration extends Form_ConfigurableProfileConfiguration
{

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
				$xslt_file = $file['tmp_name'];
				if (!empty($xslt_file)){
					$content = file_get_contents($xslt_file);
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
		$this->tvinciElements();
	}

	public function populateFromObject($object, $add_underscore = true)
	{
		$this->tvinciElements();

		parent::populateFromObject($object, $add_underscore);

		if ($object->xsltFile) {
			$this->getElement('xsltFileText')->setValue(json_encode($object->xsltFile));
		}

	}

	private function addTagItems($tagName){
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
			)
		);
	}



	protected function tvinciElements()
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
				)
		);

		// tag specific configuration
		$this->addTagItems("ism");
		$this->addTagItems("ipadnew");
		$this->addTagItems("iphonenew");
		$this->addTagItems("mbr");
		$this->addTagItems("dash");
		$this->addTagItems("widevine");
		$this->addTagItems("widevine_mbr");

		// xslt configuration
		$this->addElement('file', 'xsltFile', array(
			'label' => 'XSLT:'

		));

		$element = new Zend_Form_Element_File('xsltFile');
		$element->setLabel('XSLT:');
		// limit only 1 file
		$element->addValidator('Count', true, 1);
		// limit to 100K (the default one is 31K)
		$element->addValidator('Size', true, 102400);
		// only XML related file extensions
		$element->addValidator('Extension', true, 'xml,xslt,xsl');
		$element->addValidator(new XSLTFileValidator());
		$this->addElement($element, 'xsltFile');


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
			)
		);

	}
}