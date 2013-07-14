<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBatchGetExclusiveNotificationJobsResponse extends KalturaObject
{
	/**
	 * @var KalturaBatchJobArray
	 * @readonly
	 */
	public $notifications;

	/**
	 * @var KalturaPartnerArray
	 * @readonly
	 */
	public $partners;
}