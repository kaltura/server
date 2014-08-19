<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Delivery_UrlTokenizerAkamaiRtmp extends Form_Delivery_DeliveryProfileTokenizer
{
	public function init()
	{
		parent::init();
		$this->addElement('text', 'profile', array(
				'label'			=> 'Profile:',
				'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'type', array(
				'label'			=> 'Type:',
				'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'aifp', array(
				'label'			=> 'Aifp:',
				'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('checkbox', 'usePrefix', array(
				'label'			=> 'Use Prefix:',
		));
	}
}