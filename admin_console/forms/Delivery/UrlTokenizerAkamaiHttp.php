<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Delivery_UrlTokenizerAkamaiHttp extends Form_Delivery_DeliveryProfileTokenizer
{
	public function init()
	{
		parent::init();
		$this->addElement('text', 'paramName', array(
				'label'			=> 'Param name:',
				'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'rootDir', array(
				'label'			=> 'Root directory:',
				'filters'		=> array('StringTrim'),
		));
	}
}