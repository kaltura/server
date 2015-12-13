<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Delivery_UrlTokenizerCloudFront extends Form_Delivery_DeliveryProfileTokenizer
{
	public function init()
	{
		parent::init();
		
		$this->addElement('text', 'rootDir', array(
				'label'			=> 'Root directory:',
				'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'keyPairId', array(
				'label'			=> 'key Pair Id:',
				'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('checkbox', 'limitIpAddress', array(
				'label'			=> 'limit Ip Address:',
		));
	}
}