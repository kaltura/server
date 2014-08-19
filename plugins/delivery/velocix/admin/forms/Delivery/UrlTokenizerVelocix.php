<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Delivery_UrlTokenizerVelocix extends Form_Delivery_DeliveryProfileTokenizer
{
	public function init()
	{
		parent::init();
		$this->addElement('text', 'hdsPaths', array(
				'label'			=> 'HDS Paths:',
				'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'paramName', array(
				'label'			=> 'Param name:',
				'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'authPrefix', array(
				'label'			=> 'Secured URL prefix:',
				'filters'		=> array('StringTrim'),
		));
	}
}