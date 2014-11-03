<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Delivery_UrlTokenizerBitGravity extends Form_Delivery_DeliveryProfileTokenizer
{
	public function init()
	{
		parent::init();
		$this->addElement('text', 'hashPatternRegex', array(
				'label'			=> 'Hash Pattern Regex:',
				'filters'		=> array('StringTrim'),
		));
	}
}