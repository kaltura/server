<?php
/**
 * @package plugins.eventNotification
 * @subpackage api.objects
 */
class KalturaEventNotificationTemplateListResponse extends KalturaObject
{
	/**
	 * @var KalturaEventNotificationTemplateArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}