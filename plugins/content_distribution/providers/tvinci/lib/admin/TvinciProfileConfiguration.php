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
		/**
		 * @var Kaltura_Client_TvinciDistribution_Type_TvinciDistributionProfile $object
		 */
		$tagsArr = array();
		foreach($properties as $key => $value)
		{
			if (strpos($key, 'tvinci_distribution_tags_') === 0)
			{
				$tvinciDistributionTag = new Kaltura_Client_TvinciDistribution_Type_TvinciDistributionTag();
				$tvinciDistributionTag->tagname = $value['tag_name'];
				$tvinciDistributionTag->extension = $value['tag_extension'];
				$tvinciDistributionTag->protocol = $value['tag_protocol'];
				$tvinciDistributionTag->format = $value['tag_format'];
				$tvinciDistributionTag->filename = $value['tag_file_name'];
				$tvinciDistributionTag->ppvmodule = $value['tag_ippvmodule'];
				$tagsArr[] = $tvinciDistributionTag;
			}
		}
		$object->tags = $tagsArr;
		
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

		$tvinciDistributionTagsSubForm = new Zend_Form_SubForm(array('DisableLoadDefaultDecorators' => true));
		$tvinciDistributionTagsSubForm->addDecorator('ViewScript', array(
			'viewScript' => 'tvinci-distribution-tags-sub-form.phtml',
		));
		foreach($object->tags as $tag)
		{
			$distributionTag = new Form_TvinciTagSubForm();
			$distributionTag->populateFromObject($tag);
			$tvinciDistributionTagsSubForm->addSubForm($distributionTag, 'tvinci_distribution_tags_'.spl_object_hash($tag));
		}
		$this->addSubForm($tvinciDistributionTagsSubForm, 'tvinci_distribution_tags');
		
		if ($object->xsltFile) {
			$this->getElement('xsltFileText')->setValue(json_encode($object->xsltFile));
		}

	}

	protected function addDistributeAssetsTypeElement()
	{
		$assetsType = new Kaltura_Form_Element_EnumSelect('assets_type', array('enum' => 'Kaltura_Client_TvinciDistribution_Enum_TvinciAssetsType'));
		$assetsType->setLabel('Tvinci assets type:');
		if($this->distributionProfile->assetsType)
		{
			$assetsType->setValue($this->distributionProfile->assetsType);
		}
		else
		{
			$assetsType->setValue(Kaltura_Client_TvinciDistribution_Enum_TvinciAssetsType::REGULAR);
		}

		$this->addElement($assetsType);

		$this->addDisplayGroup(
			array('assets_type'),
			'AssetsTypeDisplayGroup',
			array(
				'legend' => 'Assets type configuration',
				'decorators' => array('FormElements', 'Fieldset'),
			)
		);
	}

	protected function tvinciElements()
	{
		$this->addDistributeAssetsTypeElement();

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

		$this->addElement('select', 'inner_type', array(
			'label' => 'Inner type:',
			'multioptions' => array(
				'catalog' => 'WS catalog',
				'ingest' => 'WS ingest',
			)
		));

		$this->addDisplayGroup(
				array('ingest_url','username','password', 'inner_type'),
				'ingest',
				array(
					'legend' => 'Ingest URL Configuration',
					'decorators' => array('FormElements', 'Fieldset'),
				)
		);

		// tag specific configuration
		$tvinciDistributionTagsSubForm = new Zend_Form_SubForm(array('DisableLoadDefaultDecorators' => true));
		$tvinciDistributionTagsSubForm->addDecorator('ViewScript', array(
			'viewScript' => 'tvinci-distribution-tags-sub-form.phtml',
		));
		$this->addSubForm($tvinciDistributionTagsSubForm, 'tvinci_distribution_tags');

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