<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Delivery_DeliveryProfileLivePackager extends Form_Delivery_DeliveryProfileConfiguration
{
	public function getAdvancedSettings()
	{
		$this->addElement('text', 'livePackagerSigningDomain', array(
			'label' 		=> 'Live Packager Signing Domain:',
			'required'		=> false,
			'filters'		=> array('StringTrim'),
		));

		$this->addElement('checkbox', 'shouldRedirect', array(
			'label'			=> 'Should redirect:',
		));

		return array('livePackagerSigningDomain', 'shouldRedirect');
	}
}