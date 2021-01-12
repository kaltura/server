<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Delivery_DeliveryProfileVodPackagerPlayServer extends Form_Delivery_DeliveryProfileVod
{

	public function getAdvancedSettings()
	{
		$this->addElement('checkbox', 'adStitchingEnabled', array(
			'label'			=> 'Enable ad stitching:',
		));

		return array_merge(parent::getAdvancedSettings(), array('adStitchingEnabled'));
	}

}