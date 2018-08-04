<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_DeliveryProfileFilter extends Form_PartnerIdFilter
{
	public function init()
	{
		parent::init();
		$filterType = $this->getElement('filter_type');
		$filterType->setMultiOptions(array(
			'none' => 'None', 
			'partnerIdEqual' => 'Publisher ID',
			'idEqual' => 'Delivery Profile ID',
		));
	}
}