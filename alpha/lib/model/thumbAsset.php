<?php
/**
 * Subclass for representing a row from the 'flavor_asset' table, used for thumb_assets
 *
 * 
 *
 * @package lib.model
 */ 
class thumbAsset extends asset 
{
	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->file_ext = 'jpg';
		$this->type = assetType::THUMBNAIL;
	}
}