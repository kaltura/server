<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Delivery_UrlTokenizerVnpt extends Form_Delivery_DeliveryProfileTokenizer
{
	public function init()
	{
		parent::init();
		$this->addElement('text', 'tokenKey', array(
				'label'			=> 'Token Key:',
				'filters'		=> array('StringTrim'),
		));
		$this->addElement('text', 'expiryTimeFrame', array(
				'label'                 => 'expiration time frame:',
				'filters'               => array('Int'),
		));
		$this->addElement('select', 'tokenizationFormat', array(
				'label'			=> 'Live Tokenization Format:',
				'filters'		=> array('Int'),
				'multiOptions'  => array("vod http","vod/live"),
		));
	}
}
