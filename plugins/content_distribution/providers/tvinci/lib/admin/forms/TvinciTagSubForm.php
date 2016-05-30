<?php
/**
 * @package plugins.contentDistribution 
 * @subpackage admin
 */
class Form_TvinciTagSubForm extends Zend_Form_SubForm
{
	public function init()
	{
		$this->addDecorator('ViewScript', array(
				'viewScript' => 'tvinci-distribution-tag-sub-form.phtml',
		));

		$this->addElement('text', 'tag_name', array(
			'filters' 		=> array('StringTrim'),
			'label'			=> 'Tag Name:',
		));

		$this->addElement('text', 'tag_extension', array(
			'filters' 		=> array('StringTrim'),
			'label'			=> 'Extension:',
		));

		$this->addElement('text', 'tag_protocol', array(
			'filters' 		=> array('StringTrim'),
			'label'			=> 'Protocol:',
		));

		$this->addElement('text', 'tag_format', array(
			'filters' 		=> array('StringTrim'),
			'label'			=> 'Format:',
		));

		$this->addElement('text', 'tag_file_name', array(
			'filters' 		=> array('StringTrim'),
			'label'			=> 'File Name:',
		));

		$this->addElement('text', 'tag_ippvmodule', array(
			'filters' 		=> array('StringTrim'),
			'label'			=> 'IppvModule:',
		));

		$this->addElement('hidden', 'belongs', array(
			'decorators'	=> array('ViewHelper'),
		));

	}

	public function populateFromObject($object, $add_underscore = true)
	{
		$this->getElement('tag_name')->setValue($object->tagname);
		$this->getElement('tag_extension')->setValue($object->extension);
		$this->getElement('tag_protocol')->setValue($object->protocol);
		$this->getElement('tag_format')->setValue($object->format);
		$this->getElement('tag_file_name')->setValue($object->filename);
		$this->getElement('tag_ippvmodule')->setValue($object->ppvmodule);
	}

	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
		return $object;
	}
}