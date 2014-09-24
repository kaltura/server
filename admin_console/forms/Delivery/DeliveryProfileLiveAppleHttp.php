<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Delivery_DeliveryProfileLiveAppleHttp extends Form_Delivery_DeliveryProfileConfiguration
{
	
	public function getAdvancedSettings()
	{
		$this->addElement('checkbox', 'disableExtraAttributes', array(
				'label'			=> 'Disable Extra attributes:',
		));
		
		$this->addElement('checkbox', 'enforceProxy', array(
				'label'			=> 'Enforce proxy:',
		));
		
		return array('disableExtraAttributes', 'enforceProxy');
	}
	
}