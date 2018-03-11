<?php
/**
 * @package server-infra
 * @subpackage clip
 */

class kClipManager
{

	//Todo: create parent job for the concat and pass it to children
	private $parentConcatJob;

	public function createParentBatchJob()
	{
		$parentConcatJob = new BatchJob();

	}


	/**
	 * @param $entryId
	 * @param $errDescription
	 * @param $partnerId
	 * @param array $operationAttributes
	 * @param int $priority
	 * @return BatchJob[]
	 * @throws APIException
	 * @throws PropelException
	 */
	public function decideAddClipEntryFlavor($entryId, &$errDescription,$partnerId,
	                                         array $operationAttributes, $priority = 0)
	{
/*		if (!$this->parentConcatJob)
		{
			KalturaLog::err("parent Job Must Be Initialize prior to starting children job");
		}*/
		$batch = array();
		$this->createDummyOriginalFlavorAsset($partnerId,$entryId);
		foreach($operationAttributes as $singleAttribute)
		{
		KalturaLog::info("Going To create Flavor for clip: " . print_r($singleAttribute));
		//$dbAsset = kFlowHelper::createOriginalFlavorAsset($partnerId, $entryId);
		$clonedID =	$this->cloneFlavorParam($singleAttribute->getAssetParamsId());
		$batch[] =
			kBusinessPreConvertDL::decideAddEntryFlavor(/**todo : parent job here**/null, $entryId,
				$clonedID, $errDescription, $this->createTempClipFlavorAsset($partnerId,$entryId,$clonedID)->getId()
				, array($singleAttribute) , $priority);
		KalturaLog::info("clip was created batch Element is:" .  print_r(end($batch)));

		}
		return $batch;
	}

	/***
	 * @param $sourceFlavorParamId
	 * @return int
	 * @throws PropelException
	 */
	private function cloneFlavorParam($sourceFlavorParamId)
	{
		//flavorParamsObj = getByPk($sourceFlavorParamId);
		$flavorParamsObj = assetParamsPeer::retrieveByPK($sourceFlavorParamId);
		// unset flavorParamsObj ID
		$flavorParamsObj->setId(null);
		$flavorParamsObj->setNew(true);
		//save the object
		$flavorParamsObj->save();
		//return the object ID
		return $flavorParamsObj->getId();
	}


	/***
	 * @param array $dynamicAttributes
	 * @return bool is clip attribute exist in dynamic attribute
	 */
	public function isClipServiceRequired(array $dynamicAttributes)
	{
		foreach ($dynamicAttributes as $value)
		{
			if ($value instanceof kClipAttributes)
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * @param int $partnerId
	 * @param string $entryId
	 * @return flavorAsset
	 */
	private function createDummyOriginalFlavorAsset($partnerId, $entryId)
	{

		$flavorAsset = assetPeer::retrieveOriginalByEntryId($entryId);
		if($flavorAsset)
		{
			//set Dummy Ready we will update it later
			$flavorAsset->setStatus(flavorAsset::ASSET_STATUS_READY);
			$flavorAsset->save();
			return $flavorAsset;
		}

		$entry = entryPeer::retrieveByPK($entryId);
		if(!$entry)
		{
			KalturaLog::err("Entry [$entryId] not found");
			return null;
		}

		// creates the flavor asset
		$flavorAsset = flavorAsset::getInstance();
		$flavorAsset->setStatus(flavorAsset::ASSET_STATUS_READY);
		$flavorAsset->incrementVersion();
		$flavorAsset->addTags(array(flavorParams::TAG_SOURCE));
		$flavorAsset->setIsOriginal(true);
		$flavorAsset->setFlavorParamsId(flavorParams::SOURCE_FLAVOR_ID);
		$flavorAsset->setPartnerId($partnerId);
		$flavorAsset->setEntryId($entryId);
		$flavorAsset->save();

		return $flavorAsset;
	}




	/**
	 * @param int $partnerId
	 * @param string $entryId
	 * @param $flavorParamId
	 * @return flavorAsset
	 */
	private function createTempClipFlavorAsset($partnerId, $entryId, $flavorParamId)
	{
		$entry = entryPeer::retrieveByPK($entryId);
		if(!$entry)
		{
			KalturaLog::err("Entry [$entryId] not found");
			return null;
		}

		// creates the flavor asset
		$flavorAsset = flavorAsset::getInstance();
		$flavorAsset->setStatus(flavorAsset::ASSET_STATUS_QUEUED);
		$flavorAsset->incrementVersion();
		$flavorAsset->addTags(array(flavorParams::TAG_TEMP_CLIP));
		$flavorAsset->setIsOriginal(false);
		$flavorAsset->setFlavorParamsId($flavorParamId);
		$flavorAsset->setPartnerId($partnerId);
		$flavorAsset->setEntryId($entryId);
		$flavorAsset->save();

		return $flavorAsset;
	}

}