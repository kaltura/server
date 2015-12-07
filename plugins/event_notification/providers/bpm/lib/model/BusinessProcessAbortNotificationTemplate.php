<?php
/**
 * @package plugins.businessProcessNotification
 * @subpackage model
 */
class BusinessProcessAbortNotificationTemplate extends BusinessProcessNotificationTemplate
{
	public function __construct()
	{
		$this->setType(BusinessProcessNotificationPlugin::getBusinessProcessNotificationTemplateTypeCoreValue(BusinessProcessNotificationTemplateType::BPM_ABORT));
		parent::__construct();
	}
	
	/* (non-PHPdoc)
	 * @see BatchEventNotificationTemplate::dispatch()
	 */
	public function dispatch(kScope $scope)
	{
		KalturaLog::debug("Dispatch id [" . $this->getId() . "]");
		return $this->dispatchPerCase($scope);
	}
}
