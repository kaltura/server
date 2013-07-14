<?php
/**
 * @package plugins.contentDistribution 
 * @subpackage admin
 */
class Form_AssetDistributionPropertyConditionSubForm extends Form_DistributionSubForm
{
	public function init()
	{
		$this->addDecorator('ViewScript', array(
			'viewScript' => 'asset-distribution-property-condition-sub-form.phtml',
		));

		$this->addElement('text', 'property_name', array(
			'filters' 		=> array('StringTrim'),
			'label'			=> 'Property Name:',
			'decorators'	=> array('ViewHelper'),
		));	
		
		$this->addElement('text', 'property_value', array(
			'filters' 		=> array('StringTrim'),
			'label'			=> 'Property Value:',
			'decorators'	=> array('ViewHelper'),
		));
	}
}