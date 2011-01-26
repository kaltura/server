<?php
/**
 * @package plugins.adminConsole
 * @subpackage api.objects
 */
class KalturaInvestigateThumbAssetData extends KalturaObject
{
	/**
	 * @var KalturaThumbAsset
	 * @readonly
	 */
	public $thumbAsset;

	/**
	 * @var KalturaFileSyncListResponse
	 * @readonly
	 */
	public $fileSyncs;

	/**
	 * @var KalturaThumbParams
	 * @readonly
	 */
	public $thumbParams;

	/**
	 * @var KalturaThumbParamsOutputListResponse
	 * @readonly
	 */
	public $thumbParamsOutputs;
}