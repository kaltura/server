<?php 
/**
 * @package plugins.drm
 * @subpackage Admin
 */
class Form_DrmPolicyFilter extends Form_PartnerIdFilter
{
	public function init()
	{
		parent::init();
		$filterType = $this->getElement('filter_type');
		$filterType->setMultiOptions(array(
			'none' => 'None',
			'partnerIdEqual' => 'Publisher ID',
		));
	}
}