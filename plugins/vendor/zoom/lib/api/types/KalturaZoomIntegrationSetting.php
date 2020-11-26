<?php
/**
 * @package plugins.vendor
 * @subpackage api.objects
 */
class KalturaZoomIntegrationSetting extends KalturaObject
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
	 * @readonly
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
	 * @var KalturaHandleParticipantsMode
	 */
	public $handleParticipantMode;

	/**
	 * @var KalturaZoomUsersMatching
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

	/**
	* @var int
	 */
	public $conversionProfileId;
}