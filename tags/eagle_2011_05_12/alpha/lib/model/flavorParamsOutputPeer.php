<?php

/**
 * Subclass for performing query and update operations on the 'flavor_params_output' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class flavorParamsOutputPeer extends assetParamsOutputPeer
{
	/** the related Propel class for this table */
	const OM_CLASS = 'flavorParamsOutput';
	
	/**
	 * @var flavorParamsOutputPeer
	 */
	private static $myInstance;
		
	public function setInstanceCriteriaFilter ()
	{
		if ( self::$s_criteria_filter == null )
			self::$s_criteria_filter = new criteriaFilter ();

		$c = self::$s_criteria_filter->getFilter();
		if($c)
		{
			$c->remove(self::DELETED_AT);
			$c->remove(self::TYPE);
		}
		else
		{
			$c = new Criteria();
		}

		$c->add(self::DELETED_AT, null, Criteria::EQUAL);
		$c->add(self::TYPE, assetType::THUMBNAIL, Criteria::NOT_EQUAL);
			
		self::$s_criteria_filter->setFilter ( $c );
	}

	private function __construct()
	{
	}

	public static function getInstance()
	{
		if(!self::$myInstance)
			self::$myInstance = new flavorParamsOutputPeer();
			
		if(!self::$instance || !(self::$instance instanceof flavorParamsOutputPeer))
			self::$instance = self::$myInstance;
			
		return self::$myInstance;
	}

	public static function doCount(Criteria $criteria, $distinct = false, PropelPDO $con = null)
	{
		self::getInstance();
		return parent::doCount($criteria, $distinct, $con);
	}
	
	public static function doSelect(Criteria $criteria, PropelPDO $con = null)
	{
		self::getInstance();
		return parent::doSelect($criteria, $con);	
	}
	
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		self::getInstance();
		return parent::doSelectOne($criteria, $con);	
	}
	
	public static function doSelectStmt(Criteria $criteria, PropelPDO $con = null)
	{
		self::getInstance();
		return parent::doSelectStmt($criteria, $con);	
	}

	/**
	 * 
	 * @param $flavorAssetId
	 * @param $flavorAssetVersion
	 * @param $con
	 * 
	 * @return flavorParamsOutput
	 */
	public static function retrieveByFlavorAssetId($flavorAssetId, $flavorAssetVersion = null, $con = null)
	{
		$criteria = new Criteria();

		$criteria->add(flavorParamsOutputPeer::FLAVOR_ASSET_ID, $flavorAssetId);
		
		if($flavorAssetVersion)
		{
			$criteria->add(flavorParamsOutputPeer::FLAVOR_ASSET_VERSION, $flavorAssetVersion);
		}
		else 
		{
			$criteria->addDescendingOrderByColumn(flavorParamsOutputPeer::FLAVOR_ASSET_VERSION);
		}

		return flavorParamsOutputPeer::doSelectOne($criteria, $con);
	}
	
	/**
	 * 
	 * @param $flavorAsset
	 * @param $con
	 * 
	 * @return flavorParamsOutput
	 */
	public static function retrieveByFlavorAsset(flavorAsset $flavorAsset, $con = null)
	{
		return self::retrieveByFlavorAssetId($flavorAsset->getId(), $flavorAsset->getVersion(), $con);
	}
	
	public static function doCopy(flavorParams $flavorParams, flavorParamsOutput $flavorParamsOutput)
	{
		$flavorParamsOutput->setFlavorParamsId($flavorParams->getId());
		$flavorParamsOutput->setFlavorParamsVersion($flavorParams->getVersion());
		$flavorParamsOutput->setName($flavorParams->getName());
		$flavorParamsOutput->setTags($flavorParams->getTags());
		$flavorParamsOutput->setDescription($flavorParams->getDescription());
		$flavorParamsOutput->setReadyBehavior($flavorParams->getReadyBehavior());
		$flavorParamsOutput->setIsDefault($flavorParams->getIsDefault());
		$flavorParamsOutput->setFormat($flavorParams->getFormat());
		$flavorParamsOutput->setVideoCodec($flavorParams->getVideoCodec());
		$flavorParamsOutput->setVideoBitrate($flavorParams->getVideoBitrate());
		$flavorParamsOutput->setAudioCodec($flavorParams->getAudioCodec());
		$flavorParamsOutput->setAudioBitrate($flavorParams->getAudioBitrate());
		$flavorParamsOutput->setAudioChannels($flavorParams->getAudioChannels());
		$flavorParamsOutput->setAudioSampleRate($flavorParams->getAudioSampleRate());
		$flavorParamsOutput->setAudioResolution($flavorParams->getAudioResolution());
		$flavorParamsOutput->setWidth($flavorParams->getWidth());
		$flavorParamsOutput->setHeight($flavorParams->getHeight());
		$flavorParamsOutput->setFrameRate($flavorParams->getFrameRate());
		$flavorParamsOutput->setGopSize($flavorParams->getGopSize());
		$flavorParamsOutput->setTwoPass($flavorParams->getTwoPass());
		$flavorParamsOutput->setConversionEngines($flavorParams->getConversionEngines());
		$flavorParamsOutput->setConversionEnginesExtraParams($flavorParams->getConversionEnginesExtraParams());
		$flavorParamsOutput->setCustomData($flavorParams->getCustomData());
		$flavorParamsOutput->save();
		
		return $flavorParamsOutput;
	}
}
