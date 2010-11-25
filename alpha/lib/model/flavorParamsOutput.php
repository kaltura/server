<?php

/**
 * Subclass for representing a row from the 'flavor_params_output' table.
 *
 * 
 *
 * @package lib.model
 */ 
class flavorParamsOutput extends assetParamsOutput
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
		$this->type = assetType::FLAVOR;
	}
	
	public function getCollectionTag()
	{
		$tags = explode(',', $this->getTags());
		foreach(flavorParams::$COLLECTION_TAGS as $tag)
		{
			if(in_array($tag, $tags))
				return $tag;
		}
		return null;
	}
	
	public function setClipOffset($v)	{$this->putInCustomData('ClipOffset', $v);}
	public function getClipOffset()		{return $this->getFromCustomData('ClipOffset');}

	public function setClipDuration($v)	{$this->putInCustomData('ClipDuration', $v);}
	public function getClipDuration()	{return $this->getFromCustomData('ClipDuration');}
}
