<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Delivery_UrlTokenizerChinaCache extends Form_Delivery_DeliveryProfileTokenizer
{
	public function init()
	{
		parent::init();
		
		$type = new Kaltura_Form_Element_EnumSelect('type', array('enum' => 'Kaltura_Client_Enum_ChinaCacheAlgorithmType'));
		$this->addElement('text', 'algorithm_id', array(
			'label'			=> 'Algorithm ID:',
			'filters'		=> array('StringTrim'),
			'multiOptions'  => array($type),
		));		
		
		$this->addElement('text', 'key_id', array(
				'label'			=> 'Key ID:',
				'validators'	=> array('Int'),
		));
	}
}
