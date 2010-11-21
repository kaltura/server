<?php

/**
 * Subclass for representing a row from the 'flavor_asset' table.
 *
 * 
 *
 * @package lib.model
 */ 
class flavorAsset extends asset
{
	public function getIsWeb()
	{
		return $this->hasTag(flavorParams::TAG_WEB);
	}
}
