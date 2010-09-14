<?php
class KalturaSchedulerStatusResponse extends KalturaObject 
{
	/**
	 * The status of all queues on the server
	 * 
	 * @var KalturaBatchQueuesStatusArray
	 */
	public $queuesStatus;
	
	
	/**
	 * The commands that sent from the control panel
	 * 
	 * @var KalturaControlPanelCommandArray
	 */
	public $controlPanelCommands;
	
	
	/**
	 * The configuration that sent from the control panel
	 * 
	 * @var KalturaSchedulerConfigArray
	 */
	public $schedulerConfigs;
}