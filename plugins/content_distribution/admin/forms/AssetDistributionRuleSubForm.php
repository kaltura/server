<?php
/**
 * @package plugins.contentDistribution 
 * @subpackage admin
 */
class Form_AssetDistributionRuleSubForm extends Form_DistributionSubForm
{
	public function init()
	{
		$this->addDecorator('ViewScript', array(
				'viewScript' => 'asset-distribution-rule-sub-form.phtml',
		));

		$this->addElement('text', 'validation_error', array(
			'filters' 		=> array('StringTrim'),
			'label'			=> 'Validation Error:',
			'decorators'	=> array('ViewHelper'),
		));

		$this->addElement('checkbox', 'required', array(
			'filters' 		=> array('StringTrim'),
			'label'			=> 'Is Required?',
			'decorators'	=> array('ViewHelper'),
		));

		$this->addElement('hidden', 'belongs', array(
			'decorators'	=> array('ViewHelper'),
		));
	}

	public function populateFromObject($object, $add_underscore = true)
	{
		/** @var $object KalturaAssetDistributionRule */
		parent::populateFromObject($object, $add_underscore);
		foreach($object->assetDistributionConditions as $assetDistributionCondition)
		{
			$assetDistributionConditionSubForm = new Form_AssetDistributionPropertyConditionSubForm();
			$assetDistributionConditionSubForm->populateFromObject($assetDistributionCondition);
			$this->addSubForm($assetDistributionConditionSubForm, 'asset_distribution_property_condition_'.spl_object_hash($assetDistributionCondition));
		}
	}

	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
		return $object;
	}

	public function setIsRequired($required)
	{
		$this->setDefault('required', $required);
	}
}