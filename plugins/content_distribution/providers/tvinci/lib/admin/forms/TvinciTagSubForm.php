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
			'decorators'	=> array('ViewHelper'),
		));

		$this->addElement('text', 'tag_extension', array(
			'filters' 		=> array('StringTrim'),
			'label'			=> 'Extension:',
			'decorators'	=> array('ViewHelper'),
		));

		$this->addElement('text', 'tag_protocol', array(
			'filters' 		=> array('StringTrim'),
			'label'			=> 'Protocol:',
			'decorators'	=> array('ViewHelper'),
		));

		$this->addElement('text', 'tag_format', array(
			'filters' 		=> array('StringTrim'),
			'label'			=> 'Format:',
			'decorators'	=> array('ViewHelper'),
		));

		$this->addElement('text', 'tag_file_name', array(
			'filters' 		=> array('StringTrim'),
			'label'			=> 'File Name:',
			'decorators'	=> array('ViewHelper'),
		));

		$this->addElement('text', 'tag_ippvmodule', array(
			'filters' 		=> array('StringTrim'),
			'label'			=> 'IppvModule:',
			'decorators'	=> array('ViewHelper'),
		));

		$this->addElement('hidden', 'belongs', array(
			'decorators'	=> array('ViewHelper'),
		));

	}

	public function populateFromObject($object, $add_underscore = true)
	{
		/** @var $object KalturaAssetDistributionRule */
//		parent::populateFromObject($object, $add_underscore);
//		foreach($object->assetDistributionConditions as $assetDistributionCondition)
//		{
//			$assetDistributionConditionSubForm = new Form_AssetDistributionPropertyConditionSubForm();
//			$assetDistributionConditionSubForm->populateFromObject($assetDistributionCondition);
//			$this->addSubForm($assetDistributionConditionSubForm, 'asset_distribution_property_condition_'.spl_object_hash($assetDistributionCondition));
//		}
	}

	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
		return $object;
	}
}