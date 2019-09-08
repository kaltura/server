<?php 
/**
 * @package plugins.sso
 * @subpackage Admin
 */
class Form_SsoProfileFilter extends Form_PartnerIdFilter
{
	public function init()
	{
		parent::init();
		$filterType = $this->getElement('filter_type');
		$filterType->setMultiOptions(array(
			'partnerIdEqual' => 'Publisher ID',
			'domainEqual' => 'Domain',
		));
	}
}