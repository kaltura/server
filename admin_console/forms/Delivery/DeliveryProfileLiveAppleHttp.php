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
		
		$this->addElement('checkbox', 'forceProxy', array(
				'label'			=> 'Force proxy:',
		));
		
		return array('disableExtraAttributes', 'forceProxy');
	}
	
}