<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Delivery_DeliveryProfileVodPackagerHls extends Form_Delivery_DeliveryProfileConfiguration
{

	public function getAdvancedSettings()
	{
		$this->addElement('checkbox', 'allowFairplayOffline', array(
			'label'			=> 'Allow Fairplay Offline:',
		));

		return array('allowFairplayOffline');
	}

}