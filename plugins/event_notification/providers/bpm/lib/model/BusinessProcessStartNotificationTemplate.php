<?php
/**
 * @package plugins.businessProcessNotification
 * @subpackage model
 */
class BusinessProcessStartNotificationTemplate extends BusinessProcessNotificationTemplate
{
	private $disableParameters = false;
	
	public function __construct()
	{
		$this->setType(BusinessProcessNotificationPlugin::getBusinessProcessNotificationTemplateTypeCoreValue(BusinessProcessNotificationTemplateType::BPM_START));
		parent::__construct();
	}
	
	/* (non-PHPdoc)
	 * @see EventNotificationTemplate::getContentParameters()
	 */
	public function getContentParameters()
	{
		return $this->disableParameters ? array() : parent::getContentParameters();
	}
	
	/* (non-PHPdoc)
	 * @see EventNotificationTemplate::getUserParameters()
	 */
	public function getUserParameters()
	{
		return $this->disableParameters ? array() : parent::getUserParameters();
	}
	
	/* (non-PHPdoc)
	 * @see BatchEventNotificationTemplate::dispatch()
	 */
	public function abort($scope)
	{
		$abortCaseJobType = BusinessProcessNotificationPlugin::getBusinessProcessNotificationTemplateTypeCoreValue(BusinessProcessNotificationTemplateType::BPM_ABORT);
		$this->disableParameters = true;
		try 
		{
			$ret = $this->dispatchPerCase($scope, $abortCaseJobType);	
		}
		catch (Exception $e)
		{
			$this->disableParameters = false;
			throw $e;
		}
		$this->disableParameters = false;
		return $ret;
	}
}
