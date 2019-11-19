<?php
/**
 * @package plugins.booleanNotification
 * @subpackage model
 */
class BooleanNotificationTemplate extends BatchEventNotificationTemplate //implements ISyncableFile
{
	public function __construct()
	{
		$this->setType(BooleanNotificationPlugin::getBooleanNotificationTemplateTypeCoreValue(BooleanNotificationTemplateType::BOOLEAN));
		parent::__construct();
	}

	/* (non-PHPdoc)
	* @see BatchEventNotificationTemplate::getJobData()
	*/
	protected function getJobData(kScope $scope = null)
	{
		$jobData = new kEventNotificationDispatchJobData();
		$jobData->setTemplateId($this->getId());
		return $jobData;
	}

	/* (non-PHPdoc)
	 * @see BatchEventNotificationTemplate::dispatch()
	 */
	public function dispatch(kScope $scope)
	{
		return;
	}
}