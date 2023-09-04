<?php

/**
 * @package Admin
 * @subpackage Partners
 */
class Form_PasswordValidationRuleItem extends Infra_Form
{
	public function init()
	{
		$this->addDecorator('ViewScript', array(
			'viewScript' => 'password_validation_rule_item.phtml',
		));
		
		$this->addElement('text', 'regex', array(
			'filters' 		=> array('StringTrim'),
			'decorators'	=> array('ViewHelper'),
		));
		
		$this->addElement('text', 'description', array(
			'filters' 		=> array('StringTrim'),
			'decorators'	=> array('ViewHelper'),
		));
	}
	
	public function populateFromObject($object, $add_underscore = true)
	{
		/** @var $object KalturaRegexItem */
		parent::populateFromObject($object, $add_underscore);
	}
}
