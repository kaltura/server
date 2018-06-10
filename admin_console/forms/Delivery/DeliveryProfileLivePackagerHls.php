<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Delivery_DeliveryProfileLivePackagerHls extends Form_Delivery_DeliveryProfileLivePackager
{
	public function getAdvancedSettings()
	{
		
		$this->addElement('checkbox', 'disableExtraAttributes', array(
			'label'			=> 'Disable Extra attributes:',
		));
		
		$this->addElement('checkbox', 'forceProxy', array(
			'label'			=> 'Force proxy:',
		));
		
		$this->addElement('text', 'livePackagerSigningDomain', array(
			'label' 		=> 'Live Packager Signing Domain:',
			'required'		=> false,
			'filters'		=> array('StringTrim'),
		));
		
		return array('disableExtraAttributes', 'forceProxy', 'livePackagerSigningDomain');
	}
}