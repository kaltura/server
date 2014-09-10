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
		
		return array('disableExtraAttributes');
	}
	
}