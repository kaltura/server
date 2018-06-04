<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Delivery_UrlTokenizerWowzaSecureToken extends Form_Delivery_DeliveryProfileTokenizer
{
	public function init()
	{
		parent::init();
						
		$this->addElement('text', 'paramPrefix', array(
				'label'			=> 'Param Prefix:',
				'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('checkbox', 'shouldIncludeClientIp', array(
				'label'         => 'include client ip',
		));
		
		$this->addElement('select', 'hashAlgorithm', array(
 				'label'			=> ' Hash Algorithm:',
 				'filters'		=> array('StringTrim'),
 				'multiOptions'  => array(
						'sha256' => 'SHA-256',
						'sha384' => 'SHA-384',
						'sha512' => 'SHA-512',
				),
 		));
		
	}
}