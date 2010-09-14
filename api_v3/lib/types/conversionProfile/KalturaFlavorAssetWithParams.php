<?php
class KalturaFlavorAssetWithParams extends KalturaObject
{
	/**
	 * The Flavor Asset (Can be null when there are params without asset)
	 * 
	 * @var KalturaFlavorAsset
	 */
	public $flavorAsset;
	
	/**
	 * The Flavor Params
	 * 
	 * @var KalturaFlavorParams
	 */
	public $flavorParams;
	
	/**
	 * The entry id
	 * 
	 * @var string
	 */
	public $entryId;
}