<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBatchGetExclusiveNotificationJobsResponse extends KalturaObject
{
	/**
	 * @var KalturaNotificationArray
	 * @readonly
	 */
	public $notifications;

	/**
	 * @var KalturaPartnerArray
	 * @readonly
	 */
	public $partners;
}