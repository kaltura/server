<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Delivery_UrlTokenizerAkamaiRtsp extends Form_Delivery_DeliveryProfileTokenizer
{
	public function init()
	{
		parent::init();
		$this->addElement('text', 'host', array(
				'label'			=> 'Host:',
				'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'cpcode', array(
				'label'			=> 'cp-code:',
				'validators'	=> array('Int'),
		));
	}
}