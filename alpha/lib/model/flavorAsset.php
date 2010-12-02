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
	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(assetType::FLAVOR);
	}

	/**
	 * Gets an array of assetParamsOutput objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this asset has previously been saved, it will retrieve
	 * related assetParamsOutputs from storage. If this asset is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array flavorParamsOutput[]
	 * @throws     PropelException
	 */
	public function getflavorParamsOutputs($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(assetPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collassetParamsOutputs === null) {
			if ($this->isNew()) {
			   $this->collassetParamsOutputs = array();
			} else {

				$criteria->add(flavorParamsOutputPeer::FLAVOR_ASSET_ID, $this->id);

				flavorParamsOutputPeer::addSelectColumns($criteria);
				$this->collassetParamsOutputs = flavorParamsOutputPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(flavorParamsOutputPeer::FLAVOR_ASSET_ID, $this->id);

				flavorParamsOutputPeer::addSelectColumns($criteria);
				if (!isset($this->lastassetParamsOutputCriteria) || !$this->lastassetParamsOutputCriteria->equals($criteria)) {
					$this->collassetParamsOutputs = flavorParamsOutputPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastassetParamsOutputCriteria = $criteria;
		return $this->collassetParamsOutputs;
	}

	/**
	 * Get the associated assetParams object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     assetParams The associated assetParams object.
	 * @throws     PropelException
	 */
	public function getFlavorParams(PropelPDO $con = null)
	{
		if ($this->aassetParams === null && ($this->flavor_params_id !== null)) {
			$this->aassetParams = flavorParamsPeer::retrieveByPk($this->flavor_params_id);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aassetParams->addassets($this);
			 */
		}
		return $this->aassetParams;
	}

	const CUSTOM_DATA_FIELD_BITRATE = "FlavorBitrate";
	const CUSTOM_DATA_FIELD_FRAME_RATE = "FlavorFrameRate";
	const CUSTOM_DATA_FIELD_VIDEO_CODEC_ID = "FlavorVideoCodecId";
	
//	Should be uncommented after migration script executed
//	public function getBitrate()			{return $this->getFromCustomData(flavorAsset::CUSTOM_DATA_FIELD_BITRATE);}
//	public function getFrameRate()			{return $this->getFromCustomData(flavorAsset::CUSTOM_DATA_FIELD_FRAME_RATEF);}
//	public function getVideoCodecId()		{return $this->getFromCustomData(flavorAsset::CUSTOM_DATA_FIELD_VIDEO_CODEC_ID);}
	
	public function setBitrate($v)			{$this->putInCustomData(flavorAsset::CUSTOM_DATA_FIELD_BITRATE, $v); return parent::setBitrate($v);}
	public function setFrameRate($v)		{$this->putInCustomData(flavorAsset::CUSTOM_DATA_FIELD_FRAME_RATE, $v); return parent::setFrameRate($v);}
	public function setVideoCodecId($v)		{$this->putInCustomData(flavorAsset::CUSTOM_DATA_FIELD_VIDEO_CODEC_ID, $v); return parent::setVideoCodecId($v);}
	
	public function getIsWeb()
	{
		return $this->hasTag(flavorParams::TAG_WEB);
	}
}
