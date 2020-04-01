<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Delivery_UrlTokenizerVideoCdn extends Form_Delivery_DeliveryProfileTokenizer
{
	/**
	 * @throws Zend_Form_Exception
	 */
	public function init()
	{
		parent::init();
		
		$type = new Kaltura_Form_Element_EnumSelect('algorithmId', array('enum' => 'Kaltura_Client_Enum_ChinaCacheAlgorithmType'));
		$type->setLabel('Algorithm ID:');
		$this->addElements(array($type));	
		
		$this->addElement('text', 'keyId', array(
				'label'			=> 'Key ID:',
				'validators'	=> array('Int'),
		));
	}
}
