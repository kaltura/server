<?php
/**
 * @package plugins.bpmEventNotificationIntegration
 * @subpackage api.objects
 */
class KalturaBpmEventNotificationIntegrationJobTriggerData extends KalturaIntegrationJobTriggerData
{
	/**
	 * KalturaBusinessProcessNotificationTemplate id
	 * @var int
	 */
	public $templateId;
	
	/**
	 * Execution unique id
	 * @var string
	 */
	public $caseId;
	
	private static $map_between_objects = array
	(
		'templateId' ,
		'caseId' ,
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}
