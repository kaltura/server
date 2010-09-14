<?php
class KalturaInvestigateFlavorAssetData extends KalturaObject
{
	/**
	 * @var KalturaFlavorAsset
	 * @readonly
	 */
	public $flavorAsset;

	/**
	 * @var KalturaFileSyncListResponse
	 * @readonly
	 */
	public $fileSyncs;

	/**
	 * @var KalturaMediaInfoListResponse
	 * @readonly
	 */
	public $mediaInfos;

	/**
	 * @var KalturaFlavorParams
	 * @readonly
	 */
	public $flavorParams;

	/**
	 * @var KalturaFlavorParamsOutputListResponse
	 * @readonly
	 */
	public $flavorParamsOutputs;
}