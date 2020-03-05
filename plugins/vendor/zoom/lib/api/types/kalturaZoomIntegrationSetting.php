<?php
/**
 * @package plugins.vendor
 * @subpackage api.objects
 */
class kalturaZoomIntegrationSetting extends KalturaObject
{
	/**
	 * @var string
	 */
	public $defaultUserId;

	/**
	 * @var string
	 */
	public $zoomCategory;

	/**
	 * @var string
	 */
	public $accountId;

	/**
	 * @var KalturaNullableBoolean
	 */
	public $enableRecordingUpload;

	/**
	 * @var KalturaNullableBoolean
	 */
	public $createUserIfNotExist;

	/**
	 * @var kalturaHandleParticipantsMode
	 */
	public $handleParticipantMode;

	/**
	 * @var string
	 */
	public $zoomUserMatchingMode;

	/**
	 * @var string
	 */
	public $zoomUserPostfix;

	/**
	 * @var string
	 */
	public $zoomWebinarCategory;

	/**
	 * @var KalturaNullableBoolean
	 */
	public $enableWebinarUploads;
}