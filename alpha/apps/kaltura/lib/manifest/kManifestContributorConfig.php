<?php
/**
 * Configuration object passed to contributors for the playManifestAction.
 * @package Core
 */
class kManifestContributorConfig
{
	/**
	 * Format of the playManifest assets: RTMP, HTTP, APPLE-HTTP...
	 * @var int
	 */
	public $format;
	
	/**
	 * ID of the storage profile from which the assets should be taken.
	 * @var int
	 */
	public $storageId;
	
	/**
	 * Entry Id in light of which the manifest action is called
	 * @var string
	 */
	public $entryId;
		
	/**
	 * @var string
	 */
	public $rendererClass;

	/**
	 * @var DeliveryProfile
	 */
	public $deliveryProfile;

	/**
	 * @var bool
	 */
	public $hasSequence;

	/**
	 * @var bool
	 */
	public $disableCaptions;

}
