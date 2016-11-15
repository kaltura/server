<?php
/**
 * @package plugins.businessProcessNotification
 * @subpackage admin
 */
class Kaltura_View_Helper_EntryBusinessProcess extends Kaltura_View_Helper_PartialViewPlugin
{
	private $entryId;
	private $partnerId;
	
	/* (non-PHPdoc)
	 * @see Kaltura_View_Helper_PartialViewPlugin::plug()
	 */
	public function plug(Zend_View_Interface $view)
	{
		$entry = $view->investigateData->entry;
		$this->entryId = $entry->id;
		$this->partnerId = $entry->partnerId;
		parent::plug($view);
	}
	
	/* (non-PHPdoc)
	 * @see Kaltura_View_Helper_PartialViewPlugin::getDataArray()
	 */
	protected function getDataArray()
	{
		$client = Infra_ClientHelper::getClient();
		$eventNotificationPlugin = Kaltura_Client_EventNotification_Plugin::get($client);
		$businessProcessNotificationPlugin = Kaltura_Client_BusinessProcessNotification_Plugin::get($client);
		
		$errDescriptions = array();
		$businessProcessCases = array();
		try
		{
			Infra_ClientHelper::impersonate($this->partnerId);
			$objectType = Kaltura_Client_EventNotification_Enum_EventNotificationEventObjectType::ENTRY;
			//Disabled until PLAT-6415 is fixed.
			//$businessProcessCases = $businessProcessNotificationPlugin->businessProcessCase->listAction($objectType, $this->entryId);
			Infra_ClientHelper::unimpersonate();
		}
		catch (Exception $e)
		{
			$errDescriptions[] = $e->getMessage();
		}
	
		$templateIds = array();
		$businessProcesses = array();
		$businessProcessCasesUrls = array();
		if(count($businessProcessCases))
		{
			foreach($businessProcessCases as $businessProcessCase)
			{
				$businessProcessCasesUrls[$businessProcessCase->businessProcessStartNotificationTemplateId] = $businessProcessNotificationPlugin->businessProcessCase->serveDiagram($objectType, $this->entryId, $businessProcessCase->businessProcessStartNotificationTemplateId);
				$businessProcesses[$businessProcessCase->businessProcessStartNotificationTemplateId] = $businessProcessCase;
				$templateIds[] = $businessProcessCase->businessProcessStartNotificationTemplateId;
			}
		}

		$eventNotificationTemplates = array();
		if(count($templateIds))
		{
			$filter = new Kaltura_Client_EventNotification_Type_EventNotificationTemplateFilter();
			$filter->idIn = implode(',', $templateIds);
			try
			{
				Infra_ClientHelper::impersonate($this->partnerId);
				$eventNotificationTemplateList = $eventNotificationPlugin->eventNotificationTemplate->listAction($filter);
				Infra_ClientHelper::unimpersonate();
				$eventNotificationTemplates = $eventNotificationTemplateList->objects;
			}
			catch (Exception $e)
			{
				$errDescriptions[] = $e->getMessage();
			}
		}
		
		return array(
			'businessProcessCases' => $businessProcesses,
			'businessProcessCasesUrls' => $businessProcessCasesUrls,
			'eventNotificationTemplates' => $eventNotificationTemplates,
			'errDescriptions' => $errDescriptions,
		);
	}
	
	/* (non-PHPdoc)
	 * @see Kaltura_View_Helper_PartialViewPlugin::getTemplatePath()
	 */
	protected function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}
	
	/* (non-PHPdoc)
	 * @see Kaltura_View_Helper_PartialViewPlugin::getPHTML()
	 */
	protected function getPHTML()
	{
		return 'entry-investigate-cases.phtml';
	}
}