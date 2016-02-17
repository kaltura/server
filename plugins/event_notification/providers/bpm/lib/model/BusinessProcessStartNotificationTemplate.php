<?php
/**
 * @package plugins.businessProcessNotification
 * @subpackage model
 */
class BusinessProcessStartNotificationTemplate extends BusinessProcessNotificationTemplate
{
	const CUSTOM_DATA_ABORT_ON_DELETION = 'abortOnDeletion';
	
	private $aborting = false;
	
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
		return $this->aborting ? array() : parent::getContentParameters();
	}
	
	/* (non-PHPdoc)
	 * @see EventNotificationTemplate::getUserParameters()
	 */
	public function getUserParameters()
	{
		return $this->aborting ? array() : parent::getUserParameters();
	}
	
	/* (non-PHPdoc)
	 * @see BusinessProcessNotificationTemplate::getMainObject()
	 */
	protected function getMainObject(BaseObject $object)
	{
		return $this->aborting ? $object : parent::getMainObject($object);
	}
	
	/* (non-PHPdoc)
	 * @see BusinessProcessNotificationTemplate::getParameters()
	 */
	protected function getParameters(kScope $scope)
	{
		$parametersValues = parent::getParameters($scope);
		$parametersValues['templateId'] = $this->getId();
		
		return $parametersValues;
	}
	
	/* (non-PHPdoc)
	 * @see BatchEventNotificationTemplate::dispatch()
	 */
	public function abort($scope)
	{
		$abortCaseJobType = BusinessProcessNotificationPlugin::getBusinessProcessNotificationTemplateTypeCoreValue(BusinessProcessNotificationTemplateType::BPM_ABORT);
		$this->aborting = true;
		try 
		{
			$ret = $this->dispatchPerCase($scope, $abortCaseJobType);	
		}
		catch (Exception $e)
		{
			$this->aborting = false;
			throw $e;
		}
		$this->aborting = false;
		return $ret;
	}

	public function getAbortOnDeletion()							{return $this->getFromCustomData(self::CUSTOM_DATA_ABORT_ON_DELETION);}

	public function setAbortOnDeletion($v)							{return $this->putInCustomData(self::CUSTOM_DATA_ABORT_ON_DELETION, $v);}
}
