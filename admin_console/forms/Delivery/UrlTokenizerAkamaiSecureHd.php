<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Delivery_UrlTokenizerAkamaiSecureHd extends Form_Delivery_DeliveryProfileTokenizer
{
	
	public function init()
	{
		parent::init();
		$this->addElement('text', 'paramName', array(
				'label'			=> 'Param name:',
				'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'aclPostfix', array(
				'label'			=> 'Acl Postfix:',
				'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'customPostfixes', array(
				'label'			=> 'Custom Postfixes:',
				'filters'		=> array('StringTrim'),
		));

		$this->addElement('text', 'rootDir', array(
			'label'			=> 'Root directory:',
			'filters'		=> array('StringTrim'),
		));

		$this->addElement('checkbox', 'limitIpAddress', array(
			'label'			=> 'Limit IP Address:',
		));
	}
}