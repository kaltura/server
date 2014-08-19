<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Delivery_UrlTokenizerLevel3 extends Form_Delivery_DeliveryProfileTokenizer
{
	public function init()
	{
		parent::init();
		$this->addElement('text', 'paramName', array(
				'label'			=> 'Param name:',
				'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'expiryName', array(
				'label'			=> 'Expiry Name:',
				'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'gen', array(
				'label'			=> 'Gen:',
				'filters'		=> array('StringTrim'),
		));
	}
}