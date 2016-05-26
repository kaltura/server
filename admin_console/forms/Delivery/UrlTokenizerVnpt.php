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
		$this->addElement('select', 'tokenizationFormat', array(
				'label'			=> 'Live Tokenization Format:',
				'filters'		=> array('Int'),
				'multiOptions'	=> array("vod","live","vod http"),
		));
		
		$this->addElement('select', 'shouldIncludeClientIp', array(
				'label'				=> 'include client ip',
				'filters'			=> array('Int'),
				'multiOptions'		=> array("no","yes"),
		));
	}
}
