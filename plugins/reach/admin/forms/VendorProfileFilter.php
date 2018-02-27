<?php 
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class Form_VendorProfileFilter extends Form_PartnerIdFilter
{
	public function init()
	{
		parent::init();
		$filterType = $this->getElement('filter_type');
		$filterType->setMultiOptions(array(
			'partnerIdEqual' => 'Publisher ID',
		));
	}
}