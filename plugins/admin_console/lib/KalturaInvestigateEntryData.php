<?php
class KalturaInvestigateEntryData extends KalturaObject
{
	/**
	 * @var KalturaBaseEntry
	 * @readonly
	 */
	public $entry;

	/**
	 * @var KalturaFileSyncListResponse
	 * @readonly
	 */
	public $fileSyncs;

	/**
	 * @var KalturaBatchJobListResponse
	 * @readonly
	 */
	public $jobs;
	
	/**
	 * @var KalturaInvestigateFlavorAssetDataArray
	 * @readonly
	 */
	public $flavorAssets;
	
	/**
	 * @var KalturaInvestigateThumbAssetDataArray
	 * @readonly
	 */
	public $thumbAssets;
	
	/**
	 * @var KalturaTrackEntryArray
	 * @readonly
	 */
	public $tracks;
}