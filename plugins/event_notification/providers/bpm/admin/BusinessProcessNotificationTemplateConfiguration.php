<?php 
/**
 * @package plugins.businessProcessNotification
 * @subpackage admin
 */
class Form_BusinessProcessNotificationTemplateConfiguration extends Form_EventNotificationTemplateConfiguration
{
	private function getProcesses(Kaltura_Client_BusinessProcessNotification_Type_BusinessProcessServer $server)
	{
	}
	
	/* (non-PHPdoc)
	 * @see Form_EventNotificationTemplateConfiguration::addTypeElements()
	 */
	protected function addTypeElements(Kaltura_Client_EventNotification_Type_EventNotificationTemplate $eventNotificationTemplate)
	{
		if(!($eventNotificationTemplate instanceof Kaltura_Client_BusinessProcessNotification_Type_BusinessProcessNotificationTemplate))
			return;
			
		$client = Infra_ClientHelper::getClient();
		$businessProcessNotificationPlugin = Kaltura_Client_BusinessProcessNotification_Plugin::get($client);

		$filter = new Kaltura_Client_BusinessProcessNotification_Type_BusinessProcessServerFilter();
		$filter->currentDcOrExternal = Kaltura_Client_Enum_NullableBoolean::TRUE_VALUE;
		$pager = new Kaltura_Client_Type_FilterPager();
		$pager->pageSize = 500;
		
		$serversList = $businessProcessNotificationPlugin->businessProcessServer->listAction($filter, $pager);
		/* @var $serversList Kaltura_Client_BusinessProcessNotification_Type_BusinessProcessServerListResponse */
		$businessProcessProvider = null;
		$servers = array('' => 'Select Server');
		foreach($serversList->objects as $server)
		{
			/* @var $server Kaltura_Client_BusinessProcessNotification_Type_BusinessProcessServer */
			if(!is_null($server->dc))
				$servers[0] = 'Kaltura';
			else
				$servers[$server->id] = $server->name;

			if($server->id == $eventNotificationTemplate->serverId || (!is_null($server->dc) && 0 == $eventNotificationTemplate->serverId))
				$businessProcessProvider = kBusinessProcessProvider::get($server);
		}

		$processes = array();
		if($businessProcessProvider)
		{
			$processes = $businessProcessProvider->listBusinessProcesses();
			asort($processes);
		}
			
 		$this->addElement('select', 'server_id', array(
			'label'			=> 'Server:',
			'multiOptions'  => $servers,
 			'default' => $eventNotificationTemplate->serverId,
 		));
 		
 		$this->addElement('select', 'process_id', array(
			'label'			=> 'Business-Process:',
			'multiOptions'  => $processes,
 			'default' => $eventNotificationTemplate->processId,
 		));
 		
		if($eventNotificationTemplate instanceof Kaltura_Client_BusinessProcessNotification_Type_BusinessProcessSignalNotificationTemplate)
		{
			$this->addElement('text', 'message', array(
				'label'			=> 'Message:',
				'filters'		=> array('StringTrim'),
				'required'		=> true,
			));
			
			$this->addElement('text', 'event_id', array(
				'label'			=> 'Event ID:',
				'filters'		=> array('StringTrim'),
				'required'		=> true,
			));
		}
 		
		if($eventNotificationTemplate instanceof Kaltura_Client_BusinessProcessNotification_Type_BusinessProcessStartNotificationTemplate)
		{
			$this->addElement('checkbox', 'abort_on_deletion', array(
				'label'			=> 'Abort on deletion:',
				'decorators'	=> array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt'))),
			));
		}
	}
}