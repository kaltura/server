<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Delivery_DeliveryProfileRtmp extends Form_Delivery_DeliveryProfileConfiguration
{
	
	public function getAdvancedSettings()
	{
		$this->addElement('text', 'prefix', array(
				'label'			=> 'Prefix:',
				'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('checkbox', 'enforceRtmpe', array(
				'label'			=> 'Enforce RtmpE:',
				'filters'		=> array('StringTrim'),
		));
		
		return array('prefix','enforceRtmpe');
	}
	
}