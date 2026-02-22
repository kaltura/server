<?php
/**
 * @package Admin
 * @subpackage Delivery
 */
class Form_Delivery_DeliveryProfileFilter extends Form_PartnerIdFilter
{
	public function init()
	{
		parent::init();
		$this->setAttrib('id', 'frmPartnerIdFilter');
		$filterType = $this->getElement('filter_type');
		$filterType->setMultiOptions(array(
			'none' => 'None',
			'by-delivery-id' => 'Delivery Profile ID',
			'byid' => 'Publisher ID',
		));
	}
}