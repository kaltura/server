<?php
/**
 * Subclass for representing a row from the 'asset' table, used for timed_thumb_assets
 *
 * @package plugins.thumbCuePoint
 * @subpackage model
 */ 
class timedThumbAsset extends thumbAsset
{
	const CUSTOM_DATA_FIELD_THUMB_CUE_POINT_ID = "thumbCuePointID";
	
	public function postInsert(PropelPDO $con = null)
	{
		$dbCuePoint = CuePointPeer::retrieveByPK($this->getCuePointID());
		
		/* @var $dbCuePoint ThumbCuePoint */
		$dbCuePoint->setAssetId($this->getId());
		$dbCuePoint->save();
		
	    return parent::postInsert();
	}

	/* (non-PHPdoc)
	 * @see Baseasset::applyDefaultValues()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(ThumbCuePointPlugin::getAssetTypeCoreValue(timedThumbAssetType::TIMED_THUMB_ASSET));
	}

	public function getCuePointID()			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_THUMB_CUE_POINT_ID);}
	public function setCuePointID($v)			{$this->putInCustomData(self::CUSTOM_DATA_FIELD_THUMB_CUE_POINT_ID, (string)$v);}

	public function keepOnEntryReplacement()
	{
		return true;
	}

}