<?php
/**
 * @package plugins.eventNotification
 * @subpackage Admin
 */
class Form_EventNotificationTemplatesFilter extends Form_PartnerIdFilter
{
	public function init()
	{
		parent::init();
		$filterType = $this->getElement('filter_type');
		$filterType->setMultiOptions(array(
			'none' => 'None',
			'partnerIdEqual' => 'Publisher ID',
			'idEqual' => 'Template ID',
			'systemNameEqual' => 'System Name',
			'typeEqual' => 'Type',
			'statusEqual' => 'Status'
		));
	}
}