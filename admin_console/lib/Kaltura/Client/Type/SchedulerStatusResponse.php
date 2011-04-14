<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Type_SchedulerStatusResponse extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaSchedulerStatusResponse';
	}
	
	/**
	 * The status of all queues on the server
	 * 
	 *
	 * @var array of KalturaBatchQueuesStatus
	 */
	public $queuesStatus;

	/**
	 * The commands that sent from the control panel
	 * 
	 *
	 * @var array of KalturaControlPanelCommand
	 */
	public $controlPanelCommands;

	/**
	 * The configuration that sent from the control panel
	 * 
	 *
	 * @var array of KalturaSchedulerConfig
	 */
	public $schedulerConfigs;


}

