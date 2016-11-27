<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Delivery_DeliveryProfileVodPackagerPlayServer extends Form_Delivery_DeliveryProfileConfiguration
{

	public function getAdvancedSettings()
	{
		$this->addElement('checkbox', 'adStitchingEnabled', array(
			'label'			=> 'Enable ad stitching:',
		));

		return array('adStitchingEnabled');
	}

}